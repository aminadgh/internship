<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { UserGroupIcon, ChatBubbleLeftRightIcon, UsersIcon, SquaresPlusIcon, Cog6ToothIcon, ArrowRightOnRectangleIcon, UserIcon, ShieldCheckIcon, ExclamationCircleIcon, ChartBarIcon, DocumentTextIcon, BoltIcon } from '@heroicons/vue/24/outline';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
const props = defineProps({
  stats: Object,
  recentUsers: Array,
  recentMessages: Array,
  logErrors: Array,
  userActivity: Array,
  messageStats: Array,
  user: Object,
});
const sidebarOpen = ref(false);
const chartRef = ref(null);
onMounted(() => {
  if (chartRef.value && props.messageStats && props.messageStats.length) {
    new Chart(chartRef.value, {
      type: 'bar',
      data: {
        labels: props.messageStats.map(s => s.date).reverse(),
        datasets: [{
          label: 'Messages Sent',
          data: props.messageStats.map(s => s.count).reverse(),
          backgroundColor: 'rgba(59, 130, 246, 0.7)',
        }],
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } },
      },
    });
  }
});
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
              <p class="text-lg text-gray-600 dark:text-gray-400 font-medium">Admin Dashboard</p>
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
            <p class="text-gray-600 dark:text-gray-400">Here's your system overview and management tools.</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-8">
        <Link 
          :href="route('users.index')" 
          class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 sm:px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center justify-center transition-all duration-200 hover:shadow-xl hover:scale-105 w-full sm:w-auto"
        >
          <UsersIcon class="w-5 h-5 mr-2" /> Manage Users
        </Link>
        <Link 
          :href="route('messages.index')" 
          class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white px-4 sm:px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center justify-center transition-all duration-200 hover:shadow-xl hover:scale-105 w-full sm:w-auto"
        >
          <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" /> Send Broadcast
        </Link>
        <Link 
          :href="route('dashboard')" 
          class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-4 sm:px-6 py-3 rounded-xl shadow-lg font-semibold flex items-center justify-center transition-all duration-200 hover:shadow-xl hover:scale-105 w-full sm:w-auto"
        >
          <Cog6ToothIcon class="w-5 h-5 mr-2" /> System Settings
        </Link>
      </div>

      <!-- Dashboard Content -->
      <div class="space-y-8">
        <!-- Stats Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
          <div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-900/20 dark:to-pink-800/20 rounded-xl shadow-lg p-6 flex items-center space-x-4 border border-pink-200 dark:border-pink-800">
            <UsersIcon class="w-10 h-10 text-pink-600 dark:text-pink-400" />
            <div>
              <div class="text-3xl font-bold text-pink-900 dark:text-pink-100">{{ stats.users }}</div>
              <div class="text-gray-600 dark:text-gray-400">Users</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl shadow-lg p-6 flex items-center space-x-4 border border-blue-200 dark:border-blue-800">
            <UserGroupIcon class="w-10 h-10 text-blue-600 dark:text-blue-400" />
            <div>
              <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ stats.contacts }}</div>
              <div class="text-gray-600 dark:text-gray-400">Contacts</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl shadow-lg p-6 flex items-center space-x-4 border border-indigo-200 dark:border-indigo-800">
            <SquaresPlusIcon class="w-10 h-10 text-indigo-600 dark:text-indigo-400" />
            <div>
              <div class="text-3xl font-bold text-indigo-900 dark:text-indigo-100">{{ stats.groups }}</div>
              <div class="text-gray-600 dark:text-gray-400">Groups</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl shadow-lg p-6 flex items-center space-x-4 border border-green-200 dark:border-green-800">
            <ChatBubbleLeftRightIcon class="w-10 h-10 text-green-600 dark:text-green-400" />
            <div>
              <div class="text-3xl font-bold text-green-900 dark:text-green-100">{{ stats.messages }}</div>
              <div class="text-gray-600 dark:text-gray-400">Messages</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl shadow-lg p-6 flex items-center space-x-4 border border-yellow-200 dark:border-yellow-800">
            <ShieldCheckIcon class="w-10 h-10 text-yellow-600 dark:text-yellow-400" />
            <div>
              <div class="text-3xl font-bold text-yellow-900 dark:text-yellow-100">{{ stats.admins }}</div>
              <div class="text-gray-600 dark:text-gray-400">Admins</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/20 dark:to-gray-700/20 rounded-xl shadow-lg p-6 flex items-center space-x-4 border border-gray-200 dark:border-gray-700">
            <UserGroupIcon class="w-10 h-10 text-gray-600 dark:text-gray-400" />
            <div>
              <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ stats.agents }}</div>
              <div class="text-gray-600 dark:text-gray-400">Agents</div>
            </div>
          </div>
        </div>

        <!-- Analytics Chart Full Width -->
        <div class="mb-8">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white flex items-center"><ChartBarIcon class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" /> Messages Sent (Last 7 Days)</h2>
            <canvas ref="chartRef" height="120"></canvas>
          </div>
        </div>

        <!-- User Activity & Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white flex items-center"><UserIcon class="w-5 h-5 mr-2 text-pink-600 dark:text-pink-400" /> User Activity Feed</h2>
            <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
              <li v-for="u in userActivity" :key="u.id" class="flex items-center">
                <UserIcon class="w-4 h-4 text-blue-500 dark:text-blue-400 mr-2" />
                <span class="font-semibold">{{ u.name }}</span> ({{ u.role }}) updated profile <span class="text-gray-400 dark:text-gray-500 ml-2">{{ new Date(u.updated_at).toLocaleString() }}</span>
              </li>
              <li v-if="userActivity.length === 0" class="text-gray-400 dark:text-gray-500">No recent activity.</li>
            </ul>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 flex flex-col gap-4 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white flex items-center"><BoltIcon class="w-5 h-5 mr-2 text-yellow-600 dark:text-yellow-400" /> Quick Admin Actions</h2>
            <Link :href="route('users.index')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg shadow font-semibold flex items-center transition-colors">
              <UsersIcon class="w-5 h-5 mr-2" /> Manage Users
            </Link>
            <Link :href="route('messages.index')" class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-lg shadow font-semibold flex items-center transition-colors">
              <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" /> Broadcast Message
            </Link>
            <Link :href="route('dashboard')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-lg shadow font-semibold flex items-center transition-colors">
              <Cog6ToothIcon class="w-5 h-5 mr-2" /> System Settings
            </Link>
            <button class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded-lg shadow font-semibold flex items-center transition-colors" @click="$inertia.reload()">
              <ExclamationCircleIcon class="w-5 h-5 mr-2" /> Refresh Dashboard
            </button>
          </div>
        </div>

        <!-- Recent Users & Recent Messages -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white flex items-center"><UserIcon class="w-5 h-5 mr-2 text-pink-600 dark:text-pink-400" /> Recent Users</h2>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Role</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Joined</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="u in recentUsers" :key="u.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-4 py-2 text-gray-800 dark:text-white">{{ u.name }}</td>
                  <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ u.email }}</td>
                  <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ u.role }}</td>
                  <td class="px-4 py-2 text-gray-400 dark:text-gray-500">{{ new Date(u.created_at).toLocaleDateString() }}</td>
                </tr>
                <tr v-if="recentUsers.length === 0">
                  <td colspan="4" class="px-4 py-2 text-gray-400 dark:text-gray-500 text-center">No recent users.</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-white flex items-center"><ChatBubbleLeftRightIcon class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" /> Recent Messages</h2>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Content</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sent At</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="msg in recentMessages" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-4 py-2 text-gray-800 dark:text-white">{{ msg.content }}</td>
                  <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ msg.sent_at ? new Date(msg.sent_at).toLocaleString() : '-' }}</td>
                  <td class="px-4 py-2">
                    <span :class="{
                      'text-green-600 dark:text-green-400 font-bold': msg.status === 'sent',
                      'text-red-600 dark:text-red-400 font-bold': msg.status === 'failed',
                      'text-gray-500 dark:text-gray-400': msg.status === 'pending',
                    }">{{ msg.status }}</span>
                  </td>
                  <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ msg.user }}</td>
                </tr>
                <tr v-if="recentMessages.length === 0">
                  <td colspan="4" class="px-4 py-2 text-gray-400 dark:text-gray-500 text-center">No recent messages.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template> 