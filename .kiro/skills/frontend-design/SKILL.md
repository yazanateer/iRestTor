---
name: frontend-design
description: Guides frontend design and UI/UX work on the iRestTOR (Slotify) app. Use when designing, building, or redesigning Vue 3 / Inertia pages, layouts, or components — landing pages and dashboard/application screens alike. Enforces premium SaaS visual quality, mobile-first responsiveness, EN/HE/AR + RTL support, and the existing Laravel + Inertia + Vue architecture.
---

# Frontend Design — iRestTOR (Slotify)

Act as a senior SaaS frontend engineer and UI/UX designer. Every screen you touch should look like it belongs to a polished, modern booking product — not a generic AI-generated template.

Read `.kiro/steering/{product,structure,tech}.md` and `CLAUDE.md` before significant work. This Skill layers design guidance on top of those rules; it never overrides them.

## Non-negotiable constraints

- Preserve the Laravel + Inertia + Vue 3 + TypeScript architecture. Do not introduce new UI frameworks or state libraries.

- Stack is fixed: Vue 3 `<script setup lang="ts">`, Tailwind v3, Bootstrap 5.3 + Bootstrap Icons, vue-i18n v11.

- Pages live in `resources/js/Pages/` and mirror controller namespaces. Layouts in `Layouts/` (`AdminLayout`, `ManagerLayout`, `GuestLayout`). Shared UI in `Components/`.

- Reuse existing components (`PrimaryButton`, `TextInput`, `Modal`, `Dropdown`, etc.) before creating new ones. Add shared types to `resources/js/types/global.d.ts`.

- All user-facing text goes through vue-i18n. Add keys to `en.ts`, `ar.ts`, and `he.ts` together — never leave a locale behind, never hardcode strings.

- This Skill only guides frontend/UI work. Do not change controllers, business logic, auth, or tenant scoping.

## Visual quality bar

Aim for premium, modern SaaS quality with strong hierarchy and intentional whitespace.

Do:

- Establish a clear type scale, consistent spacing rhythm (stick to a spacing scale, e.g. 4/8px steps), and a defined color system.

- Use restrained, consistent borders, radii, and shadows — one or two elevation levels, applied with purpose.

- Guide the eye: one primary action per view, clear headings, grouped related content.

- Keep components consistent so the same element looks and behaves the same everywhere.

Avoid (these read as generic/AI-generated):

- Excessive gradients, glassmorphism, and neon glows.

- Oversized cards, random inconsistent shadows, and cluttered layouts.

- Decorative noise that competes with content or actions.

## Branding boundaries

Keep Slotify product branding separate from tenant/business branding.

- Slotify landing pages, authentication, admin, manager dashboard, and system-level screens should use the Slotify design system.
- Public booking pages may use the business branding settings such as logo, cover image, and brand colors.
- Tenant branding must not make internal Slotify screens visually inconsistent or reduce readability/accessibility.

## Styling consistency

The project uses both Tailwind and Bootstrap.

- Inspect the existing styling approach before redesigning a component.
- Avoid arbitrarily mixing Bootstrap and Tailwind utilities inside the same component.
- Prefer reusable project-level components and consistent design tokens over repeated one-off styles.
- Do not migrate from Bootstrap to Tailwind, or vice versa, unless explicitly required by a spec.

## Mobile is a first-class target

Design mobile intentionally — never just shrink the desktop layout.

For every redesigned screen, design both desktop and mobile (and tablet where it matters):

- Adapt navigation, spacing, typography, grids, cards, forms, and actions per breakpoint.

- Touch-friendly targets (min ~44px), no horizontal overflow, no cramped tap zones.

- Use mobile-appropriate navigation (bottom bar / drawer / collapsible menu) instead of squeezing desktop nav.

- Simplify dense dashboard layouts on small screens: stack, prioritize, and progressively disclose.

- Keep the same brand identity and component language across breakpoints.

- Verify RTL behavior on mobile, not just desktop.

## RTL and i18n

- Locales: `en` (LTR), `ar` (RTL), `he` (RTL). `document.dir` is set automatically from the active locale in `app.ts`.

- Design layouts to mirror correctly in RTL: use logical direction (start/end) thinking, mirror icons/arrows/progress where directional, and check alignment, padding, and flex order.

- Both Bootstrap LTR and RTL CSS bundles are loaded — rely on direction-aware styling rather than hardcoded left/right where it affects layout mirroring.

- Test each redesign in EN, HE, and AR before considering it done.

## Accessibility

- Maintain sufficient text/background contrast.
- Preserve visible keyboard focus states.
- Use semantic HTML and proper labels for form controls.
- Interactive elements must be keyboard accessible.
- Do not communicate status using color alone.
- Keep validation and error messages clear and accessible.

## State design

Every data-driven view must handle all states with matching polish:

- Loading: skeletons or spinners that reflect final layout — avoid layout shift.

- Empty: helpful message + a clear next action, not a blank panel.

- Success: clear confirmation; use the shared `flash` prop pattern for flash messages.

- Error: readable, actionable messaging; never a raw error dump.

## Motion

- Subtle, professional micro-interactions: hover/press feedback, smooth enter/leave transitions, gentle easing.

- Keep durations short (~150–250ms). No bouncy, flashy, or attention-stealing animation.

- Respect reduced-motion preferences.

## Landing pages (`Pages/Landing/`)

Optimize for conversion and trust:

- Strong first impression and a clear, immediate value proposition.

- Polished product visualization (clean mockups/screenshots of the booking experience).

- Conversion-focused structure with prominent, unambiguous CTAs.

- Signals of trust and professionalism.

- Excellent mobile landing experience — hero, CTAs, and sections must shine on small screens.

## Dashboard / application screens (`Pages/Dashboard/`, `Pages/Admin/`, `Pages/Booking/`)

Optimize for usability over decoration:

- Clear information hierarchy and readable data.

- Efficient workflows and consistent navigation within each area's layout.

- Responsive tables → cards on mobile; responsive, well-grouped forms; touch-friendly actions.

- Mobile-friendly management flows for booking, availability, services, and appointments.

- Note: the Availability/Scheduling UI is mid-refactor and unstable — inspect current models, components, and the active spec before redesigning it.

## Working checklist

1. Read steering docs + `CLAUDE.md`; inspect the existing page/components before changing them.

2. Reuse existing components and follow the page/namespace conventions.

3. Design desktop and mobile (and tablet where relevant) intentionally.

4. Wire all text through i18n across EN/HE/AR; verify RTL.

5. Handle loading/empty/success/error states.

6. Keep visuals consistent, restrained, and premium.

7. Make the smallest change that meets the goal — no unrelated refactors, no business-logic changes.