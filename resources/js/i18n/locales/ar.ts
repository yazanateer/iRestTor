export default {
    common: {
        status: 'الحالة',
        actions: 'الإجراءات',
        active: 'نشط',
        inactive: 'غير نشط',
        edit: 'تعديل',
        delete: 'حذف',
        cancel: 'إلغاء',
        logout: 'تسجيل الخروج',
        min: 'دقيقة',
        save: 'حفظ',
        saving: 'جارٍ الحفظ...',
        create: 'إنشاء',
        update: 'تحديث',
        phone: 'الهاتف',
        email: 'البريد الإلكتروني',
        address: 'العنوان',
        timezone: 'المنطقة الزمنية',
        open: 'مفتوح',
        closed: 'مغلق',
        name: 'الاسم',
        password: 'كلمة المرور',
        description: 'الوصف',
        available: 'متاح',
        continue: 'متابعة',
        off: 'مغلق',
        back: 'رجوع',
        business: 'النشاط التجاري',
        sentAt: 'تاريخ الإرسال',
        type: 'النوع',
    },

    days: {
        sunday: 'الأحد',
        monday: 'الاثنين',
        tuesday: 'الثلاثاء',
        wednesday: 'الأربعاء',
        thursday: 'الخميس',
        friday: 'الجمعة',
        saturday: 'السبت',
    },

    daysShort: {
        sunday: 'أحد',
        monday: 'اثن',
        tuesday: 'ثلا',
        wednesday: 'أرب',
        thursday: 'خمي',
        friday: 'جمع',
        saturday: 'سبت',
    },
    admin: {

      contactRequests: {
        title: 'طلبات التواصل',
        management: 'إدارة طلبات التواصل',
        managementDescription: 'عرض أصحاب الأعمال الذين طلبوا عرضاً تجريبياً من صفحة الهبوط.',
        empty: 'لا توجد طلبات تواصل حالياً.',

        statuses: {
        new: 'جديد',
        in_progress: 'قيد المتابعة',
        converted: 'أصبح عميلاً',
        closed: 'مغلق',
    },

    actions: {
        inProgress: 'قيد المتابعة',
        convert: 'تحويل لعميل',
        close: 'إغلاق',
    },
    
    },
        nav: {
            dashboard: 'لوحة التحكم',
            businesses: 'الأعمال',
            managers: 'المديرون',
            contactRequests: 'طلبات التواصل',
        },

        platformAdmin: 'مدير المنصة',
        console: 'لوحة تحكم IRestTor',
        systemOnline: 'النظام يعمل',

        dashboard: {
            welcome: 'مرحباً، مدير النظام',
            description: 'من هنا يمكنك إدارة الأعمال والمديرين والاشتراكات وإعدادات المنصة.',
                title: 'لوحة التحكم الرئيسية',
                stats: {
                    businesses: 'الأعمال',
                    managers: 'المدراء',
                    activeAccounts: 'الحسابات النشطة',
                    platformStatus: 'حالة المنصة',
                    online: 'نشط',
                },
                overview: {
                    title: 'نظرة عامة على المنصة',
                    description: 'إدارة الأعمال والمدراء وإعدادات المنصة.',
                    businessManagement: 'إدارة الأعمال',
                    businessManagementDescription: 'إنشاء وتعديل وإدارة جميع الأعمال باستخدام IRestTor.',
                    managerAccounts: 'حسابات المدراء',
                    managerAccountsDescription: 'تعيين المدراء للأعمال والتحكم في صلاحيات الوصول.',
                },
                quickActions: {
                    title: 'إجراءات سريعة',
                    description: 'ابدأ بإدارة المنصة من هنا.',
                },
        },

        businesses: {
            title: 'الأعمال',
            management: 'إدارة الأعمال',
            managementDescription: 'إنشاء وإدارة الأعمال التجارية التي تستخدم منصة IRestTor.',
            createBusiness: 'إنشاء عمل تجاري',
            bookingLink: 'رابط الحجز',
            businessName: 'اسم العمل التجاري',
            slug: 'المعرّف',
            slugHint: 'مثال: dr-saadi-clinic. إذا تُرك فارغاً، سيتم إنشاؤه تلقائياً.',
            business: 'العمل التجاري',
            selectBusiness: 'اختر عملاً تجارياً',
            updateBusiness: 'تحديث العمل التجاري',
            editBusiness: 'تحديث العمل التجاري',
            deleteConfirm: 'هل أنت متأكد أنك تريد حذف هذا العمل التجاري؟',
            brandingSettings: 'إعدادات الهوية البصرية',
            publicTitle: 'العنوان العام',
            publicSubtitle: 'العنوان الفرعي العام',
            publicDescription: 'الوصف العام',
            primaryColor: 'اللون الأساسي',
            secondaryColor: 'اللون الثانوي',
            accentColor: 'لون التمييز',
            plan: 'الخطة',
            selectPlan: 'اختر الخطة',
        },

        managers: {
            title: 'المديرون',
            accounts: 'حسابات المديرين',
            accountsDescription: 'إنشاء وإدارة مديري الأعمال في IRestTor.',
            createManager: 'إنشاء مدير',
            managerName: 'الاسم',
            editManager: 'تعديل المدير',
            newPassword: 'كلمة مرور جديدة',
            keepPasswordHint: 'اتركه فارغاً للإبقاء على كلمة المرور الحالية.',
            updateManager: 'تحديث المدير',
            deleteConfirm: 'هل أنت متأكد أنك تريد حذف هذا المدير؟',
        },

    },

    manager: {
        nav: {
            dashboard: 'لوحة التحكم',
            appointments: 'المواعيد',
            services: 'الخدمات',
            availability: 'التوفر',
        },

        businessDashboard: 'لوحة تحكم الأعمال',
        businessManager: 'مدير الأعمال',
        console: 'لوحة تحكم أعمال IRestTor',
        businessActive: 'العمل التجاري نشط',
    },

    businesses: {
        title: 'الأعمال',
        createTitle: 'إنشاء عمل تجاري',
        editTitle: 'تعديل العمل التجاري',
        management: 'إدارة الأعمال',
        description: 'إنشاء وإدارة الأعمال التجارية التي تستخدم منصة IRestTor.',
        createBusiness: 'إنشاء عمل تجاري',
        updateBusiness: 'تحديث العمل التجاري',
        businessName: 'اسم العمل التجاري',
        slug: 'المعرّف',
        phone: 'الهاتف',
        email: 'البريد الإلكتروني',
        address: 'العنوان',
        timezone: 'المنطقة الزمنية',
        bookingLink: 'رابط الحجز',
        noBusinesses: 'لم يتم إنشاء أي أعمال تجارية بعد.',
        deleteConfirm: 'هل أنت متأكد أنك تريد حذف هذا العمل التجاري؟',
        active: 'نشط',
        inactive: 'غير نشط',
        slugExample: 'مثال: dr-saadi-clinic. إذا تُرك فارغاً، سيتم إنشاؤه تلقائياً.',
    },

    managers: {
        title: 'المديرون',
        createTitle: 'إنشاء مدير',
        editTitle: 'تعديل المدير',
        accounts: 'حسابات المديرين',
        description: 'إنشاء وإدارة مديري الأعمال في IRestTor.',
        createManager: 'إنشاء مدير',
        updateManager: 'تحديث المدير',
        managerName: 'اسم المدير',
        business: 'العمل التجاري',
        password: 'كلمة المرور',
        newPassword: 'كلمة مرور جديدة',
        keepPassword: 'اتركه فارغاً للإبقاء على كلمة المرور الحالية.',
        selectBusiness: 'اختر عملاً تجارياً',
        noManagers: 'لم يتم إنشاء أي مديرين بعد.',
        deleteConfirm: 'هل أنت متأكد أنك تريد حذف هذا المدير؟',
    },

    services: {
        title: 'الخدمات',
        createTitle: 'إنشاء خدمة',
        editTitle: 'تعديل الخدمة',
        businessServices: 'خدمات العمل التجاري',
        description: 'إدارة الخدمات التي يمكن للعملاء حجزها عبر الإنترنت.',
        createService: 'إنشاء خدمة',
        updateService: 'تحديث الخدمة',
        service: 'الخدمة',
        serviceName: 'اسم الخدمة',
        duration: 'المدة',
        durationMinutes: 'المدة (بالدقائق)',
        price: 'السعر',
        color: 'اللون',
        active: 'نشط',
        descriptionLabel: 'الوصف',
        noServices: 'لم يتم إنشاء أي خدمات بعد.',
        deleteConfirm: 'هل أنت متأكد أنك تريد حذف هذه الخدمة؟',
        editService: 'تحديث الخدمة',
        confirmationMode: 'طريقة التأكيد',
        autoConfirmAfterPhoneVerification: 'تأكيد تلقائي بعد التحقق من رقم الهاتف',
        requireManagerApproval: 'يتطلب موافقة المدير',
        premiumFeature: 'ميزة Premium',
        approvalWorkflowPremiumHint: 'ميزة الموافقة على المواعيد من قبل المدير متاحة فقط ضمن خطة Premium.',
    },

    appointments: {
        title: 'المواعيد',
        heading: 'المواعيد',
        description: 'عرض جميع الحجوزات الخاصة بعملك التجاري.',
        customer: 'العميل',
        service: 'الخدمة',
        date: 'التاريخ',
        time: 'الوقت',
        noAppointments: 'لا توجد مواعيد بعد.',
        status: 'الحالة',
        confirmed: 'مؤكد',
        actions: 'إجراءات',
        confirm: 'تأكيد',
        reject: 'رفض',
        searchPlaceholder: 'البحث حسب اسم العميل أو رقم الهاتف',

        filters: {
        all: 'الكل',
        pending: 'قيد الانتظار',
        confirmed: 'مؤكدة',
        cancelled: 'ملغاة',
    },
},

pagination: {
    previous: 'السابق',
    next: 'التالي',
},
    appointmentStatus: {
        pending: 'قيد الانتظار',
        confirmed: 'مؤكد',
        cancelled: 'ملغى',
        completed: 'مكتمل',
    },

    availability: {
        title: 'التوفر',
        workingHours: 'ساعات العمل',
        description: 'حدد متى يمكن للعملاء حجز المواعيد.',
        openDays: 'الأيام المفتوحة',
        totalWeek: 'الإجمالي / الأسبوع',
        editing: 'تعديل',
        open: 'مفتوح',
        closed: 'مغلق',
        startTime: 'وقت البدء',
        endTime: 'وقت الانتهاء',
        quickSet: 'ضبط سريع',
        applyAll: 'تطبيق هذه الساعات على جميع الأيام المفتوحة',
        closedMessage: 'هذا اليوم مُعلَّم كمغلق. فعّله لتحديد الساعات.',
        weeklySchedule: 'الجدول الأسبوعي',
        clickDay: 'انقر على يوم للتعديل',
        saveAvailability: 'حفظ التوفر',
        saved: 'تم الحفظ بنجاح',
        breakTimes: 'أوقات الاستراحة',
        addBreak: 'إضافة استراحة',
        breakStart: 'بداية الاستراحة',
        breakEnd: 'نهاية الاستراحة',
        removeBreak: 'إزالة الاستراحة',
        subtitle: 'حدد متى يمكن للعملاء حجز المواعيد.',
        noBreaks: 'لم تتم إضافة أوقات استراحة',
        applyToAllOpenDays: 'تطبيق هذه الساعات على جميع الأيام المفتوحة',
        clickDayToEdit: 'انقر على يوم للتعديل',
        halfDayAM: 'نصف يوم',
    },

    dashboard: {
        title: 'لوحة تحكم الأعمال',
        todayAppointments: 'مواعيد اليوم',
        customers: 'العملاء',
        services: 'الخدمات',
        businessStatus: 'حالة العمل التجاري',
        publicBookingLink: 'رابط الحجز العام',
        bookingLinkDescription: 'شارك هذا الرابط مع عملائك حتى يتمكنوا من حجز المواعيد.',
        copyLink: 'نسخ الرابط',
        quickActions: 'الإجراءات السريعة',
        quickActionsDescription: 'ابدأ بإعداد عملك التجاري.',
        createServices: 'إنشاء خدمات',
        createServicesDescription: 'أضف خدمات يمكن للعملاء حجزها عبر الإنترنت.',
        setAvailability: 'تحديد التوفر',
        setAvailabilityDescription: 'إعداد ساعات العمل وفترات المواعيد.',
        whatsappAutomation: 'أتمتة واتساب',
        whatsappAutomationDescription: 'أتمتة التأكيدات والتذكيرات.',
        pendingRequests: 'الطلبات المعلقة',
        thisWeek: 'هذا الأسبوع',
        totalServices: 'إجمالي الخدمات',
        todaySchedule: "جدول مواعيد اليوم",
        todayScheduleDescription: "المواعيد المجدولة لليوم.",
        noTodayAppointments: "لا توجد مواعيد مجدولة لليوم.",
    },

    booking: {
        onlineBooking: 'الحجز عبر الإنترنت',
        selectService: 'اختر خدمة',
        chooseDate: 'اختر تاريخاً',
        availableTimes: 'الأوقات المتاحة',
        yourDetails: 'بياناتك',
        finalStep: 'الخطوة الأخيرة',
        continue: 'متابعة',
        confirmAppointment: 'تأكيد الموعد',
        confirming: 'جارٍ التأكيد...',
        appointmentConfirmed: 'تم تأكيد الموعد',
        successMessage: 'تم حجز موعدك بنجاح.',
        service: 'الخدمة',
        date: 'التاريخ',
        time: 'الوقت',
        name: 'الاسم',
        phone: 'الهاتف',
        emailOptional: 'البريد الإلكتروني (اختياري)',
        fullName: 'الاسم الكامل',
        phoneNumber: 'رقم الهاتف',
        appointmentSummary: 'ملخص الموعد',
        noSlots: 'لا توجد فترات متاحة',
        selectSlot: 'اختر فترة زمنية للمتابعة.',
        available: 'متاح',
        loadingSlots: 'جارٍ تحميل الفترات المتاحة...',
        noServices: 'لا توجد خدمات متاحة',
        noServicesDescription: 'لم يقم هذا العمل التجاري بنشر أي خدمات للحجز بعد.',
        subtitle: 'اختر خدمة، وحدد تاريخًا، ثم اختر وقتًا متاحًا.',
        selectServiceDescription: 'اختر الخدمة التي تريد حجزها.',
        stepOne: 'الخطوة 1 من 3',
        chooseDateDescription: 'اختر اليوم الذي تريد زيارته فيه.',
        stepTwo: 'الخطوة 2 من 3',
        availableTimesDescription: 'اختر أحد المواعيد المتاحة.',
        stepThree: 'الخطوة 3 من 3',
        selectServiceAndDate: 'اختر الخدمة والتاريخ',
        timesWillAppear: 'ستظهر الأوقات المتاحة هنا.',
        selectTimeToContinue: 'اختر وقتًا للمتابعة.',
        pageTitle: 'حجز موعد أونلاين',
        selectServiceFirst: 'يرجى اختيار الخدمة أولاً.',
        detailsDescription: 'أدخل معلوماتك لتأكيد الموعد.',
        fullNamePlaceholder: 'أدخل اسمك',
        verifyPhone: 'تأكيد رقم الهاتف',
        verifyDescription: 'أدخل رمز التحقق الذي تم إرساله إلى هاتفك لإكمال الحجز.',
        verificationCode: 'رمز التحقق',
        verifying: 'جاري التحقق...',
        verifyCode: 'تحقق من الرمز',
        requestSent: 'تم إرسال طلب الحجز',
        requestSentMessage: 'تم إرسال طلب الحجز بنجاح. سيقوم صاحب العمل بمراجعة الطلب وستصلك رسالة تأكيد بعد الموافقة عليه.',
        noDescription: 'لا يوجد وصف متاح',
    },

    schedule: {
        title: "الجدول الزمني",
        dailyPlanner: "المخطط اليومي",
        subtitle: "اعرض المواعيد والنشاط اليومي للتاريخ المحدد.",
        appointments: "المواعيد",
        pendingRequests: "طلبات قيد الانتظار",
        selectedDay: "اليوم المحدد",
        appointmentCount: "موعد",
        noAppointmentsTitle: "لا توجد مواعيد لهذا اليوم",
        noAppointmentsDescription: "اختر تاريخاً آخر أو انتظر حجوزات جديدة.",
    },

  landing: {
    meta: {
      title: 'IRestTOR - نظام ذكي لإدارة المواعيد',
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
      features: 'المميزات',
      howItWorks: 'كيف يعمل',
      pricing: 'الأسعار',
      login: 'تسجيل الدخول',
      bookDemo: 'احجز عرضاً تجريبياً',
    },

    hero: {
      badge: 'حجز أونلاين • توفير الوقت • تذكيرات تلقائية',
      titleLineOne: 'نظام إدارة المواعيد الخاص بك،',
      titleLineTwo: 'جاهز خلال دقائق',
      description:
        'بدون مكالمات هاتفية. بدون رسائل فوضوية. بدون مشاكل في جدولة المواعيد. دع العملاء يحجزون عبر الإنترنت بينما تدير كل شيء من لوحة تحكم واحدة.',
      primaryCta: 'ابدأ تجربة مجانية · 14 يوماً',
      secondaryCta: 'شاهد العرض التوضيحي',
    },

    mockup: {
      live: 'مباشر',
      url: 'iresttor.com/book/clinic-x',
      serviceName: 'استشارة',
      duration: '30 دقيقة',
      price: '₪120',
      available: 'متاح',
      continueBooking: 'متابعة الحجز',
      businessName: 'ClinicX',
      openToday: 'مفتوح اليوم · 09:00–17:00',
      verificationCompleted: 'تم التحقق بنجاح',
      otpCode: 'OTP · #1247 ✓',

      days: {
        mon: 'الإثنين',
        tue: 'الثلاثاء',
        wed: 'الأربعاء',
      },

      stats: {
        todayAppointments: 'مواعيد اليوم',
        pendingRequests: 'طلبات بانتظار الموافقة',
        activeServices: 'الخدمات النشطة',
      },
    },

    features: {
      eyebrow: 'كل ما يحتاجه عملك',
      titleLineOne: 'أدوات قوية.',
      titleLineTwo: 'جاهزة من اليوم الأول.',
      description:
        'كل ما تحتاجه لإدارة المواعيد والعملاء والجداول والحجوزات في مكان واحد.',

      items: {
        personalBookingLink: {
          title: 'رابط حجز شخصي',
          description:
            'يحصل كل نشاط تجاري على صفحة حجز خاصة يمكن مشاركتها عبر واتساب أو إنستغرام أو الموقع الإلكتروني أو وسائل التواصل الاجتماعي.',
        },

        availabilityCalendar: {
          title: 'تقويم توفر ذكي',
          description:
            'حدد أيام العمل وساعات الدوام وفترات الاستراحة وأوقات الحجز بسهولة.',
        },

        otpVerification: {
          title: ' التحقق بالرسائل النصية SMS',  
          description:
            'يقوم العملاء بالتحقق من رقم الهاتف قبل الحجز لتقليل الحجوزات الوهمية وعدم الحضور.',
        },

        approvalWorkflow: {
          title: 'تأكيد تلقائي أو يدوي',
          description:
            'اختر ما إذا كانت المواعيد تُؤكد تلقائياً أو تحتاج إلى موافقة المدير.',
        },

        multiLanguage: {
          title: 'دعم متعدد اللغات',
          description:
            'دعم العربية والإنجليزية والعبرية للشركات التي تخدم عملاء متنوعين.',
        },

        businessDashboard: {
          title: 'لوحة تحكم للأعمال',
          description:
            'إدارة المواعيد والخدمات والجداول والطلبات والعمليات اليومية من مكان واحد.',
        },
      },
    },

    howItWorks: {
      eyebrow: 'كيف يعمل',
      title: 'ثلاث خطوات بسيطة',

      steps: {
        create: {
          title: 'أنشئ نشاطك التجاري',
          description:
            'أضف اسم النشاط والشعار والألوان وساعات العمل وتفضيلات الحجز.',
        },

        services: {
          title: 'أضف الخدمات',
          description:
            'قم بإعداد الخدمات والمدة والسعر والتوفر وإعدادات الموافقة على المواعيد.',
        },

        share: {
          title: 'شارك رابط الحجز',
          description:
            'أرسل رابط الحجز للعملاء ودعهم يحجزون مواعيدهم عبر الإنترنت.',
        },
      },
    },

    businesses: {
      eyebrow: 'مناسب لـ',
      title: 'العيادات، صالونات التجميل، المستشارين وأعمال الخدمات.',
      description:
        'تم تصميم IRestTOR للشركات التي تستقبل العملاء من خلال المواعيد.',

      items: {
        clinics: 'العيادات',
        beautySalons: 'صالونات التجميل',
        barbershops: 'الحلاقون',
        lawFirms: 'مكاتب المحاماة',
        consultants: 'المستشارون',
        coaches: 'المدربون',
        more: 'واخرى...',
      },
    },

    pricing: {
      eyebrow: 'الأسعار',
      titleLineOne: 'خطة واحدة بسيطة.',
      titleLineTwo: 'كل شيء مشمول.',
      description: 'نظام احترافي لإدارة المواعيد بدون رسوم خفية.',
      badge: 'كل شيء مشمول',
      cardDescription: 'كل ما تحتاجه لإدارة المواعيد بشكل احترافي.',
      price: '₪249',
      period: '/ شهرياً',
      cta: 'ابدأ تجربة مجانية · 14 يوماً',

      features: {
        personalBookingPage: 'صفحة حجز شخصية',
        unlimitedServices: 'خدمات غير محدودة',
        availabilityManagement: 'إدارة التوفر',
        smsOtpVerification: 'التحقق عبر OTP بالرسائل النصية',
        appointmentApprovals: 'الموافقة على المواعيد',
        businessDashboard: 'لوحة تحكم للأعمال',
        multiLanguage: 'دعم العربية والإنجليزية والعبرية',
      },
    },

    contact: {
      eyebrow: 'تواصل معنا',
      title: 'هل أنت مستعد للتوقف عن إدارة المواعيد عبر الرسائل؟',
      description:
        'اترك بياناتك وسيتواصل معك فريقنا في أقرب وقت ممكن.',
      cta: 'طلب عرض تجريبي',

      fields: {
        fullName: 'الاسم الكامل',
        businessName: 'اسم النشاط التجاري',
        phone: 'رقم الهاتف',
        businessType: 'نوع النشاط',
        message: 'أخبرنا عن نشاطك التجاري',
      },

      placeholders: {
        fullName: 'محمد أحمد',
        businessName: 'عيادتي الخاصة',
        phone: '054-720-0199',
        message: 'كم موعداً تستقبل يومياً؟',
      },

      businessTypes: {
        select: 'اختر...',
        clinic: 'عيادة',
        barbershop: 'صالون حلاقة',
        beautySalon: 'صالون تجميل',
        consulting: 'استشارات',
        other: 'أخرى',
      },
    },

    mockupShowcase: {
        eyebrow: 'تجربة حجز مباشرة',
        title: 'عملاؤك يحجزون المواعيد من هواتفهم خلال ثوانٍ.',
        description:
            'يوفر IRestTOR لكل نشاط تجاري صفحة حجز مواعيد احترافية ومتوافقة مع الهواتف، حيث يمكن للعملاء اختيار الخدمة والتاريخ والوقت وتأكيد الموعد بسهولة.',
        points: {
            bookingLink: 'شارك رابط حجز شخصي واحد',
            mobileFirst: 'مصمم خصيصًا لمستخدمي الهواتف',
            otp: 'حجز آمن مع التحقق عبر OTP',
        },
        },

        mockupPhone: {
        stepOne: 'الخطوة 1 من 3',
        chooseService: 'اختر الخدمة',
        stepTwo: 'الخطوة 2 من 3',
        chooseDate: 'اختر التاريخ',
        stepThree: 'الخطوة 3 من 3',
        availableTimes: 'الأوقات المتاحة',
        },

        pricing: {
  eyebrow: 'الأسعار',
  titleLineOne: 'اختر الخطة التي',
  titleLineTwo: 'تناسب عملك.',
  description:
    'ابدأ بالحجوزات عبر الإنترنت وقم بالترقية مع نمو نشاطك التجاري.',

  period: '/ شهرياً',
  cta: 'ابدأ التجربة المجانية',
features: {
  unlimitedAppointments: 'مواعيد غير محدودة',
  smsOtpVerification: 'التحقق عبر الرسائل النصية',
  onlineBookingPage: 'صفحة حجز إلكترونية',
  availabilityCalendar: 'إدارة التوفر',
  automaticConfirmation: 'تأكيد المواعيد تلقائياً',
  businessDashboard: 'لوحة تحكم للأعمال',

  whatsappReminders: 'تذكيرات WhatsApp',
  whatsappNotifications: 'إشعارات WhatsApp',
  approvalWorkflow: 'نظام الموافقة على المواعيد',
  approveRejectAppointments: 'الموافقة أو رفض المواعيد',
  customerNotifications: 'إشعارات للعملاء',

  everythingProfessional: 'كل ما في Professional',
  multipleStaff: 'عدة موظفين',
  multipleLocations: 'عدة فروع',
  teamScheduling: 'إدارة جداول الفريق',
  advancedReporting: 'تقارير متقدمة',
  prioritySupport: 'دعم أولوية',
},

  basic: {
    name: 'Basic',

    subtitle:
      'مثالية للأعمال الصغيرة التي تحتاج إلى نظام حجز بسيط عبر الإنترنت.',

    features: [
      'مواعيد غير محدودة',
      'التحقق عبر OTP بالرسائل النصية',
      'صفحة حجز إلكترونية',
      'إدارة التوفر',
      'تأكيد المواعيد تلقائياً',
      'لوحة تحكم للأعمال',
    ],
  },

  premium: {
    badge: 'الأكثر شعبية',

    name: 'Premium',

    subtitle:
      'قلل من حالات عدم الحضور واحصل على تحكم كامل في الموافقة على المواعيد.',

    features: [
      'مواعيد غير محدودة',
      'التحقق عبر OTP بالرسائل النصية',
      'تذكيرات WhatsApp',
      'إشعارات WhatsApp',
      'نظام الموافقة على المواعيد',
      'الموافقة أو رفض المواعيد',
      'لوحة تحكم للأعمال',
      'إشعارات للعملاء',
    ],
  },

  business: {
    name: 'Business',

    subtitle:
      'أدوات متقدمة للعيادات والفرق والأعمال المتنامية.',

    features: [
      'كل ما في Professional',
      'عدة موظفين',
      'عدة فروع',
      'إدارة جداول الفريق',
      'تقارير متقدمة',
      'دعم أولوية',
    ],
  },
},


  },
};