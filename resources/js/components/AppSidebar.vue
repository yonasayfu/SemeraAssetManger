<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import { usePage, Link, router, useRemember } from '@inertiajs/vue3'

import AppLogo from './AppLogo.vue'
import NavUser from '@/components/NavUser.vue'
import NavFooter from '@/components/NavFooter.vue'
import { type NavItem } from '@/types'

import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'

import {
  ChevronRight,
  Minimize2,
  Maximize2,
  LayoutDashboard, AlertTriangle, Clock, AlertCircle, CalendarClock, Wrench, Box, List,
  PlusCircle, LogOut, LogIn, FileSignature, Undo2, Trash2, MoveRight, CalendarPlus,
  ScrollText, ListChecks, BadgeCheck, ClipboardCheck, BarChart3, Settings, Settings2,
  ArrowLeftRight, Info, MoreHorizontal, Upload, Download, FileStack, Images,
  ClipboardList, Layers, Users, SquareUser, Building2, MapPinHouse, MapPin, Tags,
  Network, LayoutPanelLeft, HelpCircle, Mail, Scale, PlayCircle, Star, ListOrdered,
  CalendarRange, Shield, KeyRound, Palette
} from 'lucide-vue-next'

interface Props {
  class?: string
  inventoryAlertCount?: number
  myTasksCount?: number
  myTodoCount?: number
}
withDefaults(defineProps<Props>(), {
  class: '',
  inventoryAlertCount: 0,
  myTasksCount: 0,
  myTodoCount: 0,
})

const page = usePage()
const isSidebarCollapsed = ref(!(page.props as any)?.sidebarOpen)

const SIDEBAR_STORAGE_KEY = 'sidebar-open-groups'
const SIDEBAR_EXPAND_ALL_KEY = 'sidebar-expand-all'
const SCROLL_STORAGE_KEY = 'sidebar-scroll-top'

const openGroups = ref<string[]>([])
const areAllGroupsExpanded = ref(false)

// IMPORTANT: real DOM node for scroll container
const scrollContainer = ref<HTMLDivElement | null>(null)

const loadSidebarState = () => {
  try {
    const stored = localStorage.getItem(SIDEBAR_STORAGE_KEY)
    const expandedState = localStorage.getItem(SIDEBAR_EXPAND_ALL_KEY)
    openGroups.value = stored ? JSON.parse(stored) : []
    areAllGroupsExpanded.value = expandedState ? JSON.parse(expandedState) : false
  } catch {
    openGroups.value = []
    areAllGroupsExpanded.value = false
  }
}
const saveSidebarState = () => {
  try {
    localStorage.setItem(SIDEBAR_STORAGE_KEY, JSON.stringify(openGroups.value))
    localStorage.setItem(SIDEBAR_EXPAND_ALL_KEY, JSON.stringify(areAllGroupsExpanded.value))
  } catch {}
}
watch([openGroups, areAllGroupsExpanded], saveSidebarState, { deep: true })

const saveScroll = () => {
  try {
    if (scrollContainer.value) {
      localStorage.setItem(SCROLL_STORAGE_KEY, String(scrollContainer.value.scrollTop))
    }
  } catch {}
}
const restoreScroll = () => {
  try {
    const y = Number(localStorage.getItem(SCROLL_STORAGE_KEY) || 0)
    nextTick(() => {
      if (scrollContainer.value) {
        scrollContainer.value.scrollTo({ top: y })
      }
    })
  } catch {}
}

// Keep sidebar scroll position between navigations
const onBeforeNav = () => saveScroll()
const onSuccessNav = () => restoreScroll()

type Item = {
  title: string
  href: string
  icon: any
  permission?: string | string[] | null
  role?: string | null
  children?: Item[]
}
type Group = {
  id: string
  label: string
  icon: any
  items: Item[]
}

// Reorganized exactly as requested
const sidebarGroups = ref<Group[]>([
  // Alerts own group
  {
    id: 'alerts',
    label: "Alerts",
    icon: AlertTriangle,
    items: [
      { title: "Assets Due", href: "/alerts/assets-due", icon: Clock, permission: 'alerts.view' },
      { title: "Assets Past Due", href: "/alerts/assets-past-due", icon: AlertCircle, permission: 'alerts.view' },
      { title: "Leases Expiring", href: "/alerts/leases-expiring", icon: CalendarClock, permission: 'alerts.view' },
      { title: "Maintenance Due", href: "/alerts/maintenance-due", icon: Wrench, permission: 'alerts.view' },
      { title: "Maintenance Overdue", href: "/alerts/maintenance-overdue", icon: Wrench, permission: 'alerts.view' },
      { title: "Warranties Expiring", href: "/alerts/warranties-expiring", icon: BadgeCheck, permission: 'alerts.view' },
    ]
  },

  // Settings (with Activity Logs + Mailbox)
  {
    id: 'settings',
    label: "Settings",
    icon: Settings2,
    items: [
      { title: "Profile", href: "/settings/profile", icon: SquareUser, permission: null },
      { title: "Password", href: "/settings/password", icon: KeyRound, permission: null },
      { title: "Two-Factor", href: "/settings/two-factor", icon: Shield, permission: null },
      { title: "Appearance", href: "/settings/appearance", icon: Palette, permission: null },
      { title: "Activity Logs", href: "/activity-logs", icon: ClipboardList, permission: null },
      { title: "Mailbox (Dev)", href: "/mailbox", icon: Mail, permission: null },
    ]
  },

  // Assets
  {
    id: 'assets',
    label: "Assets",
    icon: Box,
    items: [
      { title: "Assets", href: "/assets", icon: Box, permission: 'assets.view' },
      { title: "Add an Asset", href: "/assets/create", icon: PlusCircle, permission: 'assets.create' },
      { title: "Check Out", href: "/assets/checkout-select", icon: LogOut, permission: 'assets.view' },
      { title: "Check In", href: "/assets/checkin-select", icon: LogIn, permission: 'assets.view' },
      { title: "Lease", href: "/assets/lease-select", icon: FileSignature, permission: 'assets.view' },
      { title: "Lease Return", href: "/assets/lease-return-select", icon: Undo2, permission: 'assets.view' },
      { title: "Dispose", href: "/assets/dispose-select", icon: Trash2, permission: 'assets.view' },
      { title: "Maintenance", href: "/assets/maintenance-select", icon: Wrench, permission: 'assets.view' },
      { title: "Move", href: "/assets/move-select", icon: MoveRight, permission: 'assets.view' },
      { title: "Reserve", href: "/assets/reserve-select", icon: CalendarPlus, permission: 'assets.view' },
      { title: "Maintenance Board", href: "/maintenance", icon: Wrench, permission: 'maintenance.view' },
      { title: "Warranty Center", href: "/warranties", icon: BadgeCheck, permission: 'warranty.view' },
      // Removed duplicate link to assets index
    ]
  },

  // Catalog & Procurement (Freshservice-style)
  {
    id: 'catalog',
    label: "Catalog & Procurement",
    icon: List,
    items: [
      { title: "Vendors", href: "/vendors", icon: Building2, permission: 'vendors.view' },
      { title: "Products", href: "/products", icon: Tags, permission: 'products.view' },
      { title: "Contracts", href: "/contracts", icon: FileSignature, permission: 'contracts.view' },
      { title: "Contracts Board", href: "/contracts/board", icon: FileSignature, permission: 'contracts.view' },
      { title: "Purchase Orders", href: "/purchase-orders", icon: ClipboardList, permission: 'purchase-orders.view' },
      { title: "Software", href: "/software", icon: Layers, permission: 'software.view' },
    ]
  },

  // Inventory (Lists)
  {
    id: 'inventory',
    label: "Inventory",
    icon: ScrollText,
    items: [
      { title: "Assets (List)", href: "/lists/assets", icon: ListChecks, permission: 'lists.view' },
      { title: "Maintenances (List)", href: "/lists/maintenances", icon: Wrench, permission: 'lists.view' },
      { title: "Warranties (List)", href: "/lists/warranties", icon: BadgeCheck, permission: 'lists.view' },
      { title: "Audits (List)", href: "/lists/audits", icon: ClipboardCheck, permission: 'lists.view' },
    ]
  },

  // Reports
  {
    id: 'reports',
    label: "Reports",
    icon: BarChart3,
    items: [
      { title: "Automated Reports", href: "/reports/automated", icon: Clock, permission: 'reports.view' },
      { title: "Custom Reports", href: "/reports/custom", icon: Settings2, permission: 'reports.view' },
      { title: "Asset Reports", href: "/reports/assets", icon: Box, permission: 'reports.view' },
      { title: "Audit Reports", href: "/reports/audits", icon: ClipboardCheck, permission: 'reports.view' },
      { title: "Check-Out Reports", href: "/reports/checkout", icon: LogOut, permission: 'reports.view' },
      { title: "Leased Asset Reports", href: "/reports/leased-assets", icon: FileSignature, permission: 'reports.view' },
      { title: "Maintenance Reports", href: "/reports/maintenance", icon: Wrench, permission: 'reports.view' },
      { title: "Contract Reports", href: "/reports/contracts", icon: FileSignature, permission: 'reports.view' },
      { title: "Purchase Order Reports", href: "/reports/purchase-orders", icon: ClipboardList, permission: 'reports.view' },
      { title: "Software Reports", href: "/reports/software", icon: Layers, permission: 'reports.view' },
      { title: "Reservation Reports", href: "/reports/reservations", icon: CalendarRange, permission: 'reports.view' },
      { title: "Status Reports", href: "/reports/status", icon: Info, permission: 'reports.view' },
      { title: "Transaction Reports", href: "/reports/transactions", icon: ArrowLeftRight, permission: 'reports.view' },
      { title: "Other Reports", href: "/reports/others", icon: MoreHorizontal, permission: 'reports.view' },
    ]
  },

  // Tools
  {
    id: 'tools',
    label: "Tools",
    icon: Wrench,
    items: [
      { title: "Import", href: "/tools/import", icon: Upload, permission: 'tools.view', role: 'Admin' },
      { title: "Export", href: "/tools/export", icon: Download, permission: 'tools.view', role: 'Admin' },
      { title: "Documents Gallery", href: "/tools/documents", icon: FileStack, permission: 'tools.view' },
      { title: "Image Gallery", href: "/tools/images", icon: Images, permission: 'tools.view' },
      { title: "Audit", href: "/tools/audits", icon: ClipboardList, permission: 'tools.view' },
    ]
  },

  // Team (flattened)
  {
    id: 'team',
    label: "Team",
    icon: Users,
    items: [
      { title: "Staff", href: "/staff", icon: Users, permission: 'staff.view' },
      { title: "Accounts (Admin)", href: "/admin/staff", icon: SquareUser, permission: 'users.manage' },
      { title: "Roles & Permissions", href: "/roles", icon: Shield, permission: ['roles.manage','users.manage','staff.manage'] },
    ]
  },

  // Advanced (flattened)
  {
    id: 'advanced',
    label: "Advanced",
    icon: Layers,
    items: [
      // Removed deprecated Persons/Employees
      { title: "Customers", href: "/advanced/customers", icon: SquareUser, permission: null },
    ]
  },

  // Setup (flattened)
  {
    id: 'setup',
    label: "Setup",
    icon: Settings,
    items: [
      { title: "Company Info", href: "/setup/company", icon: Building2, permission: 'setup.manage' },
      { title: "Sites", href: "/setup/sites", icon: MapPinHouse, permission: 'setup.manage' },
      { title: "Locations", href: "/setup/locations", icon: MapPin, permission: 'setup.manage' },
      { title: "Categories", href: "/setup/categories", icon: Tags, permission: 'setup.manage' },
      { title: "Departments", href: "/setup/departments", icon: Network, permission: 'setup.manage' },
      { title: "Manage Dashboard", href: "/setup/manage-dashboard", icon: LayoutPanelLeft, permission: 'setup.manage' },
    ]
  },

  // Help & Support (flattened)
  {
    id: 'help',
    label: "Help & Support",
    icon: HelpCircle,
    items: [
      { title: "About Us", href: "/help/about", icon: Info, permission: null },
      { title: "Contact Us", href: "/help/contact", icon: Mail, permission: null },
      { title: "Terms of Service", href: "/help/terms", icon: Scale, permission: null },
      { title: "Privacy Policy", href: "/help/privacy", icon: Shield, permission: null },
      { title: "Videos", href: "/help/videos", icon: PlayCircle, permission: null },
      { title: "User Reviews", href: "/help/reviews", icon: Star, permission: null },
      { title: "Changelog", href: "/help/changelog", icon: ListOrdered, permission: null },
    ]
  }
])

// RBAC helpers
const auth = computed(() => (page.props as any)?.auth || {})
const userPerms = computed<string[]>(() => auth.value?.permissions || [])
const userRoles = computed<string[]>(() => auth.value?.roles || [])

const hasPermission = (perm?: string | string[] | null): boolean => {
  // Admins see all items regardless of explicit permission flags
  if (userRoles.value.includes('Admin')) return true
  if (!perm) return true
  if (Array.isArray(perm)) return perm.some(p => userPerms.value.includes(p))
  return userPerms.value.includes(perm)
}
const hasRole = (role?: string | null): boolean => {
  if (!role) return true
  return userRoles.value.includes(role)
}

const filterItems = (items: Item[]): Item[] =>
  items.filter(i => hasPermission(i.permission) && hasRole(i.role))

const groupsToRender = computed(() =>
  sidebarGroups.value
    .map(g => ({ ...g, items: filterItems(g.items) }))
    .filter(g => g.items.length > 0)
)

// Limit 2 open groups unless "Expand All" is on
const toggleGroup = (groupLabel: string, event?: Event) => {
  if (event) { event.stopPropagation(); event.preventDefault() }

  const i = openGroups.value.indexOf(groupLabel)
  if (i > -1) {
    openGroups.value.splice(i, 1)
    areAllGroupsExpanded.value = false
  } else {
    if (!areAllGroupsExpanded.value && openGroups.value.length >= 2) {
      openGroups.value.shift()
    }
    openGroups.value.push(groupLabel)
  }

  nextTick(() => {
    if (areAllGroupsExpanded.value) {
      openGroups.value = groupsToRender.value.map((g: Group) => g.label)
    }
  })
}

const toggleAllGroups = (event?: Event) => {
  if (event) { event.stopPropagation(); event.preventDefault() }
  if (areAllGroupsExpanded.value) {
    openGroups.value = []
    areAllGroupsExpanded.value = false
  } else {
    openGroups.value = groupsToRender.value.map((g: Group) => g.label)
    areAllGroupsExpanded.value = true
  }
}

// Get current route to determine active link
const currentRoute = computed(() => {
  return page.url
})

// Check if a link is active
const isLinkActive = (href: string) => {
  // Exact match
  if (currentRoute.value === href) return true
  
  // For index routes, check if current route starts with href
  if (href !== '/' && currentRoute.value.startsWith(href)) return true
  
  return false
}

onMounted(() => {
  loadSidebarState()
  restoreScroll()
  // router.on('before', onBeforeNav)
  // router.on('success', onSuccessNav)
})
onBeforeUnmount(() => {
  // router.off('before', onBeforeNav)
  // router.off('success', onSuccessNav)
  saveScroll()
})

const footerNavItems: NavItem[] = []
</script>

<template>
  <Sidebar
    collapsible="icon"
    v-model:collapsed="isSidebarCollapsed"
    class="sidebar bg-sidebar text-sidebar-foreground"
  >
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link href="/dashboard">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>

        <!-- Standalone Dashboard button -->
        <SidebarMenuItem>
          <Link href="/dashboard">
            <SidebarMenuButton class="gap-2 px-3 py-2 text-sm hover:bg-muted/30 rounded-md w-full flex items-center font-medium">
              <LayoutDashboard class="h-4 w-4 flex-shrink-0" />
              <span class="truncate">Dashboard</span>
            </SidebarMenuButton>
          </Link>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <!-- REAL DOM SCROLL CONTAINER -->
      <div ref="scrollContainer" class="h-full overflow-y-auto pr-1">
        <SidebarMenu>
          <div
            v-for="group in groupsToRender"
            :key="group.id"
            class="mb-1"
          >
            <div class="group/collapsible">
              <!-- Group header -->
              <div
                @click="toggleGroup(group.label, $event)"
                class="font-medium text-sidebar-foreground hover:bg-accent hover:text-accent-foreground rounded-md transition-colors duration-150 justify-between px-3 py-2 w-full flex items-center gap-2 cursor-pointer"
                :title="group.label"
              >
                <div class="flex items-center gap-2 min-w-0">
                  <component :is="group.icon" class="h-4 w-4 flex-shrink-0" />
                  <span class="truncate">{{ group.label }}</span>
                  <span v-if="group.id==='alerts' && inventoryAlertCount>0" class="ml-1 inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-semibold text-white">
                    {{ inventoryAlertCount }}
                  </span>
                </div>
                <ChevronRight
                  class="ml-auto h-4 w-4 transition-transform duration-200 flex-shrink-0"
                  :class="{ 'rotate-90': openGroups.includes(group.label) }"
                />
              </div>

              <!-- Group body -->
              <div
                v-if="openGroups.includes(group.label)"
                class="overflow-hidden transition-all duration-200 ease-in-out"
                :class="{
                  'max-h-screen opacity-100': openGroups.includes(group.label),
                  'max-h-0 opacity-0': !openGroups.includes(group.label)
                }"
              >
                <SidebarMenu class="pl-4 mt-1 space-y-1">
                  <SidebarMenuItem v-for="item in group.items" :key="item.title">
                    <Link
                      :href="item.href || '#'"
                      class="gap-2 px-2 py-1.5 text-sm hover:bg-muted/30 rounded-md w-full flex items-center"
                      :class="{ 'bg-muted/60 font-medium': isLinkActive(item.href) }"
                    >
                      <SidebarMenuButton>
                        <component :is="item.icon" class="h-4 w-4 flex-shrink-0" />
                        <span class="truncate">{{ item.title }}</span>
                      </SidebarMenuButton>
                    </Link>
                  </SidebarMenuItem>
                </SidebarMenu>
              </div>
            </div>
          </div>
        </SidebarMenu>
      </div>
    </SidebarContent>

    <SidebarFooter class="border-t pt-2">
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton
            @click.stop="(e: Event) => toggleAllGroups(e)"
            class="w-full justify-between px-2 py-2 text-sm hover:bg-muted/30 rounded-md"
            :aria-expanded="areAllGroupsExpanded"
          >
            <div class="flex items-center gap-2">
              <component :is="areAllGroupsExpanded ? Minimize2 : Maximize2" class="h-4 w-4 flex-shrink-0" />
              <span class="truncate">{{ areAllGroupsExpanded ? 'Collapse All' : 'Expand All' }}</span>
            </div>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>

      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>

<style scoped>
/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Collapsed sidebar polish */
.sidebar[data-state="collapsed"] .truncate { display: none; }
.sidebar[data-state="collapsed"] .flex-shrink-0 { margin: 0 auto; }
.sidebar[data-state="collapsed"] .flex.items-center.gap-2 { justify-content: center; }
.sidebar[data-state="collapsed"] .min-w-0 { min-width: auto; }
</style>
