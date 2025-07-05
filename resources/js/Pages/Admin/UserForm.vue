<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-lg p-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ formTitle }}</h1>
      <form @submit.prevent="submitForm" class="space-y-6">
        <div>
          <label class="block text-gray-700 font-semibold mb-1">Name</label>
          <input v-model="form.name" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
          <div v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</div>
        </div>
        <div>
          <label class="block text-gray-700 font-semibold mb-1">Email</label>
          <input v-model="form.email" type="email" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
          <div v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</div>
        </div>
        <div v-if="!isEdit">
          <label class="block text-gray-700 font-semibold mb-1">Password</label>
          <input v-model="form.password" type="password" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
          <div v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</div>
        </div>
        <div>
          <label class="block text-gray-700 font-semibold mb-1">Role</label>
          <select v-model="form.role" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="agent">Agent</option>
            <option value="admin">Admin</option>
          </select>
          <div v-if="errors.role" class="text-red-500 text-xs mt-1">{{ errors.role }}</div>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" @click="goBack" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow">{{ isEdit ? 'Update' : 'Create' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { defineProps } from 'vue';

const props = defineProps({
  user: Object
});

const isEdit = computed(() => !!props.user);

const form = useForm({
  name: props.user?.name || '',
  email: props.user?.email || '',
  password: '',
  role: props.user?.role || 'agent',
});

const errors = form.errors;

const formTitle = computed(() => isEdit.value ? 'Edit User' : 'Add User');

function submitForm() {
  if (isEdit.value) {
    form.put(`/users/${props.user.id}`);
  } else {
    form.post('/users');
  }
}

function goBack() {
  router.visit('/users');
}
</script> 