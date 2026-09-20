# Product: Slotify (iRestTOR)

Multi-tenant appointment booking SaaS for service businesses. Each tenant is a **Business** that gets a public booking page at `/book/{slug}` where their customers can self-schedule appointments.

## Core User Roles

- **Admin** — platform operator. Manages businesses, managers, plans, and contact requests via `/admin/*`.
- **Manager** — business owner/staff. Manages their own business's services, availability, and appointments via `/dashboard/*`.
- **Customer** — anonymous public user. Books appointments through the public booking page. Verified via SMS OTP before an appointment is created.

## Key Features

- **Public booking page** — shows business branding, active services, and available time slots for a selected date. Customers pick a service, date, and slot, then verify their phone number via a 6-digit OTP before the booking is confirmed.
- **Availability management** — managers configure weekly working hours per day-of-week, recurring breaks, and per-date overrides (special hours or closures).
- **Appointment workflow** — services can be set to `auto_confirm` (slot is immediately confirmed) or `requires_approval` (slot enters `pending_approval`, manager must confirm or reject). Confirmation/rejection triggers an SMS to the customer.
- **WhatsApp reminders** — premium businesses can send automated WhatsApp reminders for confirmed appointments the day before (dispatched via `appointments:send-whatsapp-reminders` artisan command).
- **Business branding** — each business has configurable colors (primary/secondary/accent), theme style, logo, cover image, and public copy (title, subtitle, description).
- **Plan gating** — `basic` vs `premium` plans. Premium unlocks approval workflow, WhatsApp reminders, and WhatsApp messaging. **Note: gates are defined on `Business` (`canUseApprovalWorkflow()`, `canUseWhatsapp()`, `canUseReminders()`) but are not consistently enforced in all controllers — verify per feature.**
- **In-app notifications** — managers receive a database notification (`NewAppointmentCreatedNotification`) whenever a new appointment is booked.
- **Contact requests** — public landing page has a contact form; admins can view and update the status of submissions.

## Appointment Statuses

| Status | Meaning |
|---|---|
| `confirmed` | Booked and confirmed (auto or manually) |
| `pending_approval` | Awaiting manager review (requires_approval services) |
| `cancelled` | Rejected by manager or otherwise cancelled |

## Current State

Active development, **not production-ready**. The availability/scheduling UI is mid-refactor. Phone numbers are Israeli format (`05X` / `+9725X`) only.
