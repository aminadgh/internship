<template>
  <AuthenticatedLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">SMS Cost Analytics</h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            <span v-if="user_role === 'agent'">Track your SMS costs and spending patterns</span>
            <span v-else-if="user_role === 'admin'">Monitor all system SMS costs and spending analytics</span>
          </p>
        </div>
        <!-- Date Range Filter -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Cost Analysis Period</h3>
              <div class="flex items-center space-x-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
                  <input 
                    type="date" 
                    v-model="dateFrom" 
                    @change="updateDateRange"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
                  <input 
                    type="date" 
                    v-model="dateTo" 
                    @change="updateDateRange"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cost Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                  <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cost</p>
                  <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ formatCost(stats.total_cost) }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                  <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Messages</p>
                  <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.total_messages }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                  <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Cost/Message</p>
                  <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ formatCost(stats.avg_cost_per_message) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cost Breakdown Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Cost by Category -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cost by Category</h3>
              <div class="space-y-3">
                <div v-for="category in costByCategory" :key="category.emergency_category" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ formatCategory(category.emergency_category) }}</span>
                  </div>
                  <div class="text-right">
                    <div class="font-semibold text-gray-900 dark:text-white">${{ formatCost(category.total_cost) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ category.message_count }} messages</div>
                  </div>
                </div>
                <div v-if="costByCategory.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                  No category data available
                </div>
              </div>
            </div>
          </div>

          <!-- Cost by Priority -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cost by Priority</h3>
              <div class="space-y-3">
                <div v-for="priority in costByPriority" :key="priority.priority" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <div class="flex items-center space-x-3">
                    <div class="w-3 h-3" :class="getPriorityColor(priority.priority)"></div>
                    <span class="font-medium text-gray-900 dark:text-white capitalize">{{ priority.priority }}</span>
                  </div>
                  <div class="text-right">
                    <div class="font-semibold text-gray-900 dark:text-white">${{ formatCost(priority.total_cost) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ priority.message_count }} messages</div>
                  </div>
                </div>
                <div v-if="costByPriority.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                  No priority data available
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Daily Cost Trend -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8 border border-gray-200 dark:border-gray-700">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Daily Cost Trend</h3>
            <div class="space-y-3">
              <div v-for="day in dailyCosts" :key="day.date" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                  <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                  <span class="font-medium text-gray-900 dark:text-white">{{ formatDate(day.date) }}</span>
                </div>
                <div class="text-right">
                  <div class="font-semibold text-gray-900 dark:text-white">${{ formatCost(day.daily_cost) }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ day.message_count }} messages</div>
                </div>
              </div>
              <div v-if="dailyCosts.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                No daily cost data available
              </div>
            </div>
          </div>
        </div>

        <!-- Cost by User (Admin Only) -->
        <div v-if="user_role === 'admin' && costByUser" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8 border border-gray-200 dark:border-gray-700">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cost by User</h3>
            <div class="space-y-3">
              <div v-for="user in costByUser" :key="user.name" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                  <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                  <span class="font-medium text-gray-900 dark:text-white">{{ user.name }}</span>
                </div>
                <div class="text-right">
                  <div class="font-semibold text-gray-900 dark:text-white">${{ formatCost(user.total_cost) }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.message_count }} messages</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cost Optimization Tips -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
          <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-3">💡 Cost Optimization Tips</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800 dark:text-blue-200">
            <div>
              <p class="font-medium mb-2">📱 Message Length:</p>
              <ul class="space-y-1">
                <li>• Keep messages under 160 characters (1 SMS segment)</li>
                <li>• Each additional 160 characters = extra SMS cost</li>
                <li>• Use abbreviations when appropriate</li>
              </ul>
            </div>
            <div>
              <p class="font-medium mb-2">🎯 Targeting:</p>
              <ul class="space-y-1">
                <li>• Send only to relevant contact groups</li>
                <li>• Avoid "send to all" unless necessary</li>
                <li>• Use priority levels wisely</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  stats: Object,
  costByCategory: Array,
  costByPriority: Array,
  dailyCosts: Array,
  costByUser: Array,
  user_role: String,
});

const dateFrom = ref(props.stats.date_from);
const dateTo = ref(props.stats.date_to);

const updateDateRange = () => {
  router.get(route('sms.costs'), {
    date_from: dateFrom.value,
    date_to: dateTo.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const formatCost = (cost) => {
  if (!cost) return '0.00';
  return parseFloat(cost).toFixed(4);
};

const formatCategory = (category) => {
  const categories = {
    'natural_disaster': 'Natural Disaster',
    'security_alert': 'Security Alert',
    'health_emergency': 'Health Emergency',
    'infrastructure': 'Infrastructure',
    'weather': 'Weather Warning',
    'traffic': 'Traffic Alert',
    'general': 'General Alert',
    'other': 'Other'
  };
  return categories[category] || category;
};

const getPriorityColor = (priority) => {
  const colors = {
    'low': 'bg-gray-500',
    'normal': 'bg-blue-500',
    'high': 'bg-orange-500',
    'urgent': 'bg-red-500'
  };
  return colors[priority] || 'bg-gray-500';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  });
};
</script>
