# Design Document — OTP Delivery Channel

## Overview

The booking verification flow currently sends the 6-digit OTP exclusively via SMS (iRestSMS through `SmsService` / `SendOtpSmsJob`). This feature lets the Customer choose their delivery channel — **SMS** or **WhatsApp** — before the OTP is dispatched, and delivers WhatsApp OTPs through the existing Twilio integration in `WhatsappService`.

The **guiding principle** is that SMS and WhatsApp are nothing more than two interchangeable transports sitting in front of one shared OTP system. Everything about the OTP itself — generation (`random_int(100000, 999999)`), bcrypt hashing, 5-minute expiry, 5-attempt lockout, prior-record deletion, the slot double-booking guard, and the post-verification appointment-creation transaction — is completely unchanged and channel-agnostic. The only new decisions the system makes are: (1) *which* transport to dispatch, and (2) *whether* a business is allowed to use WhatsApp. Verification (`confirm()`) never learns or cares which channel delivered the code.

### Resolved Decisions

- **Decision Point A — Plan gating: PREMIUM-ONLY.** WhatsApp OTP is available only to premium-plan businesses. The frontend hides the WhatsApp option for non-premium businesses (defense-in-depth), but the authoritative gate is server-side in `BookingVerificationController@send`: a `whatsapp` channel is rejected with HTTP 422 when `$business->canUseWhatsapp()` returns `false`. The check runs *after* channel validation and *before* OTP generation. `confirm()` is never gated. (Requirements 2.5, 6.1)
- **Decision Point B — Twilio format: PRE-APPROVED TEMPLATE.** WhatsApp OTP uses a pre-approved Twilio authentication/content template. The OTP is passed as a template substitution variable via `ContentSid` + `ContentVariables`, never embedded in a free-form `Body`. A new config key `services.twilio.otp_template_sid` (env `TWILIO_OTP_TEMPLATE_SID`) holds the template SID. (Requirement 5.6)
- **Decision Point C — Resend: REUSE ORIGINAL CHANNEL.** The chosen channel is retained in frontend state when the OTP modal opens; resend re-sends with the same `delivery_channel`. The channel selector is not re-shown in the OTP modal. To change channel, the Customer closes the OTP modal and returns to the details step. (Requirement 7)

This is an incremental extension of the existing flow. No new verification architecture, no service-layer/repository abstraction, no channel-strategy registry. Business logic stays in the controller; the WhatsApp OTP method is added to the existing thin `WhatsappService` wrapper.

---

## Architecture

### End-to-end flow

```mermaid
flowchart TD
    A[Customer: details + channel selection<br/>BookingDetailsModal.vue] --> B[POST /book/slug/verification/send<br/>delivery_channel included]
    B --> C{Business active?}
    C -- no --> C1[abort 404]
    C -- yes --> D[Validate inputs incl.<br/>delivery_channel in sms|whatsapp]
    D -- invalid/absent --> D1[422 validation error<br/>no record, no job]
    D -- valid --> E{channel == whatsapp<br/>&& !canUseWhatsapp?}
    E -- yes --> E1[422 whatsapp-not-available<br/>no record, no job]
    E -- no --> F[Duplicate-slot check]
    F -- taken --> F1[422 slot-already-booked]
    F -- free --> G[Generate OTP random_int<br/>delete prior verification<br/>create BookingVerification<br/>with delivery_channel]
    G --> H{match channel}
    H -- sms --> I[dispatch SendOtpSmsJob]
    H -- whatsapp --> J[dispatch SendOtpWhatsappJob]
    I --> K[return success:true]
    J --> K
    I -. queue .-> L[SmsService::sendVerificationCode]
    J -. queue .-> M[WhatsappService::sendOtpCode<br/>Twilio Content template]
    K --> N[OTP modal: enter code<br/>description reflects channel]
    N --> O[POST /book/slug/verification/confirm<br/>phone + code ONLY]
    O --> P[Channel-agnostic verification:<br/>expiry, attempts, hash check,<br/>slot guard]
    P -- ok --> Q[DB transaction:<br/>create Appointment + verified_at]
    Q --> R[Notify managers<br/>NewAppointmentCreatedNotification]
    R --> S[return success + status]
```

### Channel dispatch approach

Dispatch is a single `match` expression in `send()`, placed after OTP persistence — deliberately minimal and consistent with the codebase's "logic in the controller" convention:

```php
match ($validated['delivery_channel']) {
    'sms'      => SendOtpSmsJob::dispatch($phone, (string) $code),
    'whatsapp' => SendOtpWhatsappJob::dispatch($phone, (string) $code),
};
```

No strategy interface, channel registry, or factory is introduced. Adding a third channel later would mean one more `match` arm plus one more thin job — which is proportionate to this codebase.

### Async delivery vs. synchronous failure feedback (design tension)

Requirement 8 expects the Customer to receive a customer-facing error when delivery fails. The current architecture dispatches delivery to a **queued** job and returns `{"success": true}` **immediately** — so at the moment the HTTP response is produced, the provider has not yet been contacted and a failure cannot be reflected synchronously.

Two options were considered:

- **(a) Keep fully async** — `send()` returns success as soon as the job is queued; provider failures are only visible in job logs / retries. This preserves current behavior but *cannot* satisfy Requirement 8's "return HTTP 500 with a customer-facing error" for the initial send, because the response is already sent before delivery is attempted.
- **(b) Deliver synchronously for immediate feedback** — perform the provider call inline during the request so a failure can be caught and surfaced as HTTP 500, then clean up the just-created record (Requirement 8.6).

**Recommended resolution: hybrid, minimal-change — dispatch synchronously *only for the purpose of surfacing provider failure*, while keeping the job classes and `$tries = 3` retry semantics intact.** Concretely, `send()` runs the delivery through the queue connection but on the request path uses `dispatchSync()` so the provider is contacted before the response is produced. Because `SmsService::sendVerificationCode` and the new `WhatsappService::sendOtpCode` already `throw RuntimeException` on failure, `send()` wraps the dispatch in a `try/catch`:

```php
try {
    match ($channel) {
        'sms'      => SendOtpSmsJob::dispatchSync($phone, (string) $code),
        'whatsapp' => SendOtpWhatsappJob::dispatchSync($phone, (string) $code),
    };
} catch (\Throwable $e) {
    $verification->delete();                 // Req 8.6 — no orphan blocking record
    Log::error('OTP delivery failed', [      // Req 8.5 — no OTP, no credentials
        'delivery_channel' => $channel,
        'phone' => $this->maskPhone($phone),
        'error' => $e->getMessage(),
    ]);
    return response()->json([
        'message' => __('booking.otpDeliveryFailed'),
    ], 500);
}
```

**Trade-offs, documented explicitly:**
- *Pro:* Satisfies Requirement 8 literally — the initial send returns HTTP 500 with a translated message, and the failed record is deleted so it does not block a retry (8.6). No new endpoints or polling. Job classes and `$tries` retry config are retained for reuse and consistency with `SendOtpSmsJob`.
- *Con:* The provider round-trip now happens inside the request, adding latency (bounded by the WhatsApp 10s timeout / SMS provider timeout) to the `send` response. This is acceptable for a single OTP send guarded by the `otp-send` rate limiter (3/min, 10/hr per IP). Under `dispatchSync`, `$tries` retry does not apply within a single request; the customer-facing error + "Resend" button is the recovery path.
- *If the team prefers to keep the send truly async* (option a), Requirement 8's initial-send error contract cannot be met and should be renegotiated (e.g., success always returned; failures surfaced only via resend). This design recommends option (b) because it is the only option that satisfies the requirement as written with minimal change.

---

## Components and Interfaces

This section presents the components introduced or changed by this feature and their interfaces (signatures, props, payloads). Backend components are grouped under "Backend Changes" and frontend components under "Frontend Changes".

### Backend Changes

### 1. Migration — add `delivery_channel`

New timestamped migration (do **not** modify `2026_05_20_180234_create_booking_verifications_table.php`):

```php
Schema::table('booking_verifications', function (Blueprint $table) {
    $table->string('delivery_channel', 20)
        ->default('sms')
        ->after('customer_email');
});
```

- Type `VARCHAR(20)`, **NOT NULL** (Requirements 3.1, 3.2).
- **Safe default `'sms'`.** `booking_verifications` is transient (5-minute TTL, deleted on resend/verify), so any pre-existing rows are effectively disposable; a default keeps the column non-null and the migration backward-safe on existing rows without a manual backfill. (Requirement 3.2)
- The allowed-value constraint (`sms` | `whatsapp`) is enforced at the **application layer** via `Rule::in([...])` validation (Requirement 3.3). The application always writes an explicit validated value, so the default only ever applies to legacy/edge rows.
- `down()` drops the `delivery_channel` column.

### 2. `BookingVerification` model

Add `delivery_channel` to `$fillable` (Requirement 3.4). No cast needed (plain string). No other model change.

### 3. `BookingVerificationController@send`

Insert the following, preserving every existing step (active check, current validation, phone normalisation, duplicate-slot check, OTP generation, prior-record deletion, record creation, success response):

1. **Validation** — add to the inline `$request->validate([...])`:
   ```php
   'delivery_channel' => ['required', Rule::in(['sms', 'whatsapp'])],
   ```
   Absent → 422; any value outside the allowlist → 422 (Requirements 2.2–2.4, 2.6, 10.1). `use Illuminate\Validation\Rule;`.
2. **Premium gate** — immediately after validation and **before** OTP generation (Requirement 6.1b):
   ```php
   if ($validated['delivery_channel'] === 'whatsapp' && ! $business->canUseWhatsapp()) {
       return response()->json(['message' => __('booking.whatsappNotAvailable')], 422);
   }
   ```
   No record is created and no job is dispatched on rejection (Requirements 2.5, 6.1a).
3. **Persist channel** — include `'delivery_channel' => $validated['delivery_channel']` in the `BookingVerification::create([...])` payload (Requirement 3.5). All other fields unchanged.
4. **Dispatch** — replace the unconditional `SendOtpSmsJob::dispatch(...)` with the `match` (see Architecture), wrapped in the failure `try/catch` described above (Requirements 4.1, 5.1, 8.1, 8.2, 8.6).

The prior-record deletion (`delete existing verification for business+phone`) and duplicate-slot guard stay exactly where they are (Requirements 9.8, 9.7).

### 4. `BookingVerificationController@confirm`

**No changes.** It does not accept, read, or validate `delivery_channel` (Requirements 3.6, 9.5, 6.3, 10.4). The appointment-creation transaction, `verified_at` set, manager notification, slot re-check, expiry/attempts/hash logic all remain identical.

### 5. New job `SendOtpWhatsappJob`

Mirrors `SendOtpSmsJob` (`app/Jobs/SendOtpWhatsappJob.php`):

```php
class SendOtpWhatsappJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3; // Req 5.9 — consistent with SendOtpSmsJob

    public function __construct(
        public string $phone,
        public string $code,
    ) {}

    public function handle(WhatsappService $whatsapp): void
    {
        $whatsapp->sendOtpCode($this->phone, $this->code);
    }
}
```

### 6. `WhatsappService::sendOtpCode(string $phone, string $code): void`

New method on the existing service (no new class), following the existing `sendAppointmentReminder` Twilio pattern (Basic Auth, `Http::asForm()`):

```php
public function sendOtpCode(string $phone, string $code): void
{
    $response = Http::asForm()
        ->timeout(10) // Req 5.4 — 10s timeout
        ->withBasicAuth(
            config('services.twilio.sid'),
            config('services.twilio.token'),
        )
        ->post(
            'https://api.twilio.com/2010-04-01/Accounts/'
                . config('services.twilio.sid') . '/Messages.json',
            [
                'From' => config('services.twilio.whatsapp_from'),
                'To' => 'whatsapp:' . $phone,                       // Req 5.2
                'ContentSid' => config('services.twilio.otp_template_sid'),
                'ContentVariables' => json_encode(['1' => $code]),   // Req 5.6 — OTP as template var
            ],
        );

    if (! $response->successful()) {                                 // Req 5.4 — non-2xx throws
        Log::error('WhatsApp OTP send failed', [                     // Req 5.8 / 8.5 — no OTP
            'phone' => $this->maskPhone($phone),
            'status' => $response->status(),
            'twilio_code' => $response->json('code'),
        ]);
        throw new RuntimeException('Failed to send WhatsApp OTP.');
    }

    Log::info('WhatsApp OTP sent', [                                 // Req 5.8 — no OTP
        'phone' => $this->maskPhone($phone),
        'status' => $response->status(),
    ]);
}
```

- Recipient is `whatsapp:` + the already-normalised `+972…` phone (Requirement 5.2).
- OTP is passed only as a `ContentVariables` substitution, never in a free-form `Body` (Requirements 5.6). Free-form `Body` path is explicitly not used.
- Throws `RuntimeException` on non-2xx **or** timeout (Requirement 5.4).
- Logs only masked phone + Twilio status/error code; the OTP plaintext, Twilio auth token, and SMS api_key never appear (Requirements 5.8, 8.5, 10.2, 10.3). A small `maskPhone()` helper masks the middle digits.

**Config requirement note:** In a sandbox or non-premium environment the template may not exist. The design requires `TWILIO_OTP_TEMPLATE_SID` to be configured for WhatsApp OTP to function; where it is missing, WhatsApp OTP will fail (surfaced as a delivery error), which is acceptable because WhatsApp is premium-gated and can be disabled by plan.

### 7. Config

Add to `config/services.php` under `twilio`:

```php
'twilio' => [
    'sid' => env('TWILIO_SID'),
    'token' => env('TWILIO_TOKEN'),
    'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    'otp_template_sid' => env('TWILIO_OTP_TEMPLATE_SID'), // new
],
```

Document `TWILIO_OTP_TEMPLATE_SID` in `.env.example`.

### 8. Failure handling & record cleanup

Handled in `send()`'s `try/catch` (see Architecture): on any delivery `RuntimeException`, the just-created `BookingVerification` is deleted (Requirement 8.6) and a translated HTTP 500 is returned (Requirements 8.1, 8.2). There is **no** silent fallback between channels — a failure on the selected channel is surfaced, never retried on the other channel (Requirement 8.3). For the Twilio "recipient not on WhatsApp" error code, `send()` inspects the exception/response to return a distinct message key suggesting SMS (Requirement 8.4; the specific Twilio error code, e.g. `63024`/`63016` for undeliverable/opt-in-required recipients, is mapped to `booking.whatsappNumberNotFound` — the exact code is confirmed against the Twilio account's error catalogue during implementation).

---

### Frontend Changes

### `BookingController@show` (page props)

Expose whether the business may use WhatsApp so the frontend can gate the option. Add to the `Inertia::render('Booking/Show', [...])` payload:

```php
'whatsappEnabled' => $business->canUseWhatsapp(),
```

This is a boolean derived server-side; the server remains the authoritative gate regardless of this prop (Requirement 6.1c).

### `resources/js/Pages/Booking/Show.vue`

- Add `const selectedChannel = ref<DeliveryChannel | null>(null)`.
- Accept the new `whatsappEnabled: boolean` prop.
- In `requestOtp`, include `delivery_channel: selectedChannel.value` in the POST body (Requirement 2.1). For non-premium businesses where the selector is skipped, default `selectedChannel` to `'sms'`.
- Retain `selectedChannel` across resend (do not clear it when the OTP modal opens); `resend` calls the same `requestOtp` and thus re-sends the same channel (Requirements 7.1, 7.2).
- Pass `:channel="selectedChannel"` to `BookingOtpModal` so its description reflects the channel.
- Surface backend error messages: initial-send failure → `bookingError` (shown in details modal, Requirement 8.7); resend failure → `otpError` (shown in OTP modal, Requirement 8.8). The existing `requestOtp` catch already routes to the correct target based on `showOtpModal`.

### `BookingDetailsModal.vue` — Channel_Selector

Place the Channel_Selector inside the details modal, above the "Confirm Appointment" action (a pre-OTP step, consistent with Decision Point C):

- Two options, SMS and WhatsApp, presented visually equal with **no default pre-selected** (Requirement 1.1, 1.2).
- Selected option shows a visual selected state (Requirement 1.3).
- New props: `whatsappEnabled: boolean`; `v-model:selected-channel`.
- When `whatsappEnabled` is `false`, render **SMS only** and auto-select SMS so the channel step is effectively skipped (Requirement 1.5).
- The Confirm button is disabled until a channel is selected (Requirement 1.4) — extend the existing `:disabled` guard to also require `selectedChannel`.
- Each option is a labelled control with `role="radio"` / `aria-checked` (or a fieldset of radios) and an accessible group label so screen readers identify each option (Requirement 1.7).
- Uses existing booking CSS classes so LTR/RTL rendering follows the current RTL mechanisms (Requirement 1.8, 11.5). No hardcoded strings (Requirement 11.6).

### `BookingOtpModal.vue`

- New prop `channel: DeliveryChannel`.
- Replace the single `booking.verifyDescription` usage with a channel-specific key: `booking.verifyDescriptionSms` or `booking.verifyDescriptionWhatsapp`, chosen from `channel`, with `{phone}` substitution (Requirements 4.4, 5.5, 11.3).
- Resend continues to emit `resend` (no channel argument needed — parent reuses stored channel). The Channel_Selector is **not** rendered here (Requirements 7.6, 7.7).

### Types — `resources/js/types/global.d.ts`

```ts
export type DeliveryChannel = 'sms' | 'whatsapp';
```

Add `whatsappEnabled` where the `Show.vue` props are typed, and the new modal props (`channel`, `selectedChannel`, `whatsappEnabled`).

---

## Data Models

`booking_verifications` (transient OTP record; 5-min TTL; max 5 attempts) — updated schema:

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| business_id | FK → businesses | cascade delete |
| service_id | FK → services | cascade delete |
| appointment_date | date | |
| start_time / end_time | time | |
| customer_name | string | |
| customer_phone | string | normalised `+972…` |
| customer_email | string nullable | |
| **delivery_channel** | **varchar(20) NOT NULL, default `'sms'`** | **NEW.** Allowed values `sms` \| `whatsapp`, enforced by application-layer `Rule::in`. |
| code_hash | string | bcrypt of OTP |
| expires_at | timestamp | `now()+5min` |
| verified_at | timestamp nullable | |
| attempts | tinyint unsigned | default 0 |
| timestamps | | |

Indexes unchanged: `booking_phone_idx (business_id, customer_phone)`, `booking_slot_idx (business_id, appointment_date, start_time)`.

`delivery_channel` is written once at `send()` and never read by `confirm()` — it is metadata attached to the pending record, not an input to verification.

---

## Error Handling

| Failure mode | Trigger | HTTP | Customer-facing key | Side effects |
| --- | --- | --- | --- | --- |
| Absent / invalid channel | `delivery_channel` missing or ∉ {sms,whatsapp} | 422 | Laravel validation message (`booking.invalidChannel` for a custom message) | None — no record, no job (Req 2.2–2.4, 10.1) |
| WhatsApp on non-premium | `whatsapp` + `!canUseWhatsapp()` | 422 | `booking.whatsappNotAvailable` | None — no record, no job (Req 6.1) |
| Slot already booked | duplicate slot at send/confirm | 422 | existing message | None (Req 9.7) |
| SMS delivery failure | `SmsService` throws | 500 | `booking.otpDeliveryFailed` | Record deleted (Req 8.1, 8.6) |
| WhatsApp delivery failure | Twilio non-2xx / timeout → `WhatsappService` throws | 500 | `booking.otpDeliveryFailed` | Record deleted (Req 8.2, 8.6) |
| Number not on WhatsApp | Twilio unregistered-recipient error code | 500 | `booking.whatsappNumberNotFound` (suggests SMS) | Record deleted (Req 8.4) |
| Inactive business | `!isActive()` | 404 | abort | None (Req 10.7) |

Cross-cutting guarantees:
- **No silent channel fallback** — a failed send on one channel never triggers the other (Req 8.3).
- **No secret leakage** — logs never contain the OTP plaintext, the Twilio auth token, or the SMS api_key; phone numbers are masked (Req 8.5, 10.2, 10.3).
- **Async tension resolution** — because `send()` uses `dispatchSync` on the request path (see Architecture), provider failures are caught and surfaced synchronously as the table above describes; the queued job classes and `$tries = 3` remain for consistency.

---

## Internationalisation

New/changed keys must be added to **all three** locale files simultaneously — `resources/js/i18n/locales/en.ts`, `ar.ts`, `he.ts` — with no key present in one file but missing from another (Requirement 11.1). English source strings below; Arabic and Hebrew values must be added in parallel and be grammatically correct RTL strings (Requirements 11.2–11.5).

| Key | English source |
| --- | --- |
| `booking.chooseChannel` | "Choose how to receive your code" |
| `booking.channelSms` | "SMS" |
| `booking.channelWhatsapp` | "WhatsApp" |
| `booking.verifyDescriptionSms` | "Enter the verification code sent via SMS to {phone}" |
| `booking.verifyDescriptionWhatsapp` | "Enter the verification code sent via WhatsApp to {phone}" |
| `booking.otpDeliveryFailed` | "We couldn't send your code. Please try again." |
| `booking.whatsappNotAvailable` | "WhatsApp verification isn't available for this business." |
| `booking.whatsappNumberNotFound` | "This number isn't on WhatsApp. Try SMS instead." |
| `booking.invalidChannel` | "Please select a valid delivery method." |

The existing `booking.verifyDescription` key is superseded by the two channel-specific variants (Requirement 11.3); it may be kept for backward safety but the modal will use the new keys. The Channel_Selector introduces no hardcoded strings (Requirement 11.6).

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

This feature is a good PBT fit: `send()` validation/gating/dispatch and the shared OTP invariants are logic that varies meaningfully with input (arbitrary channel strings, phones, codes, plan states) and benefits from wide input coverage. UI rendering (channel selector appearance, RTL) and static config (job `$tries`, migration presence) are covered by example/smoke tests instead.

### Property 1: Invalid or absent channel is rejected with no side effects

*For any* string value that is not `sms` or `whatsapp` (including a missing `delivery_channel` field), a `send` request SHALL return HTTP 422 and SHALL NOT create a `BookingVerification` record or dispatch any delivery job.

**Validates: Requirements 2.2, 2.3, 2.4, 2.6, 10.1**

### Property 2: Persisted channel equals the submitted channel

*For any* accepted `send` request, the persisted `BookingVerification.delivery_channel` SHALL equal the submitted value and SHALL be a member of `{sms, whatsapp}`; and *for any* resend for the same business+phone, the prior record SHALL be deleted and the new record SHALL store the same channel with a fresh `expires_at` and `code_hash`.

**Validates: Requirements 3.5, 7.1, 7.2, 7.3, 7.4, 9.8**

### Property 3: Verification is channel-agnostic

*For any* valid `BookingVerification`, the outcome of `confirm` (success/failure and whether an `Appointment` is created) SHALL be identical whether the stored `delivery_channel` is `sms` or `whatsapp`, and `confirm` SHALL never read a `delivery_channel` field from the request.

**Validates: Requirements 3.6, 6.3, 9.5, 9.6**

### Property 4: Correct transport is dispatched for the chosen channel

*For any* accepted `send` request, the dispatched delivery job SHALL correspond to the chosen channel (`sms` → `SendOtpSmsJob`, `whatsapp` → `SendOtpWhatsappJob`), carrying the normalised phone and the generated code; and *for any* phone, the WhatsApp recipient SHALL be `whatsapp:` prefixed to the normalised `+972` phone with the OTP passed as a Content template variable.

**Validates: Requirements 4.1, 4.2, 5.1, 5.2, 5.6**

### Property 5: Premium gate blocks WhatsApp for non-premium businesses with no side effects

*For any* non-premium business, a `send` request with `delivery_channel = whatsapp` SHALL return HTTP 422 and SHALL NOT create a `BookingVerification` or dispatch a WhatsApp job.

**Validates: Requirements 2.5, 6.1**

### Property 6: No silent channel fallback

*For any* selected channel whose delivery fails, the delivery job/service of the other channel SHALL never be dispatched or invoked as a result of that failure.

**Validates: Requirement 8.3**

### Property 7: OTP plaintext never appears in logs

*For any* generated OTP code, running either the SMS or WhatsApp delivery path SHALL produce log output that does not contain the plaintext code.

**Validates: Requirements 5.8, 10.2**

### Property 8: Shared OTP invariants hold regardless of channel

*For any* accepted `send` request on either channel, the generated code SHALL be a 6-digit integer in `[100000, 999999]`, `code_hash` SHALL be a bcrypt hash that verifies against that code, `expires_at` SHALL equal creation time plus 5 minutes, `attempts` SHALL start at 0, and lockout SHALL occur at 5 attempts.

**Validates: Requirements 9.1, 9.2, 9.3, 9.4**

### Property 9: At most one appointment per verification

*For any* valid verification, a successful `confirm` SHALL create exactly one `Appointment`, and any subsequent `confirm` for the same slot SHALL be rejected as slot-taken — never producing a duplicate `Appointment`.

**Validates: Requirements 9.7, 10.5**

### Property 10: Translation key parity across locales

*For any* new `booking.*` key introduced by this feature, the key SHALL be present in all three locale files (`en`, `ar`, `he`).

**Validates: Requirement 11.1**

---

## Testing Strategy

**Dual approach.** Property-based tests cover universal behaviors (channel validation, gating, dispatch correctness, shared OTP invariants, log non-leakage); example/integration tests cover concrete scenarios, edge cases, and Twilio/SMS wiring. Test doubles: `Bus::fake()` / `Queue::fake()` to assert job dispatch; `Http::fake()` for Twilio responses; `Log::spy()` / a fake log channel for OTP non-leakage assertions.

**Property-based testing library.** Use an existing PHP PBT library on top of PHPUnit (e.g., `giorgiosironi/eris`); do not implement property generation from scratch. Each property test runs **≥ 100 iterations** and is tagged with a comment referencing its design property, format: **Feature: otp-delivery-channel, Property {n}: {property text}**.

### Requirement 12 test-case mapping

| Req 12 case | Test type | Approach |
| --- | --- | --- |
| 12.1 SMS success path | Property 2 + 4 | `Queue::fake()`; valid `sms` send → record with `delivery_channel=sms`, `SendOtpSmsJob` dispatched. |
| 12.2 WhatsApp success path | Property 2 + 4 | Premium business, valid `whatsapp` send → record with `whatsapp`, `SendOtpWhatsappJob` dispatched. |
| 12.3 Invalid channel rejection | Property 1 | Arbitrary invalid/missing channel → 422, no record, no job. |
| 12.4 Channel-agnostic confirm | Property 3 | Seed verification with `whatsapp`; confirm with valid code → `Appointment` created. |
| 12.5 Expired OTP | Example/edge | `expires_at` in past → 422, no `Appointment`. |
| 12.6 Incorrect OTP | Example/edge | Wrong code → 422, `attempts` incremented, no `Appointment`. |
| 12.7 Exceeded attempts | Example/edge | 6th confirm after 5 fails → 429, no `Appointment`. |
| 12.8 Resend resets record | Property 2 | Second send → prior record deleted, new `expires_at`/`code_hash`. |
| 12.9 SMS delivery failure | Example | Force `SmsService` throw (`dispatchSync`) → 500, translated message, record deleted (no blocking record). |
| 12.10 WhatsApp delivery failure | Example + Property 6 | `Http::fake` Twilio non-2xx → 500, no SMS fallback. |
| 12.11 Appointment after WhatsApp verify | Property 3 + Example | WhatsApp-stored verification → confirm creates `Appointment` with correct status, `NewAppointmentCreatedNotification` fired (`Notification::fake()`). |
| 12.12 No duplicate on double confirm | Property 9 | Two confirms → exactly one `Appointment`; second → slot-taken. |
| 12.13 Plan-gate enforcement | Property 5 | Non-premium `whatsapp` send → 422, no WhatsApp job. |
| 12.14 OTP not logged | Property 7 | Log spy across both paths → code substring absent. |

Additional coverage:
- **Smoke:** `SendOtpWhatsappJob::$tries === 3` (Req 5.9); migration adds `delivery_channel` column (Req 3.1).
- **Example (frontend):** Channel_Selector renders both options when premium; SMS-only when non-premium (Req 1.5); Confirm disabled until a channel is chosen (Req 1.4).
- **Integration:** `WhatsappService::sendOtpCode` with `Http::fake()` — asserts `To == whatsapp:{phone}`, `ContentSid` set, OTP passed via `ContentVariables` not `Body`, and non-2xx throws (Req 5.2, 5.4, 5.6).
- **Manual:** RTL correctness of Arabic/Hebrew channel-selector strings (Req 11.5) — not machine-verifiable.
