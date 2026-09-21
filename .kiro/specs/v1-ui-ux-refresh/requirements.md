# Requirements Document

## Introduction

The v1 UI/UX Refresh unifies and elevates the user interface and experience of iRestTOR (Slotify) across every surface area: the public landing page, the manager dashboard, the admin console, authentication and profile screens, and the public booking flow. This is an **evolution** of the existing Slotify design, not a rebrand: the current Slotify visual identity and its core navy/blue palette are preserved and made consistent.

The work is **frontend/UI only**. No backend behavior, business logic, authorization, tenant scoping, or data model changes are in scope. The existing Laravel + Inertia + Vue 3 + TypeScript architecture, and the fixed stack (Vue 3 `<script setup lang="ts">`, Tailwind v3, Bootstrap 5.3 + Bootstrap Icons, vue-i18n v11), are preserved.

An audit identified three disconnected visual languages, two competing brand blues, non-persistent locale selection, a broken language-switcher active state, hardcoded/untranslated strings, RTL and date-formatting gaps, missing mobile navigation on dashboards, and non-responsive tables. This document captures the requirements to resolve those issues and raise the whole product to a consistent, premium SaaS quality bar.

Requirements are organized by area: Design-System Unification, Landing Page, Manager Dashboard, Admin Console, Authentication & Profile, Booking Flow, Internationalization & RTL, Mobile & Responsive, Accessibility, and State Handling. Cross-cutting hard constraints are captured in their own requirement. Ambiguous product/design decisions are collected in the "Open Questions / Decisions to Confirm" section rather than resolved by assumption.

## Glossary

- **Slotify_UI**: The Slotify frontend application (Vue 3 + Inertia pages, layouts, and components) that this refresh modifies.
- **Slotify_Design_System**: The single, unified set of design tokens (colors, typography scale, spacing rhythm, radii, borders, shadows/elevation, motion) and shared components that all internal Slotify screens use.
- **Slotify_Brand_Identity**: The existing Slotify visual identity being evolved — the navy/blue palette and product look, kept recognizable rather than replaced.
- **Tenant_Branding**: A Business's configurable branding (logo, cover image, primary/secondary/accent colors, theme style, public copy) sourced from `BusinessBrandingSettings`.
- **Internal_Screen**: Any non-public Slotify surface — landing page, authentication, profile, manager dashboard, and admin console — that uses the Slotify_Design_System (not Tenant_Branding).
- **Public_Booking_Page**: The customer-facing booking page served at `/book/{slug}` (`Pages/Booking/Show.vue`), the only surface permitted to apply Tenant_Branding.
- **Manager_Dashboard**: The manager area under `/dashboard/*`, wrapped by `ManagerLayout.vue`.
- **Admin_Console**: The admin area under `/admin/*`, wrapped by `AdminLayout.vue`.
- **Auth_Screens**: The authentication and profile screens (`Pages/Auth/*`, `Pages/Profile/*`) wrapped by `GuestLayout.vue` / authenticated profile layout, currently on the Breeze Tailwind theme.
- **Language_Switcher**: The UI control (present in landing, booking, `ManagerLayout`, `AdminLayout`) that changes the active vue-i18n locale.
- **Active_Locale**: The vue-i18n locale currently in effect (`en`, `ar`, or `he`).
- **Supported_Locale**: One of the locales the application supports — `en`, `ar`, or `he`.
- **Profile_Screen**: The profile edit screens under `Pages/Profile/*`.
- **Persisted_Locale**: The locale value stored in browser `localStorage` under the key `locale`.
- **RTL_Locale**: A locale rendered right-to-left — `ar` and `he`.
- **Mobile_Navigation**: A mobile-appropriate navigation affordance (bottom bar, drawer, or collapsible menu) as distinct from the desktop navigation.
- **Responsive_Table**: A tabular data view that adapts to a stacked/card layout on small viewports instead of overflowing horizontally.
- **View_State**: One of the data-driven display conditions of a screen — loading, empty, success, or error.
- **Availability_UI**: The mid-refactor availability/scheduling interface (`WeeklyAvailability.vue`, `SpecialDatesAvailability.vue`, `Pages/Dashboard/Availability/*`) and its active spec.
- **Design_Token**: A named, reusable style value (for example a color, spacing step, radius, or shadow level) defined once in the Slotify_Design_System.

## Requirements

### Requirement 1: Frontend-Only, Architecture-Preserving Scope (cross-cutting constraint)

**User Story:** As a maintainer, I want the refresh confined to the frontend and the existing architecture, so that behavior, security, and tenancy remain unchanged.

#### Acceptance Criteria

1. THE Slotify_UI SHALL confine every change to frontend presentation files only — Vue components, layouts, pages, stylesheets, vue-i18n message files (`en.ts`, `ar.ts`, `he.ts`), and shared TypeScript UI types in `resources/js/types/global.d.ts` — and SHALL make zero changes to files under `app/`, `routes/`, `database/`, or other backend directories.
2. THE Slotify_UI SHALL preserve existing controller behavior, business logic, authorization checks, and tenant scoping such that every controller method returns the same data and redirects as before the refresh.
3. THE Slotify_UI SHALL retain the Laravel + Inertia + Vue 3 + TypeScript architecture and SHALL NOT add any UI framework or state-management library beyond the fixed stack.
4. THE Slotify_UI SHALL implement all UI using only the fixed stack: Vue 3 `<script setup lang="ts">`, Tailwind v3, Bootstrap 5.3, Bootstrap Icons, and vue-i18n v11.
5. WHERE an existing shared component (for example `PrimaryButton`, `TextInput`, `Modal`, or `Dropdown`) satisfies a UI need, THE Slotify_UI SHALL reuse that component and SHALL NOT introduce a duplicate component for the same need.
6. WHERE a new shared data shape is required, THE Slotify_UI SHALL define the corresponding TypeScript type in `resources/js/types/global.d.ts` and SHALL NOT define it inline in a component.
7. THE Slotify_UI SHALL preserve the existing page-to-controller namespace mirroring convention under `resources/js/Pages/`, such that each added or moved page retains the mapping to its controller namespace.
8. IF a proposed change would modify a backend file, alter controller-returned data, or change authorization or tenant scoping, THEN THE Slotify_UI SHALL reject that change and retain the existing backend behavior.
9. WHEN the refresh is complete, THE Slotify_UI SHALL build successfully via `npm run build` and SHALL pass the existing PHPUnit test suite with zero newly failing tests.

### Requirement 2: Unified Slotify Design System

**User Story:** As a user of any Slotify screen, I want a single consistent visual system, so that the product feels coherent instead of three disconnected interfaces.

#### Acceptance Criteria

1. THE Slotify_Design_System SHALL define exactly one canonical set of Design_Tokens covering all seven categories: color, typography scale, spacing rhythm, border radii, border styles, elevation, and motion, with each category having at least one defined token value.
2. THE Slotify_Design_System SHALL define exactly one canonical brand-blue Design_Token and exactly one canonical navy Design_Token, each derived from the existing Slotify_Brand_Identity.
3. THE Slotify_UI SHALL render every Internal_Screen using only Design_Tokens from the Slotify_Design_System, with zero hard-coded color, spacing, radius, elevation, or motion values outside the token set.
4. WHERE an Internal_Screen contains any competing brand-blue value (`#2563eb` or `#2563ff`), the Breeze `indigo-500` accent, or an inline `#2563ff` fallback, THE Slotify_UI SHALL replace each such value with the canonical brand-blue Design_Token, leaving zero occurrences of those superseded values.
5. THE Slotify_UI SHALL apply the single canonical type scale and spacing rhythm defined in the Slotify_Design_System across all Internal_Screens, such that no Internal_Screen uses a font size, line height, or spacing step outside the defined scale.
6. THE Slotify_UI SHALL apply at most two elevation levels defined in the Slotify_Design_System across shared components, with zero per-screen shadow values outside those two levels.
7. WHEN a shared component (button, input, card, or badge) is rendered on any Internal_Screen, THE Slotify_UI SHALL render it with the same Design_Token-defined appearance and the same interaction behavior for each of its states (default, hover, focus, active, and disabled) regardless of which Internal_Screen it appears on.
8. THE Slotify_UI SHALL preserve the Slotify_Brand_Identity by retaining the existing navy and blue as the primary color pair, evolving the existing look without introducing a different primary color family.

### Requirement 3: Branding Boundary Between Product and Tenant

**User Story:** As a Slotify operator, I want tenant branding confined to public booking pages, so that internal screens stay consistent and readable.

#### Acceptance Criteria

1. WHEN the Public_Booking_Page is rendered, THE Slotify_UI SHALL apply Tenant_Branding, where Tenant_Branding consists of the primary color, secondary color, accent color, logo, cover image, public title, public subtitle, public description, and theme style, and SHALL NOT apply Tenant_Branding on any other route.
2. WHEN any Internal_Screen is rendered, THE Slotify_UI SHALL render it using the Slotify_Design_System and SHALL NOT apply any Tenant_Branding attribute, where Internal_Screens are all admin (`/admin/*`), manager dashboard (`/dashboard/*`), authentication, and profile screens.
3. WHILE Tenant_Branding is applied on the Public_Booking_Page, THE Slotify_UI SHALL maintain text and background contrast and readability that meet the accessibility criteria defined in Requirement 9.
4. THE Slotify_UI SHALL read Tenant_Branding values from the existing `BusinessBrandingSettings` record in a read-only manner and SHALL NOT create, modify, or delete any `BusinessBrandingSettings` value.
5. IF one or more Tenant_Branding attributes are absent, null, or fail their expected format, THEN THE Slotify_UI SHALL substitute the corresponding Slotify_Design_System default value for each affected attribute and SHALL render the Public_Booking_Page without error.

### Requirement 4: Landing Page Refresh

**User Story:** As a prospective customer, I want an impressive, trustworthy landing page, so that I understand Slotify's value and am motivated to act.

#### Acceptance Criteria

1. WHEN the Landing page loads at any viewport width from 320px through 1920px, THE Landing page SHALL render the primary value-proposition headline and its supporting subheading within the initial viewport height without requiring the user to scroll.
2. THE Landing page SHALL render its primary call-to-action in a visually distinct style (using the primary-action Design_Token treatment) that differs from secondary actions, with exactly one primary-styled action per section.
3. WHEN a user activates the primary call-to-action, THE Landing page SHALL navigate to the associated destination route.
4. THE Landing page SHALL render using the Slotify_Design_System and the canonical Slotify_Brand_Identity, such that its colors, typography, and spacing resolve from Design_Tokens with zero hardcoded color or spacing literals.
5. THE Landing page SHALL present a product visualization of the booking experience rendered with Slotify_Design_System Design_Tokens.
6. WHEN the Landing page is viewed at a mobile viewport width of 320px to 767px, THE Landing page SHALL present the hero, calls-to-action, and sections with no horizontal overflow and no clipped or truncated content.
7. WHEN the Landing page renders any user-facing text, THE Landing page SHALL source that text from vue-i18n translation keys as specified in Requirement 9, with zero hardcoded user-facing display strings.
8. WHILE the Active_Locale is an RTL_Locale (`ar` or `he`), THE Landing page SHALL set document direction to right-to-left and mirror layout, alignment, and directional icons accordingly.

### Requirement 5: Manager Dashboard Refresh

**User Story:** As a manager, I want a calm, usable dashboard, so that I can manage my business efficiently.

#### Acceptance Criteria

1. THE Manager_Dashboard SHALL render every `/dashboard/*` screen using the Slotify_Design_System components and the canonical Slotify_Brand_Identity, such that all typography, spacing, and color values resolve from Design_Tokens with zero hardcoded color or spacing literals.
2. THE Manager_Dashboard SHALL group related content into distinct sections and SHALL emphasize exactly one primary action per view using the primary-action Design_Token treatment.
3. THE Manager_Dashboard SHALL render navigation via a single shared `ManagerLayout.vue` wrapper on all `/dashboard/*` screens, presenting an identical set of navigation destinations, in identical order, across every screen.
4. WHILE the viewport width is 767 pixels or less, THE Manager_Dashboard SHALL present Mobile_Navigation as specified in Requirement 8.
5. THE Manager_Dashboard SHALL display all tabular data as Responsive_Tables as specified in Requirement 8.
6. WHILE the Availability_UI is in scope, THE Manager_Dashboard SHALL apply the constraints in Requirement 13 to that area.
7. IF a data set requested for a Manager_Dashboard view fails to load, THEN THE Manager_Dashboard SHALL present the error View_State defined in Requirement 12 rather than a raw error.
8. IF a data set requested for a Manager_Dashboard view loads with zero items, THEN THE Manager_Dashboard SHALL present the empty View_State defined in Requirement 12.

### Requirement 6: Admin Console Refresh

**User Story:** As a platform admin, I want a consistent, readable admin console, so that I can manage businesses, managers, and requests efficiently.

#### Acceptance Criteria

1. THE Admin_Console SHALL render every `/admin/*` screen using the Slotify_Design_System components and the canonical Slotify_Brand_Identity, such that all typography, spacing, and color values resolve from Design_Tokens with zero hardcoded color or spacing literals.
2. THE Admin_Console SHALL contain zero references to raw Bootstrap classes and zero references to ad-hoc `admin-*` CSS classes, all such styling having been replaced by shared Slotify_Design_System components and Design_Tokens.
3. THE Admin_Console SHALL render navigation via a single shared `AdminLayout.vue` wrapper on all `/admin/*` screens, presenting an identical set of navigation destinations, in identical order, across every screen.
4. WHILE the viewport width is 767 pixels or less, THE Admin_Console SHALL present Mobile_Navigation as specified in Requirement 8.
5. WHILE the viewport width is 768 pixels or greater, THE Admin_Console SHALL present the full persistent navigation via `AdminLayout.vue`.
6. THE Admin_Console SHALL display all tabular data as Responsive_Tables as specified in Requirement 8.
7. IF a data set requested for a table is empty, THEN THE Admin_Console SHALL display an empty-state message indicating that no records exist and SHALL NOT render an empty table body.

### Requirement 7: Authentication and Profile Alignment

**User Story:** As a user signing in or editing my profile, I want these screens to match the rest of Slotify, so that the experience is seamless.

#### Acceptance Criteria

1. THE Auth_Screens SHALL render using the Slotify_Design_System components and Design_Tokens instead of the untouched Breeze Tailwind gray theme, such that no Breeze default gray palette classes remain on any Auth_Screen or Profile_Screen.
2. THE Auth_Screens SHALL replace every occurrence of the Breeze `indigo-500` accent with the canonical brand-blue Design_Token, such that no `indigo-500` reference remains in any Auth_Screen or Profile_Screen.
3. WHEN an Auth_Screen or Profile_Screen renders any user-facing text, THE Auth_Screens SHALL source that text from vue-i18n translation keys, such that no hard-coded user-facing string literals remain.
4. WHILE the active locale is an RTL_Locale (ar or he), THE Auth_Screens SHALL render with `document.dir` set to `rtl` and SHALL mirror layout direction, text alignment, and directional icons accordingly.
5. IF a vue-i18n translation key is missing for the active locale, THEN THE Auth_Screens SHALL render the key's `en` fallback text without displaying the raw key identifier.
6. WHEN an Auth_Screen or Profile_Screen is viewed on a mobile viewport of 320 px to 767 px width, THE Auth_Screens SHALL render without horizontal overflow and SHALL present all interactive targets at a minimum touch size of 44 by 44 CSS pixels.

### Requirement 8: Booking Flow Refresh

**User Story:** As a customer, I want a clear, polished booking flow, so that I can book an appointment with confidence.

#### Acceptance Criteria

1. THE Public_Booking_Page SHALL present the service selection, date selection, slot selection, and OTP verification steps in that order, with a visible indication of the current step and the steps completed.
2. THE Public_Booking_Page SHALL apply Tenant_Branding as permitted by Requirement 3 while meeting the accessibility criteria in Requirement 9.
3. THE Public_Booking_Page SHALL replace every inline hardcoded color fallback (for example `#2563ff` in service dots) with a Design_Token or a resolved Tenant_Branding value, leaving zero inline hardcoded color literals.
4. IF a Tenant_Branding color required for a booking-page element cannot be resolved, THEN THE Public_Booking_Page SHALL apply the corresponding Slotify_Design_System default Design_Token and SHALL render the element without error.
5. WHEN the Public_Booking_Page is viewed at a mobile viewport width of 320px to 767px, THE Public_Booking_Page SHALL present each booking step with no horizontal overflow and SHALL render every interactive target at a minimum tap area of 44px by 44px.
6. WHEN the Public_Booking_Page renders any user-facing text, THE Public_Booking_Page SHALL source that text from vue-i18n translation keys as specified in Requirement 9, with zero hardcoded user-facing display strings.
7. WHILE the Active_Locale is an RTL_Locale (`ar` or `he`), THE Public_Booking_Page SHALL set document direction to right-to-left and mirror layout, alignment, and directional icons accordingly.
8. IF OTP verification fails, THEN THE Public_Booking_Page SHALL present a readable, accessible error message associated with the OTP input, as specified in Requirement 11, and SHALL allow the user to retry.
9. THE Public_Booking_Page SHALL preserve the existing booking behavior, OTP handling, and appointment-creation flow without change.

### Requirement 9: Internationalization and RTL Consistency

**User Story:** As a multilingual user, I want fully translated screens that persist my language and mirror correctly, so that I can use Slotify in English, Arabic, or Hebrew.

#### Acceptance Criteria

1. THE Slotify_UI SHALL route all user-facing text through vue-i18n, and for every added or changed user-facing string SHALL provide a matching key in each of `en.ts`, `ar.ts`, and `he.ts`.
2. THE Slotify_UI SHALL NOT render user-facing text from hardcoded string literals in components.
3. IF a translation key requested for the Active_Locale is missing, THEN THE Slotify_UI SHALL render the corresponding `en` fallback value for that key.
4. WHEN a user selects a Supported_Locale (`en`, `ar`, or `he`) via the Language_Switcher, THE Slotify_UI SHALL set the Active_Locale to the selected locale and store it as the Persisted_Locale under the storage key `locale`.
5. WHEN the application initializes and a Persisted_Locale exists under the storage key `locale` whose value is one of the Supported_Locales, THE Slotify_UI SHALL set the Active_Locale to that Persisted_Locale.
6. IF, at application initialization, no Persisted_Locale exists or its stored value is not one of the Supported_Locales (`en`, `ar`, `he`), THEN THE Slotify_UI SHALL set the Active_Locale to `en`.
7. WHILE a Supported_Locale is the Active_Locale, THE Language_Switcher SHALL render exactly that one locale option in a visually distinct selected state and render all other locale options in the unselected state.
8. WHILE the Active_Locale is an RTL_Locale (`ar` or `he`), THE Slotify_UI SHALL set the document direction to right-to-left and mirror horizontal layout, text alignment, horizontal padding/margins, flex ordering, and directional icons accordingly.
9. WHILE the Active_Locale is a non-RTL_Locale (`en`), THE Slotify_UI SHALL set the document direction to left-to-right.
10. WHEN a date is displayed, THE Slotify_UI SHALL format the date according to the Active_Locale.
11. THE Slotify_UI SHALL render every refreshed screen with all user-facing text resolved to the Active_Locale (no untranslated keys or fallback placeholders visible) in each of `en`, `ar`, and `he`.

> Note (verified in audit): several surfaces already call `localStorage.setItem('locale', ...)`, but `resources/js/i18n/index.ts` hardcodes `locale: 'en'` and no code reads the stored value on init, so the locale resets on reload. Criterion 4 targets this read-on-init gap.

### Requirement 10: Mobile and Responsive Behavior

**User Story:** As a mobile user, I want screens intentionally designed for small viewports, so that Slotify is comfortable to use on a phone.

#### Acceptance Criteria

1. WHEN any refreshed screen is viewed at a mobile viewport width of 320px to 767px, THE Slotify_UI SHALL adapt navigation, spacing, typography, grids, cards, forms, and actions for that viewport rather than rendering the desktop layout scaled or unchanged.
2. WHEN the Manager_Dashboard or Admin_Console is viewed at a viewport width of 320px to 767px, THE Slotify_UI SHALL present Mobile_Navigation (a bottom bar, drawer, or collapsible menu) that exposes every top-level destination available in the desktop navigation and that is visually distinct from the desktop navigation.
3. WHEN a data table is viewed at a viewport width of 320px to 767px, THE Slotify_UI SHALL present it as a Responsive_Table using a stacked or card layout in which every column value from the desktop table remains visible.
4. THE Slotify_UI SHALL render every interactive touch target on viewports of 320px to 767px with a minimum tap area of 44px by 44px.
5. WHEN any refreshed screen is viewed at any viewport width from 320px through 1440px or wider, THE Slotify_UI SHALL fit all content within the viewport width so that no horizontal scrollbar appears and no element extends beyond the viewport edge.
6. WHEN a refreshed screen is viewed at a viewport width of 320px to 767px in an RTL_Locale (Arabic or Hebrew), THE Slotify_UI SHALL set document direction to right-to-left and mirror layout, navigation placement, and directional icons accordingly, with no left-to-right visual artifacts.
7. THE Slotify_UI SHALL apply the same Slotify_Brand_Identity (colors, typography, logo) and shared component library across all supported breakpoints from 320px through 1440px or wider.
8. IF content at any viewport width from 320px through 1440px or wider cannot fit within the viewport width, THEN THE Slotify_UI SHALL wrap, stack, or constrain that content within the viewport and SHALL NOT introduce horizontal scrolling of the page.

### Requirement 11: Accessibility

**User Story:** As a user relying on assistive technology or keyboard navigation, I want accessible screens, so that I can operate Slotify effectively.

#### Acceptance Criteria

1. THE Slotify_UI SHALL maintain a text/background contrast ratio of at least 4.5:1 for normal text and at least 3:1 for large text, icons, and UI component boundaries, on refreshed screens across the `en`, `ar`, and `he` locales in both RTL and LTR directions.
2. WHEN an interactive element receives keyboard focus, THE Slotify_UI SHALL display a visible focus indicator with a contrast ratio of at least 3:1 against its adjacent background.
3. THE Slotify_UI SHALL use semantic HTML elements and SHALL programmatically associate a label with every form control, and SHALL NOT rely on placeholder text as the sole label.
4. THE Slotify_UI SHALL make every interactive element operable by keyboard alone in a logical focus order, without requiring a pointing device.
5. THE Slotify_UI SHALL convey each appointment status (confirmed, pending_approval, cancelled) and other status information using text or iconography in addition to color, and SHALL NOT rely on color alone.
6. IF input validation fails, THEN THE Slotify_UI SHALL present a text error message that identifies the affected field and the required correction, programmatically associate that message with the field, move focus to the first field in error, and retain the user's entered input.
7. WHERE the operating system or browser reports a reduced-motion preference, THE Slotify_UI SHALL reduce or remove non-essential motion and SHALL cap any remaining motion at 500ms.

### Requirement 12: View State Handling

**User Story:** As a user of any data-driven screen, I want polished loading, empty, and error states, so that the interface stays clear in every condition.

#### Acceptance Criteria

1. WHILE a data-driven view is loading, THE Slotify_UI SHALL present a loading View_State (skeleton or spinner) that occupies the same dimensions as the final loaded content, such that no visible content reflow (layout shift) occurs when data arrives.
2. WHEN a data-driven view finishes loading and its data collection contains zero items, THE Slotify_UI SHALL present an empty View_State containing an explanatory message and at least one actionable control (link or button) that navigates to the primary next step for that view.
3. WHEN an operation succeeds, THE Slotify_UI SHALL present a success View_State by rendering the message from the shared `flash.success` Inertia prop.
4. IF an operation fails, THEN THE Slotify_UI SHALL present an error View_State that renders a human-readable message describing the failure and at least one recovery action (such as retry or dismiss), and SHALL NOT display raw exception text, stack traces, or raw error payloads.
5. WHEN a data-driven view does not complete loading within 10 seconds, THE Slotify_UI SHALL transition from the loading View_State to the error View_State defined in criterion 4.

### Requirement 13: Availability/Scheduling UI Caution

**User Story:** As a maintainer, I want the mid-refactor availability UI treated cautiously, so that this refresh does not destabilize in-progress work.

#### Acceptance Criteria

1. WHEN a redesign of the Availability_UI is initiated, THE Slotify_UI SHALL first review the current availability models, the WeeklyAvailability and SpecialDatesAvailability components, the Availability page components, and the active availability spec, and record the existing component inputs, outputs, and request payloads that constitute its data flow.
2. WHEN the Slotify_Design_System is applied to the Availability_UI, THE Slotify_UI SHALL keep the component inputs (props), outputs (emitted events), and server request/response payloads identical to those recorded before the redesign.
3. WHEN the Slotify_Design_System is applied to the Availability_UI, THE Slotify_UI SHALL preserve the existing scheduling behavior such that weekly hours, recurring breaks, and per-date overrides produce the same saved values and the same computed available slots as before the redesign.
4. IF applying the Slotify_Design_System to the Availability_UI would change any recorded component input, output, request payload, or scheduling result, THEN THE Slotify_UI SHALL reject that change, retain the pre-redesign behavior, and surface an indication that the change was blocked to preserve the in-progress refactor.
5. WHEN the Availability_UI redesign is completed, THE Slotify_UI SHALL pass all pre-existing availability and scheduling tests with zero new runtime or console errors introduced by the redesign.

### Requirement 14: Motion and Micro-interactions

**User Story:** As a user, I want subtle, professional motion, so that interactions feel responsive without being distracting.

#### Acceptance Criteria

1. WHEN a pointer hovers over a refreshed interactive element, THE Slotify_UI SHALL apply a visible hover state change (such as background, border, or opacity change) within 150–250ms.
2. WHEN a user presses a refreshed interactive element, THE Slotify_UI SHALL apply a visible press-state change (such as scale reduction of 2–6% or opacity reduction) that reverts within 150–250ms of release.
3. WHEN a refreshed element enters or leaves the viewport or DOM, THE Slotify_UI SHALL animate the enter/leave transition with a duration between 150ms and 250ms inclusive.
4. THE Slotify_UI SHALL constrain all interaction and transition durations on refreshed elements to a range of 150ms to 250ms inclusive.
5. THE Slotify_UI SHALL exclude gradient fills, glassmorphism (background blur with translucency), neon glow effects, and continuously looping or auto-playing animations from refreshed interactive elements.
6. WHERE the operating system or browser reports a reduced-motion preference, THE Slotify_UI SHALL disable enter/leave and press/hover motion transitions and present state changes without animation, as specified in Requirement 11.

## Open Questions / Decisions to Confirm

These decisions are ambiguous and are flagged for review rather than assumed. Answers will shape the design phase.

1. **Canonical brand blue** — The audit found two competing brand blues: `#2563eb` (43 occurrences) and `#2563ff` (30 occurrences), plus Breeze `indigo-500` and inline `#2563ff` fallbacks. Which single value is canonical for the Slotify_Design_System — `#2563eb`, `#2563ff`, or a newly agreed value? (Requirement 2.2, 2.4)
2. **Navy anchor color** — Landing/booking currently use navy `#071533`. Is this the canonical navy Design_Token, or should it be adjusted? (Requirement 2.2)
3. **Bootstrap vs Tailwind per area** — The Skill says do not migrate between the two without a spec. For the admin/manager areas currently on raw Bootstrap + `admin-*` CSS, should the design system be delivered by (a) keeping Bootstrap and normalizing tokens, (b) standardizing internal screens on Tailwind, or (c) a defined hybrid boundary? (Requirement 2, 6.2)
4. **Mobile navigation pattern** — For Manager_Dashboard and Admin_Console, which Mobile_Navigation pattern is preferred: bottom bar, slide-in drawer, or collapsible top menu? Should manager and admin use the same pattern? (Requirement 10.2)
5. **Availability UI scope** — Should the Availability_UI be included in this refresh now, deferred until its own refactor lands, or limited to token/color alignment only? (Requirement 13)
6. **Locale persistence source of truth** — Confirm `localStorage` (key `locale`) is the intended persistence mechanism for guests, and whether authenticated users should additionally persist a preferred locale server-side (note: server-side persistence would touch backend and fall outside the frontend-only constraint). (Requirement 9.3, 9.4)
7. **Date formatting approach** — Should locale-aware date formatting use the native `Intl.DateTimeFormat`, or is a formatting helper/library preferred? Confirm the expected date/time format per locale. (Requirement 9.7)
8. **Supported breakpoints** — Confirm the target breakpoint set (mobile/tablet/desktop thresholds) the responsive criteria should be verified against. (Requirement 10)
9. **Contrast target** — Confirm the intended contrast standard (for example WCAG AA) so accessibility criteria can be verified consistently. (Requirement 11.1)
10. **Landing product visualization** — Should the polished product visualization use real screenshots of the booking experience or designed mockups? (Requirement 4.4)
