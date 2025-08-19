<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
  XMarkIcon,
  HomeIcon,
  UserGroupIcon,
  SquaresPlusIcon,
  ChatBubbleLeftRightIcon,
  ClockIcon,
  UsersIcon,
  Cog6ToothIcon,
  ChartBarIcon,
  MapIcon,
  BellIcon,
  CurrencyDollarIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  isOpen: Boolean,
  user: Object,
});

const emit = defineEmits(['close']);

const page = usePage();

// Navigation items with icons and categories
const navigationItems = computed(() => [
  {
    category: 'Main',
    items: [
      { 
        name: 'Dashboard', 
        href: route('dashboard'), 
        icon: HomeIcon,
        description: 'Overview and statistics',
        badge: null
      },
      { 
        name: 'Contacts', 
        href: route('contacts.index'), 
        icon: UserGroupIcon,
        description: 'Manage your contacts',
        badge: null
      },
      { 
        name: 'Groups', 
        href: route('groups.index'), 
        icon: SquaresPlusIcon,
        description: 'Organize contacts by area',
        badge: null
      },
    ]
  },
        {
        category: 'Communication',
        items: [
          { 
            name: 'Send SMS', 
            href: route('messages.index'), 
            icon: ChatBubbleLeftRightIcon,
            description: 'Send alerts to groups',
            badge: null
          },
          { 
            name: 'Message History', 
            href: route('messages.history'), 
            icon: ClockIcon,
            description: 'View message history',
            badge: null
          },
          { 
            name: 'SMS Costs', 
            href: route('sms.costs'), 
            icon: CurrencyDollarIcon,
            description: 'Track SMS spending',
            badge: null
          },
        ]
      }
]);

// Admin-only navigation items
const adminNavigationItems = computed(() => [
  { 
    name: 'User Management', 
    href: route('users.index'), 
    icon: UsersIcon,
    description: 'Manage system users',
    badge: 'Admin'
  },
  { 
    name: 'System Settings', 
    href: route('settings'), 
    icon: Cog6ToothIcon,
    description: 'Configure system settings',
    badge: 'Admin'
  },
]);

const isAdmin = computed(() => props.user?.role === 'admin');

// Safe user data with defaults
const safeUser = computed(() => ({
  name: props.user?.name || 'User',
  email: props.user?.email || 'user@example.com',
  role: props.user?.role || 'agent',
  email_verified_at: props.user?.email_verified_at || null
}));

// Close sidebar
const closeSidebar = () => {
  emit('close');
};

// Check if current page matches navigation item
const isCurrentPage = (href) => {
  return page.url.startsWith(href);
};
</script>

<template>
  <!-- Mobile sidebar overlay -->
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 lg:hidden"
    @click="closeSidebar"
  >
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"></div>
  </div>

  <!-- Sidebar -->
  <div
    :class="[
      'fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r-2 border-gray-200 dark:border-gray-700 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-xl lg:shadow-none',
      isOpen ? 'translate-x-0' : '-translate-x-full'
    ]"
  >
    <!-- Sidebar header -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900/20">
      <!-- Close button for mobile -->
      <button
        @click="closeSidebar"
        class="lg:hidden p-1 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200"
      >
        <XMarkIcon class="h-6 w-6" />
      </button>
    </div>

    <!-- User profile section -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900/20">
      <div class="flex items-center space-x-3">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
          <span class="text-white font-bold text-lg">{{ safeUser.name.charAt(0).toUpperCase() }}</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ safeUser.name }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ safeUser.email }}</p>
          <div class="flex items-center mt-1 space-x-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 capitalize">
              {{ safeUser.role }}
            </span>
            <span v-if="safeUser.email_verified_at" class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-500 text-white">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Email Verification Alert -->
    <div v-if="safeUser && !safeUser.email_verified_at" class="px-6 py-4 border-b border-yellow-200 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20">
      <div class="flex items-start space-x-3">
        <div class="flex-shrink-0 mt-0.5">
          <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1">
            Email Verification Required
          </h4>
          <p class="text-xs text-yellow-700 dark:text-yellow-300 mb-3">
            Please verify your email to unlock all features
          </p>
          <div class="flex flex-col space-y-2">
            <Link
              :href="route('verification.send')"
              method="post"
              as="button"
              class="w-full text-center bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-200"
            >
              Resend Verification Email
            </Link>
            <Link
              :href="route('profile.edit')"
              class="w-full text-center border border-yellow-300 text-yellow-700 dark:text-yellow-300 bg-white dark:bg-gray-800 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-200"
            >
              Go to Settings
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-6 overflow-y-auto">
      <!-- Main Navigation Categories -->
      <div v-for="category in navigationItems" :key="category.category" class="space-y-2">
        <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
          {{ category.category }}
        </h3>
        <div class="space-y-1">
          <Link
            v-for="item in category.items"
            :key="item.name"
            :href="item.href"
            @click="closeSidebar"
            class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md"
            :class="[
              isCurrentPage(item.href)
                ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg transform scale-105'
                : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gradient-to-r hover:from-gray-100 hover:to-blue-100 dark:hover:from-gray-800 dark:hover:to-blue-900/20'
            ]"
          >
            <component 
              :is="item.icon" 
              class="w-5 h-5 mr-3 flex-shrink-0" 
              :class="isCurrentPage(item.href) ? 'text-white' : 'text-gray-400 group-hover:text-blue-500'"
            />
            <div class="flex-1">
              <div class="flex items-center justify-between">
                <span class="font-medium">{{ item.name }}</span>
                <span v-if="item.badge" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                  {{ item.badge }}
                </span>
              </div>
              <p class="text-xs mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                 :class="isCurrentPage(item.href) ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400'">
                {{ item.description }}
              </p>
            </div>
          </Link>
        </div>
      </div>

      <!-- Admin Navigation -->
      <template v-if="isAdmin">
        <div class="pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
          <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
            Administration
          </h3>
          <div class="space-y-1">
            <Link
              v-for="item in adminNavigationItems"
              :key="item.name"
              :href="item.href"
              @click="closeSidebar"
              class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md"
              :class="[
                isCurrentPage(item.href)
                  ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg transform scale-105'
                  : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gradient-to-r hover:from-gray-100 hover:to-purple-100 dark:hover:from-gray-800 dark:hover:to-purple-900/20'
              ]"
            >
              <component 
                :is="item.icon" 
                class="w-5 h-5 mr-3 flex-shrink-0" 
                :class="isCurrentPage(item.href) ? 'text-white' : 'text-gray-400 group-hover:text-purple-500'"
              />
              <div class="flex-1">
                <div class="flex items-center justify-between">
                  <span class="font-medium">{{ item.name }}</span>
                  <span v-if="item.badge" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">
                    {{ item.badge }}
                  </span>
                </div>
                <p class="text-xs mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                   :class="isCurrentPage(item.href) ? 'text-purple-100' : 'text-gray-500 dark:text-gray-400'">
                  {{ item.description }}
                </p>
              </div>
            </Link>
          </div>
        </div>
      </template>
    </nav>

    <!-- Sidebar footer -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900/20">
      <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
        <div class="flex items-center justify-center space-x-2 mb-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          <span class="font-bold text-blue-600 dark:text-blue-400">SwiftNotify</span>
        </div>
        <p class="font-medium">© 2025 SwiftNotify</p>
        <p class="mt-1 text-blue-600 dark:text-blue-400">Built with ❤️ for Tataouine</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

/* Smooth transitions */
* {
  transition: all 0.2s ease-in-out;
}
</style>
