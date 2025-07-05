<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Cog6ToothIcon, DevicePhoneMobileIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

const generalSettings = ref({
  appName: 'SwiftNotify',
  timezone: 'UTC',
});
const smsSettings = ref({
  provider: 'Twilio',
  accountSid: '',
  authToken: '',
  fromNumber: '',
});
</script>
<template>
  <AuthenticatedLayout>
    <div class="max-w-3xl mx-auto py-10">
      <div class="flex items-center mb-8">
        <Cog6ToothIcon class="w-8 h-8 text-indigo-600 mr-3" />
        <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
      </div>
      <!-- General Settings -->
      <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">General</h2>
        <div class="mb-4">
          <label class="block text-gray-600 font-medium mb-1">App Name</label>
          <input v-model="generalSettings.appName" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-gray-600 font-medium mb-1">Timezone</label>
          <select v-model="generalSettings.timezone" class="w-full border rounded px-3 py-2">
            <option value="UTC">UTC</option>
            <option value="Africa/Tunis">Africa/Tunis</option>
            <option value="Europe/Paris">Europe/Paris</option>
            <!-- Add more as needed -->
          </select>
        </div>
      </div>
      <!-- SMS/Twilio Settings -->
      <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-700 flex items-center"><DevicePhoneMobileIcon class="w-5 h-5 mr-2 text-green-600" /> SMS Provider</h2>
        <div class="mb-4">
          <label class="block text-gray-600 font-medium mb-1">Provider</label>
          <input v-model="smsSettings.provider" class="w-full border rounded px-3 py-2" disabled />
        </div>
        <div class="mb-4">
          <label class="block text-gray-600 font-medium mb-1">Account SID</label>
          <input v-model="smsSettings.accountSid" class="w-full border rounded px-3 py-2" placeholder="Enter Twilio Account SID" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-600 font-medium mb-1">Auth Token</label>
          <input v-model="smsSettings.authToken" class="w-full border rounded px-3 py-2" placeholder="Enter Twilio Auth Token" type="password" />
        </div>
        <div>
          <label class="block text-gray-600 font-medium mb-1">From Number</label>
          <input v-model="smsSettings.fromNumber" class="w-full border rounded px-3 py-2" placeholder="Enter Twilio Number" />
        </div>
      </div>
      <!-- Placeholder for more settings -->
      <div class="bg-white rounded-xl shadow p-6 flex items-center space-x-4">
        <ExclamationCircleIcon class="w-6 h-6 text-yellow-500" />
        <div>
          <div class="text-lg font-semibold text-gray-700">More settings coming soon...</div>
          <div class="text-gray-500">This page will allow you to configure more system options in the future.</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template> 