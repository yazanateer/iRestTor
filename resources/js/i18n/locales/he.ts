export default {
    common: {
        status: 'סטטוס',
        actions: 'פעולות',
        active: 'פעיל',
        inactive: 'לא פעיל',
        edit: 'עריכה',
        delete: 'מחיקה',
        cancel: 'ביטול',
        logout: 'התנתקות',
        min: 'דק\'',
        save: 'שמירה',
        saving: 'שומר...',
        create: 'יצירה',
        update: 'עדכון',
        phone: 'טלפון',
        email: 'אימייל',
        address: 'כתובת',
        timezone: 'אזור זמן',
        open: 'פתוח',
        closed: 'סגור',
        name: 'שם',
        password: 'סיסמה',
        description: 'תיאור',
        available: 'זמין',
        continue: 'המשך',
        off: 'כבוי',
        back: 'חזור',
        business: 'עסק',
        sentAt: 'נשלח',
        type: 'סוג',

    },

    days: {
        sunday: 'ראשון',
        monday: 'שני',
        tuesday: 'שלישי',
        wednesday: 'רביעי',
        thursday: 'חמישי',
        friday: 'שישי',
        saturday: 'שבת',
    },

    daysShort: {
        sunday: 'א׳',
        monday: 'ב׳',
        tuesday: 'ג׳',
        wednesday: 'ד׳',
        thursday: 'ה׳',
        friday: 'ו׳',
        saturday: 'ש׳',
    },
    admin: {

      contactRequests: {
      title: 'פניות',
      management: 'ניהול פניות',
      managementDescription: 'צפייה בעסקים שביקשו הדגמה דרך דף הנחיתה.',
      empty: 'אין פניות כרגע.',

       statuses: {
        new: 'חדש',
        in_progress: 'בטיפול',
        converted: 'הומר ללקוח',
        closed: 'נסגר',
    },

    actions: {
        inProgress: 'העבר לטיפול',
        convert: 'המר ללקוח',
        close: 'סגור',
    },

      },
        nav: {
            dashboard: 'לוח בקרה',
            businesses: 'עסקים',
            managers: 'מנהלים',
            contactRequests: 'פניות',
        },

        platformAdmin: 'מנהל פלטפורמה',
        console: 'קונסולת מנהל IRestTor',
        systemOnline: 'המערכת פעילה',

        dashboard: {
            welcome: 'ברוך הבא, מנהל SaaS',
            description: 'מכאן תנהל עסקים, מנהלים, מנויים והגדרות פלטפורמה.',
                title: 'לוח בקרה ראשי',
            stats: {
                businesses: 'עסקים',
                managers: 'מנהלים',
                activeAccounts: 'חשבונות פעילים',
                platformStatus: 'סטטוס הפלטפורמה',
                online: 'פעיל',
            },
            overview: {
                title: 'סקירת המערכת',
                description: 'ניהול עסקים, מנהלים והגדרות הפלטפורמה.',
                businessManagement: 'ניהול עסקים',
                businessManagementDescription: 'יצירה, עריכה וניהול של כל העסקים במערכת IRestTor.',
                managerAccounts: 'חשבונות מנהלים',
                managerAccountsDescription: 'שיוך מנהלים לעסקים ושליטה בהרשאות הגישה.',
            },
            quickActions: {
                title: 'פעולות מהירות',
                description: 'התחל לנהל את הפלטפורמה מכאן.',
            },
        },

        businesses: {
            title: 'עסקים',
            management: 'ניהול עסקים',
            managementDescription: 'צור ונהל עסקים המשתמשים בפלטפורמת IRestTor.',
            createBusiness: 'יצירת עסק',
            bookingLink: 'קישור הזמנה',
            businessName: 'שם העסק',
            slug: 'כתובת מזהה',
            slugHint: 'דוגמה: dr-saadi-clinic. אם ריק, ייווצר אוטומטית.',
            business: 'עסק',
            selectBusiness: 'בחר עסק',
            updateBusiness: 'עדכן עסק',
            editBusiness: 'עדכון עסק',
            deleteConfirm: 'האם אתה בטוח שברצונך למחוק את העסק הזה?',
            brandingSettings: 'הגדרות מיתוג',
            publicTitle: 'כותרת ציבורית',
            publicSubtitle: 'כותרת משנה ציבורית',
            publicDescription: 'תיאור ציבורי',
            primaryColor: 'צבע ראשי',
            secondaryColor: 'צבע משני',
            accentColor: 'צבע הדגשה',
            plan: 'תוכנית',
           selectPlan: 'בחר/י תוכנית',
           empty: 'לא נמצאו עסקים.',
        },

        managers: {
            title: 'מנהלים',
            accounts: 'חשבונות מנהלים',
            accountsDescription: 'צור ונהל מנהלי עסקים ב-IRestTor.',
            createManager: 'יצירת מנהל',
            managerName: 'שם',
            editManager: 'עריכת מנהל',
            newPassword: 'סיסמה חדשה',
            keepPasswordHint: 'השאר ריק כדי לשמור על הסיסמה הנוכחית.',
            updateManager: 'עדכון מנהל',
            deleteConfirm: 'האם אתה בטוח שברצונך למחוק את המנהל הזה?',
        },

    },

    manager: {
        nav: {
            dashboard: 'לוח בקרה',
            appointments: 'תורים',
            services: 'שירותים',
            availability: 'זמינות',
        },

        businessDashboard: 'לוח בקרה עסקי',
        businessManager: 'מנהל עסק',
        console: 'קונסולת עסק IRestTor',
        businessActive: 'העסק פעיל',
    },

    businesses: {
        title: 'עסקים',
        createTitle: 'יצירת עסק',
        editTitle: 'עריכת עסק',
        management: 'ניהול עסקים',
        description: 'צור ונהל עסקים המשתמשים בפלטפורמת IRestTor.',
        createBusiness: 'יצירת עסק',
        updateBusiness: 'עדכון עסק',
        businessName: 'שם העסק',
        slug: 'כתובת מזהה',
        phone: 'טלפון',
        email: 'אימייל',
        address: 'כתובת',
        timezone: 'אזור זמן',
        bookingLink: 'קישור הזמנה',
        noBusinesses: 'לא נוצרו עסקים עדיין.',
        deleteConfirm: 'האם אתה בטוח שברצונך למחוק את העסק הזה?',
        active: 'פעיל',
        inactive: 'לא פעיל',
        slugExample: 'דוגמה: dr-saadi-clinic. אם ריק, ייווצר אוטומטית.',
        
    },

    managers: {
        title: 'מנהלים',
        createTitle: 'יצירת מנהל',
        editTitle: 'עריכת מנהל',
        accounts: 'חשבונות מנהלים',
        description: 'צור ונהל מנהלי עסקים ב-IRestTor.',
        createManager: 'יצירת מנהל',
        updateManager: 'עדכון מנהל',
        managerName: 'שם המנהל',
        business: 'עסק',
        password: 'סיסמה',
        newPassword: 'סיסמה חדשה',
        keepPassword: 'השאר ריק כדי לשמור על הסיסמה הנוכחית.',
        selectBusiness: 'בחר עסק',
        noManagers: 'לא נוצרו מנהלים עדיין.',
        deleteConfirm: 'האם אתה בטוח שברצונך למחוק את המנהל הזה?',
    },

    services: {
        title: 'שירותים',
        createTitle: 'יצירת שירות',
        editTitle: 'עריכת שירות',
        businessServices: 'שירותי העסק',
        description: 'נהל שירותים שלקוחות יכולים להזמין אונליין.',
        createService: 'יצירת שירות',
        updateService: 'עדכון שירות',
        service: 'שירות',
        serviceName: 'שם השירות',
        duration: 'משך',
        durationMinutes: 'משך (דקות)',
        price: 'מחיר',
        color: 'צבע',
        active: 'פעיל',
        descriptionLabel: 'תיאור',
        noServices: 'לא נוצרו שירותים עדיין.',
        deleteConfirm: 'האם אתה בטוח שברצונך למחוק את השירות הזה?',
        editService: 'עדכון שירות',
        confirmationMode: 'מצב אישור',
        autoConfirmAfterPhoneVerification: 'אישור אוטומטי לאחר אימות טלפון',
        requireManagerApproval: 'נדרש אישור מנהל',
        premiumFeature: 'תכונת פרימיום',
        approvalWorkflowPremiumHint: 'תהליך אישור תורים על ידי מנהל זמין בתוכנית Premium בלבד.',
    },

    appointments: {
        title: 'תורים',
        heading: 'תורים',
        description: 'צפה בכל ההזמנות של העסק שלך.',
        customer: 'לקוח',
        service: 'שירות',
        date: 'תאריך',
        time: 'שעה',
        noAppointments: 'אין תורים עדיין.',
        status: 'סטטוס',
        confirmed: 'מאושר',
        actions: 'פעולות',
        confirm: 'אישור',
        reject: 'דחייה',
        searchPlaceholder: 'חיפוש לפי שם לקוח או טלפון',

        filters: {
        all: 'הכל',
        pending: 'ממתינים',
        confirmed: 'מאושרים',
        cancelled: 'מבוטלים',
    },
},

pagination: {
    previous: 'הקודם',
    next: 'הבא',
},
    appointmentStatus: {
        pending: 'ממתין לאישור',
        confirmed: 'מאושר',
        cancelled: 'בוטל',
        completed: 'הושלם',
    },

    availability: {
        title: 'זמינות',
        workingHours: 'שעות עבודה',
        description: 'הגדר מתי לקוחות יכולים לקבוע תורים.',
        openDays: 'ימים פתוחים',
        totalWeek: 'סה"כ / שבוע',
        editing: 'עריכה',
        open: 'פתוח',
        closed: 'סגור',
        startTime: 'שעת התחלה',
        endTime: 'שעת סיום',
        quickSet: 'הגדרה מהירה',
        applyAll: 'החל שעות אלו על כל הימים הפתוחים',
        closedMessage: 'יום זה מסומן כסגור. הפעל כדי להגדיר שעות.',
        weeklySchedule: 'לוח שבועי',
        clickDay: 'לחץ על יום לעריכה',
        saveAvailability: 'שמור זמינות',
        saved: 'נשמר בהצלחה',
        breakTimes: 'הפסקות',
        addBreak: 'הוסף הפסקה',
        breakStart: 'תחילת הפסקה',
        breakEnd: 'סיום הפסקה',
        removeBreak: 'הסר הפסקה',
        subtitle: 'הגדר מתי לקוחות יכולים לקבוע תורים.',
        noBreaks: 'לא נוספו הפסקות',
        applyToAllOpenDays: 'החל שעות אלו על כל הימים הפתוחים',
        clickDayToEdit: 'לחץ על יום לעריכה',
        halfDayAM: 'חצי יום',
    },

    dashboard: {
        title: 'לוח בקרה עסקי',
        todayAppointments: 'תורים להיום',
        customers: 'לקוחות',
        services: 'שירותים',
        businessStatus: 'סטטוס העסק',
        publicBookingLink: 'קישור הזמנה ציבורי',
        bookingLinkDescription: 'שתף קישור זה עם הלקוחות שלך כדי שיוכלו לקבוע תורים.',
        copyLink: 'העתק קישור',
        quickActions: 'פעולות מהירות',
        quickActionsDescription: 'התחל להגדיר את העסק שלך.',
        createServices: 'יצירת שירותים',
        createServicesDescription: 'הוסף שירותים שלקוחות יכולים להזמין אונליין.',
        setAvailability: 'הגדרת זמינות',
        setAvailabilityDescription: 'הגדר שעות עבודה וחלונות זמן לתורים.',
        whatsappAutomation: 'אוטומציה בוואטסאפ',
        whatsappAutomationDescription: 'אוטומציה של אישורים ותזכורות.',
        pendingRequests: 'בקשות ממתינות',
        thisWeek: 'השבוע',
        totalServices: 'סה״כ שירותים',
        todaySchedule: "לוח הזמנים להיום",
        todayScheduleDescription: "התורים שנקבעו להיום.",
        noTodayAppointments: "לא נקבעו תורים להיום.",
    },

    booking: {
        onlineBooking: 'הזמנה אונליין',
        selectService: 'בחר שירות',
        chooseDate: 'בחר תאריך',
        availableTimes: 'שעות פנויות',
        yourDetails: 'הפרטים שלך',
        finalStep: 'שלב אחרון',
        continue: 'המשך',
        confirmAppointment: 'אישור תור',
        confirming: 'מאשר...',
        appointmentConfirmed: 'התור אושר',
        successMessage: 'התור שלך נקבע בהצלחה.',
        service: 'שירות',
        date: 'תאריך',
        time: 'שעה',
        name: 'שם',
        phone: 'טלפון',
        emailOptional: 'אימייל (אופציונלי)',
        fullName: 'שם מלא',
        phoneNumber: 'מספר טלפון',
        appointmentSummary: 'סיכום תור',
        noSlots: 'אין משבצות פנויות',
        selectSlot: 'בחר משבצת זמן כדי להמשיך.',
        available: 'פנוי',
        loadingSlots: 'טוען משבצות פנויות...',
        noServices: 'אין שירותים זמינים',
        noServicesDescription: 'עסק זה טרם פרסם שירותים לקביעת תורים.',
        subtitle: 'בחר שירות, תאריך ושעה פנויה.',
        selectServiceDescription: 'בחר את השירות שברצונך להזמין.',
        stepOne: 'שלב 1 מתוך 3',
        chooseDateDescription: 'בחר את היום שבו תרצה להגיע.',
        stepTwo: 'שלב 2 מתוך 3',
        availableTimesDescription: 'בחר אחת מהשעות הפנויות לתור.',
        stepThree: 'שלב 3 מתוך 3',
        selectServiceAndDate: 'בחר שירות ותאריך',
        timesWillAppear: 'השעות הזמינות יופיעו כאן.',
        selectTimeToContinue: 'בחר שעת תור כדי להמשיך.',
        pageTitle: 'הזמנת תור אונליין',
        selectServiceFirst: 'יש לבחור שירות קודם.',
        detailsDescription: 'הזן את הפרטים שלך כדי לאשר את התור.',
        fullNamePlaceholder: 'הזן את השם שלך',
        verifyPhone: 'אימות מספר טלפון',
        verifyDescription: 'הזן את קוד האימות שנשלח לטלפון שלך כדי להשלים את ההזמנה.',
        verificationCode: 'קוד אימות',
        verifying: 'מאמת...',
        verifyCode: 'אמת קוד',
        requestSent: 'בקשת התור נשלחה',
        requestSentMessage: 'בקשת התור שלך נשלחה בהצלחה. בית העסק יעבור על הבקשה וישלח לך הודעת אישור לאחר אישורה.',
        noDescription: 'אין תיאור זמין',
    },
    
    schedule: {
        title: "לוח זמנים",
        dailyPlanner: "מתכנן יומי",
        subtitle: "צפה בפגישות ובפעילות היומית עבור התאריך שנבחר.",
        appointments: "פגישות",
        pendingRequests: "בקשות ממתינות",
        selectedDay: "היום הנבחר",
        appointmentCount: "פגישות",
        noAppointmentsTitle: "אין פגישות ביום זה",
        noAppointmentsDescription: "בחר תאריך אחר או המתן להזמנות חדשות.",
    },

  landing: {
    meta: {
      title: 'IRestTOR - מערכת חכמה לניהול תורים',
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
      features: 'יתרונות',
      howItWorks: 'איך זה עובד',
      pricing: 'מחיר',
      login: 'כניסה',
      bookDemo: 'קביעת דמו',
    },

    hero: {
      badge: 'קביעת תורים אונליין • חיסכון בזמן • תזכורות אוטומטיות',
      titleLineOne: 'מערכת ניהול התורים שלך,',
      titleLineTwo: 'מוכנה תוך דקות',
      description:
        'בלי טלפונים. בלי הודעות מבולגנות. בלי כאב ראש. הלקוחות קובעים תור אונליין, ואתה מנהל הכול מדאשבורד אחד.',
      primaryCta: 'התחל ניסיון חינם · 14 יום',
      secondaryCta: 'צפה בדמו',
    },

    mockup: {
      live: 'פעיל',
      url: 'iresttor.com/book/clinic-x',
      serviceName: 'ייעוץ',
      duration: '30 דק׳',
      price: '₪120',
      available: 'זמין',
      continueBooking: 'המשך להזמנה',
      businessName: 'ClinicX',
      openToday: 'פתוח היום · 09:00–17:00',
      verificationCompleted: 'האימות הושלם',
      otpCode: 'OTP · #1247 ✓',
      onlineBooking: 'הזמנת תורים אונליין',
      subtitle: 'מערכת לקביעת תורים למרפאה',
      serviceDescription: 'בדיקה מקיפה עבור המטופל',
      days: {
        mon: 'ב׳',
        tue: 'ג׳',
        wed: 'ד׳',
      },

      stats: {
        todayAppointments: 'תורים היום',
        pendingRequests: 'בקשות ממתינות',
        activeServices: 'שירותים פעילים',
      },
    },

    features: {
      eyebrow: 'כל מה שהעסק שלך צריך',
      titleLineOne: 'כלים חזקים.',
      titleLineTwo: 'מוכנים מהיום הראשון.',
      description:
        'כל מה שצריך כדי לנהל תורים, לקוחות, זמינות והזמנות במקום אחד.',

      items: {
        personalBookingLink: {
          title: 'קישור אישי להזמנת תורים',
          description:
            'כל עסק מקבל דף הזמנות אישי שאפשר לשתף בוואטסאפ, אינסטגרם, אתר או רשתות חברתיות.',
        },

        availabilityCalendar: {
          title: 'יומן זמינות חכם',
          description:
            'הגדר ימי עבודה, שעות פעילות, הפסקות וזמינות לתורים בצורה פשוטה.',
        },

        otpVerification: {
          title: 'אימות ב-SMS',
          description:
            'לקוחות מאמתים את מספר הטלפון לפני קביעת תור כדי להפחית הזמנות מזויפות ואי-הגעה.',
        },

        approvalWorkflow: {
          title: 'אישור ידני או אוטומטי',
          description:
            'בחר אם תורים יאושרו אוטומטית או ימתינו לאישור מנהל העסק.',
        },

        multiLanguage: {
          title: 'תמיכה בריבוי שפות',
          description:
            'תמיכה בעברית, ערבית ואנגלית לעסקים שמשרתים מגוון לקוחות.',
        },

        businessDashboard: {
          title: 'דאשבורד עסקי',
          description:
            'נהל תורים, שירותים, לוחות זמנים, בקשות ופעילות יומית ממקום אחד.',
        },
      },
    },

    howItWorks: {
      eyebrow: 'איך זה עובד',
      title: 'שלושה צעדים פשוטים',

      steps: {
        create: {
          title: 'יוצרים את העסק',
          description:
            'מוסיפים שם עסק, לוגו, צבעים, שעות עבודה והעדפות להזמנות.',
        },

        services: {
          title: 'מוסיפים שירותים',
          description:
            'מגדירים שירותים, משך זמן, מחיר, זמינות והגדרות אישור תור.',
        },

        share: {
          title: 'משתפים את קישור ההזמנה',
          description:
            'שולחים ללקוחות את הקישור האישי והם קובעים תור אונליין.',
        },
      },
    },

    businesses: {
      eyebrow: 'מתאים ל',
      title: 'קליניקות, מספרות, יועצים ועסקי שירות.',
      description: 'IRestTOR מיועד לעסקים שמקבלים לקוחות לפי תורים.',

      items: {
        clinics: 'קליניקות',
        beautySalons: 'סלוני יופי',
        barbershops: 'מספרות',
        lawFirms: 'משרדי עורכי דין',
        consultants: 'יועצים',
        coaches: 'מאמנים',
        more: 'ועוד...',
      },
    },

    pricing: {
      eyebrow: 'מחיר',
      titleLineOne: 'תוכנית אחת פשוטה.',
      titleLineTwo: 'הכול כלול.',
      description: 'מערכת מקצועית לניהול תורים בלי עלויות נסתרות.',
      badge: 'הכול כלול',
      cardDescription: 'כל מה שצריך כדי לנהל תורים בצורה מקצועית.',
      price: '₪249',
      period: '/ חודש',
      cta: 'התחל ניסיון חינם · 14 יום',

      features: {
        personalBookingPage: 'דף הזמנות אישי',
        unlimitedServices: 'שירותים ללא הגבלה',
        availabilityManagement: 'ניהול זמינות',
        smsOtpVerification: 'אימות OTP ב-SMS',
        appointmentApprovals: 'אישור תורים',
        businessDashboard: 'דאשבורד עסקי',
        multiLanguage: 'תמיכה בעברית, ערבית ואנגלית',
      },
    },

    contact: {
      eyebrow: 'צרו קשר',
      title: 'מוכנים להפסיק לנהל תורים דרך הודעות?',
      description: 'השאירו פרטים והצוות שלנו יחזור אליכם בהקדם.',
      cta: 'בקשת דמו',

      fields: {
        fullName: 'שם מלא',
        businessName: 'שם העסק',
        phone: 'מספר טלפון',
        businessType: 'סוג העסק',
        message: 'ספרו לנו על העסק',
      },

      placeholders: {
        fullName: 'אמיר אמיר',
        businessName: 'הקליניקה שלי',
        phone: '054-720-0199',
        message: 'כמה תורים אתם מקבלים ביום?',
      },

      businessTypes: {
        select: 'בחר...',
        clinic: 'קליניקה',
        barbershop: 'מספרה',
        beautySalon: 'סלון יופי',
        consulting: 'ייעוץ',
        other: 'אחר',
      },
    },

    mockupShowcase: {
        eyebrow: 'חוויית הזמנת תורים חיה',
        title: 'הלקוחות שלכם קובעים תור מהטלפון תוך שניות.',
        description:
            'IRestTOR מעניקה לכל עסק עמוד הזמנת תורים מותאם לנייד, שבו הלקוחות יכולים לבחור שירות, תאריך, שעה ולאשר את התור בקלות.',
        points: {
            bookingLink: 'קישור אישי אחד להזמנת תורים',
            mobileFirst: 'מותאם במיוחד למשתמשי מובייל',
            otp: 'הזמנה מאובטחת עם אימות OTP',
        },
        },

        mockupPhone: {
        stepOne: 'שלב 1 מתוך 3',
        chooseService: 'בחירת שירות',
        stepTwo: 'שלב 2 מתוך 3',
        chooseDate: 'בחירת תאריך',
        stepThree: 'שלב 3 מתוך 3',
        availableTimes: 'שעות זמינות',
        },

        pricing: {
          eyebrow: 'מחירים',
          titleLineOne: 'בחרו את התוכנית',
          titleLineTwo: 'המתאימה לעסק שלכם.',
          description:
            'התחילו עם מערכת הזמנות אונליין ושדרגו ככל שהעסק גדל.',

          period: '/ חודש',
          cta: 'התחילו ניסיון חינם',

          features: {
            unlimitedAppointments: 'תורים ללא הגבלה',
            smsOtpVerification: 'אימות באמצעות SMS',
            onlineBookingPage: 'עמוד הזמנות אונליין',
            availabilityCalendar: 'ניהול זמינות',
            automaticConfirmation: 'אישור תורים אוטומטי',
            businessDashboard: 'דאשבורד עסקי',

            whatsappReminders: 'תזכורות WhatsApp',
            whatsappNotifications: 'התראות WhatsApp',
            approvalWorkflow: 'מערכת אישור תורים',
            approveRejectAppointments: 'אישור או דחיית תורים',
            customerNotifications: 'התראות ללקוחות',

            everythingProfessional: 'כל מה שבתוכנית Professional',
            multipleStaff: 'מספר עובדים',
            multipleLocations: 'מספר סניפים',
            teamScheduling: 'ניהול יומנים לצוות',
            advancedReporting: 'דוחות מתקדמים',
            prioritySupport: 'תמיכה בעדיפות גבוהה',
          },




          basic: {
            name: 'Basic',

            subtitle:
              'מתאים לעסקים קטנים שצריכים מערכת פשוטה להזמנת תורים.',

            features: [
              'תורים ללא הגבלה',
              'אימות SMS באמצעות OTP',
              'עמוד הזמנות אונליין',
              'ניהול זמינות',
              'אישור תורים אוטומטי',
              'דאשבורד עסקי',
            ],
  },

  premium: {
    badge: 'הפופולרי ביותר',

    name: 'Premium',

    subtitle:
      'צמצום אי-הגעה ושליטה מלאה על אישור התורים.',

    features: [
      'תורים ללא הגבלה',
      'אימות SMS באמצעות OTP',
      'תזכורות WhatsApp',
      'התראות WhatsApp',
      'מערכת אישור תורים',
      'אישור או דחיית תורים',
      'דאשבורד עסקי',
      'התראות ללקוחות',
    ],
  },

  business: {
    name: 'Business',

    subtitle:
      'כלים מתקדמים למרפאות, צוותים ועסקים בצמיחה.',

    features: [
      'כל מה שבתוכנית Professional',
      'מספר עובדים',
      'מספר סניפים',
      'ניהול יומנים לצוות',
      'דוחות מתקדמים',
      'תמיכה בעדיפות גבוהה',
    ],
  },
},
  },

};