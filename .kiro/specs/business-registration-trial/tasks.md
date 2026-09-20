# Implementation Plan: Business Registration & Trial

## Overview

This plan implements public self-service business registration with a 7-day free trial, email-verification-gated dashboard access, and post-expiration plan selection. It follows the design's component structure exactly: data-layer changes (migration + model helpers) come first, then the registration controller and trial middleware, then route wiring and frontend, then the full test suite. Email-verification (`verified`) enforcement is wired **before** trial enforcement (`trial`) on the dashboard routes throughout, per design sections 5 and 6. No payment is implemented, no new runtime dependencies are added, tenant isolation and the admin-created business flow are preserved.

Language: PHP (Laravel) for backend, Vue 3 + Inertia + vue-i18n for frontend — as specified concretely in the design (no pseudocode).

## Tasks

- [x] 1. Data layer: migration and model changes
  - [x] 1.1 Create migration adding `trial_ends_at` and `tos_accepted_at` to `businesses`
    - New migration `database/migrations/XXXX_XX_XX_add_trial_and_tos_to_businesses_table.php`
    - `up()` adds `dateTime('trial_ends_at')->nullable()->after('plan_id')` and `dateTime('tos_accepted_at')->nullable()->after('trial_ends_at')`
    - `down()` drops both columns; do not edit existing applied migrations
    - _Design: Components and Interfaces §1; Data Models (New columns on `businesses`)_
    - _Requirements: 4.3, 3.3, 4.4_

  - [x] 1.2 Make `User` implement `MustVerifyEmail`
    - Add `implements MustVerifyEmail` (import `Illuminate\Contracts\Auth\MustVerifyEmail`) to `app/Models/User.php`
    - Leave `$fillable`/`$casts` unchanged; verification uses existing `users.email_verified_at` column
    - _Design: Components and Interfaces §1a_
    - _Requirements: 12.2, 12.1_

  - [x] 1.3 Add trial columns and state-resolution helpers to `Business`
    - Add `trial_ends_at` and `tos_accepted_at` to `$fillable`; cast both to `datetime` in `casts()`
    - Implement `isPaid()`, `onTrial()`, `trialExpired()`, `trialDaysRemaining()` exactly per design (leave existing capability gates unchanged)
    - _Design: Components and Interfaces §2; Trial state resolution table_
    - _Requirements: 4.2, 5.4, 8.1, 8.2, 8.4, 9.3_

  - [x]* 1.4 Write unit tests for `Business` state helpers
    - Test `isPaid()`/`onTrial()`/`trialExpired()`/`trialDaysRemaining()` across plan_id null/non-null and trial_ends_at past/future/null (use Carbon time-travel)
    - _Design: Testing Strategy (P8, state resolution)_
    - _Requirements: 8.1, 8.2, 5.4_

- [x] 2. Backend: registration controller
  - [x] 2.1 Implement `RegisteredUserController` (create/store)
    - New `app/Http/Controllers/Auth/RegisteredUserController.php`
    - `create()` renders Inertia `Auth/Register`
    - `store()` uses inline `$request->validate([...])` (no Form Request) with `name`, `business_name`, `email` (unique:users,email + email + lowercase), `phone` (nullable), `timezone`, `password` (`confirmed` + `Password::defaults()`), `terms` (`accepted`)
    - Wrap `Business::create` (plan_id null, trial_ends_at `now()+7d`, tos_accepted_at `now()`, unique slug) and `User::create` (role `manager`, business_id) in `DB::transaction`
    - Add `uniqueSlug()` mirroring the admin flow's `Str::slug` approach with numeric suffixing
    - `Auth::login($user)` then `redirect()->route('dashboard')`; ensure the `Registered` event fires so the verification email is sent (dispatch explicitly if needed)
    - _Design: Components and Interfaces §3; Architecture (Registration flow); Error Handling_
    - _Requirements: 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 2.2, 2.3, 2.4, 2.6, 3.2, 3.3, 3.4, 4.1, 4.2, 7.1, 12.1_

- [x] 3. Backend: trial-enforcement middleware
  - [x] 3.1 Implement `EnsureTrialActive` middleware
    - New `app/Http/Middleware/EnsureTrialActive.php` in the style of `EnsureUserIsManager`
    - Resolve `auth()->user()?->business` (manager's own business only); `abort(403)` if none
    - Allow when `isPaid()` or `onTrial()`; otherwise `redirect()->route('plans')` for expired trial
    - _Design: Components and Interfaces §5; Trial-enforcement request flow; Error Handling (403 vs redirect)_
    - _Requirements: 5.1, 6.1, 6.3, 6.5, 6.6, 7.3, 8.2, 9.3_

  - [x] 3.2 Register the `trial` middleware alias in `bootstrap/app.php`
    - Add `'trial' => \App\Http\Middleware\EnsureTrialActive::class` to the existing `$middleware->alias([...])` block alongside `admin`/`manager`
    - _Design: Components and Interfaces §5 (Registration in bootstrap/app.php)_
    - _Requirements: 7.4_

  - [x]* 3.3 Write unit test for `EnsureTrialActive` decision logic
    - Assert allow for paid/active-trial businesses, redirect to `plans` for expired, 403 for no-business (Carbon time-travel)
    - _Design: Testing Strategy (P10, P12)_
    - _Requirements: 5.1, 6.1, 6.3, 6.5_

- [x] 4. Backend: plan selection controller
  - [x] 4.1 Implement `Dashboard/PlanController@index`
    - New `app/Http/Controllers/Dashboard/PlanController.php`
    - `index()` renders Inertia `Dashboard/Plans` with `Plan::where('is_active', true)->get()`
    - _Design: Components and Interfaces §6_
    - _Requirements: 6.4, 8.3, 8.4_

- [x] 5. Route wiring (verified before trial)
  - [x] 5.1 Register the register routes and ensure Breeze verification routes are active
    - In `routes/auth.php`, inside the existing `guest` group, add `GET register` (name `register`) and `POST register` → `RegisteredUserController`
    - Ensure Breeze verification routes are present/active: `verification.notice` (`EmailVerificationPromptController`), `verification.verify` (`VerifyEmailController`), `verification.send` (`EmailVerificationNotificationController`)
    - _Design: Components and Interfaces §4_
    - _Requirements: 1.1, 12.3, 12.5, 12.7_

  - [x] 5.2 Guard dashboard routes with `['auth','manager','verified','trial']` and add plan-selection route
    - In `routes/dashboard.php`, update both blocks (the `GET /dashboard` closure and the `prefix('dashboard')` group) to `['auth','manager','verified','trial']` (verified before trial)
    - Add `GET /dashboard/plan` → `Dashboard/PlanController@index` named `plans` under `['auth','manager','verified']` only (NOT `trial`), outside the trial-guarded blocks
    - _Design: Components and Interfaces §5, §6; Placement of trial middleware on dashboard routes_
    - _Requirements: 5.1, 6.1, 6.2, 6.3, 12.3, 12.4_

  - [x] 5.3 Share the `trial` status prop from the dashboard route to the frontend
    - In the `GET /dashboard` closure (which already loads `$business`), add a `trial` prop: `onTrial`, `expired`, `isPaid`, `endsAt`, `daysRemaining` derived from the `Business` helpers; leave `HandleInertiaRequests` (`auth.user`, `flash`) unchanged
    - _Design: Components and Interfaces §7_
    - _Requirements: 5.4, 6.6_

- [x] 6. Frontend: registration, plans, trial banner, and i18n
  - [x] 6.1 Add synchronized i18n namespaces `register`, `trial`, `plans`, `verifyEmail`
    - Add identical key sets to `resources/js/i18n/locales/en.ts`, `ar.ts`, and `he.ts` (English, Arabic, Hebrew), keeping keys in sync; RTL is handled by existing `document.dir` logic (no new RTL code)
    - _Design: Components and Interfaces §8 (i18n)_
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 12.8_

  - [x] 6.2 Extend `Pages/Auth/Register.vue`
    - Start from the Breeze stub inside `GuestLayout`; add business-name, timezone, optional phone fields, and a required `terms` checkbox with inline ToS/Privacy links
    - `useForm` payload: `{ name, business_name, email, phone, timezone, password, password_confirmation, terms }`; surface `form.errors.*` per field; all labels/links via `t('register....')`
    - _Design: Components and Interfaces §8; Error Handling (validation errors)_
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 10.1_

  - [x] 6.3 Create `Pages/Dashboard/Plans.vue`
    - Receives `plans`, lists each active plan (name, price) with a disabled/placeholder "choose" affordance (no payment); wrapped in `ManagerLayout`; all strings via i18n `plans.*`
    - _Design: Components and Interfaces §6, §8_
    - _Requirements: 6.4, 8.3, 10.1_

  - [x] 6.4 Create `TrialBanner.vue` and mount it in the dashboard
    - Reads the `trial` prop; shown when `trial.onTrial` is true using a pluralized i18n `trial.*` message for `daysRemaining`; hidden for paid businesses
    - _Design: Components and Interfaces §7, §8_
    - _Requirements: 5.4, 10.1_

- [x] 7. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Test factory support
  - [x]* 8.1 Add factory states for trial and verification testing
    - Add `trial()` state to `BusinessFactory` (plan_id null, trial_ends_at `now()+7d`) and reuse `premium()`; add `unverified()` to `UserFactory`; ensure `PlanFactory` supports active/inactive plans
    - _Design: Testing Strategy (Frameworks and conventions)_
    - _Requirements: 11.1, 11.3, 11.4, 11.6_

- [ ] 9. Property-based tests (data-provider PHPUnit, RefreshDatabase, Carbon time-travel)
  - [ ]* 9.1 Registration happy path
    - **Property 1: Valid registration creates correctly-linked records in Active_Trial**
    - **Property 2: Registration persists trial and consent timestamps**
    - Data provider over several valid payloads; assert one Business + one manager User linked, plan_id null, `onTrial()` true, `trial_ends_at == now()->addDays(7)` (frozen time), `tos_accepted_at` set, `assertAuthenticatedAs`
    - **Validates: Requirements 1.2, 1.3, 1.4, 1.6, 4.2, 4.1, 3.3**

  - [ ]* 9.2 Slug uniqueness
    - **Property 3: Slugs are unique and slug-consistent**
    - Register several businesses with the same name; assert all slugs unique and `Str::slug`-consistent
    - **Validates: Requirements 1.5**

  - [ ]* 9.3 Per-field validation and terms requirement
    - **Property 4: Invalid input identifies the failing field**
    - **Property 5: Missing Terms/Privacy acceptance is rejected**
    - Data provider omitting each required field → `assertSessionHasErrors([field])`; omit/false `terms` → error on `terms`
    - **Validates: Requirements 2.1, 3.2, 3.4**

  - [ ]* 9.4 Atomicity — invalid input creates no records
    - **Property 6: Invalid input creates no records (atomicity)**
    - Data provider of invalid payloads (missing field, dup email, bad email, weak password, missing terms); assert `Business::count()` and `User::count()` unchanged
    - **Validates: Requirements 2.5, 1.8**

  - [ ]* 9.5 Active-trial dashboard access and capability level
    - **Property 7: Active-trial manager reaches the dashboard**
    - **Property 8: Trial grants basic-level access with no premium capabilities**
    - Travel to several pre-expiry times; acting as trial manager GET dashboard routes → 200; assert `isPremium()` and all `canUse*()` false
    - **Validates: Requirements 5.1, 5.2, 5.3**

  - [ ]* 9.6 Remaining trial duration exposed consistently
    - **Property 9: Remaining trial duration is exposed consistently**
    - Travel to several pre-expiry times; assert shared `trial.daysRemaining` matches computed value
    - **Validates: Requirements 5.4**

  - [ ]* 9.7 Expired-trial block and redirect
    - **Property 10: Expired-trial manager is blocked and redirected to plan selection**
    - Travel past `trial_ends_at`; acting as trial manager GET each protected dashboard route → redirect to `route('plans')`
    - **Validates: Requirements 6.1, 6.3**

  - [ ]* 9.8 Plan listing shows only active plans
    - **Property 11: Plan selection lists exactly the active plans**
    - Seed active + inactive plans; GET `route('plans')` → `plans` prop contains exactly the active ones
    - **Validates: Requirements 6.4**

  - [ ]* 9.9 Paid business has full access regardless of trial_ends_at
    - **Property 12: A non-null plan_id means full access regardless of trial_ends_at**
    - For `trial_ends_at` in {past, future, null} with non-null `plan_id`, acting as that manager GET dashboard routes → 200
    - **Validates: Requirements 6.5, 8.2, 9.3**

  - [ ]* 9.10 Tenant isolation
    - **Property 13: Tenant isolation is preserved**
    - Manager of business A attempts to confirm/reject an appointment (and read a service) owned by business B → denied (403/404) and B's records unchanged
    - **Validates: Requirements 7.1, 7.2, 7.3, 7.4, 7.5**

  - [ ]* 9.11 Localization key parity
    - **Property 14: New localization keys exist and are synchronized**
    - Assert the new `register`/`trial`/`plans`/`verifyEmail` namespaces have identical, non-empty keys across `en`/`ar`/`he`
    - **Validates: Requirements 10.1, 10.2, 10.4**

  - [ ]* 9.12 Email-verification gate
    - **Property 15: Email verification gates dashboard access**
    - Data provider over verified/unverified states: unverified manager GET each protected `/dashboard/*` → redirect to `route('verification.notice')` (use `unverified()`); verified manager reaches dashboard on trial/paid and is redirected to `plans` when expired (Carbon travel past `trial_ends_at`)
    - **Validates: Requirements 12.3, 12.4, 11.6**

- [x] 10. Example / edge / regression tests
  - [x]* 10.1 Registration route and redirect examples
    - Guest GET `/register` → 200; valid POST → redirect to `route('dashboard')` (EXAMPLE 1.1/1.7); register page exposes ToS/Privacy links (EXAMPLE 3.1)
    - **Validates: Requirements 1.1, 1.7, 3.1**

  - [x]* 10.2 Duplicate/malformed email and weak password edges
    - Seed an existing email then register with it → `email` error, no new records (2.2); malformed emails and too-short/unconfirmed password → errors, no records (2.3/2.4)
    - **Validates: Requirements 2.2, 2.3, 2.4**

  - [x]* 10.3 Admin-created business and premium gate regressions
    - Admin creates a business → `trial_ends_at` null, `plan_id` non-null, resolves to `Paid_Business` and manager reaches dashboard (4.4/9.3); expired-trial manager GET `route('plans')` → 200 (6.2); premium business `canUse*()` still true (8.4); admin `store` still creates business + branding and rejects missing `plan_id` (9.1/9.2)
    - **Validates: Requirements 4.4, 6.2, 8.4, 9.1, 9.2, 9.3**

- [x] 11. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass with `php artisan test` / `composer test`, ask the user if questions arise.
  - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5, 11.6, 11.7_

## Notes

- Tasks marked with `*` are optional (tests and factory support) and can be skipped for a faster MVP; core implementation tasks are never optional.
- Each task references specific requirement sub-clauses and design sections for traceability.
- Ordering respects dependencies: migration/model (§1, §1a, §2) → controller/middleware (§3, §5) → plan controller (§6) → route wiring/prop sharing (§4, §5, §7) → frontend (§8) → factories → property tests → example/edge/regression tests.
- `verified` middleware is wired before `trial` on all dashboard routes so unverified managers are sent to the verification notice before trial state is evaluated; the `plans` route intentionally omits `trial` to avoid a redirect loop.
- Property tests are data-provider-driven PHPUnit tests (no new PBT dependency) using `RefreshDatabase` and Carbon test-time travel, per the design's Testing Strategy.
- No payment is implemented; no new runtime dependencies; tenant isolation and the admin-created business flow are preserved.

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2", "1.3", "6.1"] },
    { "id": 1, "tasks": ["1.4", "2.1", "3.1", "4.1"] },
    { "id": 2, "tasks": ["3.2", "3.3", "6.2", "6.3", "6.4"] },
    { "id": 3, "tasks": ["5.1", "5.2"] },
    { "id": 4, "tasks": ["5.3", "8.1"] },
    { "id": 5, "tasks": ["9.1", "9.2", "9.3", "9.4", "9.5", "9.6", "9.7", "9.8", "9.9", "9.10", "9.11", "9.12"] },
    { "id": 6, "tasks": ["10.1", "10.2", "10.3"] }
  ]
}
```
