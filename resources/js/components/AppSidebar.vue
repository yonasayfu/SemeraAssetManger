<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    AlertTriangle,
    Clock,
    AlertCircle,
    CalendarClock,
    Wrench,
    Box,
    List,
    PlusCircle,
    LogOut,
    LogIn,
    FileSignature,
    Undo2,
    Trash2,
    MoveRight,
    CalendarPlus,
    ScrollText,
    ListChecks,
    BadgeCheck,
    ClipboardCheck,
    BarChart3,
    Settings,
    Settings2,
    ArrowLeftRight,
    Info,
    MoreHorizontal,
    Upload,
    Download,
    FileStack,
    Images,
    ClipboardList,
    Layers,
    Users,
    SquareUser,
    Building2,
    MapPinHouse,
    MapPin,
    Tags,
    Network,
    LayoutPanelLeft,
    HelpCircle,
    Mail,
    Scale,
    PlayCircle,
    Star,
    ListOrdered,
    CalendarRange,
    Shield,
    BookOpen,
    Folder,
} from 'lucide-vue-next';
import { ref } from 'vue';
import AppLogo from './AppLogo.vue';

interface Props {
    class?: string;
}

withDefaults(defineProps<Props>(), {
    class: '',
});

const sidebarGroups = ref([
  {
    id: 'main',
    label: "Main",
    icon: LayoutDashboard,
    items: [
      { title: "Dashboard", href: "/dashboard", icon: LayoutDashboard, permission: null },
      {
        title: "Alerts",
        href: "/alerts",
        icon: AlertTriangle,
        permission: null,
        children: [
          { title: "Assets Due", href: "/alerts/assets-due", icon: Clock, permission: null },
          { title: "Assets Past Due", href: "/alerts/assets-past-due", icon: AlertCircle, permission: null },
          { title: "Leases Expiring", href: "/alerts/leases-expiring", icon: CalendarClock, permission: null },
          { title: "Maintenance Due", href: "/alerts/maintenance-due", icon: Wrench, permission: null },
          { title: "Maintenance Overdue", href: "/alerts/maintenance-overdue", icon: Wrench, permission: null },
          { title: "Warranties Expiring", href: "/alerts/warranties-expiring", icon: BadgeCheck, permission: null }
        ]
      }
    ]
  },
  {
    id: 'assets',
    label: "Assets",
    icon: Box,
    items: [
      {
        title: "Assets",
        href: "/assets",
        icon: Box,
        permission: null,
        children: [
          { title: "Add an Asset", href: "/assets/create", icon: PlusCircle, permission: null },
          { title: "Check Out", href: "/assets/checkout-select", icon: LogOut, permission: null },
          { title: "Check In", href: "/assets/checkin-select", icon: LogIn, permission: null },
          { title: "Lease", href: "/assets/lease-select", icon: FileSignature, permission: null },
          { title: "Lease Return", href: "/assets/lease-return-select", icon: Undo2, permission: null },
          { title: "Dispose", href: "/assets/dispose-select", icon: Trash2, permission: null },
          { title: "Maintenance", href: "/assets/maintenance-select", icon: Wrench, permission: null },
          { title: "Move", href: "/assets/move-select", icon: MoveRight, permission: null },
          { title: "Reserve", href: "/assets/reserve-select", icon: CalendarPlus, permission: null }
        ]
      },
      { title: "Maintenance Board", href: "/maintenance", icon: Wrench, permission: null },
      { title: "Warranty Center", href: "/warranties", icon: BadgeCheck, permission: null },
      { title: "List of Assets", href: "/assets", icon: List, permission: null }
    ]
  },
  {
    id: 'lists',
    label: "Lists",
    icon: ScrollText,
    items: [
      {
        title: "Lists",
        href: "/lists",
        icon: ScrollText,
        permission: null,
        children: [
          { title: "List of Assets", href: "/lists/assets", icon: ListChecks, permission: null },
          { title: "List of Maintenances", href: "/lists/maintenances", icon: Wrench, permission: null },
          { title: "List of Warranties", href: "/lists/warranties", icon: BadgeCheck, permission: null },
          { title: "List of Audits", href: "/lists/audits", icon: ClipboardCheck, permission: null }
        ]
      }
    ]
  },
  {
    id: 'reports',
    label: "Reports",
    icon: BarChart3,
    items: [
      {
        title: "Reports",
        href: "/reports",
        icon: BarChart3,
        permission: null,
        children: [
          { title: "Automated Reports", href: "/reports/automated", icon: Clock, permission: null },
          { title: "Custom Reports", href: "/reports/custom", icon: Settings2, permission: null },
          { title: "Asset Reports", href: "/reports/assets", icon: Box, permission: null },
          { title: "Audit Reports", href: "/reports/audits", icon: ClipboardCheck, permission: null },
          { title: "Check-Out Reports", href: "/reports/checkout", icon: LogOut, permission: null },
          { title: "Leased Asset Reports", href: "/reports/leased-assets", icon: FileSignature, permission: null },
          { title: "Maintenance Reports", href: "/reports/maintenance", icon: Wrench, permission: null },
          { title: "Reservation Reports", href: "/reports/reservations", icon: CalendarRange, permission: null },
          { title: "Status Reports", href: "/reports/status", icon: Info, permission: null },
          { title: "Transaction Reports", href: "/reports/transactions", icon: ArrowLeftRight, permission: null },
          { title: "Other Reports", href: "/reports/others", icon: MoreHorizontal, permission: null }
        ]
      }
    ]
  },
  {
    id: 'tools',
    label: "Tools",
    icon: Wrench,
    items: [
      {
        title: "Tools",
        href: "/tools",
        icon: Wrench,
        permission: null,
        children: [
          { title: "Import", href: "/tools/import", icon: Upload, permission: null },
          { title: "Export", href: "/tools/export", icon: Download, permission: null },
          { title: "Documents Gallery", href: "/tools/documents", icon: FileStack, permission: null },
          { title: "Image Gallery", href: "/tools/images", icon: Images, permission: null },
          { title: "Audit", href: "/tools/audit", icon: ClipboardList, permission: null }
        ]
      }
    ]
  },
  {
    id: 'advanced',
    label: "Advanced",
    icon: Layers,
    items: [
      {
        title: "Advanced",
        href: "/advanced",
        icon: Layers,
        permission: null,
        children: [
          { title: "Persons / Employees", href: "/advanced/persons", icon: Users, permission: null },
          { title: "Customers", href: "/advanced/customers", icon: SquareUser, permission: null }
        ]
      }
    ]
  },
  {
    id: 'setup',
    label: "Setup",
    icon: Settings,
    items: [
      {
        title: "Setup",
        href: "/setup",
        icon: Settings,
        permission: null,
        children: [
          { title: "Company Info", href: "/setup/company", icon: Building2, permission: null },
          { title: "Sites", href: "/setup/sites", icon: MapPinHouse, permission: null },
          { title: "Locations", href: "/setup/locations", icon: MapPin, permission: null },
          { title: "Categories", href: "/setup/categories", icon: Tags, permission: null },
          { title: "Departments", href: "/setup/departments", icon: Network, permission: null },
          { title: "Manage Dashboard", href: "/setup/manage-dashboard", icon: LayoutPanelLeft, permission: null }
        ]
      }
    ]
  },
  {
    id: 'help',
    label: "Help & Support",
    icon: HelpCircle,
    items: [
      {
        title: "Help / Support",
        href: "/help",
        icon: HelpCircle,
        permission: null,
        children: [
          { title: "About Us", href: "/help/about", icon: Info, permission: null },
          { title: "Contact Us", href: "/help/contact", icon: Mail, permission: null },
          { title: "Terms of Service", href: "/help/terms", icon: Scale, permission: null },
          { title: "Privacy Policy", href: "/help/privacy", icon: Shield, permission: null },
          { title: "Videos", href: "/help/videos", icon: PlayCircle, permission: null },
          { title: "User Reviews", href: "/help/reviews", icon: Star, permission: null },
          { title: "Changelog", href: "/help/changelog", icon: ListOrdered, permission: null }
        ]
      }
    ]
  }
]);


const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="app-sidebar"
        :class="class"
    >
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :groups="sidebarGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
