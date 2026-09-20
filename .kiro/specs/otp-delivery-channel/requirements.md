# Requirements Document

## Introduction

The current booking verification flow sends OTP codes exclusively via SMS (iRestSMS). This feature extends the flow so that, before the OTP is dispatched, the customer can choose their preferred delivery channel: **SMS** or **WhatsApp**.

The existing SMS path must remain backward-compatible and unchanged in behavior. WhatsApp OTP delivery reuses the Twilio integration already present in `WhatsappService` (currently used for appointment reminders). All OTP logic — generation, storage, hashing, expiration, attempt counting, and post-verification appointment creation — is shared across both channels and must not be altered by this feature.

The `booking_verifications` table will gain a `delivery_channel` column (migration required) so that the chosen channel is persisted alongside the pending verification record.

---

## Glossary

- **Booking_Verification**: The transient OTP record stored in `booking_verifications`. Has a 5-minute TTL, max 5 verification attempts, and a bcrypt-hashed code.
- **Delivery_Channel**: The transport used to deliver the OTP to the customer. Accepted values: `sms`, `whatsapp`.
- **OTP**: A 6-digit one-time passcode generated during the `send` step, hashed with bcrypt, and validated during the `confirm` step.
- **BookingVerificationController**: The Laravel controller at `app/Http/Controllers/BookingVerificationController.php` that handles `send` and `confirm` actions.
- **SendOtpSmsJob**: The queued job (`app/Jobs/SendOtpSmsJob.php`, `$tries = 3`) that calls `SmsService::sendVerificationCode`.
- **SmsService**: The HTTP wrapper for the iRestSMS provider at `app/Services/SmsService.php`.
- **WhatsappService**: The Twilio WhatsApp API wrapper at `app/Services/WhatsappService.php`.
- **Business**: A tenant entity with plan-gating methods including `canUseWhatsapp(): bool` (returns `true` for premium plan only).
- **Customer**: An anonymous public user who interacts with the booking page at `/book/{slug}`.
- **OTP_Modal**: The frontend component (`BookingOtpModal.vue`) that accepts the 6-digit code and provides a resend action.
- **Details_Modal**: The frontend component (`BookingDetailsModal.vue`) where the customer enters name, phone, and email.
- **Channel_Selector**: The new UI element (to be designed and placed on the Details_Modal or as a pre-OTP step) that presents SMS and WhatsApp as delivery options.
- **Rate_Limiter**: Laravel rate limiting applied at the route level: `otp-send` (3/min, 10/hour per IP) and `otp-confirm` (5/min, 20/hour per IP).
- **Twilio**: The external service used for WhatsApp messaging. Credentials: `services.twilio.sid`, `services.twilio.token`, `services.twilio.whatsapp_from`.

---

## Decision Points

The following items cannot be resolved from existing code alone. They must be explicitly decided before implementation begins.

### Decision Point A — Plan Gating for WhatsApp OTP

**Context**: `Business::canUseWhatsapp()` returns `true` only for premium-plan businesses. CLAUDE.md notes that plan gates are "defined but not consistently enforced in all controllers." WhatsApp is currently used for appointment reminders (a premium feature). Extending WhatsApp to OTP delivery raises the question of whether the same gate should apply.

**Options**:
1. **WhatsApp OTP is premium-only**: The Channel_Selector on the booking page only shows the WhatsApp option when the business has a premium plan. If a non-premium business's booking page somehow receives a `whatsapp` channel value in the `send` request, the backend rejects it with a 422 error.
2. **WhatsApp OTP is available to all plans**: The Channel_Selector shows both options regardless of plan. `canUseWhatsapp()` is not applied to OTP delivery.

**Impact on requirements**: Requirements 2 and 6 below are written to cover both options, with conditional clauses that depend on this decision. The decision must be recorded in the design document before implementation.

**Recommended default if not explicitly decided before implementation**: Option 1 (premium-only), to remain consistent with the existing `canUseWhatsapp()` semantics and avoid silently expanding a plan-gated feature.

### Decision Point B — Twilio WhatsApp Message Format

**Context**: Twilio WhatsApp Business messaging for transactional/authentication use cases may require a pre-approved message template (especially for messages sent outside a 24-hour customer-initiated window). The OTP is always system-initiated, so a free-form message may be blocked by WhatsApp Business Policy.

**Options**:
1. **Use a pre-approved Twilio content template** (e.g., Twilio Content API template for OTP/authentication messages). The backend passes the template SID and the OTP as a substitution variable. This is the most reliable approach for production use.
2. **Use a free-form message** (same pattern as `sendAppointmentReminder`). This may work in sandbox mode or in regions where the business account has session messaging active, but is not guaranteed in production.

**Impact on requirements**: Requirement 4 (WhatsApp Channel) documents both options. The design document must specify which approach is used and supply the template SID (if applicable).

### Decision Point C — Channel Switching on Resend

**Context**: The Channel_Selector is shown before the OTP_Modal opens. Once the OTP_Modal is open, the customer only sees the code-entry UI and a "Resend" button. The design question is whether a resend should allow the customer to go back and pick a different channel, or whether it should silently reuse the channel already chosen.

**Options**:
1. **Resend reuses the previously chosen channel**: The channel is stored in frontend state when the OTP_Modal opens. Resend posts the same `delivery_channel` value without showing the selector again.
2. **Resend allows channel switching**: The customer can close the OTP_Modal, return to the Details_Modal (which still shows the Channel_Selector), change the channel, and trigger a new send.

**Impact on requirements**: Requirement 7 (Resend) is written for Option 1 as the default, which is consistent with the current UI flow (channel selection is a pre-OTP step). Document the choice in the design document.

---

## Requirements

### Requirement 1: Delivery Channel Selection UI

**User Story:** As a Customer booking an appointment, I want to choose whether my OTP is delivered via SMS or WhatsApp before I confirm my details, so that I can receive the code on the channel I prefer and am most likely to see quickly.

#### Acceptance Criteria

1. THE Channel_Selector SHALL display two options to the Customer: SMS and WhatsApp.
2. WHEN the Customer has not yet selected a channel, THE Channel_Selector SHALL present both options in a visually equal manner with no default pre-selected.
3. WHEN the Customer selects a channel, THE Channel_Selector SHALL visually indicate the selected state.
4. THE Details_Modal SHALL NOT allow the Customer to proceed to the OTP step until a delivery channel has been selected.
5. WHERE the resolved answer to Decision Point A is "premium-only", THE Channel_Selector SHALL display the WhatsApp option only when the Business has a premium plan; for non-premium businesses THE Channel_Selector SHALL show SMS only and the channel selection step SHALL be skipped (SMS is used automatically).
6. WHERE the resolved answer to Decision Point A is "available to all plans", THE Channel_Selector SHALL display both options regardless of the Business plan.
7. THE Channel_Selector SHALL include accessible labels (ARIA attributes) so that screen readers can identify each option.
8. THE Channel_Selector SHALL render correctly in both LTR (English) and RTL (Arabic, Hebrew) layouts.

---

### Requirement 2: Delivery Channel Transmission to Backend

**User Story:** As the System, I need the selected delivery channel to be sent to the backend with the OTP send request so that the correct transport can be used to deliver the OTP.

#### Acceptance Criteria

1. WHEN the Customer submits the OTP send request, THE Booking_Page SHALL include a `delivery_channel` field in the POST body sent to `BookingVerificationController@send`.
2. THE `delivery_channel` field SHALL be a string with one of the values: `sms` or `whatsapp`.
3. WHEN `delivery_channel` is absent from the request, THE BookingVerificationController SHALL return a 422 validation error.
4. WHEN `delivery_channel` contains any value other than `sms` or `whatsapp`, THE BookingVerificationController SHALL return a 422 validation error.
5. WHERE the resolved answer to Decision Point A is "premium-only", WHEN `delivery_channel` is `whatsapp` and the Business does not have a premium plan, THE BookingVerificationController SHALL return a 422 error indicating that WhatsApp OTP is not available for this business.
6. THE `delivery_channel` value SHALL be validated server-side; the backend SHALL NOT trust the client-supplied value without explicit validation against the accepted enum.

---

### Requirement 3: Schema Change — `delivery_channel` Column

**User Story:** As a Developer, I need the `booking_verifications` table to store the selected delivery channel so that the correct transport is reliably associated with each pending OTP record without re-reading it from the request.

#### Acceptance Criteria

1. THE System SHALL add a `delivery_channel` column of type `string` (e.g., `VARCHAR(20)`) to the `booking_verifications` table via a new Laravel migration.
2. THE `delivery_channel` column SHALL NOT allow null values.
3. THE `delivery_channel` column SHALL store only the validated value (`sms` or `whatsapp`); the migration MAY add a CHECK constraint or the application layer SHALL enforce this.
4. THE `BookingVerification` model SHALL include `delivery_channel` in its `$fillable` array.
5. THE `BookingVerificationController@send` method SHALL persist the validated `delivery_channel` value into the new `BookingVerification` record.
6. THE `BookingVerificationController@confirm` method SHALL NOT read or validate `delivery_channel`; OTP verification logic SHALL remain channel-agnostic.

---

### Requirement 4: SMS OTP Delivery

**User Story:** As a Customer who selects SMS, I want my OTP delivered via the existing iRestSMS integration, so that the current behavior is preserved and my booking is not disrupted.

#### Acceptance Criteria

1. WHEN `delivery_channel` is `sms`, THE BookingVerificationController SHALL dispatch `SendOtpSmsJob` with the normalised phone number and the plaintext OTP code, exactly as the current implementation does.
2. THE `SendOtpSmsJob` SHALL call `SmsService::sendVerificationCode` with 3 retry attempts, unchanged from the current behavior.
3. WHEN the SMS send request succeeds, THE BookingVerificationController SHALL return `{"success": true}` with HTTP 200.
4. THE OTP_Modal description shown to the Customer after an SMS send SHALL use a translation key that conveys "code sent via SMS to {phone}".
5. THE existing SMS delivery path SHALL NOT be altered by this feature in any way that changes current behavior for `delivery_channel = sms`.

---

### Requirement 5: WhatsApp OTP Delivery

**User Story:** As a Customer who selects WhatsApp, I want my OTP delivered to my WhatsApp account on the same phone number, so that I can complete booking verification using a channel I actively monitor.

#### Acceptance Criteria

1. WHEN `delivery_channel` is `whatsapp`, THE BookingVerificationController SHALL dispatch a queued job (new or extended) that calls a new `WhatsappService::sendOtpCode(string $phone, string $code)` method (or equivalent) using the Twilio Messages API.
2. THE WhatsApp OTP message SHALL be sent to a recipient value formed by prefixing `whatsapp:` to the normalised phone number (in `+972` E.164 form as produced by the existing phone normalisation), using the Twilio credentials configured at `services.twilio.sid`, `services.twilio.token`, and `services.twilio.whatsapp_from`.
3. WHEN the WhatsApp send request to Twilio returns an HTTP status in the range 200 to 299 inclusive, THE BookingVerificationController SHALL return a response body of `{"success": true}` with HTTP status 200.
4. IF the WhatsApp send request to Twilio returns an HTTP status outside the range 200 to 299 inclusive, or the request fails to complete within a 10 second timeout, THEN THE WhatsappService SHALL throw a runtime exception so the queued job records the failure (failure handling and record cleanup are governed by Requirement 8).
5. THE OTP_Modal description shown to the Customer after a WhatsApp send SHALL use a single translation key whose rendered text conveys "code sent via WhatsApp to {phone}", where `{phone}` is substituted with the normalised phone number.
6. WHERE Decision Point B is resolved as "use a pre-approved template", THE WhatsappService OTP method SHALL pass the template SID and the OTP code as the substitution variable, and SHALL NOT embed the OTP in a free-form `Body` string sent to the Twilio API.
7. WHERE Decision Point B is resolved as "use a free-form message", THE WhatsappService OTP method SHALL compose a message body containing the OTP code and the business name and send it as the `Body` parameter, AND the design document SHALL state that this path may require an active WhatsApp Business session window.
8. THE WhatsappService OTP method SHALL NOT write the OTP code to any log; WHEN the WhatsappService logs a send attempt or outcome, THE log entry SHALL contain only the phone number (masked where possible) and the Twilio response status, and SHALL NOT contain the OTP code.
9. THE WhatsApp OTP delivery job SHALL set the maximum retry count to 3 attempts (`$tries = 3`), consistent with `SendOtpSmsJob`.

---

### Requirement 6: Plan Gating Enforcement (Server-Side)

**User Story:** As the Platform, I need WhatsApp OTP availability to be enforced on the server side consistent with the resolved plan gating decision, so that plan-gated features cannot be bypassed by manipulating the client request.

#### Acceptance Criteria

1. WHERE the resolved answer to Decision Point A is "premium-only":
   a. WHEN `delivery_channel` is `whatsapp` and `$business->canUseWhatsapp()` returns `false`, THE BookingVerificationController SHALL return HTTP 422 with a message indicating WhatsApp is not available for this business.
   b. THE enforcement check SHALL occur in `BookingVerificationController@send` after channel validation and before OTP generation.
   c. THE frontend Channel_Selector SHALL hide the WhatsApp option for non-premium businesses (defense-in-depth), but the server-side check SHALL be the authoritative gate.
2. WHERE the resolved answer to Decision Point A is "available to all plans":
   a. THE `canUseWhatsapp()` method SHALL NOT be called during OTP send for channel gating purposes.
   b. THE requirements document MUST note that this would expand WhatsApp usage beyond the current premium gate and requires explicit product approval.
3. In either case, THE plan-gating check SHALL NOT be applied in `BookingVerificationController@confirm`; verification is always channel-agnostic.

---

### Requirement 7: Resend Behavior

**User Story:** As a Customer who did not receive the OTP, I want to request a new code via the same channel I originally chose, so that I do not need to restart the booking flow or re-select a channel.

#### Acceptance Criteria

1. WHEN the Customer taps "Resend" in the OTP_Modal, THE Booking_Page SHALL re-submit the `send` request with the same `delivery_channel` value that was used for the original send.
2. THE `delivery_channel` value SHALL be retained in frontend component state from the original send until the OTP_Modal is closed or the booking flow is restarted.
3. WHEN a resend `send` request is received, THE BookingVerificationController SHALL delete the prior `Booking_Verification` for the same business+phone and create a new one, as it does today.
4. THE new `Booking_Verification` created on resend SHALL store the same `delivery_channel` as the resend request.
5. THE existing rate limits (`otp-send`: 3/min per IP, 10/hour per IP) SHALL apply to resend requests identically to initial send requests.
6. THE OTP_Modal SHALL NOT re-display the Channel_Selector on resend; channel selection is a pre-OTP step and is considered final once the OTP_Modal is open. (See Decision Point C.)
7. IF the Customer wishes to change channels, THE Customer SHALL close the OTP_Modal, which returns to the Details_Modal where the Channel_Selector is visible and a new channel can be selected before triggering a new send.

---

### Requirement 8: Delivery Failure Handling

**User Story:** As a Customer, I want to receive a clear error message when OTP delivery fails, so that I know the code was not sent and can take corrective action (e.g., try the other channel or check my number).

#### Acceptance Criteria

1. WHEN `SmsService::sendVerificationCode` throws a `RuntimeException` (SMS delivery failure), THE BookingVerificationController SHALL catch the exception and return HTTP 500 with a customer-facing error message (translated) that does not expose provider credentials, stack traces, or internal error details.
2. WHEN the Twilio WhatsApp API returns a non-2xx response (WhatsApp delivery failure), THE WhatsappService OTP method SHALL throw a `RuntimeException`. THE BookingVerificationController SHALL catch it and return HTTP 500 with a customer-facing error message (translated).
3. THE System SHALL NOT silently fall back from WhatsApp to SMS, or from SMS to WhatsApp, under any failure condition. If delivery on the selected channel fails, the error SHALL be surfaced to the customer.
4. IF the phone number is not registered on WhatsApp (Twilio returns a specific error for unregistered recipients), THE error message returned to the customer SHALL be distinct and suggest trying SMS instead. The design document shall identify the specific Twilio error code that indicates this condition.
5. WHEN a delivery error occurs (either channel), THE System SHALL log the following fields: phone number, `delivery_channel`, HTTP status returned by the provider, and the provider's error body or error code. THE System SHALL NOT log the OTP code, the Twilio auth token, or the iRestSMS API key.
6. WHEN a delivery error is caught and an error response is returned, THE System SHALL NOT persist the `Booking_Verification` record, OR the previously created record SHALL be deleted, so that the failed OTP does not occupy the business+phone slot and block a subsequent retry.
7. THE frontend SHALL display the translated error message received from the backend in the Details_Modal (before the OTP_Modal opens) when the initial send fails.
8. THE frontend SHALL display the translated error message in the OTP_Modal when a resend fails.

---

### Requirement 9: Shared OTP Logic Preservation

**User Story:** As the System, I need all OTP generation, storage, and verification logic to remain unchanged regardless of delivery channel, so that the security properties of the booking flow are not degraded.

#### Acceptance Criteria

1. THE OTP code SHALL always be a cryptographically random 6-digit integer (range 100000–999999), generated using `random_int`, regardless of `delivery_channel`.
2. THE `code_hash` stored in `booking_verifications` SHALL always be a bcrypt hash of the plaintext code, regardless of `delivery_channel`.
3. THE `expires_at` SHALL always be set to `now()->addMinutes(5)`, regardless of `delivery_channel`.
4. THE maximum attempt count before lockout SHALL remain 5, regardless of `delivery_channel`.
5. THE `BookingVerificationController@confirm` method SHALL NOT accept or read a `delivery_channel` field; the verification logic SHALL be identical for all channels.
6. WHEN OTP verification succeeds, THE appointment creation transaction (create `Appointment`, set `verified_at`, dispatch `NewAppointmentCreatedNotification`) SHALL execute identically regardless of which channel delivered the OTP.
7. THE slot double-booking guard in `confirm()` (check that `start_time` is not already taken before creating the `Appointment`) SHALL remain in place and SHALL NOT be affected by this feature.
8. THE prior-record deletion logic in `send()` (delete existing `Booking_Verification` for same business+phone before creating a new one) SHALL remain in place for both channels.

---

### Requirement 10: Security Constraints

**User Story:** As the Platform, I need the delivery channel feature to not introduce new attack surfaces or weaken existing security protections in the booking flow.

#### Acceptance Criteria

1. THE `delivery_channel` value SHALL be validated on the server side against an explicit allowlist (`['sms', 'whatsapp']`) before any OTP is generated or any job is dispatched.
2. THE OTP plaintext code SHALL NOT appear in any log entry at any log level, on either the SMS or WhatsApp delivery path.
3. THE Twilio auth token (`services.twilio.token`) and the iRestSMS API key (`services.sms.api_key`) SHALL NOT appear in any log entry.
4. WHEN a `Booking_Verification` record exists and an OTP has been sent, THE customer SHALL NOT be able to switch the `delivery_channel` for that record by submitting a new confirm request with a different channel value, because `confirm()` does not accept a `delivery_channel` field.
5. THE existing protections — attempt limit (max 5), expiry (5 minutes), slot-taken check, and duplicate appointment prevention — SHALL all remain enforced after this feature is added.
6. THE rate limiter configuration (`otp-send`, `otp-confirm`) SHALL NOT be changed by this feature.
7. WHEN the business is inactive (`$business->isActive()` returns `false`), THE `send` and `confirm` endpoints SHALL continue to abort with 404, regardless of `delivery_channel`.

---

### Requirement 11: Internationalisation

**User Story:** As a Customer who uses the booking page in Arabic, Hebrew, or English, I want all new UI strings related to channel selection and OTP delivery to be presented in my chosen language and in the correct text direction.

#### Acceptance Criteria

1. THE System SHALL add translation keys for all new customer-facing strings to `resources/js/i18n/locales/en.ts`, `ar.ts`, and `he.ts` simultaneously; no key SHALL be present in one locale file but absent from another.
2. THE Channel_Selector labels ("SMS", "WhatsApp") SHALL have translation keys; even if the brand names are not translated, the surrounding UI copy (e.g., "Choose how to receive your code") SHALL be translated.
3. THE OTP_Modal description key (`booking.verifyDescription`) SHALL be replaced with two variants:
   - `booking.verifyDescriptionSms` — "Enter the verification code sent via SMS to {phone}"
   - `booking.verifyDescriptionWhatsapp` — "Enter the verification code sent via WhatsApp to {phone}"
   Both keys SHALL be present in all three locale files.
4. Error messages returned from the backend that are shown to the customer (delivery failure, invalid channel, plan-gate rejection) SHALL use translation keys in all three locale files.
5. Arabic and Hebrew translation values SHALL be grammatically correct RTL strings; existing RTL layout mechanisms SHALL be preserved.
6. THE Channel_Selector component SHALL not introduce any hardcoded English strings.

---

### Requirement 12: Test Coverage

**User Story:** As a Developer, I need automated tests to cover the new delivery channel logic so that regressions are caught before deployment.

#### Acceptance Criteria

The following test cases SHALL be covered by the automated test suite (PHPUnit for backend; existing frontend test conventions if applicable):

1. **SMS success path**: WHEN a valid request with `delivery_channel = sms` is posted to `send`, THE System SHALL create a `Booking_Verification` with `delivery_channel = sms` and dispatch `SendOtpSmsJob`.
2. **WhatsApp success path**: WHEN a valid request with `delivery_channel = whatsapp` is posted to `send` (and the business satisfies plan requirements per the resolved Decision Point A), THE System SHALL create a `Booking_Verification` with `delivery_channel = whatsapp` and dispatch the WhatsApp OTP job.
3. **Invalid channel rejection**: WHEN `delivery_channel` is absent or contains an unsupported value, THE `send` endpoint SHALL return HTTP 422 and SHALL NOT create a `Booking_Verification` or dispatch any job.
4. **Channel-agnostic confirmation**: WHEN `confirm` is called with valid phone and code after a WhatsApp-delivered OTP, THE System SHALL successfully verify the OTP and create the `Appointment`, regardless of the stored `delivery_channel`.
5. **Expired OTP**: WHEN `confirm` is called after `expires_at` has passed, THE System SHALL return HTTP 422 and SHALL NOT create an `Appointment`.
6. **Incorrect OTP**: WHEN `confirm` is called with a wrong code, THE System SHALL return HTTP 422, increment `attempts`, and SHALL NOT create an `Appointment`.
7. **Exceeded attempt limit**: WHEN `confirm` is called a 6th time (after 5 failed attempts), THE System SHALL return HTTP 429 and SHALL NOT create an `Appointment`.
8. **Resend resets record**: WHEN `send` is called a second time for the same business+phone, THE System SHALL delete the prior `Booking_Verification` and create a new one with a new `expires_at` and `code_hash`.
9. **SMS delivery failure**: WHEN `SmsService::sendVerificationCode` throws, THE `send` endpoint SHALL return HTTP 500 with a translated error message and SHALL NOT leave an unverifiable `Booking_Verification` that blocks subsequent retries (per Requirement 8, AC 6).
10. **WhatsApp delivery failure**: WHEN the Twilio API returns a non-2xx response, THE `send` endpoint SHALL return HTTP 500 with a translated error message and SHALL NOT silently fall back to SMS.
11. **Appointment created after WhatsApp verification**: WHEN OTP verification succeeds following WhatsApp delivery, THE System SHALL create an `Appointment` with the correct `status` (based on `confirmation_mode`) and fire `NewAppointmentCreatedNotification`.
12. **No duplicate appointment on double confirm**: WHEN `confirm` is called twice in rapid succession with a valid code, THE System SHALL create the `Appointment` only once; the second call SHALL return an error indicating the slot is taken.
13. **Plan-gate enforcement (if Decision Point A resolves to premium-only)**: WHEN `delivery_channel = whatsapp` is posted for a non-premium business, THE `send` endpoint SHALL return HTTP 422 and SHALL NOT dispatch the WhatsApp job.
14. **OTP code not logged**: Test logs produced during the SMS and WhatsApp delivery paths SHALL be asserted to NOT contain the OTP plaintext code.
