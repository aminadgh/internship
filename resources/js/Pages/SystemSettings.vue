<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Cog6ToothIcon, DevicePhoneMobileIcon, ExclamationCircleIcon, CheckCircleIcon, ExclamationTriangleIcon, InformationCircleIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline';

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

const isSmsConfigured = computed(() => {
  return Boolean(smsSettings.value.accountSid && smsSettings.value.authToken && smsSettings.value.fromNumber);
});
</script>
<template>
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-6 lg:py-10 px-4 sm:px-6 lg:px-8">
      <!-- Enhanced Header -->
      <div class="mb-8 bg-gradient-to-r from-blue-600/10 to-purple-600/10 backdrop-blur-sm border border-blue-200/20 dark:border-blue-800/20 rounded-2xl p-8">
        <div class="flex items-center space-x-4">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-lg opacity-75 animate-pulse"></div>
            <Cog6ToothIcon class="w-12 h-12 text-white relative z-10" />
          </div>
          <div>
            <h1 class="text-3xl sm:text-4xl font-black text-gray-800 dark:text-white mb-1">
              <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">System Settings</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 font-medium">Configure your SMS alert platform</p>
          </div>
        </div>
      </div>

      <!-- Overview Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow p-6">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Application</p>
              <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ generalSettings.appName || '—' }}</p>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Timezone: <span class="font-medium text-gray-700 dark:text-gray-300">{{ generalSettings.timezone }}</span></p>
            </div>
            <CheckCircleIcon class="w-6 h-6 text-emerald-500" />
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow p-6">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">SMS Provider</p>
              <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ smsSettings.provider }}</p>
              <div class="mt-2 inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold"
                   :class="isSmsConfigured ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'">
                <span class="w-2 h-2 rounded-full mr-2"
                      :class="isSmsConfigured ? 'bg-emerald-500' : 'bg-amber-500'" />
                {{ isSmsConfigured ? 'Connected' : 'Action required' }}
              </div>
            </div>
            <DevicePhoneMobileIcon class="w-6 h-6 text-blue-500" />
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow p-6">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Security & Status</p>
              <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Healthy</p>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All systems operational</p>
            </div>
            <CheckCircleIcon class="w-6 h-6 text-emerald-500" />
          </div>
        </div>
      </div>

      <!-- Main Layout: Sidebar + Content -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar -->
        <div class="space-y-6 order-last lg:order-first">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Quick Links</h3>
            <ul class="space-y-3 text-sm">
              <li>
                <a href="https://www.twilio.com/console" target="_blank" rel="noreferrer" class="flex items-center justify-between text-indigo-600 dark:text-indigo-400 hover:underline">
                  Twilio Console
                  <ArrowTopRightOnSquareIcon class="w-4 h-4 ml-2" />
                </a>
              </li>
              <li>
                <a href="https://console.twilio.com/us1/develop/sms/settings" target="_blank" rel="noreferrer" class="flex items-center justify-between text-indigo-600 dark:text-indigo-400 hover:underline">
                  Messaging Settings
                  <ArrowTopRightOnSquareIcon class="w-4 h-4 ml-2" />
                </a>
              </li>
              <li>
                <a href="https://support.twilio.com" target="_blank" rel="noreferrer" class="flex items-center justify-between text-indigo-600 dark:text-indigo-400 hover:underline">
                  Twilio Support
                  <ArrowTopRightOnSquareIcon class="w-4 h-4 ml-2" />
                </a>
              </li>
            </ul>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Tips</h3>
            <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
              <li class="flex items-start">
                <InformationCircleIcon class="w-5 h-5 text-blue-500 mr-2 mt-0.5" />
                Use a dedicated Twilio number for Tataouine to improve deliverability.
              </li>
              <li class="flex items-start">
                <InformationCircleIcon class="w-5 h-5 text-blue-500 mr-2 mt-0.5" />
                Restrict account access and rotate tokens regularly.
              </li>
              <li class="flex items-start">
                <InformationCircleIcon class="w-5 h-5 text-blue-500 mr-2 mt-0.5" />
                Keep app name and timezone aligned with your audience.
              </li>
            </ul>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Status</h3>
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
              <CheckCircleIcon class="w-5 h-5 text-emerald-500 mr-2" />
              No incidents reported
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="lg:col-span-2 space-y-8">
          <!-- General Settings -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold mb-6 text-gray-700 dark:text-white flex items-center">
              <Cog6ToothIcon class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
              General Settings
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-gray-600 dark:text-gray-400 font-medium mb-2">App Name</label>
                <input 
                  v-model="generalSettings.appName" 
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" 
                />
              </div>
              <div>
                <label class="block text-gray-600 dark:text-gray-400 font-medium mb-2">Timezone</label>
                <select 
                  v-model="generalSettings.timezone" 
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                >
                  <option value="UTC">UTC</option>
                  <option value="Africa/Tunis">Africa/Tunis</option>
                  <option value="Europe/Paris">Europe/Paris</option>
                </select>
              </div>
            </div>
          </div>

          <!-- SMS/Twilio Settings -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-start justify-between mb-6">
              <h2 class="text-xl font-semibold text-gray-700 dark:text-white flex items-center">
                <DevicePhoneMobileIcon class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" /> 
                SMS Provider Configuration
              </h2>
              <div class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold"
                   :class="isSmsConfigured ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'">
                {{ isSmsConfigured ? 'Connected' : 'Not configured' }}
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-gray-600 dark:text-gray-400 font-medium mb-2">Provider</label>
                <input 
                  v-model="smsSettings.provider" 
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-300 cursor-not-allowed" 
                  disabled 
                />
              </div>
              <div>
                <label class="block text-gray-600 dark:text-gray-400 font-medium mb-2">Account SID</label>
                <input 
                  v-model="smsSettings.accountSid" 
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" 
                  placeholder="Enter Twilio Account SID" 
                />
              </div>
              <div>
                <label class="block text-gray-600 dark:text-gray-400 font-medium mb-2">Auth Token</label>
                <input 
                  v-model="smsSettings.authToken" 
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" 
                  placeholder="Enter Twilio Auth Token" 
                  type="password" 
                />
              </div>
              <div>
                <label class="block text-gray-600 dark:text-gray-400 font-medium mb-2">From Number</label>
                <input 
                  v-model="smsSettings.fromNumber" 
                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" 
                  placeholder="Enter Twilio Number" 
                />
              </div>
            </div>

            <div class="mt-6 flex items-start space-x-3">
              <ExclamationTriangleIcon class="w-5 h-5 text-amber-500 mt-0.5" />
              <p class="text-sm text-gray-600 dark:text-gray-300">
                Store your credentials securely. Do not share Account SID or Auth Token publicly.
              </p>
            </div>
          </div>

          <!-- Placeholder for more settings -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-start space-x-4">
              <ExclamationCircleIcon class="w-6 h-6 text-yellow-500 dark:text-yellow-400" />
              <div>
                <div class="text-lg font-semibold text-gray-700 dark:text-white">More settings coming soon...</div>
                <div class="text-gray-500 dark:text-gray-400">This page will allow you to configure more system options in the future.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template> 