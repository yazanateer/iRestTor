
export type Branding = {
    logo_path?: string | null
    cover_image_path?: string | null
    primary_color: string
    secondary_color: string
    accent_color: string
    public_title?: string | null
    public_subtitle?: string | null
    public_description?: string | null
    theme_style: string

}

export type Business = {
    id: number
    name: string
    slug: string
    phone?: string | null
    email?: string | null
    address?: string | null
    timezone: string
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