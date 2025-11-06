import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User | null;
    roles: string[];
    permissions: string[];
    can?: {
        viewStaff?: boolean;
        manageUsers?: boolean;
        manageRoles?: boolean;
        [key: string]: boolean | undefined;
    };
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | string;
    isActive?: boolean;
    description?: string;
    badge?: string;
    permission?: string | null;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    breadcrumbs?: BreadcrumbItem[];
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    account_status: string;
    account_type: string;
    approved_at: string | null;
    approved_by: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface ActivityLogChange {
    old?: unknown;
    new?: unknown;
}

export interface ActivityLog {
    id: number;
    causer_id: number | null;
    causer: {
        id: number;
        name: string;
        email?: string | null;
    } | null;
    action: string;
    description: string | null;
    subject_type: string;
    subject_id: number | string | null;
    changes: Record<string, ActivityLogChange | null> | null;
    created_at: string;
    updated_at: string;
}

export type Company = {
    id: number;
    name: string;
    address: string;
    city: string;
    state: string;
    postal_code: string;
    country: string;
    timezone: string;
    currency: string;
    date_format: string;
    financial_year_start: string;
    logo: string;
};

export type Site = {
    id: number;
    name: string;
    description: string;
    address: string;
    suite: string;
    city: string;
    state: string;
    postal_code: string;
    country: string;
};

export type Location = {
    id: number;
    site_id: number;
    name: string;
    description: string;
};

export type Category = {
    id: number;
    name: string;
    description: string;
    parent_id: number | null;
};

export type Department = {
    id: number;
    name: string;
    description: string;
    parent_id: number | null;
};

export type Asset = {
    id: number;
    asset_tag: string;
    description: string;
    purchase_date: string;
    cost: number;
    currency: string;
    purchased_from: string;
    brand: string;
    model: string;
    serial_no: string;
    project_code: string;
    asset_condition: 'New' | 'Good' | 'Fair' | 'Poor' | 'Broken';
    vendor_id: number | null;
    product_id: number | null;
    purchase_order_item_id?: number | null;
    site_id: number | null;
    location_id: number | null;
    category_id: number | null;
    department_id: number | null;
    staff_id: number | null;
    status: 'Available' | 'Checked Out' | 'Under Repair' | 'Leased' | 'Disposed' | 'Lost' | 'Donated' | 'Sold';
    photo: string | null;
    custom_fields?: Record<string, unknown> | null;
    created_by: number;
};

export type VendorOption = {
    id: number;
    name: string;
};

export type ProductOption = {
    id: number;
    vendor_id: number | null;
    name: string;
    warranty_months: number | null;
    unit_cost_minor: number | null;
    currency: string | null;
};

export type PurchaseOrderItemOption = {
    id: number;
    purchase_order_id: number;
    po_number: string | null;
    vendor_id: number | null;
    product_id: number | null;
    product_name: string | null;
    qty: number;
    received_qty: number | null;
    unit_cost_minor: number | null;
    currency: string | null;
};

export type Checkout = {
    id: number;
    asset_id: number;
    assignee_type: string;
    assignee_id: number;
    due_at: string | null;
    checked_out_at: string | null;
    checked_in_at: string | null;
    condition_out_id: string | null;
    condition_in_id: string | null;
    notes: string | null;
    status: 'open' | 'closed' | 'overdue';
};

export type Lease = {
    id: number;
    asset_id: number;
    lessee_type: string;
    lessee_id: number;
    start_at: string;
    end_at: string;
    rate_minor: number;
    currency: string;
    terms: string | null;
    status: 'active' | 'returned' | 'overdue' | 'cancelled';
};

export type Maintenance = {
    id: number;
    asset_id: number;
    title: string;
    description: string | null;
    maintenance_type: 'Preventive' | 'Corrective';
    scheduled_for: string | null;
    completed_at: string | null;
    status: 'Open' | 'Scheduled' | 'Completed' | 'Cancelled';
    cost: number | null;
    vendor: string | null;
};

export type Move = {
    id: number;
    asset_id: number;
    from_location_id: number | null;
    to_location_id: number | null;
    moved_by: number;
    moved_at: string;
    reason: string | null;
};

export type Reservation = {
    id: number;
    asset_id: number;
    requester_id: number;
    start_at: string;
    end_at: string;
    status: 'pending' | 'approved' | 'cancelled' | 'fulfilled';
};

export type Alert = {
    id: number;
    type: 'Maintenance Due' | 'Warranty Expiring' | 'Audit Due';
    asset_id: number | null;
    due_date: string;
    sent: boolean;
    sent_at: string | null;
};

export type Warranty = {
    id: number;
    asset_id: number;
    description: string;
    provider: string | null;
    length_months: number;
    start_date: string;
    expiry_date: string;
    active: boolean;
    notes: string | null;
};

export type Audit = {
    id: number;
    name: string;
    site_id: number | null;
    location_id: number | null;
    status: 'Draft' | 'Ongoing' | 'Completed';
    started_at: string | null;
    completed_at: string | null;
};

export type AuditAsset = {
    id: number;
    audit_id: number;
    asset_id: number;
    found: boolean;
    notes: string | null;
    checked_at: string | null;
};

export type Customer = {
    id: number;
    name: string;
    contact_name: string | null;
    email: string | null;
    phone: string | null;
    address_json: Record<string, any> | null;
};
