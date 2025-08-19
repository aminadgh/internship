<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  users: Array
});

const showAddModal = ref(false);
const showEditModal = ref(false);
const editingUser = ref(null);

const addForm = useForm({
  name: '',
  email: '',
  password: '',
  role: 'agent',
});

const editForm = useForm({
  name: '',
  email: '',
  password: '',
  role: 'agent',
});

function openAddModal() {
  addForm.reset();
  showAddModal.value = true;
}
function closeAddModal() {
  showAddModal.value = false;
  addForm.reset();
}
function openEditModal(user) {
  editingUser.value = user;
  editForm.reset();
  editForm.name = user.name;
  editForm.email = user.email;
  editForm.password = '';
  editForm.role = user.role;
  showEditModal.value = true;
}
function closeEditModal() {
  showEditModal.value = false;
  editingUser.value = null;
  editForm.reset();
}
function submitAddForm() {
  addForm.post('/users', {
    onSuccess: () => {
      closeAddModal();
      router.reload({ only: ['users'] });
    },
  });
}
function submitEditForm() {
  if (!editingUser.value) return;
  editForm.put(`/users/${editingUser.value.id}`, {
    onSuccess: () => {
      closeEditModal();
      router.reload({ only: ['users'] });
    },
  });
}
function deleteUser(id) {
  if (confirm('Are you sure you want to delete this user?')) {
    router.delete(`/users/${id}`);
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-5xl mx-auto py-8">
      <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
          <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold shadow transition">+ Add User</button>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ user.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="user.role === 'admin' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'" class="px-2 py-1 rounded text-xs font-semibold">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                  <button @click="openEditModal(user)" class="inline-flex items-center px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg font-semibold text-xs shadow transition">Edit</button>
                  <button @click="deleteUser(user.id)" class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold text-xs shadow transition">Delete</button>
                </td>
              </tr>
              <tr v-if="users.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-gray-400">No users found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Add User Modal -->
      <Modal :show="showAddModal" @close="closeAddModal">
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4">Add User</h2>
          <form @submit.prevent="submitAddForm" class="space-y-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Name</label>
              <input v-model="addForm.name" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="addForm.errors.name" class="text-red-500 text-xs mt-1">{{ addForm.errors.name }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Email</label>
              <input v-model="addForm.email" type="email" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="addForm.errors.email" class="text-red-500 text-xs mt-1">{{ addForm.errors.email }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Password</label>
              <input v-model="addForm.password" type="password" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="addForm.errors.password" class="text-red-500 text-xs mt-1">{{ addForm.errors.password }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Role</label>
              <select v-model="addForm.role" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="agent">Agent</option>
                <option value="admin">Admin</option>
              </select>
              <div v-if="addForm.errors.role" class="text-red-500 text-xs mt-1">{{ addForm.errors.role }}</div>
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" @click="closeAddModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow">Create</button>
            </div>
          </form>
        </div>
      </Modal>
      <!-- Edit User Modal -->
      <Modal :show="showEditModal" @close="closeEditModal">
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4">Edit User</h2>
          <form @submit.prevent="submitEditForm" class="space-y-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Name</label>
              <input v-model="editForm.name" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="editForm.errors.name" class="text-red-500 text-xs mt-1">{{ editForm.errors.name }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Email</label>
              <input v-model="editForm.email" type="email" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="editForm.errors.email" class="text-red-500 text-xs mt-1">{{ editForm.errors.email }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Password <span class="text-xs text-gray-400">(leave blank to keep current)</span></label>
              <input v-model="editForm.password" type="password" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="editForm.errors.password" class="text-red-500 text-xs mt-1">{{ editForm.errors.password }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Role</label>
              <select v-model="editForm.role" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="agent">Agent</option>
                <option value="admin">Admin</option>
              </select>
              <div v-if="editForm.errors.role" class="text-red-500 text-xs mt-1">{{ editForm.errors.role }}</div>
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" @click="closeEditModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow">Update</button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
  </AuthenticatedLayout>
</template> 