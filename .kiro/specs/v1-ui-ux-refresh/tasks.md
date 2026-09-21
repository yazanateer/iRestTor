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

- [ ] 7. Checkpoint - Foundation, primitives, and helpers
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 8. Migrate Auth and Profile screens off Breeze
  - [ ] 8.1 Migrate `GuestLayout.vue` onto the design system
    - Replace Breeze gray palette (`bg-gray-100`, `text-gray-500`) with token background/surface, brand logo, i18n-wired text, RTL-aware direction
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 9.1, 9.2_ (Design §Components › Layout designs › GuestLayout)

  - [ ] 8.2 Migrate `Pages/Auth/*` and `Pages/Profile/*` screens
    - Remove all Breeze default gray classes and every `indigo-500` reference; route all user-facing text through vue-i18n keys (add matching keys in `en.ts`/`ar.ts`/`he.ts`); RTL mirroring; no horizontal overflow at 320–767px; interactive targets ≥44×44px
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 9.1, 9.2, 10.4_ (Design §Components › Layout designs)

- [ ] 9. Migrate the Admin console
  - [ ] 9.1 Add mobile-navigation parity to `AdminLayout.vue`
    - Apply the `mobile-bottom-nav` + `mobile-logout-btn` pattern (matching `ManagerLayout`) so ≤767px exposes every top-level destination; keep the full persistent sidebar at ≥768px; reuse the shared `LanguageSwitcher`; identical nav destination set/order across all `/admin/*` screens
    - _Requirements: 6.3, 6.4, 6.5, 10.2_ (Design §Components › Layout designs › AdminLayout)

  - [ ] 9.2 Token-align admin screens and replace ad-hoc styling
    - `Pages/Admin/*`: replace `admin-*` and raw Bootstrap literals (`#2563ff`/`#2563eb`) with token-driven shared components + `var(--token)`; route text through i18n; use `ResponsiveTable` for tabular data; render `EmptyState` when a table has zero rows (no empty table body)
    - _Requirements: 6.1, 6.2, 6.6, 6.7, 9.1, 9.2, 12.2_ (Design §Components › Layout designs, §View-state design)

- [ ] 10. Migrate the Manager dashboard
  - [ ] 10.1 Align `ManagerLayout.vue` and dashboard screens to tokens
    - Token-align layout; reuse shared `LanguageSwitcher`; identical nav destination set/order across `/dashboard/*`; one primary action per view via `PageHeader`; route text through i18n
    - _Requirements: 5.1, 5.2, 5.3, 9.1, 9.2_ (Design §Components › Layout designs › ManagerLayout)

  - [ ] 10.2 Apply responsive tables and view states to dashboard views
    - Use `ResponsiveTable` for tabular data; wire `LoadingSkeleton`/`EmptyState`/`ErrorState` per data-driven view; mobile navigation at ≤767px; success via shared `flash.success` prop; loading→error transition at 10s
    - _Requirements: 5.4, 5.5, 5.7, 5.8, 10.2, 12.1, 12.2, 12.3, 12.4, 12.5_ (Design §View-state design)

- [ ] 11. Refresh the Landing page
  - [ ] 11.1 Rebuild landing sections token-driven and conversion-focused
    - Render `Pages/Landing/*` with tokens (zero hardcoded color/spacing literals); hero headline + subheading within the initial viewport at 320–1920px; exactly one primary-styled CTA per section wired to its destination route; token-rendered product visualization
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_ (Design §Components › Layout designs)

  - [ ] 11.2 Make landing i18n-complete, RTL-aware, and mobile-safe
    - Route all text through vue-i18n keys (matching keys in `en`/`ar`/`he`); RTL direction + mirrored layout/icons for `ar`/`he`; no horizontal overflow or clipped content at 320–767px
    - _Requirements: 4.6, 4.7, 4.8, 9.1, 9.2, 9.8, 10.6_ (Design §Components › i18n and RTL design)

- [ ] 12. Refresh the Booking flow
  - [ ] 12.1 Rebuild `Pages/Booking/Show.vue` stepper and apply tenant branding via resolver
    - 4-step stepper (service → date → slot → OTP) showing current + completed steps; apply `Tenant_Branding` only here via the branding resolver (task 6.2) with token fallbacks; replace inline hardcoded fallbacks (e.g. `#2563ff` service dots) with tokens/resolved branding; preserve existing OTP handling and appointment-creation behavior unchanged
    - _Requirements: 3.1, 3.5, 8.1, 8.2, 8.3, 8.4, 8.9_ (Design §Components › Layout designs › Booking)

  - [ ] 12.2 Make booking i18n-complete, RTL-aware, mobile-safe, and error-accessible
    - Route all text through vue-i18n keys; RTL for `ar`/`he`; each step no horizontal overflow and interactive targets ≥44×44px at 320–767px; OTP failure shows an accessible error associated with the OTP input and allows retry
    - _Requirements: 8.5, 8.6, 8.7, 8.8, 9.1, 9.2, 10.4_ (Design §Components, §Error Handling)

- [ ] 13. Checkpoint - Screen migrations
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 14. Availability UI token/visual alignment (caution)
  - [ ] 14.1 Record and preserve the Availability_UI contract
    - Before any change, record the existing props, emitted events, and request/response payloads of `WeeklyAvailability.vue`, `SpecialDatesAvailability.vue`, and `Pages/Dashboard/Availability/*` as a snapshot to compare against
    - _Requirements: 13.1, 13.2_ (Design §Testing Strategy › Regression gate)

  - [ ] 14.2 Apply token/visual alignment only
    - Replace color/spacing literals with `var(--token)` and align to shared components visually; do NOT change props, emits, request/response payloads, or scheduling results; if a token change would alter any recorded contract or scheduling result, revert it and keep pre-redesign behavior
    - _Requirements: 13.2, 13.3, 13.4_ (Design §Components)

- [ ] 15. Static / lint design-system hygiene checks
  - [ ] 15.1 Banned-literal and off-token scan
    - Grep internal-screen sources (admin, manager, auth, profile, landing) for `#2563eb`, `#2563ff`, and `indigo-500` and assert zero matches; flag hardcoded color/spacing/radius/shadow literals outside the token set
    - _Requirements: 2.3, 2.4, 2.5, 2.6, 4.4, 7.2, 8.3_ (Design §Testing Strategy › Static/lint checks)

  - [ ] 15.2 i18n and branding-boundary checks
    - Assert no hardcoded user-facing string literals on refreshed components; verify i18n key parity across `en`/`ar`/`he`; assert only `Booking/Show.vue` consumes the `Branding` type (internal screens do not)
    - _Requirements: 3.1, 3.2, 4.7, 7.3, 8.6, 9.1, 9.2_ (Design §Testing Strategy › Static/lint checks)

- [ ] 16. Implement the remaining property-based tests
  - [ ]* 16.1 Property test P3 — locale persistence round-trip
    - **Property 3: Locale persistence round-trip** — **Validates: Requirements 9.4, 9.5**
    - fast-check under Vitest, single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 3: {property_text}`

  - [ ]* 16.2 Property test P4 — invalid/missing locale defaults to English
    - **Property 4: Invalid or missing persisted locale defaults to English** — **Validates: Requirements 9.6**
    - fast-check single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 4: {property_text}`

  - [ ]* 16.3 Property test P5 — direction is RTL exactly for RTL locales
    - **Property 5: Direction is RTL exactly for RTL locales** — **Validates: Requirements 9.8, 9.9**
    - fast-check single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 5: {property_text}`

  - [ ]* 16.4 Property test P6 — translation key parity across locales
    - **Property 6: Translation key parity across locales** — **Validates: Requirements 9.1**
    - fast-check single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 6: {property_text}`

  - [ ]* 16.5 Property test P7 — exactly one active locale indicator
    - **Property 7: Exactly one active locale indicator** — **Validates: Requirements 9.7**
    - fast-check single test, ≥100 iterations, tagged `// Feature: v1-ui-ux-refresh, Property 7: {property_text}`

- [ ] 17. Cross-screen component/regression tests
  - [ ]* 17.1 Nav parity, view-state timing, and i18n fallback tests
    - Following `TrialBanner.spec.ts`: nav destination set identical across an area's screens and mobile-nav set equals desktop set; fake-timer test for loading→error at 10s; assert vue-i18n `fallbackLocale === 'en'` and a representative missing-key example renders the `en` value
    - _Requirements: 5.3, 6.3, 7.5, 9.3, 10.2, 12.5_

- [ ] 18. Final verification and frontend-only guarantee
  - [ ] 18.1 Build and backend regression gate
    - Run `npm run build` (must succeed) and the existing PHPUnit suite (`composer test` / `php artisan test`) proving zero new PHP failures (controllers, business logic, auth, tenant scoping, booking/OTP/appointment, availability behavior untouched)
    - _Requirements: 1.2, 1.9, 8.9, 13.5_ (Design §Testing Strategy › Regression gate)

  - [ ] 18.2 Availability contract snapshot comparison
    - Compare `WeeklyAvailability`/`SpecialDatesAvailability` props, emits, and request/response payloads before vs after; assert identical
    - _Requirements: 13.2, 13.4, 13.5_

  - [ ] 18.3 Manual responsive / RTL / contrast verification checklist
    - Responsive review at 320/375/768/1024/1440/1920px (no horizontal overflow, hero-in-viewport, 44px targets); RTL review in `ar`/`he` (mirrored layout/nav/icons, no LTR artifacts, desktop + mobile); accessibility audit (AA contrast across locales/directions, visible focus rings ≥3:1, keyboard-only operability, reduced-motion)
    - _Requirements: 4.1, 4.6, 4.8, 7.4, 7.6, 8.5, 8.7, 9.8, 10.1, 10.5, 10.6, 11.1, 11.2, 11.4, 11.7_ (Design §Testing Strategy › Manual/visual verification)

- [ ] 19. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

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
