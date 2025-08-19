<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
  UserGroupIcon, 
  ChatBubbleLeftRightIcon, 
  SquaresPlusIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  ChartBarIcon,
  PlusIcon,
  EyeIcon
} from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  stats: Object,
  recentMessages: Array,
  recentContacts: Array,
  recentGroups: Array,
  messageStats: Array,
  topGroups: Array,
  user: Object,
});

// Calculate delivery rate
const deliveryRate = props.stats.total_recipients > 0 
  ? Math.round((props.stats.successful_deliveries / props.stats.total_recipients) * 100)
  : 0;

// Format date for display
function formatDate(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now - date);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 1) return 'Yesterday';
  if (diffDays < 7) return `${diffDays} days ago`;
  return date.toLocaleDateString();
}
</script>

<template>
  <AuthenticatedLayout :user="user">
    <div class="max-w-7xl mx-auto py-4 sm:py-6 lg:py-8 px-4 sm:px-6 lg:px-8">
      <!-- Enhanced Welcome Header with SwiftNotify Branding -->
      <div class="mb-8 bg-gradient-to-r from-blue-600/10 to-purple-600/10 backdrop-blur-sm border border-blue-200/20 rounded-2xl p-8">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-4">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-lg opacity-75 animate-pulse"></div>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl sm:text-4xl font-black text-gray-800 dark:text-white mb-1">
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">SwiftNotify</span>
              </h1>
              <p class="text-lg text-gray-600 dark:text-gray-400 font-medium">Agent Dashboard</p>
            </div>
          </div>
          <div class="text-right">
            <div class="flex items-center justify-end mb-2">
              <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Welcome back, {{ user.name }}! 👋</h2>
              <span v-if="user.email_verified_at" class="ml-3 inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </span>
            </div>
            <p class="text-gray-600 dark:text-gray-400">Here's what's happening with your SMS campaigns today.</p>
          </div>
        </div>
      </div>

      <!-- Email Verification Alert -->
      <div v-if="!$page.props.auth.user.email_verified_at" class="mb-6 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6 shadow-lg">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
          </div>
          <div class="ml-4 flex-1">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">
              ⚠️ Email Verification Required
            </h3>
            <p class="text-yellow-700 mb-4">
              Your email address <strong>{{ $page.props.auth.user.email }}</strong> hasn't been verified yet. 
              Please verify your email to unlock all SwiftNotify features and ensure you receive important notifications.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
              <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Resend Verification Email
              </Link>
              <Link
                :href="route('profile.edit')"
                class="inline-flex items-center px-4 py-2 border border-yellow-300 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 bg-white dark:bg-gray-800 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 text-sm font-medium rounded-lg transition-colors duration-200"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Go to Profile Settings
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-8">
        <Link 
          :href="route('messages.index')" 
          class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 sm:px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center justify-center transition-all duration-200 hover:shadow-xl hover:scale-105 w-full sm:w-auto"
        >
          <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" /> Send SMS Alert
        </Link>
        <Link 
          :href="route('contacts.index')" 
          class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white px-4 sm:px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center justify-center transition-all duration-200 hover:shadow-xl hover:scale-105 w-full sm:w-auto"
        >
          <PlusIcon class="w-5 h-5 mr-2" /> Add Contact
        </Link>
        <Link 
          :href="route('groups.index')" 
          class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-4 sm:px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center justify-center transition-all duration-200 hover:shadow-xl hover:scale-105 w-full sm:w-auto"
        >
          <SquaresPlusIcon class="w-5 h-5 mr-2" /> Manage Groups
        </Link>
      </div>

      <!-- KPI Cards Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 sm:gap-6 mb-8">
        <!-- Total Contacts -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Contacts</p>
              <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ stats.contacts }}</p>
            </div>
            <div class="p-2 bg-blue-500 rounded-lg">
              <UserGroupIcon class="w-8 h-8 text-white" />
            </div>
          </div>
        </div>

        <!-- Total SMS Cost -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-green-600 dark:text-green-400">Total SMS Cost</p>
              <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ stats.total_cost || '$0.00' }}</p>
            </div>
            <div class="p-2 bg-green-500 rounded-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Contact Groups -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl shadow-lg p-6 border-l-4 border-indigo-500 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Contact Groups</p>
              <p class="text-3xl font-bold text-indigo-900 dark:text-indigo-100">{{ stats.groups }}</p>
            </div>
            <div class="p-2 bg-indigo-500 rounded-lg">
              <SquaresPlusIcon class="w-8 h-8 text-white" />
            </div>
          </div>
        </div>

        <!-- Messages Sent -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-green-600 dark:text-green-400">Messages Sent</p>
              <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ stats.messages }}</p>
            </div>
            <div class="p-2 bg-green-500 rounded-lg">
              <ChatBubbleLeftRightIcon class="w-8 h-8 text-white" />
            </div>
          </div>
        </div>

        <!-- Delivery Rate -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-all duration-200 transform hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Delivery Rate</p>
              <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ deliveryRate }}%</p>
            </div>
            <div class="p-2 bg-purple-500 rounded-lg">
              <ChartBarIcon class="w-8 h-8 text-white" />
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Recent Messages + Activity Chart -->
        <div class="lg:col-span-8 space-y-6">
          <!-- Recent Messages Table -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2 text-blue-500" />
                Recent Messages
              </h2>
              <Link 
                :href="route('messages.history')" 
                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium flex items-center"
              >
                View All
                <EyeIcon class="w-4 h-4 ml-1" />
              </Link>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Message</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sent At</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recipients</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Success Rate</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="msg in recentMessages" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-4">
                      <div class="max-w-xs">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ msg.content }}</p>
                      </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                      {{ formatDate(msg.sent_at) }}
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                      <div class="flex items-center space-x-2">
                        <span class="text-green-600 dark:text-green-400 font-medium">{{ msg.sent_count }}</span>
                        <span class="text-gray-400 dark:text-gray-500">/</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ msg.total_recipients }}</span>
                      </div>
                    </td>
                    <td class="px-4 py-4">
                      <div class="flex items-center">
                        <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-2">
                          <div 
                            class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                            :style="{ width: msg.success_rate + '%' }"
                          ></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ msg.success_rate }}%</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="recentMessages.length === 0">
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                      <ChatBubbleLeftRightIcon class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
                      <p>No messages sent yet.</p>
                      <Link 
                        :href="route('messages.index')" 
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium"
                      >
                        Send your first message
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Message Statistics Chart -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
              <ChartBarIcon class="w-5 h-5 mr-2 text-purple-500" />
              Message Activity (Last 7 Days)
            </h2>
            <div class="h-64 flex items-end justify-center space-x-2">
              <div v-for="stat in messageStats" :key="stat.date" class="flex flex-col items-center">
                <div 
                  class="w-8 bg-purple-500 rounded-t transition-all duration-300 hover:bg-purple-600"
                  :style="{ height: (stat.count * 20) + 'px' }"
                  :title="`${stat.count} messages on ${stat.date}`"
                ></div>
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ new Date(stat.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
              </div>
              <div v-if="messageStats.length === 0" class="text-gray-400 dark:text-gray-500 text-center py-8">
                <ChartBarIcon class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
                <p>No message data available</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Delivery Stats, Top Groups, SMS Cost, Recent Activity -->
        <div class="lg:col-span-4 space-y-6">
          <!-- Delivery Statistics -->
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl shadow-lg p-6 border border-blue-200/20">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4 flex items-center">
              <div class="p-2 bg-blue-500 rounded-lg mr-3">
                <ChartBarIcon class="w-5 h-5 text-white" />
              </div>
              Delivery Statistics
            </h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <div class="flex items-center">
                  <CheckCircleIcon class="w-5 h-5 text-green-500 mr-2" />
                  <span class="text-sm font-medium text-green-700 dark:text-green-300">Successful</span>
                </div>
                <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ stats.successful_deliveries }}</span>
              </div>
              <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <div class="flex items-center">
                  <XCircleIcon class="w-5 h-5 text-red-500 mr-2" />
                  <span class="text-sm font-medium text-red-700 dark:text-red-300">Failed</span>
                </div>
                <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ stats.failed_deliveries }}</span>
              </div>
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center">
                  <ClockIcon class="w-5 h-5 text-gray-500 mr-2" />
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Recipients</span>
                </div>
                <span class="text-lg font-bold text-gray-600 dark:text-gray-400">{{ stats.total_recipients }}</span>
              </div>
            </div>
          </div>

          <!-- Top Groups -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
              <SquaresPlusIcon class="w-5 h-5 mr-2 text-indigo-500" />
              Top Groups
            </h3>
            <div class="space-y-3">
              <div v-for="group in topGroups" :key="group.id" class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div>
                  <p class="font-medium text-gray-800 dark:text-white">{{ group.name }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ group.area_name }}</p>
                </div>
                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ group.contacts_count }}</span>
              </div>
              <div v-if="topGroups.length === 0" class="text-gray-400 dark:text-gray-500 text-center py-4">
                No groups yet
              </div>
            </div>
          </div>

          <!-- SMS Cost Breakdown -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
              SMS Cost Tracking
            </h3>
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                  <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-green-700 dark:text-green-300">This Month</span>
                    <span class="text-lg font-bold text-green-800 dark:text-green-200">{{ stats.monthly_cost || '$0.00' }}</span>
                  </div>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                  <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Avg. Cost/Message</span>
                    <span class="text-lg font-bold text-blue-800 dark:text-blue-200">{{ stats.avg_cost_per_message || '$0.00' }}</span>
                  </div>
                </div>
              </div>
              <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center justify-between">
                  <span class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Cost per SMS (160 chars)</span>
                  <span class="text-lg font-bold text-yellow-800 dark:text-yellow-200">$0.0079</span>
                </div>
                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Based on Twilio US rates</p>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
              <ClockIcon class="w-5 h-5 mr-2 text-green-500" />
              Recent Activity
            </h3>
            <div class="space-y-3">
              <div v-for="contact in recentContacts" :key="contact.id" class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-800 dark:text-white">{{ contact.full_name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(contact.created_at) }}</p>
                </div>
              </div>
              <div v-if="recentContacts.length === 0" class="text-gray-400 dark:text-gray-500 text-center py-4">
                No recent contacts
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template> 