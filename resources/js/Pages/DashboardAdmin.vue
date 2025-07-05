<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
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
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-64 bg-white border-r shadow-lg">
      <div class="h-20 flex items-center justify-center border-b">
        <span class="text-2xl font-bold text-blue-700">SwiftNotify Admin</span>
      </div>
      <nav class="flex-1 px-4 py-6 space-y-2">
        <Link href="/dashboard" class="flex items-center px-3 py-2 rounded-lg text-blue-700 bg-blue-100 font-semibold">
          <ShieldCheckIcon class="w-5 h-5 mr-2" /> Dashboard
        </Link>
        <Link href="/users" class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
          <UsersIcon class="w-5 h-5 mr-2" /> Manage Users
        </Link>
        <Link href="/contacts" class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
          <UserGroupIcon class="w-5 h-5 mr-2" /> Contacts
        </Link>
        <Link href="/groups" class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
          <SquaresPlusIcon class="w-5 h-5 mr-2" /> Groups
        </Link>
        <Link href="/messages" class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
          <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" /> Messages
        </Link>
        <Link href="/settings" class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
          <Cog6ToothIcon class="w-5 h-5 mr-2" /> System Settings
        </Link>
      </nav>
      <div class="p-4 border-t">
        <Link href="/logout" method="post" as="button" class="flex items-center text-red-600 font-semibold">
          <ArrowRightOnRectangleIcon class="w-5 h-5 mr-2" /> Logout
        </Link>
      </div>
    </aside>
    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
      <!-- Topbar -->
      <header class="bg-white shadow flex items-center justify-between px-6 h-20">
        <div class="flex items-center space-x-4">
          <button class="md:hidden" @click="sidebarOpen = !sidebarOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
          <span class="text-xl font-bold text-blue-700">Admin Control Center</span>
        </div>
        <div class="flex items-center space-x-4">
          <UserIcon class="w-7 h-7 text-gray-500" />
          <span class="font-semibold text-gray-700">{{ user.name }}</span>
        </div>
      </header>
      <!-- Dashboard Content -->
      <main class="flex-1 p-8 bg-gray-100">
        <!-- Stats Cards Row -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
          <div class="bg-gradient-to-br from-pink-100 to-pink-200 rounded-xl shadow p-6 flex items-center space-x-4">
            <UsersIcon class="w-10 h-10 text-pink-600" />
            <div>
              <div class="text-3xl font-bold">{{ stats.users }}</div>
              <div class="text-gray-600">Users</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl shadow p-6 flex items-center space-x-4">
            <UserGroupIcon class="w-10 h-10 text-blue-600" />
            <div>
              <div class="text-3xl font-bold">{{ stats.contacts }}</div>
              <div class="text-gray-600">Contacts</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-xl shadow p-6 flex items-center space-x-4">
            <SquaresPlusIcon class="w-10 h-10 text-indigo-600" />
            <div>
              <div class="text-3xl font-bold">{{ stats.groups }}</div>
              <div class="text-gray-600">Groups</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-xl shadow p-6 flex items-center space-x-4">
            <ChatBubbleLeftRightIcon class="w-10 h-10 text-green-600" />
            <div>
              <div class="text-3xl font-bold">{{ stats.messages }}</div>
              <div class="text-gray-600">Messages</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl shadow p-6 flex items-center space-x-4">
            <ShieldCheckIcon class="w-10 h-10 text-yellow-600" />
            <div>
              <div class="text-3xl font-bold">{{ stats.admins }}</div>
              <div class="text-gray-600">Admins</div>
            </div>
          </div>
          <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl shadow p-6 flex items-center space-x-4">
            <UserGroupIcon class="w-10 h-10 text-gray-600" />
            <div>
              <div class="text-3xl font-bold">{{ stats.agents }}</div>
              <div class="text-gray-600">Agents</div>
            </div>
          </div>
        </div>
        <!-- Analytics Chart Full Width -->
        <div class="mb-8">
          <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 flex items-center"><ChartBarIcon class="w-5 h-5 mr-2 text-blue-600" /> Messages Sent (Last 7 Days)</h2>
            <canvas ref="chartRef" height="120"></canvas>
          </div>
        </div>
        <!-- User Activity & Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
          <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 flex items-center"><UserIcon class="w-5 h-5 mr-2 text-pink-600" /> User Activity Feed</h2>
            <ul class="text-sm text-gray-700 space-y-2">
              <li v-for="u in userActivity" :key="u.id" class="flex items-center">
                <UserIcon class="w-4 h-4 text-blue-500 mr-2" />
                <span class="font-semibold">{{ u.name }}</span> ({{ u.role }}) updated profile <span class="text-gray-400 ml-2">{{ new Date(u.updated_at).toLocaleString() }}</span>
              </li>
              <li v-if="userActivity.length === 0" class="text-gray-400">No recent activity.</li>
            </ul>
          </div>
          <div class="bg-white rounded-xl shadow p-6 flex flex-col gap-4">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 flex items-center"><BoltIcon class="w-5 h-5 mr-2 text-yellow-600" /> Quick Admin Actions</h2>
            <Link href="/users" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg shadow font-semibold flex items-center">
              <UsersIcon class="w-5 h-5 mr-2" /> Manage Users
            </Link>
            <Link href="/messages/create" class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-lg shadow font-semibold flex items-center">
              <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" /> Broadcast Message
            </Link>
            <Link href="/settings" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-lg shadow font-semibold flex items-center">
              <Cog6ToothIcon class="w-5 h-5 mr-2" /> System Settings
            </Link>
            <button class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded-lg shadow font-semibold flex items-center" @click="$inertia.reload()">
              <ExclamationCircleIcon class="w-5 h-5 mr-2" /> Refresh Dashboard
            </button>
          </div>
        </div>
        <!-- Recent Users & Recent Messages -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 flex items-center"><UserIcon class="w-5 h-5 mr-2 text-pink-600" /> Recent Users</h2>
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="u in recentUsers" :key="u.id" class="hover:bg-gray-50">
                  <td class="px-4 py-2 text-gray-800">{{ u.name }}</td>
                  <td class="px-4 py-2 text-gray-600">{{ u.email }}</td>
                  <td class="px-4 py-2 text-gray-500">{{ u.role }}</td>
                  <td class="px-4 py-2 text-gray-400">{{ new Date(u.created_at).toLocaleDateString() }}</td>
                </tr>
                <tr v-if="recentUsers.length === 0">
                  <td colspan="4" class="px-4 py-2 text-gray-400 text-center">No recent users.</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 flex items-center"><ChatBubbleLeftRightIcon class="w-5 h-5 mr-2 text-green-600" /> Recent Messages</h2>
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Content</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sent At</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="msg in recentMessages" :key="msg.id" class="hover:bg-gray-50">
                  <td class="px-4 py-2 text-gray-800">{{ msg.content }}</td>
                  <td class="px-4 py-2 text-gray-600">{{ msg.sent_at ? new Date(msg.sent_at).toLocaleString() : '-' }}</td>
                  <td class="px-4 py-2">
                    <span :class="{
                      'text-green-600 font-bold': msg.status === 'sent',
                      'text-red-600 font-bold': msg.status === 'failed',
                      'text-gray-500': msg.status === 'pending',
                    }">{{ msg.status }}</span>
                  </td>
                  <td class="px-4 py-2 text-gray-500">{{ msg.user }}</td>
                </tr>
                <tr v-if="recentMessages.length === 0">
                  <td colspan="4" class="px-4 py-2 text-gray-400 text-center">No recent messages.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
</template> 