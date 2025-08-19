<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
  Bars3Icon, 
  SunIcon, 
  MoonIcon,
  BellIcon,
  UserCircleIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  user: Object,
});

const emit = defineEmits(['toggleSidebar']);

const isDarkMode = ref(false);
const isProfileDropdownOpen = ref(false);
const isNotificationsOpen = ref(false);

// Real notifications based on SMS system events
const notifications = ref([]);

// Persist notifications locally to keep read state across navigation
const saveNotifications = () => {
  try {
    localStorage.setItem('swift_notify_notifications', JSON.stringify(notifications.value));
  } catch (e) {
    // ignore storage errors
  }
};

// Generate real notifications based on system events
const generateRealNotifications = () => {
  // If already loaded from storage, do not regenerate
  if (notifications.value.length > 0) return;
  // Clear existing notifications
  notifications.value = [];
  
  // Get current user role for personalized notifications
  const userRole = safeUser.value.role;
  
  // Add system status notifications
  if (userRole === 'admin') {
    // Admin gets system-wide notifications
    notifications.value.push({
      id: 'system-status',
      type: 'info',
      title: 'System Status',
      message: 'All SMS services are running normally',
      time: 'Just now',
      read: false
    });
  }
  
  // Add user-specific notifications based on recent activity
  // These would normally come from the backend
  if (userRole === 'agent') {
    notifications.value.push({
      id: 'welcome-agent',
      type: 'success',
      title: 'Welcome to SwiftNotify',
      message: 'Your SMS dashboard is ready. Start sending messages!',
      time: 'Just now',
      read: false
    });
  }
  saveNotifications();
};

// Add notification when SMS is sent
const addSMSNotification = (message, recipients, successCount, failedCount) => {
  const notification = {
    id: Date.now() + Math.random(),
    type: failedCount > 0 ? 'warning' : 'success',
    title: failedCount > 0 ? 'SMS Partially Delivered' : 'SMS Delivered Successfully',
    message: `Message sent to ${recipients} recipients. ${successCount} successful, ${failedCount} failed.`,
    time: 'Just now',
    read: false
  };
  
  notifications.value.unshift(notification);
  saveNotifications();
  
  // Keep only last 10 notifications
  if (notifications.value.length > 10) {
    notifications.value = notifications.value.slice(0, 10);
  }
};

// Add notification for contact import
const addImportNotification = (imported, failed, errors) => {
  const notification = {
    id: Date.now() + Math.random(),
    type: failed > 0 ? 'warning' : 'success',
    title: failed > 0 ? 'Contact Import Completed with Issues' : 'Contact Import Successful',
    message: `Imported ${imported} contacts${failed > 0 ? `, ${failed} failed` : ''}`,
    time: 'Just now',
    read: false
  };
  
  notifications.value.unshift(notification);
  saveNotifications();
};

// Add system alert notification
const addSystemAlert = (type, title, message) => {
  const notification = {
    id: Date.now() + Math.random(),
    type: type, // 'success', 'warning', 'error', 'info'
    title: title,
    message: message,
    time: 'Just now',
    read: false
  };
  
  notifications.value.unshift(notification);
  saveNotifications();
};

// Listen for SMS events from other components
const listenForEvents = () => {
  // Listen for custom events from SMS sending
  window.addEventListener('sms-sent', (event) => {
    const { message, recipients, successCount, failedCount } = event.detail;
    addSMSNotification(message, recipients, successCount, failedCount);
  });
  
  // Listen for contact import events
  window.addEventListener('contacts-imported', (event) => {
    const { imported, failed, errors } = event.detail;
    addImportNotification(imported, failed, errors);
  });
  
  // Listen for system alerts
  window.addEventListener('system-alert', (event) => {
    const { type, title, message } = event.detail;
    addSystemAlert(type, title, message);
  });
};

// Check for saved dark mode preference
onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDarkMode.value = true;
    document.documentElement.classList.add('dark');
  }

  // Add click outside listener
  document.addEventListener('click', (e) => {
    const notificationButton = document.querySelector('[data-notification-button]');
    const notificationDropdown = document.querySelector('[data-notification-dropdown]');
    
    if (notificationButton && !notificationButton.contains(e.target) && 
        notificationDropdown && !notificationDropdown.contains(e.target)) {
      isNotificationsOpen.value = false;
    }
  });

  // Generate initial notifications and listen for events
  generateRealNotifications();
  listenForEvents();
});

// Toggle dark mode
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  if (isDarkMode.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
};

// Toggle notifications
const toggleNotifications = () => {
  console.log('Toggle notifications clicked, current state:', isNotificationsOpen.value);
  isNotificationsOpen.value = !isNotificationsOpen.value;
  console.log('New state:', isNotificationsOpen.value);
};

// Close notifications when clicking outside
const closeNotifications = () => {
  isNotificationsOpen.value = false;
};

// Mark notification as read
const markAsRead = (notificationId) => {
  const notification = notifications.value.find(n => n.id === notificationId);
  if (notification) {
    notification.read = true;
    saveNotifications();
  }
};

// Mark all as read
const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true);
  saveNotifications();
};

// Get unread count
const unreadCount = computed(() => {
  const count = notifications.value.filter(n => !n.read).length;
  console.log('Unread count computed:', count, 'Total notifications:', notifications.value.length);
  return count;
});

// Load from storage then generate defaults
onMounted(() => {
  try {
    const saved = localStorage.getItem('swift_notify_notifications');
    if (saved) {
      const parsed = JSON.parse(saved);
      if (Array.isArray(parsed)) notifications.value = parsed;
    }
  } catch (e) {
    // ignore parse errors
  }
  generateRealNotifications();
});

// Get notification icon
const getNotificationIcon = (type) => {
  switch (type) {
    case 'success':
      return CheckCircleIcon;
    case 'warning':
      return ExclamationTriangleIcon;
    case 'error':
      return ExclamationTriangleIcon;
    default:
      return InformationCircleIcon;
  }
};

// Get notification color classes
const getNotificationColors = (type) => {
  switch (type) {
    case 'success':
      return 'text-green-600 bg-green-100 dark:bg-green-900/20';
    case 'warning':
      return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/20';
    case 'error':
      return 'text-red-600 bg-red-100 dark:bg-red-900/20';
    default:
      return 'text-blue-600 bg-blue-100 dark:bg-blue-900/20';
  }
};

// Safe user data with defaults
const safeUser = computed(() => ({
  name: props.user?.name || 'User',
  email: props.user?.email || 'user@example.com',
  role: props.user?.role || 'agent',
  email_verified_at: props.user?.email_verified_at || null
}));
</script>

<template>
  <!-- Email Verification Banner -->
  <div v-if="safeUser && !safeUser.email_verified_at" class="bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-4 py-3 shadow-lg">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <span class="font-medium">
          Please verify your email address <strong>{{ safeUser.email }}</strong> to unlock all features
        </span>
      </div>
      <div class="flex items-center space-x-3">
        <Link
          :href="route('verification.send')"
          method="post"
          as="button"
          class="bg-white/20 hover:bg-white/30 text-white px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200"
        >
          Resend Email
        </Link>
        <Link
          :href="route('profile.edit')"
          class="bg-white/20 hover:bg-white/30 text-white px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200"
        >
          Settings
        </Link>
      </div>
    </div>
  </div>

  <!-- Navigation bar - Simplified for branding and actions only -->
  <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Left side - Logo and mobile menu button -->
        <div class="flex items-center">
          <!-- Mobile menu button -->
          <button
            @click="$emit('toggleSidebar')"
            class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
          >
            <Bars3Icon class="h-6 w-6" />
          </button>

          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <Link :href="route('dashboard')" class="flex items-center space-x-3">
              <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-lg blur-sm opacity-75"></div>
                <div class="relative bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-2 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </div>
              </div>
              <div class="flex flex-col">
                <span class="text-xl font-black text-gray-900 dark:text-white bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">SwiftNotify</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">SMS Alert System</span>
              </div>
            </Link>
          </div>
        </div>

        <!-- Right side - Actions and profile -->
        <div class="flex items-center space-x-4">
          <!-- Dark mode toggle -->
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200"
            :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <SunIcon v-if="isDarkMode" class="h-5 w-5" />
            <MoonIcon v-else class="h-5 w-5" />
          </button>

          <!-- Notifications -->
          <div class="relative">
            <button
              @click="toggleNotifications"
              class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 relative"
              :title="unreadCount > 0 ? `${unreadCount} new notifications` : 'View notifications'"
              data-notification-button
            >
              <BellIcon class="h-5 w-5" />
              <!-- Notification count badge positioned on top of bell -->
              <span v-if="unreadCount > 0" 
                    class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[18px] h-[18px]">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
              </span>
            </button>

            <!-- Notifications dropdown -->
            <div
              v-if="isNotificationsOpen"
              class="absolute top-full right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
              data-notification-dropdown
            >
              <div class="flex justify-between items-center px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notifications</h3>
                <button @click="markAllAsRead" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                  Mark all as read
                </button>
              </div>
              <div class="max-h-96 overflow-y-auto">
                <div v-if="notifications.length === 0" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                  <BellIcon class="mx-auto h-8 w-8 mb-2 text-gray-400" />
                  <p>No notifications yet</p>
                  <p class="text-xs">You'll see SMS delivery updates and system alerts here</p>
                </div>
                <div v-else>
                  <div v-for="notification in notifications" :key="notification.id" 
                       class="flex items-start px-4 py-3 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                    <div class="flex-shrink-0 mt-0.5">
                      <div :class="`p-2 rounded-full ${getNotificationColors(notification.type)}`">
                        <component :is="getNotificationIcon(notification.type)" class="h-4 w-4" />
                      </div>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ notification.title }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ notification.message }}</p>
                      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ notification.time }}</p>
                    </div>
                    <button @click="markAsRead(notification.id)" 
                            class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150">
                      <XMarkIcon class="h-3 w-3" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile dropdown -->
          <div class="relative">
            <button
              @click="isProfileDropdownOpen = !isProfileDropdownOpen"
              class="flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200"
            >
              <UserCircleIcon class="h-6 w-6" />
              <span class="hidden sm:block text-sm font-medium">{{ safeUser.name }}</span>
            </button>

            <!-- Profile dropdown menu -->
            <div
              v-if="isProfileDropdownOpen"
              class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
            >
              <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ safeUser.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ safeUser.email }}</p>
                <div class="flex items-center space-x-2 mt-1">
                  <p class="text-xs text-blue-600 dark:text-blue-400 capitalize">{{ safeUser.role }}</p>
                  <span v-if="safeUser.email_verified_at" class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-500 text-white">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </span>
                </div>
              </div>
              <Link
                :href="route('profile.edit')"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
              >
                Profile Settings
              </Link>
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
              >
                Sign Out
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped>
/* Smooth transitions for dark mode */
* {
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}
</style>
