# Implementation Plan: v1 UI/UX Refresh

## Overview

This plan implements the v1 UI/UX Refresh as a **frontend/UI-only** change. Every task touches only `resources/js/**`, `resources/css/**`, the vue-i18n message files (`en.ts`, `ar.ts`, `he.ts`), and shared TypeScript UI types in `resources/js/types/global.d.ts`. No task modifies files under `app/`, `routes/`, or `database/`, alters controller-returned data, or changes authorization/tenant scoping (Requirement 1; Design §Architecture, §Data Models).

The plan follows the design's dependency order: foundation (design tokens, i18n init, shared UI types) → shared primitives and components → area-by-area screen migration → helpers → static/property/component tests → final verification. Property-based tests use fast-check under Vitest, each a single test running ≥100 iterations, tagged per the design's Testing Strategy.

## Tasks

- [x] 1. Establish the canonical design-token foundation
  - [x] 1.1 Create the design-token layer `resources/css/tokens.css`
    - Formalize the existing `admin.css :root` block into a single canonical `:root` token set: color (`--brand-blue` de-facto `#2563ff`, `--brand-navy` `#071533`, surface/border/text/muted, status success/warning/danger each paired with text+icon color), typography scale (Figtree family, `--text-xs`…`--text-3xl` + line heights), spacing rhythm (4/8px steps `--space-1`…`--space-8`), radii (`--radius-sm/md/lg/full`), borders (`--border-width`, `--border-color`), exactly two elevation levels (`--elevation-1`, `--elevation-2`), motion (`--motion-fast` 150ms, `--motion-base` 200ms, `--motion-slow` 250ms, `--ease-standard`)
    - Keep the existing `--admin-navy`/`--admin-blue`/`--admin-bg`/`--admin-card`/`--admin-border`/`--admin-text`/`--admin-muted` names as aliases pointing at the new canonical tokens to avoid a churny rename
    - Add a global `@media (prefers-reduced-motion: reduce)` rule that disables enter/leave and press/hover transitions
    - _Requirements: 2.1, 2.2, 2.6, 2.8, 11.7, 14.4, 14.5, 14.6_ (Design §Architecture › Design token layer)

  - [x] 1.2 Wire tokens into Tailwind and the app entry point
    - Import `tokens.css` first in `resources/js/app.ts` (before `admin.css` and page CSS) so all downstream CSS resolves `var(--token)`
    - Extend `tailwind.config.js` `theme.extend` to map utilities to `var(--token)` values: `colors` (`brand`, `navy`, surface/border/text, status), `spacing`, `borderRadius`, `boxShadow`, `transitionDuration`, `fontFamily` (Figtree already `sans`)
    - _Requirements: 2.1, 2.3, 2.5_ (Design §Architecture › How both styling systems consume one token set)

- [x] 2. Fix the i18n foundation and add shared UI types
  - [x] 2.1 Add locale read-on-init + validation in `resources/js/i18n/index.ts`
    - Read `localStorage['locale']` on init; if the value is one of `en`/`ar`/`he`, set it as the active locale; otherwise default to `en`
    - Keep `fallbackLocale: 'en'` and the existing `app.ts` `setDirection`/`watch(locale)` logic unchanged
    - _Requirements: 9.3, 9.5, 9.6, 9.8, 9.9_ (Design §Components › i18n and RTL design)

  - [x] 2.2 Add shared UI types to `resources/js/types/global.d.ts`
    - Add `AppointmentStatus`, `SupportedLocale`, `ViewState`, `LanguageOption`, `TableColumn` (do not define these inline in components)
    - _Requirements: 1.6_ (Design §Data Models)

- [x] 3. Extract and fix the shared Language_Switcher
  - [x] 3.1 Create `resources/js/Components/LanguageSwitcher.vue` from the duplicated Admin/Manager markup
    - Extract the switcher markup currently copy-pasted in `AdminLayout.vue` and `ManagerLayout.vue` into one reused component
    - Fix the active-state bug: the template compares `locale.value === lang.code`, but `locale` from `useI18n()` is auto-unwrapped in templates so `locale.value` is `undefined` — compare against `locale` directly (or a `computed` current-locale) so exactly one option renders selected
    - On selection: set `locale.value = lang` and `localStorage.setItem('locale', lang)`
    - Consume design tokens for styling
    - _Requirements: 1.5, 9.4, 9.7_ (Design §Components › i18n and RTL design › Language-switcher active-state fix)

  - [x]* 3.2 Write component test for LanguageSwitcher active state
    - Following the `resources/js/Components/TrialBanner.spec.ts` Vitest + Vue Test Utils pattern, assert exactly one option is selected for a given active locale
    - _Requirements: 9.7_

- [x] 4. Promote existing components to token-driven primitives
  - [x] 4.1 Token-align button primitives
    - `PrimaryButton.vue`: brand-blue background, token radius/motion, focus-ring token — becomes THE primary-action treatment; `SecondaryButton.vue` and `DangerButton.vue`: secondary/danger semantic tokens; remove `bg-gray-*`/`indigo-500` literals
    - _Requirements: 2.3, 2.7, 4.2, 5.2, 7.2, 11.2_ (Design §Components › Existing components promoted to token-driven primitives)

  - [x] 4.2 Token-align form primitives
    - `TextInput.vue`: replace `indigo-500` focus border/ring with brand-blue token; `InputLabel.vue`: token text color, keep programmatic `<label>` association; `InputError.vue`: token danger color + appropriate role for accessible errors; `Checkbox.vue`: token focus/border
    - _Requirements: 2.3, 7.2, 11.3, 11.6_ (Design §Components)

  - [x] 4.3 Token-align overlay/navigation primitives
    - `Modal.vue`, `Dropdown.vue`, `DropdownLink.vue`, `NavLink.vue`, `ResponsiveNavLink.vue`: token-align focus/hover/border, keep behavior unchanged
    - _Requirements: 2.3, 2.7, 11.2_ (Design §Components)

  - [x]* 4.4 Write primitive state snapshot tests
    - Following `TrialBanner.spec.ts`, snapshot `PrimaryButton` and `TextInput` states (default/hover/focus/active/disabled) to prove cross-screen consistency
    - _Requirements: 2.7_

- [x] 5. Build new shared components
  - [x] 5.1 Create `StatusBadge.vue`
    - Render appointment status (`confirmed`, `pending_approval`, `cancelled`) using text + icon + token color, never color alone; prop `status: AppointmentStatus`
    - _Requirements: 11.5_ (Design §Components › New shared components)

  - [x] 5.2 Create `ResponsiveTable.vue`
    - Desktop `<table>` that collapses to a stacked card layout ≤767px, keeping every column value visible; columns via `TableColumn[]` + rows via slots
    - _Requirements: 6.6, 10.3_ (Design §Components)

  - [x] 5.3 Create view-state components `LoadingSkeleton.vue`, `EmptyState.vue`, `ErrorState.vue`
    - `LoadingSkeleton`: placeholder matching final content dimensions (no layout shift); `EmptyState`: message + at least one actionable control to the primary next step; `ErrorState`: human-readable message + recovery action (retry/dismiss), never raw exception/stack/payload
    - _Requirements: 12.1, 12.2, 12.4_ (Design §Components, §View-state design)

  - [x] 5.4 Create `PageHeader.vue`
    - Consistent title/eyebrow + single primary-action slot for internal screens (supports one-primary-action-per-view)
    - _Requirements: 5.2_ (Design §Components)

  - [x]* 5.5 Write component tests for new shared components
    - Following `TrialBanner.spec.ts`: `StatusBadge` renders text and icon (not color alone) per status; `EmptyState` renders a next-step action; `ErrorState` renders a recovery action and no raw error text; `LoadingSkeleton` matches final dimensions; `ResponsiveTable` renders every column as a card row at mobile width
    - _Requirements: 10.3, 11.5, 12.1, 12.2, 12.4_

- [x] 6. Add locale-aware date and branding helpers
  - [x] 6.1 Create `resources/js/lib/formatDate.ts`
    - Locale-aware date formatting via native `Intl.DateTimeFormat(activeLocale, options)`; no new library
    - _Requirements: 9.10_ (Design §Components › Locale-aware dates)

  - [x] 6.2 Create the booking-page branding resolver helper
    - Pure function that takes a `Branding` input (possibly absent/null/malformed attributes) and returns a fully-populated set of usable values, substituting design-system default tokens per affected attribute; never throws; read-only (mutates no branding value)
    - _Requirements: 3.4, 3.5, 8.4_ (Design §Components › Layout designs › Booking, §Data Models)

  - [x]* 6.3 Write property tests for date and branding helpers
    - **Property 1: Branding resolver completeness and safety** — **Validates: Requirements 3.5, 8.4**
    - **Property 2: Booking branding meets contrast floor** — **Validates: Requirements 3.3, 8.2, 11.1**
    - **Property 8: Date formatting routes by active locale** — **Validates: Requirements 9.10**
    - fast-check under Vitest, single test per property, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property {N}: {property_text}`

- [x] 7. Checkpoint - Foundation, primitives, and helpers
  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Migrate Auth and Profile screens off Breeze
  - [x] 8.1 Migrate `GuestLayout.vue` onto the design system
    - Replace Breeze gray palette (`bg-gray-100`, `text-gray-500`) with token background/surface, brand logo, i18n-wired text, RTL-aware direction
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 9.1, 9.2_ (Design §Components › Layout designs › GuestLayout)

  - [x] 8.2 Migrate `Pages/Auth/*` and `Pages/Profile/*` screens
    - Remove all Breeze default gray classes and every `indigo-500` reference; route all user-facing text through vue-i18n keys (add matching keys in `en.ts`/`ar.ts`/`he.ts`); RTL mirroring; no horizontal overflow at 320–767px; interactive targets ≥44×44px
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 9.1, 9.2, 10.4_ (Design §Components › Layout designs)

- [x] 9. Migrate the Admin console
  - [x] 9.1 Add mobile-navigation parity to `AdminLayout.vue`
    - Apply the `mobile-bottom-nav` + `mobile-logout-btn` pattern (matching `ManagerLayout`) so ≤767px exposes every top-level destination; keep the full persistent sidebar at ≥768px; reuse the shared `LanguageSwitcher`; identical nav destination set/order across all `/admin/*` screens
    - _Requirements: 6.3, 6.4, 6.5, 10.2_ (Design §Components › Layout designs › AdminLayout)

  - [x] 9.2 Token-align admin screens and replace ad-hoc styling
    - `Pages/Admin/*`: replace `admin-*` and raw Bootstrap literals (`#2563ff`/`#2563eb`) with token-driven shared components + `var(--token)`; route text through i18n; use `ResponsiveTable` for tabular data; render `EmptyState` when a table has zero rows (no empty table body)
    - _Requirements: 6.1, 6.2, 6.6, 6.7, 9.1, 9.2, 12.2_ (Design §Components › Layout designs, §View-state design)

- [x] 10. Migrate the Manager dashboard
  - [x] 10.1 Align `ManagerLayout.vue` and dashboard screens to tokens
    - Token-align layout; reuse shared `LanguageSwitcher`; identical nav destination set/order across `/dashboard/*`; one primary action per view via `PageHeader`; route text through i18n
    - _Requirements: 5.1, 5.2, 5.3, 9.1, 9.2_ (Design §Components › Layout designs › ManagerLayout)

  - [x] 10.2 Apply responsive tables and view states to dashboard views
    - Use `ResponsiveTable` for tabular data; wire `LoadingSkeleton`/`EmptyState`/`ErrorState` per data-driven view; mobile navigation at ≤767px; success via shared `flash.success` prop; loading→error transition at 10s
    - _Requirements: 5.4, 5.5, 5.7, 5.8, 10.2, 12.1, 12.2, 12.3, 12.4, 12.5_ (Design §View-state design)

- [x] 11. Refresh the Landing page
  - [x] 11.1 Rebuild landing sections token-driven and conversion-focused
    - Render `Pages/Landing/*` with tokens (zero hardcoded color/spacing literals); hero headline + subheading within the initial viewport at 320–1920px; exactly one primary-styled CTA per section wired to its destination route; token-rendered product visualization
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_ (Design §Components › Layout designs)

  - [x] 11.2 Make landing i18n-complete, RTL-aware, and mobile-safe
    - Route all text through vue-i18n keys (matching keys in `en`/`ar`/`he`); RTL direction + mirrored layout/icons for `ar`/`he`; no horizontal overflow or clipped content at 320–767px
    - _Requirements: 4.6, 4.7, 4.8, 9.1, 9.2, 9.8, 10.6_ (Design §Components › i18n and RTL design)

- [x] 12. Refresh the Booking flow
  - [x] 12.1 Rebuild `Pages/Booking/Show.vue` stepper and apply tenant branding via resolver
    - 4-step stepper (service → date → slot → OTP) showing current + completed steps; apply `Tenant_Branding` only here via the branding resolver (task 6.2) with token fallbacks; replace inline hardcoded fallbacks (e.g. `#2563ff` service dots) with tokens/resolved branding; preserve existing OTP handling and appointment-creation behavior unchanged
    - _Requirements: 3.1, 3.5, 8.1, 8.2, 8.3, 8.4, 8.9_ (Design §Components › Layout designs › Booking)

  - [x] 12.2 Make booking i18n-complete, RTL-aware, mobile-safe, and error-accessible
    - Route all text through vue-i18n keys; RTL for `ar`/`he`; each step no horizontal overflow and interactive targets ≥44×44px at 320–767px; OTP failure shows an accessible error associated with the OTP input and allows retry
    - _Requirements: 8.5, 8.6, 8.7, 8.8, 9.1, 9.2, 10.4_ (Design §Components, §Error Handling)

- [x] 13. Checkpoint - Screen migrations
  - Ensure all tests pass, ask the user if questions arise.
  - Verified: `npx vite build` clean; `npm run test:unit` 39/39; `php artisan test` 76/81 (same 5 pre-existing `/profile` 404 failures, present on `main`, unrelated to this spec). Found 25 en-only i18n keys (Availability editor + some Landing/plan strings) missing from ar/he, confirmed pre-existing on `main` — deferred to task 15 per user decision.

- [x] 14. Availability UI token/visual alignment (caution)
  - [x] 14.1 Record and preserve the Availability_UI contract
    - Before any change, record the existing props, emitted events, and request/response payloads of `WeeklyAvailability.vue`, `SpecialDatesAvailability.vue`, and `Pages/Dashboard/Availability/*` as a snapshot to compare against
    - _Requirements: 13.1, 13.2_ (Design §Testing Strategy › Regression gate)
    - Verified `WeeklyAvailability.vue` and `SpecialDatesAvailability.vue` are orphaned (zero references anywhere in `resources/js`) — superseded by `Pages/Dashboard/Availability/Index.vue` + its `components/` subfolder, which is the live implementation. Contract snapshot recorded below for 14.2 to compare against; out-of-scope files left untouched.

  - [x] 14.2 Apply token/visual alignment only
    - Replace color/spacing literals with `var(--token)` and align to shared components visually; do NOT change props, emits, request/response payloads, or scheduling results; if a token change would alter any recorded contract or scheduling result, revert it and keep pre-redesign behavior
    - _Requirements: 13.2, 13.3, 13.4_ (Design §Components)
    - Remapped the page-local `--slot-*` alias layer (in `index.css`) to reference canonical `--brand-*`/`--color-*` tokens where an exact-value match existed (navy, blue, blue-2, blue-soft→tint, bg, card, border, text, muted, success, success-soft); left `--slot-amber`/`--slot-danger` as their own literals (no canonical equivalent) but wired their duplicated raw-hex usages in `CalendarExceptionsCard.vue` to consume the local `var(--slot-amber)`/`var(--slot-danger)` aliases instead of repeating hex. Swapped exact-match literals (`#ffffff`/`#fff`→`var(--color-surface)`, `#dc2626`→`var(--color-danger)`, `#f8fbff`/`#eef2f7`/`#eef2f9`→`var(--color-bg)`, `#cbd5e1`→`var(--brand-border)`) across `index.css` and all 5 live components. Left 3 genuine one-offs with no token match (`#dbe3ef`, `#fff7ed`, `#b6c1d4`) untouched. Verified zero script/template diffs (all changes confined to `<style scoped>` blocks) — props/emits/payload contract from 14.1 unchanged. Visually verified via Playwright (desktop + mobile, scratch manager account, cleaned up after) — no visual regressions.

- [x] 15. Static / lint design-system hygiene checks
  - [x] 15.1 Banned-literal and off-token scan
    - Grep internal-screen sources (admin, manager, auth, profile, landing) for `#2563eb`, `#2563ff`, and `indigo-500` and assert zero matches; flag hardcoded color/spacing/radius/shadow literals outside the token set
    - _Requirements: 2.3, 2.4, 2.5, 2.6, 4.4, 7.2, 8.3_ (Design §Testing Strategy › Static/lint checks)
    - Zero matches for `#2563eb`/`#2563ff`/`indigo-500` confirmed across admin/manager/auth/profile/landing (fixed 4 remaining occurrences: `Admin/Businesses/{Create,Edit}.vue` and `Dashboard/Services/{Create,Edit}.vue` form-default hex, extracted into a new shared `resources/js/lib/designTokens.ts` mirror since native `<input type="color">` defaults can't use `var()`; `schedule.css`/`appointments.css` CSS literals swapped directly to `var(--brand-blue)`). Also swept ~90 additional off-token literals (exact/near-exact matches to `--color-surface`, `--brand-navy`, `--color-border`, `--color-muted`, `--color-bg`, `--color-success`, `--color-danger`, `--brand-border`, `--color-warning`, `--color-text`) across admin.css, Layout, Landing, Dashboard, Auth, Profile, Components, Layouts. User approved swapping `appointments.css`/`schedule.css` hand-rolled status-pastel colors to `--color-warning/success/danger-bg/-text`. Left 8 literals as flagged one-offs with no canonical token match: `admin.css` dark-sidebar gradient/nav-link shades (`#0b1d43`, `#9db8ff`×2, `#cbd7f7`), `appointments.css` (`#dce6f5` border, `#eef2ff` hover, `#e2e8f0` neutral badge), `ContactSuccessCard.vue` (`#ecfdf3`) — flagged for a future dedicated design-token pass, not fixed here. `Dashboard/Availability` and `Components/{WeeklyAvailability,SpecialDatesAvailability}.vue` excluded (task 14's separate caution scope / confirmed orphaned). Verified via `npx vite build`, `npm run test:unit` (39/39), `php artisan test` (76/81, baseline), and a Playwright visual check (status badges + schedule icon, scratch data, cleaned up after).

  - [x] 15.2 i18n and branding-boundary checks
    - Assert no hardcoded user-facing string literals on refreshed components; verify i18n key parity across `en`/`ar`/`he`; assert only `Booking/Show.vue` consumes the `Branding` type (internal screens do not)
    - _Requirements: 3.1, 3.2, 4.7, 7.3, 8.6, 9.1, 9.2_ (Design §Testing Strategy › Static/lint checks)
    - Branding-type check: found and removed an unused `import type { Branding }` in `Admin/Businesses/Edit.vue` (dead import, never referenced) — now only `Booking/Show.vue` consumes it, confirmed via repo-wide grep. Hardcoded-string sweep (excluding Booking/Availability, already covered by tasks 12/14) found and fixed real gaps: `AppointmentDetailsModal.vue` had zero i18n (fully wired now, reusing existing `appointments.*`/`common.*`/`appointmentStatus.*` keys plus 6 new `appointments.*` keys; also switched its date formatting from a hardcoded `'en-GB'` format to the existing shared `lib/formatDate.ts` locale-aware helper, previously unused anywhere); `ContactSuccessCard.vue` had zero i18n (wired, new `landing.contact.success.*` keys); `AdminLayout.vue`/`ManagerLayout.vue` sidebar `<h1>IRestTor</h1>` was hardcoded (now `t('common.console')`, an existing unused key) and `AdminLayout.vue`'s tagline was hardcoded (new `admin.tagline` key, mirroring `manager.businessDashboard`). All 9 new keys added to en/ar/he together. i18n parity check: same 25 pre-existing en-only keys as Checkpoint 13 (Availability + landing/plan strings, confirmed already on `main`, deferred by user decision) — zero new gaps introduced. Verified via `npx vite build`, `npm run test:unit` (39/39), `php artisan test` (76/81, baseline), and a Playwright visual check of the fully-wired Appointment Details modal (scratch data, cleaned up after).
    - **Update from task 16.4**: a rigorous recursive (dot-path) parity check — as opposed to the bare-key-name regex used above and in Checkpoint 13 — showed the real gap was only 19 keys (18 live `availability.*` keys actively rendered by the Availability page, plus `common.console` which task 15.2 itself introduced as a new consumer without verifying ar/he had it), not 25/26; the rest were false positives from bare-name matching (duplicate/orphaned dead namespaces already present in all three locales). All 19 fixed, plus 2 reverse-direction extras (`landing.pricing.premium.features`, dead content present only in ar/he) removed for exact parity. See 16.4 for detail.

- [x] 16. Implement the remaining property-based tests
  - [x]* 16.1 Property test P3 — locale persistence round-trip
    - **Property 3: Locale persistence round-trip** — **Validates: Requirements 9.4, 9.5**
    - fast-check under Vitest, single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 3: {property_text}`
    - Exported `SUPPORTED_LOCALES`/`resolveInitialLocale` from `i18n/index.ts` (testability-only, no behavior change) and added `resources/js/i18n/index.spec.ts`.

  - [x]* 16.2 Property test P4 — invalid/missing locale defaults to English
    - **Property 4: Invalid or missing persisted locale defaults to English** — **Validates: Requirements 9.6**
    - fast-check single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 4: {property_text}`
    - Added alongside 16.1 in `resources/js/i18n/index.spec.ts`.

  - [x]* 16.3 Property test P5 — direction is RTL exactly for RTL locales
    - **Property 5: Direction is RTL exactly for RTL locales** — **Validates: Requirements 9.8, 9.9**
    - fast-check single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 5: {property_text}`
    - Extracted the inline `isRTL` check in `app.ts` into a new reusable `resources/js/lib/direction.ts` (`resolveDirection`), behavior-preserving; added `direction.spec.ts`.

  - [x]* 16.4 Property test P6 — translation key parity across locales
    - **Property 6: Translation key parity across locales** — **Validates: Requirements 9.1**
    - fast-check single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 6: {property_text}`
    - Added a shared `resources/js/lib/flattenKeys.ts` (dot-path recursive key flattener) and `resources/js/i18n/localeParity.spec.ts`. Writing this test surfaced that the property did not actually hold: a rigorous flatten found 19 real gaps (18 live `availability.*` keys + `common.console`, introduced by task 15.2) plus 2 reverse-direction-only keys in ar/he (`landing.pricing.premium.features`, dead/orphaned). User approved fixing; all 19 translated into ar/he and the 2 dead extras removed — verified 598/598/598 keys, zero gaps either direction, before writing the test.

  - [x]* 16.5 Property test P7 — exactly one active locale indicator
    - **Property 7: Exactly one active locale indicator** — **Validates: Requirements 9.7**
    - Added to the existing `resources/js/Components/LanguageSwitcher.spec.ts` (which already had `it.each` coverage for the 3 concrete locales; this adds the ≥100-iteration fast-check property alongside it).
    - Verified via `npx vite build` (clean), `npm run test:unit` (44/44, up from 39), `php artisan test` (76/81, unchanged baseline), `git status --porcelain -- app database routes` (empty).
    - fast-check single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 7: {property_text}`

- [x] 17. Cross-screen component/regression tests
  - [x]* 17.1 Nav parity, view-state timing, and i18n fallback tests
    - Following `TrialBanner.spec.ts`: nav destination set identical across an area's screens and mobile-nav set equals desktop set; fake-timer test for loading→error at 10s; assert vue-i18n `fallbackLocale === 'en'` and a representative missing-key example renders the `en` value
    - _Requirements: 5.3, 6.3, 7.5, 9.3, 10.2, 12.5_
    - Added `Layouts/navParity.spec.ts`: mounts `AdminLayout.vue`/`ManagerLayout.vue` (mocking `usePage`/`route`, real i18n) and asserts the desktop `.admin-nav` and mobile `.mobile-bottom-nav` render an identical href set in identical order (both already derive from the same `navItems` array by construction — this guards the invariant), and that the two nav elements are visually distinct (Requirements 5.3, 6.3, 10.2).
    - Added `i18n/fallback.spec.ts`: asserts the real `i18n.global.fallbackLocale.value === 'en'`, and mounts a component against a synthetic i18n instance with a key present only in `en` to prove it renders the `en` text (not the raw key) when active locale is `ar` (Requirements 7.5, 9.3).
    - **Skipped, documented rather than implemented**: the 10s loading→error fake-timer test. Audited the whole app — every screen is server-rendered via Inertia props at initial page load; there is no client-side `loading` state, fetch/XHR, or timeout logic anywhere, and the shared `ErrorState.vue` (built in task 5) has zero consumers. There is nothing live to regression-test, and building new async-loading infrastructure with no current consumer would be speculative feature work outside this task's scope. User confirmed: skip and document rather than build unused infrastructure. Flagging for whoever eventually adds a client-fetch-driven view.
    - Verified via `npx vite build` (clean), `npm run test:unit` (50/50, up from 44), `php artisan test` (76/81, unchanged baseline), `git status --porcelain -- app database routes` (empty).

- [x] 18. Final verification and frontend-only guarantee
  - [x] 18.1 Build and backend regression gate
    - Run `npm run build` (must succeed) and the existing PHPUnit suite (`composer test` / `php artisan test`) proving zero new PHP failures (controllers, business logic, auth, tenant scoping, booking/OTP/appointment, availability behavior untouched)
    - _Requirements: 1.2, 1.9, 8.9, 13.5_ (Design §Testing Strategy › Regression gate)
    - `npm run build` — clean. `composer test` / `php artisan test` — 76/81 passing, 3163 assertions, same 5 `Tests\Feature\ProfileTest` failures throughout this entire spec (missing `/profile` route, 404). Re-confirmed this run that the failures are pre-existing on `main` too (temporarily diffed `routes/` against `main` — identical, zero difference — then restored; nothing on this branch touches routing/controllers/models/migrations). `git status --porcelain -- app database routes` — empty for the whole spec's work.

  - [x] 18.2 Availability contract snapshot comparison
    - Compare `WeeklyAvailability`/`SpecialDatesAvailability` props, emits, and request/response payloads before vs after; assert identical
    - _Requirements: 13.2, 13.4, 13.5_
    - `WeeklyAvailability.vue`/`SpecialDatesAvailability.vue` (orphaned, confirmed in 14.1) — zero diff, untouched. Live implementation (`Pages/Dashboard/Availability/Index.vue` + its 5 `components/*.vue`, and `AvailabilityController.php`): re-extracted every prop/emit from the current files and diffed against the 14.1 snapshot — identical, word-for-word. `git diff` on `Index.vue` and `AvailabilityController.php` is empty (zero lines); the 5 sub-components' diffs are 100% confined to `<style scoped>` blocks (confirmed by hunk line numbers, all past each file's script/template boundary) — task 14.2's token-only work is the only change, and it never touched props, emits, or the request/response contract.

  - [x] 18.3 Manual responsive / RTL / contrast verification checklist
    - Responsive review at 320/375/768/1024/1440/1920px (no horizontal overflow, hero-in-viewport, 44px targets); RTL review in `ar`/`he` (mirrored layout/nav/icons, no LTR artifacts, desktop + mobile); accessibility audit (AA contrast across locales/directions, visible focus rings ≥3:1, keyboard-only operability, reduced-motion)
    - _Requirements: 4.1, 4.6, 4.8, 7.4, 7.6, 8.5, 8.7, 9.8, 10.1, 10.5, 10.6, 11.1, 11.2, 11.4, 11.7_ (Design §Testing Strategy › Manual/visual verification)
    - Playwright-driven pass across Landing, Auth/Login, Booking, Manager Dashboard, Manager Availability, Admin Console (scratch data, cleaned up after) at all 6 breakpoints, in `en` and `ar` (RTL), with programmatic overflow/touch-target/reduced-motion checks plus visual screenshot review. Found and fixed 3 real bugs:
      - **Horizontal overflow on Manager/Admin dashboards at 320–375px** (Requirement 10.5): `.admin-main` was a flex item with no `min-width: 0`, so it refused to shrink below its content's intrinsic width (the raw public booking-link URL), forcing the whole page to ~380px wide with a horizontal scrollbar even at 320px viewports. Fixed in `admin.css` (`min-width: 0` on `.admin-main`) plus `overflow-wrap: break-word` on `.dashboard-list-item` for defense in depth. Re-verified zero overflow at every breakpoint × every screen × both directions afterward.
      - **Auth text inputs under the 44px touch-target minimum** (Requirement 7.6): shared `TextInput.vue` (used by every Auth screen) had no explicit height, rendering at 42px. Added `min-h-[44px]` + padding; updated its outdated snapshot test.
      - **Two native inputs with no visible focus indicator** (Requirement 11.2): `.contact-form` (Landing) and `.schedule-date-picker` (Manager Schedule) both did `outline: none` with zero replacement — keyboard users tabbing to them would see nothing. Added focus-visible border/box-shadow treatments matching the pattern already used elsewhere; verified via screenshot.
    - Reduced-motion: confirmed `tokens.css`'s existing `@media (prefers-reduced-motion: reduce)` block collapses transition/animation duration to ~0 under `page.emulateMedia({reducedMotion: 'reduce'})`.
    - Keyboard operability: tabbed through Login — logical focus order, visible ring at every stop (screenshot-verified).
    - Contrast: spot-checked key token pairs with the existing `contrastRatio()` helper from `resolveBranding.ts`. All pass AA except `--color-muted` (#6b7890) on white at 4.45:1 — 0.05 under the 4.5:1 normal-text threshold. This is the canonical muted-text token used across effectively every screen in the app; flagged rather than unilaterally changed. User decision: leave as-is (0.05 under threshold, not worth a pervasive token change).
    - Verified via `npx vite build` (clean), `npm run test:unit` (50/50, snapshot updated), `php artisan test` (76/81, baseline unchanged), `git status --porcelain -- app database routes` (empty).

- [x] 19. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
  - `npm run build` clean. `npm run test:unit` 50/50. `composer test` 76/81 — same 5 pre-existing `Tests\Feature\ProfileTest` failures (missing `/profile` route, 404) present on `main`, unrelated to this spec, consistent since Checkpoint 13 and re-confirmed at every gate since. All 19 top-level tasks and every subtask in this file are now checked; `grep "\[ \]"` against the whole file returns nothing else. `git status --porcelain -- app database routes` is empty for the spec's entire history — zero backend files touched. Spec complete.

## Notes

- Tasks marked with `*` are optional test-writing/nice-to-have work and can be skipped for a faster MVP. Core migration tasks, the 8 property-based tests, and final verification (task 18) are required — the PBTs are grouped under optional sub-tasks per the workflow's test-marking convention, but the design's Testing Strategy treats them as the correctness contract.
- Every task references specific requirement clauses and design sections for traceability.
- Foundation (tokens, i18n init, types) must land before any screen migration because all downstream styling resolves `var(--token)` and all text routes through i18n.
- The Availability UI is treated with caution (Requirement 13): visual/token alignment only, with a before/after contract snapshot guarding props/emits/payloads/scheduling.
- No task modifies backend files, controllers, routes, or the database (Requirement 1); the booking page is the only surface that reads `Branding`, and it does so read-only.

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "2.2"] },
    { "id": 1, "tasks": ["1.2", "2.1"] },
    { "id": 2, "tasks": ["3.1", "4.1", "4.2", "4.3", "6.1", "6.2"] },
    { "id": 3, "tasks": ["3.2", "4.4", "5.1", "5.2", "5.3", "5.4", "6.3"] },
    { "id": 4, "tasks": ["5.5", "8.1", "9.1", "10.1", "11.1", "14.1"] },
    { "id": 5, "tasks": ["8.2", "9.2", "10.2", "11.2", "12.1", "14.2"] },
    { "id": 6, "tasks": ["12.2", "15.1", "15.2", "16.1", "16.2", "16.3", "16.4", "16.5", "17.1"] },
    { "id": 7, "tasks": ["18.1", "18.2", "18.3"] }
  ]
}
```
