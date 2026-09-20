# Requirements Document

## Introduction

This feature adds public, self-service registration for new businesses to Slotify. Today, businesses and their manager accounts are created exclusively by an admin through the `/admin/*` area. This feature introduces a second, public path: a visitor can register, which creates a new `Business` (tenant) and a manager `User` account belonging to that business, records acceptance of the Terms of Service and Privacy Policy, and starts a 7-day free trial. During the trial the manager can access the dashboard with basic-level feature access. When the trial expires with no paid plan, the manager is locked out of the dashboard except for a plan-selection screen.

The feature must coexist with the existing admin-created business flow without breaking it, preserve the existing session-based (Breeze) authentication and hand-rolled multi-tenant isolation, and be designed so a future subscription/payment feature can assign a paid plan without conflicting with the trial state model.

Scope boundaries (explicitly out of scope for this spec):
- No payment or subscription processing is implemented here. Plan selection after expiration surfaces available plans only; actual billing is a future feature.
- The existing authentication scaffold and tenant architecture are not redesigned.
- Email verification IS required after Self_Service_Registration before dashboard access; the `User` model will implement `MustVerifyEmail`, and dashboard access is gated by Laravel's `verified` middleware. The 7-day trial clock still starts at registration regardless of verification status.

## Glossary

- **Slotify**: The multi-tenant appointment-booking application.
- **Tenant**: A single `Business` record and all data scoped to it (users, services, appointments, availability, branding). Each `Business` is one tenant.
- **Business**: The `App\Models\Business` Eloquent model representing a tenant. Fillable fields include `name`, `email`, `slug`, `phone`, `address`, `timezone`, `is_active`, `plan_id`, `booking_window_days`.
- **Manager**: A `User` whose `role` is `'manager'` and who belongs to a `Business` via `business_id`. Managers operate in the `/dashboard/*` area.
- **Admin**: A `User` whose `role` is `'admin'`, operating in the `/admin/*` area. Admins are not scoped to a single business.
- **Visitor**: An unauthenticated guest who has not yet registered.
- **Self_Service_Registration**: The public registration flow introduced by this feature, distinct from the admin-created business flow.
- **Registration_Controller**: The server-side controller that handles the self-service registration request (the Breeze `RegisteredUserController` referenced in `routes/auth.php`, or an equivalent controller in `app/Http/Controllers/Auth/`).
- **Trial**: A time-limited period, starting at registration, during which a new business has dashboard access without a paid plan.
- **trial_ends_at**: A new nullable datetime field on the `Business` model recording the timestamp when the trial expires. It is set to registration time plus 7 days at registration.
- **Active_Trial**: The state of a business where `plan_id` is null and the current time is at or before `trial_ends_at`.
- **Expired_Trial**: The state of a business where `plan_id` is null and the current time is after `trial_ends_at`.
- **Paid_Business**: The state of a business where `plan_id` references a `Plan` record (assigned by the future payment feature).
- **Plan**: The `App\Models\Plan` model; `slug` is `'basic'` or `'premium'`. Plans are seeded/managed. Assignment of a paid plan to a business is a future feature.
- **Trial_Enforcement_Middleware**: A server-side middleware (following the existing hand-rolled `admin`/`manager` middleware pattern registered in `bootstrap/app.php`) that determines a business's trial state on protected dashboard routes and restricts access accordingly.
- **Plan_Selection_Screen**: The page shown to a manager of an Expired_Trial business, listing available active plans. In this spec it presents plans only; it does not process payment.
- **ToS_Accepted_At**: A new nullable datetime field on the `Business` (or manager `User`) record capturing when the Terms of Service and Privacy Policy were accepted during registration.
- **Locale_Files**: The i18n message files at `resources/js/i18n/locales/{en,ar,he}.ts`, kept synchronized across English, Arabic, and Hebrew.
- **Email_Verification_Notice**: The screen shown to an authenticated manager whose email is unverified, prompting them to verify their email, served by the existing Breeze `EmailVerificationPromptController`.

## Requirements

### Requirement 1: Public self-service registration

**User Story:** As a Visitor, I want to register a new business and manager account, so that I can start using Slotify without waiting for an admin to create my account.

#### Acceptance Criteria

1. THE Slotify SHALL expose a public registration page reachable by a Visitor without authentication.
2. WHEN a Visitor submits the registration form with valid data, THE Registration_Controller SHALL create one Business record and one manager User record within a single database transaction.
3. WHEN the Registration_Controller creates the manager User, THE Registration_Controller SHALL set the User `role` to `'manager'` and set the User `business_id` to the identifier of the newly created Business.
4. WHEN the Registration_Controller creates the Business, THE Registration_Controller SHALL set `plan_id` to null.
5. WHEN the Business is created, THE Registration_Controller SHALL generate a unique `slug` for the Business consistent with the existing slug generation approach used by the admin business flow.
6. WHEN registration completes successfully, THE Registration_Controller SHALL authenticate the new manager User using the existing session-based Breeze authentication mechanism.
7. WHEN registration completes successfully, THE Registration_Controller SHALL redirect the authenticated manager to the dashboard. (Dashboard access is additionally gated by email verification per Requirement 12; an unverified manager is redirected to the email-verification notice screen.)
8. IF the creation of the Business or the manager User fails during registration, THEN THE Registration_Controller SHALL roll back the transaction so that neither a partial Business nor a partial User record persists.

### Requirement 2: Registration validation and duplicate handling

**User Story:** As a Visitor, I want clear validation feedback when my registration input is invalid, so that I can correct my input and complete registration.

#### Acceptance Criteria

1. WHEN a Visitor submits registration without a required field, THE Registration_Controller SHALL reject the submission and return a validation error identifying the missing field.
2. IF the submitted email is already used by an existing User, THEN THE Registration_Controller SHALL reject the submission and return a validation error indicating the email is already registered.
3. IF the submitted email is not a syntactically valid email address, THEN THE Registration_Controller SHALL reject the submission and return a validation error indicating the email is invalid.
4. IF the submitted password does not meet the password rules used by the existing Breeze registration scaffold, THEN THE Registration_Controller SHALL reject the submission and return a validation error describing the password requirement.
5. IF registration validation fails, THEN THE Registration_Controller SHALL NOT create a Business or a User record.
6. WHEN validation is performed, THE Registration_Controller SHALL use inline request validation within the controller, consistent with the project's existing validation convention.

### Requirement 3: Terms of Service and Privacy Policy acceptance

**User Story:** As a business owner registering, I want to explicitly accept the Terms of Service and Privacy Policy, so that my acceptance is recorded and the platform has consent on file.

#### Acceptance Criteria

1. THE Slotify SHALL present links to the Terms of Service and the Privacy Policy on the registration page.
2. IF a Visitor submits registration without accepting the Terms of Service and Privacy Policy, THEN THE Registration_Controller SHALL reject the submission and return a validation error requiring acceptance.
3. WHEN registration completes successfully, THE Registration_Controller SHALL persist the acceptance timestamp in the ToS_Accepted_At field.
4. THE Registration_Controller SHALL treat acceptance of the Terms of Service and Privacy Policy as a required condition for creating a Business and manager User.

### Requirement 4: Start the 7-day free trial

**User Story:** As a newly registered manager, I want a 7-day free trial to begin automatically, so that I can evaluate Slotify before choosing a paid plan.

#### Acceptance Criteria

1. WHEN a Business is created through Self_Service_Registration, THE Registration_Controller SHALL set the Business `trial_ends_at` to the registration timestamp plus 7 days.
2. WHEN a Business is created through Self_Service_Registration, THE Registration_Controller SHALL leave `plan_id` null so that the Business is in the Active_Trial state.
3. THE Slotify SHALL persist `trial_ends_at` as a nullable datetime field on the Business model.
4. WHERE a Business is created through the admin-created business flow, THE Slotify SHALL leave `trial_ends_at` null so that admin-created businesses are not treated as trial businesses.

### Requirement 5: Dashboard access during the active trial

**User Story:** As a manager during my free trial, I want to access the dashboard with basic features, so that I can configure my business and take bookings while evaluating Slotify.

#### Acceptance Criteria

1. WHILE a Business is in the Active_Trial state, THE Trial_Enforcement_Middleware SHALL allow the business's manager to access the `/dashboard/*` routes, provided the manager's email is verified per Requirement 12.
2. WHILE a Business is in the Active_Trial state, THE Slotify SHALL grant the business basic-level feature access equivalent to the `'basic'` plan level.
3. WHILE a Business is in the Active_Trial state, THE Slotify SHALL NOT grant premium-only capabilities (the capabilities gated by `isPremium()`, `canUseApprovalWorkflow()`, `canUseWhatsapp()`, and `canUseReminders()`).
4. WHILE a Business is in the Active_Trial state, THE Slotify SHALL make the remaining trial duration available to the dashboard interface so the manager can see how much trial time remains.

### Requirement 6: Trial expiration and post-expiration access

**User Story:** As a manager whose trial has ended, I want to be directed to choose a paid plan, so that I understand what I must do to regain full dashboard access.

#### Acceptance Criteria

1. WHILE a Business is in the Expired_Trial state, THE Trial_Enforcement_Middleware SHALL block the business's manager from accessing the standard `/dashboard/*` routes.
2. WHILE a Business is in the Expired_Trial state, THE Trial_Enforcement_Middleware SHALL allow the business's manager to access the Plan_Selection_Screen.
3. WHEN a manager of an Expired_Trial business requests a blocked dashboard route, THE Trial_Enforcement_Middleware SHALL redirect the manager to the Plan_Selection_Screen.
4. WHEN the Plan_Selection_Screen is shown, THE Slotify SHALL list the active Plan records available for selection.
5. WHILE a Business is a Paid_Business, THE Trial_Enforcement_Middleware SHALL allow the business's manager full access to the `/dashboard/*` routes.
6. THE Slotify SHALL determine trial state (Active_Trial, Expired_Trial, Paid_Business) on the server for each protected request rather than relying on hidden or disabled interface elements.

### Requirement 7: Preserve multi-tenant isolation

**User Story:** As a platform operator, I want each business's data to remain isolated to its own tenant, so that self-service registration does not weaken existing authorization or tenant scoping.

#### Acceptance Criteria

1. THE Registration_Controller SHALL scope the newly created manager User to the newly created Business via `business_id` only.
2. WHEN a manager accesses dashboard resources, THE Slotify SHALL continue to scope those resources to the authenticated manager's own `business_id` using the existing hand-rolled ownership checks.
3. THE Trial_Enforcement_Middleware SHALL evaluate trial state using the authenticated manager's own Business only.
4. THE Slotify SHALL NOT introduce cross-tenant access, and SHALL preserve the existing `admin` and `manager` middleware behavior.
5. IF an authenticated manager attempts to access a resource belonging to a different `business_id`, THEN THE Slotify SHALL deny access, consistent with existing behavior.

### Requirement 8: Compatibility with the future payment feature

**User Story:** As a developer of the future subscription feature, I want the trial state model to be compatible with paid-plan assignment, so that I can add billing later without reworking the trial design.

#### Acceptance Criteria

1. THE Slotify SHALL represent the trial using trial-tracking fields on the Business (such as `trial_ends_at`) that are independent of the `plan_id` field.
2. WHEN a paid Plan is assigned to a Business by a future feature (setting a non-null `plan_id`), THE Trial_Enforcement_Middleware SHALL treat the Business as a Paid_Business regardless of `trial_ends_at`.
3. THE Slotify SHALL NOT implement payment processing, subscription billing, or new payment dependencies within this feature.
4. THE Slotify SHALL keep the Plan model, plan slugs (`'basic'`, `'premium'`), and existing plan-based capability gates unchanged by this feature.

### Requirement 9: Preserve the existing admin-created business flow

**User Story:** As an admin, I want to continue creating businesses and managers through the admin area, so that self-service registration does not disrupt the existing workflow.

#### Acceptance Criteria

1. THE Slotify SHALL preserve the existing `/admin/*` business creation flow without functional change caused by this feature.
2. WHEN an admin creates a Business through the admin flow, THE Slotify SHALL continue to require and assign a `plan_id`, consistent with existing behavior.
3. WHERE a Business has a non-null `plan_id` and a null `trial_ends_at`, THE Trial_Enforcement_Middleware SHALL treat the Business as a Paid_Business and SHALL NOT apply trial expiration restrictions.
4. THE Slotify SHALL allow admin-created businesses and self-service-registered businesses to coexist without conflict.

### Requirement 10: Internationalization of new user-facing strings

**User Story:** As a manager who reads English, Arabic, or Hebrew, I want all new registration and trial screens localized, so that I can use the feature in my language with correct text direction.

#### Acceptance Criteria

1. THE Slotify SHALL provide English, Arabic, and Hebrew translations for all new user-facing strings introduced by this feature using vue-i18n.
2. THE Slotify SHALL keep the Locale_Files (`en`, `ar`, `he`) synchronized so that every new message key exists in all three locales.
3. WHILE the active locale is Arabic or Hebrew, THE Slotify SHALL render the new screens in right-to-left layout.
4. THE Slotify SHALL NOT display untranslated raw message keys for the new registration, trial, and plan-selection screens in any of the three supported locales.

### Requirement 11: Automated tests

**User Story:** As a developer, I want feature tests covering registration, trial behavior, expiration, and tenant isolation, so that regressions in this feature are caught.

#### Acceptance Criteria

1. THE Slotify SHALL include a feature test verifying that valid Self_Service_Registration creates a Business and a manager User with `role` `'manager'`, `plan_id` null, and a `trial_ends_at` set to 7 days ahead.
2. THE Slotify SHALL include a feature test verifying that invalid registration input (missing fields, duplicate email, and missing Terms/Privacy acceptance) is rejected with validation errors and creates no records.
3. THE Slotify SHALL include a feature test verifying that a manager of an Active_Trial business can access the dashboard.
4. THE Slotify SHALL include a feature test verifying that a manager of an Expired_Trial business is blocked from the standard dashboard routes and redirected to the Plan_Selection_Screen.
5. THE Slotify SHALL include a feature test verifying that a manager cannot access resources belonging to a different `business_id` (tenant isolation).
6. THE Slotify SHALL include a feature test verifying that a manager with an unverified email is redirected to the Email_Verification_Notice when requesting a dashboard route, and that a manager with a verified email reaches the dashboard (subject to trial state).
7. THE Slotify SHALL run these tests under PHPUnit using `php artisan test` / `composer test`.

### Requirement 12: Email verification on registration

**User Story:** As a newly registered manager, I want to verify my email address before accessing the dashboard, so that Slotify confirms I own the email used to register my business.

#### Acceptance Criteria

1. WHEN a manager completes Self_Service_Registration, THE Slotify SHALL send a verification email to the registered email address.
2. THE User model SHALL implement Laravel's email-verification functionality (the `MustVerifyEmail` contract) so that registration triggers the standard verification email.
3. WHILE the manager's email is unverified, THE Slotify SHALL restrict access to the `/dashboard/*` routes and SHALL redirect the manager to the Email_Verification_Notice, using Laravel's existing `verified` middleware and the Breeze email-verification scaffold.
4. WHEN the manager verifies their email, THE Slotify SHALL grant access to the `/dashboard/*` routes, subject to the trial state defined in Requirements 5 and 6.
5. WHEN the manager requests a resend of the verification email, THE Slotify SHALL send a new verification email to the registered email address, using the existing Breeze `EmailVerificationNotificationController`.
6. WHEN a Business is created through Self_Service_Registration, THE Registration_Controller SHALL set `trial_ends_at` to the registration timestamp plus 7 days regardless of the manager's email-verification status, so that email verification gates dashboard access without changing when the trial clock starts.
7. THE Slotify SHALL preserve the existing session-based Breeze authentication and the existing `verified` middleware, and SHALL NOT redesign the authentication mechanism.
8. THE Slotify SHALL provide English, Arabic, and Hebrew translations for all new user-facing strings introduced by the email-verification screens, consistent with Requirement 10.

## Open Questions / Decisions for Review

The following product decisions were not finalized by the user. Each lists options and a recommended default that the acceptance criteria above are currently written against. Please confirm or override; requirements will be updated accordingly.

### Open Question 1: Trial plan/feature level
During the 7-day trial, which features should a new business receive?
- (a) Premium-level features
- (b) Basic-level features
- (c) A separate trial state where `plan_id` stays null until payment

**Recommended default (used in Requirements 4, 5, 8):** Treat the trial as a separate trial state — add trial-tracking fields on the Business (e.g., `trial_ends_at`), keep `plan_id` null until the future payment feature assigns a paid plan, and grant basic-level feature access during the trial. This combines option (c) for the state model with option (b) for feature access. Please confirm.

### Open Question 2: Post-expiration access level
After the 7 days expire with no paid plan, what should the manager still be able to do?
- (a) Locked out of the dashboard except the plan-selection screen
- (b) Read-only dashboard
- (c) Also disable the public booking page for that business

**Recommended default (used in Requirement 6):** Lock the manager out of the standard dashboard except the Plan_Selection_Screen (option a). Please confirm.

**Sub-question (not decided):** When a trial expires, should the business's public booking page (`/book/{slug}`) remain live, or be disabled until a plan is selected? The recommended default above leaves the public booking page behavior unspecified pending your decision. Please confirm the desired behavior so a corresponding acceptance criterion can be added.

### Confirmed decision (for reference)
- Email verification IS required on first registration (at trial start). After Self_Service_Registration the manager must verify their email before accessing the dashboard. The `User` model will implement `MustVerifyEmail`, and dashboard access is gated by Laravel's existing `verified` middleware and Breeze email-verification scaffold. The 7-day trial still starts at registration (`trial_ends_at` is set at registration regardless of verification status); verification gates dashboard ACCESS only. See Requirement 12.
