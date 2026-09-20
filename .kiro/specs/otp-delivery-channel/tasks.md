# Implementation Plan: OTP Delivery Channel

## Overview

This plan extends the existing Laravel + Vue/Inertia booking OTP flow so the Customer chooses **SMS** or **WhatsApp** before the OTP is dispatched. It is an incremental extension of the current flow — SMS behavior and the channel-agnostic `confirm()` remain unchanged.

Confirmed decisions (from the design): WhatsApp OTP is **premium-only** (authoritative server-side gate), Twilio uses a **pre-approved Content template** (`ContentSid` + `ContentVariables`), and **resend reuses** the originally chosen channel.

Build order (each step builds on the previous, ending with wiring): schema → model → WhatsApp delivery path (service + job) → config → controller wiring → frontend types/props → frontend components → i18n → tests. Implementation tasks are paired with their tests so functionality is verified as it lands.

Concrete files touched:
- `database/migrations/{new}_add_delivery_channel_to_booking_verifications_table.php`
- `app/Models/BookingVerification.php`
- `app/Services/WhatsappService.php`
- `app/Jobs/SendOtpWhatsappJob.php` (new)
- `config/services.php`, `.env.example`
- `app/Http/Controllers/BookingVerificationController.php`
- `app/Http/Controllers/BookingController.php`
- `resources/js/types/global.d.ts`
- `resources/js/Pages/Booking/Show.vue`
- `resources/js/Components/Booking/BookingDetailsModal.vue`
- `resources/js/Components/Booking/BookingOtpModal.vue`
- `resources/js/i18n/locales/{en,ar,he}.ts`
- `tests/Feature/*`, `tests/Unit/*`

## Tasks

- [x] 1. Schema and model for `delivery_channel`
  - [x] 1.1 Create migration adding `delivery_channel` to `booking_verifications`
    - Create a new timestamped migration `database/migrations/{timestamp}_add_delivery_channel_to_booking_verifications_table.php` (do NOT modify `2026_05_20_180234_create_booking_verifications_table.php`)
    - `up()`: `$table->string('delivery_channel', 20)->default('sms')->after('customer_email');` — VARCHAR(20), NOT NULL, safe default `'sms'`
    - `down()`: drop the `delivery_channel` column
    - Allowed-value constraint enforced at application layer (validation), not a DB CHECK
    - _Requirements: 3.1, 3.2, 3.3_

  - [x] 1.2 Add `delivery_channel` to the `BookingVerification` model
    - In `app/Models/BookingVerification.php`, add `'delivery_channel'` to `$fillable`
    - No cast needed (plain string); no other model change
    - _Requirements: 3.4_

  - [x]* 1.3 Smoke test: migration adds the column
    - Test in `tests/Feature/` asserting `Schema::hasColumn('booking_verifications', 'delivery_channel')` after migration
    - _Requirements: 3.1_

- [x] 2. WhatsApp OTP delivery path (service + job)
  - [x] 2.1 Add config key for the Twilio OTP template
    - In `config/services.php`, add `'otp_template_sid' => env('TWILIO_OTP_TEMPLATE_SID')` under the `twilio` array
    - Add/document `TWILIO_OTP_TEMPLATE_SID` in `.env.example`
    - _Requirements: 5.6_

  - [x] 2.2 Implement `WhatsappService::sendOtpCode(string $phone, string $code): void`
    - In `app/Services/WhatsappService.php`, add a new method following the existing `sendAppointmentReminder` Twilio pattern (Basic Auth, `Http::asForm()`); do NOT introduce a new class or channel abstraction
    - POST to the Twilio Messages API with `From` = `config('services.twilio.whatsapp_from')`, `To` = `'whatsapp:' . $phone` (normalised `+972` E.164), `ContentSid` = `config('services.twilio.otp_template_sid')`, `ContentVariables` = `json_encode(['1' => $code])` — OTP passed only as a template variable, never in a free-form `Body`
    - Apply a 10s HTTP timeout; throw `RuntimeException` on non-2xx response or timeout
    - Add a `maskPhone()` helper; log only masked phone + Twilio status/error code — never the OTP plaintext, Twilio auth token, or SMS api_key
    - _Requirements: 5.1, 5.2, 5.4, 5.6, 5.8, 8.5, 10.2, 10.3_

  - [x]* 2.3 Integration test for `WhatsappService::sendOtpCode`
    - `tests/Feature/` (or `tests/Unit/`) using `Http::fake()`: assert request `To == whatsapp:{normalised phone}`, `ContentSid` is set, OTP is passed via `ContentVariables` and NOT in `Body`
    - Assert a non-2xx Twilio response throws `RuntimeException`
    - _Requirements: 5.2, 5.4, 5.6_

  - [x]* 2.4 Property test: OTP plaintext never appears in logs (WhatsApp path)
    - **Property 7: OTP plaintext never appears in logs**
    - **Validates: Requirements 5.8, 10.2**
    - Tag: `Feature: otp-delivery-channel, Property 7`; use a log spy; assert the generated code substring is absent across the WhatsApp delivery path

  - [x] 2.5 Create `SendOtpWhatsappJob`
    - New `app/Jobs/SendOtpWhatsappJob.php` mirroring `SendOtpSmsJob`: `ShouldQueue`, `use Queueable`, `public int $tries = 3`, constructor `(public string $phone, public string $code)`, `handle(WhatsappService $whatsapp)` calls `$whatsapp->sendOtpCode($this->phone, $this->code)`
    - _Requirements: 5.1, 5.9_

  - [x]* 2.6 Smoke test: `SendOtpWhatsappJob::$tries === 3`
    - _Requirements: 5.9_

- [x] 3. Controller wiring — `send()` channel selection, gating, dispatch, failure handling
  - [x] 3.1 Add `delivery_channel` validation and premium gate to `send()`
    - In `app/Http/Controllers/BookingVerificationController.php`, add `'delivery_channel' => ['required', Rule::in(['sms', 'whatsapp'])]` to the inline `$request->validate([...])` (`use Illuminate\Validation\Rule;`) — absent or out-of-allowlist → 422
    - Immediately after validation and BEFORE OTP generation: if `delivery_channel === 'whatsapp' && ! $business->canUseWhatsapp()`, return HTTP 422 with `__('booking.whatsappNotAvailable')` — no record created, no job dispatched
    - Preserve all existing steps: active check, phone normalisation, duplicate-slot check
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6, 6.1, 10.1, 10.7_

  - [x] 3.2 Persist channel and dispatch the correct transport with failure handling
    - Include `'delivery_channel' => $validated['delivery_channel']` in the `BookingVerification::create([...])` payload; keep prior-record deletion and OTP generation exactly as-is
    - Replace the unconditional `SendOtpSmsJob::dispatch(...)` with a `match` on the channel using `dispatchSync` inside a `try/catch`: `'sms' => SendOtpSmsJob::dispatchSync(...)`, `'whatsapp' => SendOtpWhatsappJob::dispatchSync(...)`
    - On caught delivery failure: delete the just-created `BookingVerification`, log masked phone + `delivery_channel` + provider status (never OTP/credentials), return translated HTTP 500 (`booking.otpDeliveryFailed`); for the Twilio "number not on WhatsApp" error code return `booking.whatsappNumberNotFound`
    - No silent fallback between channels
    - _Requirements: 3.5, 4.1, 4.2, 5.1, 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 9.8_

  - [x] 3.3 Verify `confirm()` remains unchanged (channel-agnostic)
    - Confirm `confirm()` does NOT accept, read, or validate `delivery_channel`; appointment-creation transaction, `verified_at`, manager notification, slot re-check, expiry/attempts/hash logic all unchanged
    - _Requirements: 3.6, 6.3, 9.5, 9.6, 9.7, 10.4_

  - [x] 3.4 Expose `whatsappEnabled` to the booking page
    - In `app/Http/Controllers/BookingController.php@show`, add `'whatsappEnabled' => $business->canUseWhatsapp()` to the `Inertia::render('Booking/Show', [...])` payload (defense-in-depth; server remains authoritative gate)
    - _Requirements: 6.1c_

- [x] 4. Checkpoint — backend
  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Frontend — types, page state, components
  - [x] 5.1 Add `DeliveryChannel` type and prop types
    - In `resources/js/types/global.d.ts`, add `export type DeliveryChannel = 'sms' | 'whatsapp';`
    - Add `whatsappEnabled: boolean` to the `Show.vue` props type and the new modal prop types (`channel`, `selectedChannel`)
    - _Requirements: 2.1, 2.2_

  - [x] 5.2 Wire channel state into `Show.vue`
    - In `resources/js/Pages/Booking/Show.vue`: accept the `whatsappEnabled: boolean` prop; add `const selectedChannel = ref<DeliveryChannel | null>(null)`
    - In `requestOtp`, include `delivery_channel: selectedChannel.value` in the POST body; for non-premium businesses default `selectedChannel` to `'sms'`
    - Retain `selectedChannel` across resend (do not clear when the OTP modal opens); pass `:channel="selectedChannel"` to `BookingOtpModal` and `:whatsapp-enabled` / `v-model:selected-channel` to `BookingDetailsModal`
    - Route backend errors as today: initial-send failure → `bookingError` (details modal), resend failure → `otpError` (OTP modal)
    - _Requirements: 2.1, 7.1, 7.2, 8.7, 8.8_

  - [x] 5.3 Add the Channel_Selector to `BookingDetailsModal.vue`
    - In `resources/js/Components/Booking/BookingDetailsModal.vue`, add a Channel_Selector above the Confirm action: two options (SMS, WhatsApp), visually equal, NO default pre-selected; selected option shows a visual selected state
    - New props `whatsappEnabled: boolean` and `v-model:selected-channel`; when `whatsappEnabled` is false, render SMS only and auto-select SMS (skip the step)
    - Disable the Confirm button until a channel is selected (extend existing `:disabled` guard)
    - Each option is a labelled radio-style control (`role="radio"`/`aria-checked` or fieldset of radios) with an accessible group label; use existing booking CSS classes for LTR/RTL; no hardcoded strings (use i18n keys)
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.7, 1.8, 11.2, 11.6_

  - [x] 5.4 Channel-specific description in `BookingOtpModal.vue`
    - In `resources/js/Components/Booking/BookingOtpModal.vue`, add prop `channel: DeliveryChannel`; replace the single `booking.verifyDescription` usage with `booking.verifyDescriptionSms` / `booking.verifyDescriptionWhatsapp` chosen from `channel`, with `{phone}` substitution
    - Do NOT render the Channel_Selector here; resend continues to emit `resend` with no channel argument (parent reuses stored channel)
    - _Requirements: 4.4, 5.5, 7.6, 7.7, 11.3_

- [x] 6. Internationalisation
  - [x] 6.1 Add new translation keys to all three locales
    - Add to `resources/js/i18n/locales/en.ts`, `ar.ts`, and `he.ts` simultaneously (no key present in one file but absent from another): `booking.chooseChannel`, `booking.channelSms`, `booking.channelWhatsapp`, `booking.verifyDescriptionSms`, `booking.verifyDescriptionWhatsapp`, `booking.otpDeliveryFailed`, `booking.whatsappNotAvailable`, `booking.whatsappNumberNotFound`, `booking.invalidChannel`
    - Arabic and Hebrew values must be grammatically correct RTL strings; preserve existing RTL layout mechanisms
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5, 11.6_

  - [x]* 6.2 Property test: translation key parity across locales
    - **Property 10: Translation key parity across locales**
    - **Validates: Requirement 11.1**
    - Tag: `Feature: otp-delivery-channel, Property 10`; assert every new `booking.*` key exists in `en`, `ar`, and `he`

- [x] 7. Checkpoint — feature complete
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 8. Test harness setup for property-based testing — **SKIPPED by user decision**: no new PBT dependency added; Properties 1-10 implemented instead as plain PHPUnit tests (loops of ≥100 iterations, same `Feature:` tag convention) in tasks 2.4, 6.2, and 9.1-9.11.
  - [ ]* 8.1 Add and configure a PHP property-based testing library
    - Add an existing PHP PBT library as a dev dependency via composer (e.g. `giorgiosironi/eris`); do not implement generators from scratch
    - Verify the PBT harness runs under PHPUnit (each property test ≥ 100 iterations); establish the tag convention `Feature: otp-delivery-channel, Property {n}: {property text}`
    - _Requirements: 12 (test infrastructure)_

- [x] 9. Backend behavior tests (Requirement 12 mapping + properties)
  - [x]* 9.1 Property test: invalid/absent channel rejected with no side effects
    - **Property 1: Invalid or absent channel is rejected with no side effects**
    - **Validates: Requirements 2.2, 2.3, 2.4, 2.6, 10.1** (Req 12.3)
    - Arbitrary invalid/missing `delivery_channel` → 422, no `BookingVerification`, no job (`Queue::fake()`)

  - [x]* 9.2 Property test: persisted channel equals submitted; resend resets record
    - **Property 2: Persisted channel equals the submitted channel**
    - **Validates: Requirements 3.5, 7.1, 7.2, 7.3, 7.4, 9.8** (Req 12.1, 12.8)
    - Accepted send → record `delivery_channel` == submitted; second send for same business+phone deletes prior record and creates a new one with fresh `expires_at`/`code_hash` and same channel

  - [x]* 9.3 Property test: correct transport dispatched for the chosen channel
    - **Property 4: Correct transport is dispatched for the chosen channel**
    - **Validates: Requirements 4.1, 4.2, 5.1, 5.2, 5.6** (Req 12.1, 12.2)
    - `Queue::fake()`: `sms` → `SendOtpSmsJob`, `whatsapp` (premium) → `SendOtpWhatsappJob`, carrying normalised phone + code

  - [x]* 9.4 Property test: premium gate blocks WhatsApp for non-premium with no side effects
    - **Property 5: Premium gate blocks WhatsApp for non-premium businesses with no side effects**
    - **Validates: Requirements 2.5, 6.1** (Req 12.13)
    - Non-premium business + `whatsapp` → 422, no record, no WhatsApp job

  - [x]* 9.5 Property test: verification is channel-agnostic
    - **Property 3: Verification is channel-agnostic**
    - **Validates: Requirements 3.6, 6.3, 9.5, 9.6** (Req 12.4, 12.11)
    - Seed verification with `whatsapp`; `confirm` with valid code creates `Appointment` and fires `NewAppointmentCreatedNotification` (`Notification::fake()`), identical to `sms`

  - [x]* 9.6 Property test: no silent channel fallback
    - **Property 6: No silent channel fallback**
    - **Validates: Requirement 8.3** (Req 12.10)
    - When the selected channel's delivery fails, the other channel's job/service is never dispatched or invoked

  - [x]* 9.7 Property test: shared OTP invariants hold regardless of channel
    - **Property 8: Shared OTP invariants hold regardless of channel**
    - **Validates: Requirements 9.1, 9.2, 9.3, 9.4**
    - For either channel: code is 6-digit int in [100000, 999999], `code_hash` bcrypt-verifies, `expires_at` == creation + 5 min, `attempts` starts at 0, lockout at 5

  - [x]* 9.8 Property test: at most one appointment per verification
    - **Property 9: At most one appointment per verification**
    - **Validates: Requirements 9.7, 10.5** (Req 12.12)
    - Two `confirm` calls → exactly one `Appointment`; second → slot-taken error

  - [x]* 9.9 Example/edge tests: expired, incorrect, exceeded-attempts OTP
    - Expired (`expires_at` past) → 422, no `Appointment` (Req 12.5)
    - Wrong code → 422, `attempts` incremented, no `Appointment` (Req 12.6)
    - 6th confirm after 5 fails → 429, no `Appointment` (Req 12.7)
    - _Requirements: 9.3, 9.4, 12.5, 12.6, 12.7_

  - [x]* 9.10 Example tests: delivery failure paths
    - Force `SmsService::sendVerificationCode` to throw (`dispatchSync`) → 500, translated message, record deleted so no blocking record (Req 12.9)
    - `Http::fake()` Twilio non-2xx → 500, translated message, no SMS fallback (Req 12.10)
    - _Requirements: 8.1, 8.2, 8.3, 8.6, 12.9, 12.10_

  - [x]* 9.11 Test: OTP plaintext not logged across both paths
    - Log spy over SMS and WhatsApp send paths → generated code substring absent (Req 12.14)
    - **Validates: Requirements 5.8, 10.2** (complements Property 7)

- [x] 10. Frontend component tests
  - [x]* 10.1 Channel_Selector rendering and Confirm-gating tests
    - `BookingDetailsModal.vue`: renders both options when `whatsappEnabled` is true; renders SMS-only (auto-selected) when false (Req 1.5); Confirm disabled until a channel is chosen (Req 1.4)
    - _Requirements: 1.1, 1.2, 1.4, 1.5_

- [x] 11. Final checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional (test/hardening) and can be skipped for a faster MVP; core implementation tasks are required.
- Each task references specific requirement acceptance criteria and, where relevant, the correctness property it implements, for traceability.
- The SMS delivery path and the channel-agnostic `confirm()` are preserved unchanged (Requirements 4.5, 9.5).
- No silent channel fallback, no new verification architecture, no replacement of iRestSMS — this is a minimal, incremental extension consistent with codebase conventions (logic in controllers, thin service wrappers, inline validation, queued jobs, timestamped migrations, vue-i18n across all three locales, RTL preserved).
- Property tests run ≥ 100 iterations and are tagged `Feature: otp-delivery-channel, Property {n}: {property text}`.

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "2.1"] },
    { "id": 1, "tasks": ["1.2", "2.2", "5.1"] },
    { "id": 2, "tasks": ["1.3", "2.3", "2.4", "2.5", "6.1", "8.1"] },
    { "id": 3, "tasks": ["2.6", "3.1", "3.4", "5.4", "6.2"] },
    { "id": 4, "tasks": ["3.2", "3.3", "5.2", "5.3"] },
    { "id": 5, "tasks": ["9.1", "9.2", "9.3", "9.4", "9.5", "9.6", "9.7", "9.8", "9.9", "9.10", "9.11", "10.1"] }
  ]
}
```
