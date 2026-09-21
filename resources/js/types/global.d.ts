
export type Branding = {
    logo_path?: string | null
    cover_image_path?: string | null
    logo_url?: string | null
    cover_image_url?: string | null
    primary_color: string
    secondary_color: string
    accent_color: string
    public_title?: string | null
    public_subtitle?: string | null
    public_description?: string | null
    theme_style: string

}

// Resolved (defaulted, accessibility-safe) background + text color pairing
export type ResolvedBrandingColor = {
    background: string
    onColor: string
}

// Fully-populated, never-throwing result of the booking-page branding resolver
export type ResolvedBranding = {
    primary: ResolvedBrandingColor
    secondary: ResolvedBrandingColor
    accent: ResolvedBrandingColor
    themeStyle: string
    logoUrl: string | null
    coverImageUrl: string | null
    publicTitle: string | null
    publicSubtitle: string | null
    publicDescription: string | null
}

export type Business = {
    id: number
    name: string
    slug: string
    phone?: string | null
    email?: string | null
    address?: string | null
    timezone: string
    plan_id: number
    is_active: boolean
    branding?: Branding | null
}

export type Service = {
    id: number;
    name: string;
    description?: string | null;
    duration_minutes: number;
    price?: number | null;
    color?: string | null;
    is_active: boolean;
    confirmation_mode: 'auto_confirm' | 'requires_approval'
};

export type Manager = {
    id: number;
    name: string;
    email: string;
    phone: number;
    business?: Business | null
    business_id: number;

}

export type Day = {
    day_of_week: number;
    label: string;
    is_active: boolean;
    start_time: string;
    end_time: string;
};

export type AvailabilityBreak = {
    id?: number;
    day_of_week: number | null;
    date: string | null;
    start_time: string;
    end_time: string;
};

export type Slot = {
    start_time: string;
    end_time: string;
    label: string;
};

export type Appointment = {
    id: number;
    customer_name: string;
    customer_phone: string;
    appointment_date: string;
    start_time: string;
    end_time: string;
    status: string;
    service?: Service | null;
    created_at: string;
    confirmed_at: string | null;
    cancelled_at: string | null;
};

export type Plan = {
    id: number
    name: string
    slug: string
    price: string | number
};

export type DateOverride = {
    id?: number
    date: string
    is_active: boolean
    start_time: string
    end_time: string
};

export type DeliveryChannel = 'sms' | 'whatsapp';

// Narrows the existing Appointment.status string to the product's known values
export type AppointmentStatus = 'confirmed' | 'pending_approval' | 'cancelled';

export type SupportedLocale = 'en' | 'ar' | 'he';

// View-state contract for data-driven screens
export type ViewState = 'loading' | 'empty' | 'success' | 'error';

// LanguageSwitcher option
export type LanguageOption = {
    code: SupportedLocale;
    label: string;
    name: string;
};

// ResponsiveTable column definition
export type TableColumn = {
    key: string;
    labelKey: string;
    align?: 'start' | 'end' | 'center';
};
