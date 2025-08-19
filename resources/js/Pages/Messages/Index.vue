<template>
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto py-8">
      <h1 class="text-2xl font-bold mb-6">Send SMS to Group</h1>
      <div class="bg-white rounded shadow p-6">
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block mb-1 font-semibold">Group(s)</label>
            <select v-model="form.group_ids" class="w-full border rounded p-2" multiple>
              <option v-for="group in groups" :key="group.id" :value="group.id">
                {{ group.name }}
              </option>
            </select>
            <div v-if="form.errors.group_ids" class="text-red-500 text-sm mt-1">{{ form.errors.group_ids }}</div>
          </div>
          <div class="mb-4">
            <label class="block mb-1 font-semibold">Alert Template</label>
            <select v-model="selectedTemplateIdx" @change="onTemplateChange" class="w-full border rounded p-2">
              <option value="">-- Select a template --</option>
              <option v-for="(tpl, idx) in alertTemplates" :key="tpl.name" :value="idx">
                {{ tpl.name }}
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block mb-1 font-semibold">Message</label>
            <textarea v-model="form.content" class="w-full border rounded p-2" rows="4" placeholder="Type your message..."></textarea>
            <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
          </div>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Send SMS</button>
        </form>
        <div v-if="form.recentlySuccessful" class="mt-4 text-green-600">SMS sent successfully!</div>
        <div v-if="form.errors.error" class="mt-4 text-red-600">{{ form.errors.error }}</div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  groups: Array
});

const form = useForm({
  group_ids: [],
  content: ''
});

const alertTemplates = [
  { name: 'Scheduled Maintenance', content: 'Scheduled maintenance in your area. Service will be restored by [time].' },
  { name: 'Health Alert', content: 'Health alert: Please take precautions due to [reason].' },
  { name: 'Weather Warning', content: 'Weather warning: [details]. Stay safe.' },
  { name: 'Water Outage', content: 'Water outage in your area. We apologize for the inconvenience.' },
  { name: 'Electricity Interruption', content: 'Electricity interruption due to maintenance. Thank you for your understanding.' },
];

const selectedTemplateIdx = ref('');

function onTemplateChange() {
  const idx = Number(selectedTemplateIdx.value);
  if (!isNaN(idx) && alertTemplates[idx]) {
    form.content = alertTemplates[idx].content;
  }
}

function submit() {
  form.post(route('messages.store'), {
    onError: () => {},
  });
}
</script> 