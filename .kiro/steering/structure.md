# Project Structure

## PHP / Backend

```
app/
  Console/Commands/
    SendAppointmentWhatsappRemindersCommand.php  # artisan: appointments:send-whatsapp-reminders
  Http/
    Controllers/
      Admin/           # Role: admin — BusinessController, ManagerController, ContactRequestController
      Auth/            # Breeze-generated auth controllers
      Dashboard/       # Role: manager — AppointmentController, AvailabilityController, ScheduleController, ServiceController
      BookingController.php             # Public: show booking page + slot availability
      BookingVerificationController.php # Public: OTP send + confirm → creates Appointment
      ContactRequestController.php      # Public: landing page contact form submission
      LandingPageController.php         # Public: landing page
      ProfileController.php             # Auth: user profile
    Middleware/
      EnsureUserIsAdmin.php    # Checks role === 'admin'; alias: 'admin'
      EnsureUserIsManager.php  # Checks role === 'manager'; alias: 'manager'
      HandleInertiaRequests.php # Shares auth.user + flash messages to all Inertia pages
  Jobs/
    SendOtpSmsJob.php                      # Queued; dispatched during booking verification
    SendAppointmentWhatsappReminderJob.php  # Queued; dispatched by artisan command
  Models/
    Appointment.php               # Core booking record
    BookingVerification.php       # Transient OTP record (5 min TTL, max 5 attempts)
    Business.php                  # Tenant; has plan-gating methods
    BusinessAvailability.php      # Weekly hours per day_of_week (0=Sun–6=Sat)
    BusinessAvailabilityBreak.php # Break windows (recurring by day_of_week or one-off by date)
    BusinessBrandingSettings.php  # Colors, logo, cover image, public copy, theme_style
    BusinessDateOverride.php      # Per-date open/close override
    ContactRequest.php
    Plan.php                      # slug: 'basic' | 'premium'
    Service.php                   # confirmation_mode: 'auto_confirm' | 'requires_approval'
    User.php                      # role: 'admin' | 'manager'; belongs to Business
  Notifications/
    NewAppointmentCreatedNotification.php  # Database notification; sent to all business managers on new booking
  Providers/
    AppServiceProvider.php
  Services/
    SmsService.php       # HTTP wrapper for custom SMS provider
    WhatsappService.php  # Twilio WhatsApp API wrapper

bootstrap/
  app.php  # Middleware registration + route file includes

routes/
  web.php        # Public routes: landing, booking flow, contact form
  admin.php      # /admin/* — requires 'auth' + 'admin' middleware
  dashboard.php  # /dashboard/* — requires 'auth' + 'manager' middleware
  auth.php       # Breeze auth routes
  console.php    # Scheduled commands

database/
  migrations/    # Chronological; all standard Laravel timestamp filenames
  factories/
  seeders/
```

## Frontend (`resources/js/`)

```
app.ts           # Entry point: Inertia app setup, ZiggyVue, vue-i18n, RTL direction logic
bootstrap.js     # Axios config

Pages/           # Inertia page components — mirrors controller namespacing
  Landing/       # Index.vue — public marketing/landing page
  Auth/          # Login, Register, Password reset (Breeze)
  Dashboard/     # Manager area
    Index.vue
    Appointments/
    Availability/  # ⚠ mid-refactor; treat as unstable
    Schedule/
    Services/
  Admin/         # Admin area
    Dashboard.vue
    Businesses/
    Managers/
    ContactRequests/
  Booking/
    Show.vue     # Public booking page (service picker → date → slot → OTP)
  Profile/

Layouts/
  AdminLayout.vue    # Wraps all /admin pages
  ManagerLayout.vue  # Wraps all /dashboard pages
  GuestLayout.vue    # Wraps auth/public pages

Components/
  # Generic reusable UI: PrimaryButton, TextInput, Modal, Dropdown, etc.
  Booking/           # Booking-flow specific components
  Landing/           # Landing page sections
  UI/                # ContactSuccessCard
  WeeklyAvailability.vue   # Availability grid
  SpecialDatesAvailability.vue

i18n/
  index.ts           # createI18n setup (legacy: false, default locale: 'en')
  locales/
    en.ts
    ar.ts  # RTL
    he.ts  # RTL

types/
  global.d.ts  # Shared TypeScript types: Business, Service, Appointment, Slot, Day,
               # AvailabilityBreak, DateOverride, Plan, Branding, Manager
```

## CSS (`resources/css/`)

```
app.css      # Main stylesheet (Tailwind + custom)
admin.css    # Admin-specific styles
Layout/      # Layout-scoped CSS
Pages/       # Page-scoped CSS
```

## Conventions

- **Controllers grouped by role area** — `Admin/`, `Dashboard/`, `Auth/` subdirectories. Public controllers sit at the top level.
- **Inertia pages mirror controller namespaces** — `Admin/BusinessController` → `Pages/Admin/Businesses/`. Always match this pattern when adding pages.
- **No repository or service-layer abstraction** — business logic lives directly in controllers. The only service classes are the two external-API wrappers in `app/Services/`.
- **Authorization is hand-rolled** — no Gates or Policies. Role checks are done via middleware aliases (`admin`, `manager`). Tenant scoping (ensuring a manager only touches their own business's data) is done with manual `business_id` ownership checks inside each controller method. Every new controller must replicate this pattern.
- **Flash messages** — success/error flashes are set via `->with('success', ...)` / `->with('error', ...)` on redirects and consumed from the shared `flash` Inertia prop.
- **Validation** — all input validation uses inline `$request->validate([...])` inside controller methods. No dedicated Form Request classes (except the Breeze-generated `LoginRequest`).
- **`updateOrCreate` for availability/branding** — the pattern for upsert operations on `BusinessAvailability`, `BusinessBrandingSettings`, and `BusinessDateOverride` is `updateOrCreate`; follow the same pattern for similar features.
- **TypeScript types** — all shared data shapes live in `resources/js/types/global.d.ts`. Add new types there rather than defining them inline in components.
