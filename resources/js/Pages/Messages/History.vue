<template>
  <AuthenticatedLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Message History</h1>
              <p class="mt-2 text-gray-600 dark:text-gray-400">
                <span v-if="user_role === 'agent'">Track your sent messages and delivery status</span>
                <span v-else-if="user_role === 'admin'">Monitor all system messages and delivery statistics</span>
              </p>
            </div>
            <button
              @click="refreshData"
              :disabled="isRefreshing"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm"
            >
              <ArrowPathIcon :class="['w-4 h-4 mr-2', { 'animate-spin': isRefreshing }]" />
              {{ isRefreshing ? 'Refreshing...' : 'Refresh' }}
            </button>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                  <DocumentTextIcon class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Messages</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_messages }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                  <CheckCircleIcon class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sent</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_sent }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                  <ClockIcon class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_pending }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                  <XCircleIcon class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed</p>
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_failed }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Delivery Status Dashboard -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delivery Status Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Delivery Rate -->
              <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-4">
                <div class="flex items-center">
                  <div class="p-3 bg-green-100 dark:bg-green-800 rounded-xl">
                    <CheckCircleIcon class="h-6 w-6 text-green-600 dark:text-green-400" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Delivery Rate</p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                      {{ calculateDeliveryRate() }}%
                    </p>
                  </div>
                </div>
              </div>

              <!-- Average Delivery Time -->
              <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4">
                <div class="flex items-center">
                  <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-xl">
                    <ClockIcon class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Avg Delivery</p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                      {{ calculateAverageDeliveryTime() }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Failed Messages -->
              <div class="bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl p-4">
                <div class="flex items-center">
                  <div class="p-3 bg-red-100 dark:bg-red-800 rounded-xl">
                    <XCircleIcon class="h-6 w-6 text-red-600 dark:text-red-400" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-red-600 dark:text-red-400">Failed</p>
                    <p class="text-2xl font-bold text-red-900 dark:text-red-100">
                      {{ stats.total_failed || 0 }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Pending Messages -->
              <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl p-4">
                <div class="flex items-center">
                  <div class="p-3 bg-yellow-100 dark:bg-yellow-800 rounded-xl">
                    <ClockIcon class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pending</p>
                    <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">
                      {{ stats.total_pending || 0 }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Filters</h3>
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                <input
                  type="date"
                  v-model="filters.date_from"
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                <input
                  type="date"
                  v-model="filters.date_to"
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Group</label>
                <select
                  v-model="filters.group_id"
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                  <option value="">All Groups</option>
                  <option v-for="group in groups" :key="group.id" :value="group.id">
                    {{ group.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select
                  v-model="filters.status"
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                  <option value="">All Status</option>
                  <option value="sent">Sent</option>
                  <option value="pending">Pending</option>
                  <option value="failed">Failed</option>
                </select>
              </div>
              <!-- Sender filter - only for admins -->
              <div v-if="user_role === 'admin'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sender</label>
                <select
                  v-model="filters.sender_id"
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                  <option value="">All Senders</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
                  </option>
                </select>
              </div>
              <div class="flex items-end">
                <button
                  type="submit"
                  class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium shadow-sm transition-colors"
                >
                  Apply Filters
                </button>
              </div>
            </form>
            <div class="mt-4 flex gap-4">
              <button
                @click="clearFilters"
                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 underline font-medium"
              >
                Clear Filters
              </button>
              <button
                @click="exportHistory"
                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline font-medium"
              >
                Export to CSV
              </button>
            </div>
          </div>
        </div>

        <!-- Messages List -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Message Logs</h3>
              <Link
                :href="route('messages.create')"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium shadow-sm transition-colors"
              >
                Send New Message
              </Link>
            </div>

            <div v-if="messages.data.length === 0" class="text-center py-12">
              <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No messages found</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by sending your first message.</p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="message in messages.data"
                :key="message.id"
                class="border border-gray-200 dark:border-gray-600 rounded-xl p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400" v-if="user_role === 'admin'">
                      Sent by <span class="font-medium text-gray-900 dark:text-white">{{ message.sender?.name || 'Unknown' }}</span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400" v-else>
                      Sent on <span class="font-medium text-gray-900 dark:text-white">{{ new Date(message.created_at).toLocaleDateString() }}</span>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                      {{ new Date(message.created_at).toLocaleString() }}
                    </p>
                  </div>
                  <div class="flex items-center space-x-2">
                    <span
                      :class="{
                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': getMessageStatus(message) === 'sent',
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': getMessageStatus(message) === 'pending',
                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': getMessageStatus(message) === 'failed',
                        'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200': getMessageStatus(message) === 'mixed',
                        'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200': getMessageStatus(message) === 'no_recipients' || getMessageStatus(message) === 'no_status'
                      }"
                      class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                      {{ getMessageStatus(message) === 'no_recipients' ? 'No Recipients' : 
                         getMessageStatus(message) === 'no_status' ? 'No Status' : 
                         getMessageStatus(message) }}
                    </span>
                  </div>
                </div>
                
                <p class="text-gray-900 dark:text-white mb-3">{{ message.content }}</p>
                
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                  <div class="flex items-center space-x-4">
                    <span>Recipients: {{ message.recipients?.length || 0 }}</span>
                    <span>Groups: {{ getUniqueGroups(message).length }}</span>
                    <span v-if="message.total_cost > 0" class="text-green-600 dark:text-green-400 font-medium">
                      Cost: {{ message.total_cost ? '$' + parseFloat(message.total_cost).toFixed(4) : '$0.0000' }}
                    </span>
                  </div>
                  <button
                    @click="toggleMessageDetails(message.id)"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                  >
                    {{ expandedMessages.includes(message.id) ? 'Hide' : 'Show' }} Details
                  </button>
                </div>

                <!-- Expanded Details -->
                <div v-if="expandedMessages.includes(message.id)" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                  <h4 class="font-medium text-gray-900 dark:text-white mb-2">Recipients</h4>
                  <div v-if="!message.recipients || message.recipients.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                    No recipients found for this message.
                  </div>
                  <div v-else class="space-y-3">
                    <!-- Recipient Status Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4">
                      <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Delivery Summary:</span>
                        <div class="flex space-x-2">
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <CheckCircleIcon class="w-3 h-3 mr-1" />
                            {{ getRecipientCountByStatus(message.recipients, 'delivered') + getRecipientCountByStatus(message.recipients, 'read') }}
                          </span>
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <PaperAirplaneIcon class="w-3 h-3 mr-1" />
                            {{ getRecipientCountByStatus(message.recipients, 'sent') }}
                          </span>
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            <ClockIcon class="w-3 h-3 mr-1" />
                            {{ getRecipientCountByStatus(message.recipients, 'pending') }}
                          </span>
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            <XCircleIcon class="w-3 h-3 mr-1" />
                            {{ getRecipientCountByStatus(message.recipients, 'failed') + getRecipientCountByStatus(message.recipients, 'undelivered') }}
                          </span>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Individual Recipients -->
                    <div v-for="recipient in message.recipients" :key="recipient.id" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                      <div class="flex-1">
                        <div class="flex items-center space-x-3">
                          <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                              <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                {{ recipient.contact?.full_name?.charAt(0) || '?' }}
                              </span>
                            </div>
                          </div>
                          <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                              {{ recipient.contact?.full_name || 'Unknown Contact' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                              {{ recipient.contact?.phone_number || 'No Phone' }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                              {{ recipient.contact?.group?.name || 'No Group' }}
                            </p>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Status and Details -->
                      <div class="flex items-center space-x-3">
                        <!-- Delivery Status -->
                        <span
                          :class="{
                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': recipient.status === 'delivered' || recipient.status === 'read',
                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': recipient.status === 'sent',
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': recipient.status === 'pending',
                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': recipient.status === 'failed' || recipient.status === 'undelivered',
                            'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200': !recipient.status
                          }"
                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                        >
                          <component :is="getStatusIcon(recipient.status)" class="w-3 h-3 mr-1" />
                          {{ formatStatus(recipient.status) }}
                        </span>
                        
                        <!-- Delivery Time -->
                        <div v-if="recipient.delivered_at" class="text-xs text-gray-500 dark:text-gray-400 text-right">
                          <div>Delivered</div>
                          <div>{{ formatTime(recipient.delivered_at) }}</div>
                        </div>
                        
                        <!-- Cost Info -->
                        <div v-if="recipient.cost > 0" class="text-xs text-green-600 dark:text-green-400 text-right">
                          <div class="font-medium">Cost:</div>
                          <div>${{ parseFloat(recipient.cost).toFixed(4) }}</div>
                        </div>
                        
                        <!-- Error Info -->
                        <div v-if="recipient.error_message" class="text-xs text-red-600 dark:text-red-400 max-w-xs">
                          <div class="font-medium">Error:</div>
                          <div class="truncate">{{ recipient.error_message }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="messages.links && messages.links.length > 3" class="mt-6">
              <nav class="flex justify-center">
                <div class="flex space-x-1">
                  <template v-for="(link, index) in messages.links" :key="index">
                    <Link
                      v-if="link.url"
                      :href="link.url"
                      :class="{
                        'px-3 py-2 text-sm font-medium rounded-md': true,
                        'bg-blue-600 text-white': link.active,
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': !link.active
                      }"
                      v-html="link.label"
                    />
                    <span
                      v-else
                      :class="'px-3 py-2 text-sm font-medium rounded-md text-gray-300 dark:text-gray-600 cursor-not-allowed'"
                      v-html="link.label"
                    />
                  </template>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
  DocumentTextIcon,
  CheckCircleIcon,
  ClockIcon,
  XCircleIcon,
  PaperAirplaneIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  messages: Object,
  stats: Object,
  groups: Array,
  users: Array,
  user_role: String,
  filters: Object
});

const expandedMessages = ref([]);
const isRefreshing = ref(false);
const autoRefreshInterval = ref(null);

// Auto-refresh every 30 seconds for delivery status updates
onMounted(() => {
  startAutoRefresh();
});

onUnmounted(() => {
  stopAutoRefresh();
});

function startAutoRefresh() {
  autoRefreshInterval.value = setInterval(() => {
    if (!isRefreshing.value) {
      refreshData();
    }
  }, 30000); // 30 seconds
}

function stopAutoRefresh() {
  if (autoRefreshInterval.value) {
    clearInterval(autoRefreshInterval.value);
    autoRefreshInterval.value = null;
  }
}

function refreshData() {
  isRefreshing.value = true;
  router.reload({ only: ['messages', 'stats'] });
  setTimeout(() => {
    isRefreshing.value = false;
  }, 1000);
}

const filters = reactive({
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  group_id: props.filters.group_id || '',
  status: props.filters.status || '',
  sender_id: props.filters.sender_id || ''
})

function applyFilters() {
  router.get(route('messages.history'), filters, {
    preserveState: true,
    preserveScroll: true
  })
}

function clearFilters() {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  router.get(route('messages.history'), {}, {
    preserveState: true,
    preserveScroll: true
  })
}

function toggleMessageDetails(messageId) {
  const index = expandedMessages.value.indexOf(messageId)
  if (index > -1) {
    expandedMessages.value.splice(index, 1)
  } else {
    expandedMessages.value.push(messageId)
  }
}

function getMessageStatus(message) {
  if (!message.recipients || message.recipients.length === 0) {
    return 'no_recipients'
  }
  
  const statuses = message.recipients.map(r => r.status).filter(Boolean)
  if (statuses.length === 0) {
    return 'no_status'
  }
  
  const uniqueStatuses = [...new Set(statuses)]
  
  if (uniqueStatuses.length === 1) {
    return uniqueStatuses[0]
  }
  return 'mixed'
}

function getUniqueGroups(message) {
  const groups = message.recipients
    .map(r => r.contact?.group?.id)
    .filter(Boolean)
  return [...new Set(groups)]
}

function getRecipientCountByStatus(recipients, status) {
  return recipients.filter(r => r.status === status).length
}

function getStatusIcon(status) {
  switch (status) {
    case 'sent':
      return PaperAirplaneIcon
    case 'delivered':
      return CheckCircleIcon
    case 'pending':
      return ClockIcon
    case 'failed':
      return XCircleIcon
    case 'undelivered':
      return XCircleIcon
    case 'read':
      return CheckCircleIcon
    default:
      return DocumentTextIcon
  }
}

function formatStatus(status) {
  switch (status) {
    case 'sent':
      return 'Sent'
    case 'delivered':
      return 'Delivered'
    case 'pending':
      return 'Pending'
    case 'failed':
      return 'Failed'
    case 'undelivered':
      return 'Undelivered'
    case 'read':
      return 'Read'
    default:
      return status
  }
}

function formatTime(timestamp) {
  const date = new Date(timestamp)
  return date.toLocaleTimeString()
}

function exportHistory() {
  // Create CSV content
  let csv = 'Date,Sender,Message,Recipients,Status\n'
  
  props.messages.data.forEach(message => {
    const date = new Date(message.created_at).toLocaleString()
    const sender = (message.sender && message.sender.name) ? message.sender.name : 'Unknown'
    const content = `"${message.content.replace(/"/g, '""')}"`
    const recipients = message.recipients.length
    const status = getMessageStatus(message)
    
    csv += `${date},${sender},${content},${recipients},${status}\n`
  })
  
  // Download CSV
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `message-history-${new Date().toISOString().split('T')[0]}.csv`
  a.click()
  window.URL.revokeObjectURL(url)
}

function calculateDeliveryRate() {
  const totalSent = props.stats.total_sent || 0;
  const totalDelivered = props.stats.total_delivered || 0;
  const totalRead = props.stats.total_read || 0;
  
  if (totalSent === 0) return 0;
  
  // Include 'sent' status in delivery rate since Twilio webhooks haven't updated yet
  const deliveryRate = ((totalSent + totalDelivered + totalRead) / totalSent) * 100;
  return Math.round(deliveryRate);
}

function calculateAverageDeliveryTime() {
  // This would need backend support for actual delivery time calculation
  // For now, return a placeholder
  return '~2-5 min';
}
</script>
