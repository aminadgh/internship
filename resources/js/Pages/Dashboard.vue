<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { UserGroupIcon, ChatBubbleLeftRightIcon, UsersIcon, SquaresPlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  stats: Object,
  recentMessages: Array,
  user: Object,
});

const user = computed(() => usePage().props.auth.user);
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto py-8">
      <h1 class="text-3xl font-bold mb-4 text-gray-800">
        Welcome back, {{ props.user?.name || 'User' }}!
      </h1>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded shadow p-6 flex items-center space-x-4">
          <UserGroupIcon class="w-8 h-8 text-blue-600" />
          <div>
            <div class="text-2xl font-bold">{{ stats.contacts }}</div>
            <div class="text-gray-500">Contacts</div>
          </div>
        </div>
        <div class="bg-white rounded shadow p-6 flex items-center space-x-4">
          <SquaresPlusIcon class="w-8 h-8 text-indigo-600" />
          <div>
            <div class="text-2xl font-bold">{{ stats.groups }}</div>
            <div class="text-gray-500">Groups</div>
          </div>
        </div>
        <div class="bg-white rounded shadow p-6 flex items-center space-x-4">
          <ChatBubbleLeftRightIcon class="w-8 h-8 text-green-600" />
          <div>
            <div class="text-2xl font-bold">{{ stats.messages }}</div>
            <div class="text-gray-500">Messages Sent</div>
          </div>
        </div>
        <div v-if="props.user && props.user.role === 'admin'" class="bg-white rounded shadow p-6 flex items-center space-x-4">
          <UsersIcon class="w-8 h-8 text-pink-600" />
          <div>
            <div class="text-2xl font-bold">{{ stats.users }}</div>
            <div class="text-gray-500">Users</div>
          </div>
        </div>
      </div>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div class="flex gap-4">
          <Link :href="route('messages.index')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-semibold flex items-center">
            <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" /> Send SMS
          </Link>
          <Link :href="route('contacts.index')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow font-semibold flex items-center">
            <UserGroupIcon class="w-5 h-5 mr-2" /> Add Contact
          </Link>
        </div>
      </div>
      <div class="bg-white rounded shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Recent Messages</h2>
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Content</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sent At</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
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
            </tr>
            <tr v-if="recentMessages.length === 0">
              <td colspan="3" class="px-4 py-2 text-gray-400 text-center">No recent messages.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
