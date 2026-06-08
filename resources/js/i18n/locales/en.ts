export default {
    common: {
        status: 'Status',
        actions: 'Actions',
        active: 'Active',
        inactive: 'Inactive',
        edit: 'Edit',
        delete: 'Delete',
        cancel: 'Cancel',
        logout: 'Logout',
        min: 'min',
        save: 'Save',
        saving: 'Saving...',
        create: 'Create',
        update: 'Update',
        phone: 'Phone',
        email: 'Email',
        address: 'Address',
        timezone: 'Timezone',
        open: 'Open',
        closed: 'Closed',
        name: 'Name',
        password: 'Password',
        description: 'Description',
        console: 'IRestTor',
        available: 'Available',
        continue: 'Continue',
        off: 'Off',
        back: 'Back',

    },
    days: {
        sunday: 'Sunday',
        monday: 'Monday',
        tuesday: 'Tuesday',
        wednesday: 'Wednesday',
        thursday: 'Thursday',
        friday: 'Friday',
        saturday: 'Saturday',
    },

    daysShort: {
        sunday: 'SUN',
        monday: 'MON',
        tuesday: 'TUE',
        wednesday: 'WED',
        thursday: 'THU',
        friday: 'FRI',
        saturday: 'SAT',
    },
    admin: {
        nav: {
            dashboard: 'Dashboard',
            businesses: 'Businesses',
            managers: 'Managers',
        },

        platformAdmin: 'Platform Admin',
        console: 'IRestTor Admin Console',
        systemOnline: 'System Online',

        dashboard: {
            welcome: 'Welcome, SaaS Admin',
            description: 'From here you will manage businesses, managers, subscriptions, and platform settings.',
            title: 'Admin Dashboard',
            stats: {
                businesses: 'Businesses',
                managers: 'Managers',
                activeAccounts: 'Active Accounts',
                platformStatus: 'Platform Status',
                online: 'Online',
            },

            overview: {
                title: 'Platform Overview',
                description: 'Manage businesses, managers, and the operational setup of the platform.',
                businessManagement: 'Business Management',
                businessManagementDescription: 'Create, edit, and manage all businesses using IRestTor.',
                managerAccounts: 'Manager Accounts',
                managerAccountsDescription: 'Assign managers to businesses and control access.',
            },

            quickActions: {
                title: 'Quick Actions',
                description: 'Start managing the platform from here.',
            },
        },

    businesses: {
        title: 'Businesses',
        management: 'Business Management',
        managementDescription: 'Create and manage businesses using the IRestTor platform.',
        createBusiness: 'Create Business',
        bookingLink: 'Booking Link',
        businessName: 'Business Name',
        slug: 'Slug',
        slugHint: 'Example: dr-saadi-clinic. If empty, it will be generated automatically.',
        business: 'Business',
        selectBusiness: 'Select business',
        updateBusiness: 'Update Business',
        editBusiness: 'Update Business',
        deleteConfirm: 'Are you sure you want to delete this business?',
        brandingSettings: 'Branding Settings',
        publicTitle: 'Public Title',
        publicSubtitle: 'Public Subtitle',
        publicDescription: 'Public Description',
        primaryColor: 'Primary Color',
        secondaryColor: 'Secondary Color',
        accentColor: 'Accent Color',
    },

    managers: {
    title: 'Managers',
    accounts: 'Manager Accounts',
    accountsDescription: 'Create and manage business managers across IRestTor.',
    createManager: 'Create Manager',
    managerName: 'Name',
    editManager: 'Edit Manager',
    newPassword: 'New Password',
    keepPasswordHint: 'Leave empty to keep current password.',
    updateManager: 'Update Manager',
    deleteConfirm: 'Are you sure you want to delete this manager?',
},

    },

    manager: {
        nav: {
            dashboard: 'Dashboard',
            appointments: 'Appointments',
            services: 'Services',
            availability: 'Availability',
        },

        businessDashboard: 'Business Dashboard',
        businessManager: 'Business Manager',
        console: 'IRestTor Business Console',
        businessActive: 'Business Active',
    },

    businesses: {
        title: 'Businesses',
        createTitle: 'Create Business',
        editTitle: 'Edit Business',
        management: 'Business Management',
        description: 'Create and manage businesses using the IRestTor platform.',
        createBusiness: 'Create Business',
        updateBusiness: 'Update Business',
        businessName: 'Business Name',
        slug: 'Slug',
        phone: 'Phone',
        email: 'Email',
        address: 'Address',
        timezone: 'Timezone',
        bookingLink: 'Booking Link',
        noBusinesses: 'No businesses created yet.',
        deleteConfirm: 'Are you sure you want to delete this business?',
        active: 'Active',
        inactive: 'Inactive',
        slugExample: 'Example: dr-saadi-clinic. If empty, it will be generated automatically.',
    },

    managers: {
        title: 'Managers',
        createTitle: 'Create Manager',
        editTitle: 'Edit Manager',
        accounts: 'Manager Accounts',
        description: 'Create and manage business managers across IRestTor.',
        createManager: 'Create Manager',
        updateManager: 'Update Manager',
        managerName: 'Manager Name',
        business: 'Business',
        password: 'Password',
        newPassword: 'New Password',
        keepPassword: 'Leave empty to keep current password.',
        selectBusiness: 'Select business',
        noManagers: 'No managers created yet.',
        deleteConfirm: 'Are you sure you want to delete this manager?',
    },

    services: {
        title: 'Services',
        createTitle: 'Create Service',
        editTitle: 'Edit Service',
        businessServices: 'Business Services',
        description: 'Manage services customers can book online.',
        createService: 'Create Service',
        updateService: 'Update Service',
        service: 'Service',
        serviceName: 'Service Name',
        duration: 'Duration',
        durationMinutes: 'Duration (Minutes)',
        price: 'Price',
        color: 'Color',
        active: 'Active',
        descriptionLabel: 'Description',
        noServices: 'No services created yet.',
        deleteConfirm: 'Are you sure you want to delete this service?',
        editService: 'Update Service',
    },

    appointments: {
        title: 'Appointments',
        heading: 'Appointments',
        description: 'View all bookings for your business.',
        customer: 'Customer',
        service: 'Service',
        date: 'Date',
        time: 'Time',
        noAppointments: 'No appointments yet.',
        status: 'Status',
        confirmed: 'Confirmed',
        actions: 'Actions',
        confirm: 'Confirm',
        reject: 'Reject',
        searchPlaceholder: 'Search by customer name or phone',

        filters: {
        all: 'All',
        pending: 'Pending',
        confirmed: 'Confirmed',
        cancelled: 'Cancelled',
        },
    },

    pagination: {
    previous: 'Previous',
    next: 'Next',
},


    appointmentStatus: {
    pending: 'Pending Approval',
    confirmed: 'Confirmed',
    cancelled: 'Cancelled',
    completed: 'Completed',
},

    availability: {
        title: 'Availability',
        workingHours: 'Working Hours',
        description: 'Set when customers can book appointments.',
        openDays: 'Open Days',
        totalWeek: 'Total / Week',
        editing: 'Editing',
        open: 'Open',
        closed: 'Closed',
        startTime: 'Start Time',
        endTime: 'End Time',
        quickSet: 'Quick Set',
        applyAll: 'Apply these hours to all open days',
        closedMessage: 'This day is marked as closed. Toggle open to set hours.',
        weeklySchedule: 'Weekly Schedule',
        clickDay: 'Click a day to edit',
        saveAvailability: 'Save Availability',
        saved: 'Saved successfully',
        breakTimes: 'Break Times',
        addBreak: 'Add Break',
        breakStart: 'Break Start',
        breakEnd: 'Break End',
        removeBreak: 'Remove Break',
        subtitle: 'Set when customers can book appointments.',
        noBreaks: 'No break times added',
        applyToAllOpenDays: 'Apply these hours to all open days',
        clickDayToEdit: 'Click a day to edit',
        halfDayAM: 'Half Day',
    },

    dashboard: {
        title: 'Business Dashboard',
        todayAppointments: "Today's Appointments",
        customers: 'Customers',
        services: 'Services',
        businessStatus: 'Business Status',
        publicBookingLink: 'Public Booking Link',
        bookingLinkDescription: 'Share this link with your customers so they can book appointments.',
        copyLink: 'Copy Link',
        quickActions: 'Quick Actions',
        quickActionsDescription: 'Start configuring your business.',
        createServices: 'Create Services',
        createServicesDescription: 'Add services customers can book online.',
        setAvailability: 'Set Availability',
        setAvailabilityDescription: 'Configure working hours and appointment slots.',
        whatsappAutomation: 'WhatsApp Automation',
        whatsappAutomationDescription: 'Automate confirmations and reminders.',
        pendingRequests: 'Pending Requests',
        thisWeek: 'This Week',
        totalServices: 'Total Services',
        todaySchedule: "Today's Schedule",
        todayScheduleDescription: "Appointments scheduled for today.",
        noTodayAppointments: "No appointments scheduled for today.",
    },

    booking: {
        onlineBooking: 'Online Booking',
        selectService: 'Select a Service',
        chooseDate: 'Choose Date',
        availableTimes: 'Available Times',
        yourDetails: 'Your Details',
        finalStep: 'Final Step',
        continue: 'Continue',
        confirmAppointment: 'Confirm Appointment',
        confirming: 'Confirming...',
        appointmentConfirmed: 'Appointment Confirmed',
        successMessage: 'Your appointment has been booked successfully.',
        service: 'Service',
        date: 'Date',
        time: 'Time',
        name: 'Name',
        phone: 'Phone',
        emailOptional: 'Email Optional',
        fullName: 'Full Name',
        phoneNumber: 'Phone Number',
        appointmentSummary: 'Appointment Summary',
        noSlots: 'No slots available',
        selectSlot: 'Select a time slot to continue.',
        available: 'Available',
        loadingSlots: 'Loading available slots...',
        noServices: 'No services available',
        noServicesDescription: 'This business has not published bookable services yet.',
        subtitle: 'Choose a service, select a date, and pick an available time.',
        selectServiceDescription: 'Pick the service you want to book.',
        stepOne: 'Step 1 of 3',
        chooseDateDescription: 'Select the day you want to visit.',
        stepTwo: 'Step 2 of 3',
        availableTimesDescription: 'Pick one of the available appointment slots.',
        stepThree: 'Step 3 of 3',
        selectServiceAndDate: 'Select service and date',
        timesWillAppear: 'Available times will appear here.',
        selectTimeToContinue: 'Select a time slot to continue.',
        pageTitle: 'Online Booking',
        selectServiceFirst: 'Please select a service first.',
        detailsDescription: 'Enter your information to confirm the appointment.',
        fullNamePlaceholder: 'Enter your name',
        verifyPhone: 'Verify Phone Number',
        verifyDescription: 'Enter the verification code sent to your phone to complete the booking.',
        verificationCode: 'Verification Code',
        verifying: 'Verifying...',
        verifyCode: 'Verify Code',
        requestSent: 'Booking Request Sent',
        requestSentMessage: 'Your booking request has been sent successfully. The business will review your request and you will receive a confirmation message once it is approved.',
        noDescription: 'No description available',
    },

    schedule: {
        title: "Schedule",
        dailyPlanner: "Daily Planner",
        subtitle: "View appointments and daily activity for your selected date.",
        appointments: "Appointments",
        pendingRequests: "Pending Requests",
        selectedDay: "Selected Day",
        appointmentCount: "appointments",
        noAppointmentsTitle: "No appointments for this day",
        noAppointmentsDescription: "Select another date or wait for new bookings.",
    },

    landing: {
  meta: {
    title: 'IRestTOR - Smart Appointment Booking System',
  },
  nav: {
    subtitle: 'AI Booking OS',
    login: 'Login',
    cta: 'Start Now',
  },
  hero: {
    eyebrow: 'IRestTOR Booking Platform',
    title: 'A smart appointment system for modern businesses',
    description:
      'IRestTOR helps businesses manage services, availability, bookings, approvals, and customer notifications from one clean dashboard.',
    primaryCta: 'Request a Demo',
    secondaryCta: 'Explore Features',
  },
  preview: {
    cover: 'Booking Page Preview',
    eyebrow: 'Online Booking',
    description: 'A branded booking page where customers can choose a service, date, and available time slot.',
    service: 'Medical Consultation',
    continue: 'Continue',
  },
  features: {
    eyebrow: 'Platform Features',
    title: 'Everything needed to manage appointments online',
    description: 'From service setup to SMS confirmations, IRestTOR gives every business a professional booking experience.',
    items: {
      bookingLink: {
        title: 'Personal Booking Link',
        description: 'Every business gets a dedicated booking link to share with customers.',
      },
      availability: {
        title: 'Smart Availability',
        description: 'Configure working hours, open days, and available appointment slots.',
      },
      services: {
        title: 'Service Management',
        description: 'Create services with duration, price, color, and confirmation mode.',
      },
      notifications: {
        title: 'SMS Notifications',
        description: 'Notify customers when appointments are confirmed or rejected.',
      },
      approval: {
        title: 'Approval Workflow',
        description: 'Choose between automatic confirmation or manager approval.',
      },
      dashboard: {
        title: 'Business Dashboard',
        description: 'Track daily appointments, pending requests, services, and schedules.',
      },
    },
  },
  howItWorks: {
    eyebrow: 'How It Works',
    title: 'Launch a booking page in simple steps',
    description: 'IRestTOR keeps the setup simple for business owners and smooth for customers.',
    steps: {
      business: {
        title: 'Create the business',
        description: 'Add business details, branding, colors, logo, and public booking content.',
      },
      availability: {
        title: 'Set services and availability',
        description: 'Define what customers can book and when appointments are available.',
      },
      share: {
        title: 'Share the booking link',
        description: 'Customers open the link, choose a service, verify their phone, and book.',
      },
    },
  },
  businesses: {
    eyebrow: 'Suitable For',
    title: 'Built for service-based businesses',
    items: {
      clinics: 'Clinics',
      salons: 'Beauty Salons',
      barbers: 'Barbers',
      lawyers: 'Lawyers',
      consultants: 'Consultants',
      coaches: 'Coaches',
    },
  },
  cta: {
    eyebrow: 'Start With IRestTOR',
    title: 'Ready to manage appointments smarter?',
    description: 'Give your business a professional booking page and manage everything from one dashboard.',
    whatsapp: 'Contact Us on WhatsApp',
  },
},


  landing: {
    meta: {
      title: 'IRestTOR - Smart Appointment Booking',
    },

    brand: {
      name: 'IRestTOR',
    },

    languages: {
      ar: 'AR',
      en: 'EN',
      he: 'HE',
    },

    nav: {
      features: 'Features',
      howItWorks: 'How It Works',
      pricing: 'Pricing',
      login: 'Login',
      bookDemo: 'Book a Demo',
    },

    hero: {
      badge: 'Online Booking • Save Time • Automatic Reminders',
      titleLineOne: 'Your Appointment Booking System,',
      titleLineTwo: 'Ready in Minutes',
      description:
        'No phone calls. No messy messages. No scheduling headaches. Let customers book online while you manage everything from one dashboard.',
      primaryCta: 'Start Free Trial · 14 Days',
      secondaryCta: 'Watch Demo',
    },

    mockup: {
      live: 'live',
      url: 'iresttor.com/book/clinic-x',
      serviceName: 'Consultation',
      duration: '30 min',
      price: '$35',
      available: 'Available',
      continueBooking: 'Continue Booking',
      businessName: 'ClinicX',
      openToday: 'Open Today · 09:00–17:00',
      verificationCompleted: 'Verification Completed',
      otpCode: 'OTP · #1247 ✓',

      days: {
        mon: 'Mon',
        tue: 'Tue',
        wed: 'Wed',
      },

      stats: {
        todayAppointments: "Today's Appointments",
        pendingRequests: 'Pending Requests',
        activeServices: 'Active Services',
      },
    },

    features: {
      eyebrow: 'Everything Your Business Needs',
      titleLineOne: 'Powerful Tools.',
      titleLineTwo: 'Ready From Day One.',
      description:
        'Everything required to manage appointments, customers, schedules, and bookings in one place.',

      items: {
        personalBookingLink: {
          title: 'Personal Booking Link',
          description:
            'Every business gets a dedicated booking page that can be shared through WhatsApp, Instagram, websites, or social media.',
        },

        availabilityCalendar: {
          title: 'Smart Availability Calendar',
          description:
            'Set working days, business hours, breaks, and appointment availability with ease.',
        },

        otpVerification: {
          title: 'SMS Verification',
          description:
            'Customers verify their phone number before booking to reduce fake appointments and no-shows.',
        },

        approvalWorkflow: {
          title: 'Approve or Auto-Confirm',
          description:
            'Choose whether appointments are approved automatically or require manual confirmation.',
        },

        multiLanguage: {
          title: 'Multi-Language Support',
          description:
            'English, Arabic, and Hebrew support for businesses serving diverse customers.',
        },

        businessDashboard: {
          title: 'Business Dashboard',
          description:
            'Manage appointments, services, schedules, requests, and daily operations from one place.',
        },
      },
    },

    howItWorks: {
      eyebrow: 'How It Works',
      title: 'Three Simple Steps',

      steps: {
        create: {
          title: 'Create Your Business',
          description:
            'Add your business name, logo, colors, working hours, and booking preferences.',
        },

        services: {
          title: 'Add Services',
          description:
            'Configure services, duration, pricing, availability, and appointment approval settings.',
        },

        share: {
          title: 'Share Your Booking Link',
          description:
            'Send your personal booking link to customers and let them schedule appointments online.',
        },
      },
    },

    businesses: {
      eyebrow: 'Perfect For',
      title: 'Clinics, Salons, Consultants, and Service Businesses.',
      description:
        'IRestTOR is designed for businesses that receive customers by appointment.',

      items: {
        clinics: 'Clinics',
        beautySalons: 'Beauty Salons',
        barbershops: 'Barbershops',
        lawFirms: 'Law Firms',
        consultants: 'Consultants',
        coaches: 'Coaches',
        more: 'more...',
      },
    },

    pricing: {
      eyebrow: 'Pricing',
      titleLineOne: 'One Simple Plan.',
      titleLineTwo: 'Everything Included.',
      description: 'Professional appointment booking without hidden fees.',
      badge: 'Everything Included',
      cardDescription: 'Everything you need to manage appointments professionally.',
      price: '$29',
      period: '/ month',
      cta: 'Start Free Trial · 14 Days',

      features: {
        personalBookingPage: 'Personal booking page',
        unlimitedServices: 'Unlimited services',
        availabilityManagement: 'Availability management',
        smsOtpVerification: 'SMS OTP verification',
        appointmentApprovals: 'Appointment approvals',
        businessDashboard: 'Business dashboard',
        multiLanguage: 'English, Arabic & Hebrew support',
      },
    },

    contact: {
      eyebrow: 'Contact Us',
      title: 'Ready To Stop Managing Appointments Through Messages?',
      description: 'Leave your details and our team will contact you shortly.',
      cta: 'Request Demo',

      fields: {
        fullName: 'Full Name',
        businessName: 'Business Name',
        phone: 'Phone Number',
        businessType: 'Business Type',
        message: 'Tell Us About Your Business',
      },

      placeholders: {
        fullName: 'Amir Amir',
        businessName: 'My Clinic',
        phone: '054-720-0199',
        message: 'How many appointments do you handle each day?',
      },

      businessTypes: {
        select: 'Select...',
        clinic: 'Clinic',
        barbershop: 'Barbershop',
        beautySalon: 'Beauty Salon',
        consulting: 'Consulting',
        other: 'Other',
      },
    },

    mockupShowcase: {
      eyebrow: 'Live Booking Experience',
      title: 'Your customers book from their phone in seconds.',
      description:
        'IRestTOR gives every business a clean mobile booking page where customers can choose a service, select a date, pick a time, and confirm their appointment.',
      points: {
        bookingLink: 'Share one personal booking link',
        mobileFirst: 'Built for mobile customers',
        otp: 'Secure booking with OTP verification',
      },
    },

    mockupPhone: {
      stepOne: 'Step 1 of 3',
      chooseService: 'Choose Service',
      stepTwo: 'Step 2 of 3',
      chooseDate: 'Choose Date',
      stepThree: 'Step 3 of 3',
      availableTimes: 'Available Times',
    },

    pricing: {
  eyebrow: 'Pricing',
  titleLineOne: 'Choose the plan that',
  titleLineTwo: 'fits your business.',
  description:
    'Start with online booking and upgrade as your business grows.',

  period: '/ month',
  cta: 'Start Free Trial',

features: {
  unlimitedAppointments: 'Unlimited appointments',
  smsOtpVerification: 'SMS verification',
  onlineBookingPage: 'Online booking page',
  availabilityCalendar: 'Availability calendar',
  automaticConfirmation: 'Automatic appointment confirmation',
  businessDashboard: 'Business dashboard',
  whatsappReminders: 'WhatsApp reminders',
  whatsappNotifications: 'WhatsApp notifications',
  approvalWorkflow: 'Appointment approval workflow',
  approveRejectAppointments: 'Approve or reject appointments',
  customerNotifications: 'Customer notifications',
  everythingProfessional: 'Everything in Professional',
  multipleStaff: 'Multiple staff members',
  multipleLocations: 'Multiple locations',
  teamScheduling: 'Team scheduling',
  advancedReporting: 'Advanced reporting',
  prioritySupport: 'Priority support',
},

  basic: {
    name: 'Basic',
    subtitle: 'Perfect for small businesses that need simple online booking.',

    features: [
      'Unlimited appointments',
      'SMS OTP verification',
      'Online booking page',
      'Availability calendar',
      'Automatic appointment confirmation',
      'Business dashboard',
    ],
  },

  premium: {
    badge: 'MOST POPULAR',

    name: 'Premium',

    subtitle:
      'Reduce no-shows and gain full control over appointment approvals.',

  },

  business: {
    name: 'Business',

    subtitle:
      'Advanced tools for clinics, teams, and growing businesses.',

    features: [
      'Everything in Professional',
      'Multiple staff members',
      'Multiple locations',
      'Team scheduling',
      'Advanced reporting',
      'Priority support',
    ],
  },
},
  },



};

