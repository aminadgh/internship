<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { UserGroupIcon, PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';

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
    <div class="max-w-5xl mx-auto py-4 sm:py-6 lg:py-8 px-4 sm:px-6 lg:px-8">
      <!-- Enhanced Header -->
      <div class="mb-8 bg-gradient-to-r from-blue-600/10 to-purple-600/10 backdrop-blur-sm border border-blue-200/20 dark:border-blue-800/20 rounded-2xl p-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-lg opacity-75 animate-pulse"></div>
              <UserGroupIcon class="w-12 h-12 text-white relative z-10" />
            </div>
            <div>
              <h1 class="text-3xl sm:text-4xl font-black text-gray-800 dark:text-white mb-1">
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">User Management</span>
              </h1>
              <p class="text-lg text-gray-600 dark:text-gray-400 font-medium">Manage system users and permissions</p>
            </div>
          </div>
          <button 
            @click="openAddModal" 
            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center space-x-2"
          >
            <PlusIcon class="w-5 h-5" />
            <span>Add User</span>
          </button>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-white font-medium">{{ user.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">{{ user.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :class="user.role === 'admin' 
                      ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-300' 
                      : 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 dark:from-blue-900/30 dark:to-indigo-900/30 dark:text-blue-300'" 
                    class="px-3 py-1.5 rounded-full text-xs font-bold shadow-sm"
                  >
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                  <button 
                    @click="openEditModal(user)" 
                    class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-orange-400 hover:from-yellow-500 hover:to-orange-500 text-white rounded-lg font-semibold text-xs shadow transition-all duration-200 transform hover:scale-105"
                  >
                    <PencilIcon class="w-3 h-3 mr-1" />
                    Edit
                  </button>
                  <button 
                    @click="deleteUser(user.id)" 
                    class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white rounded-lg font-semibold text-xs shadow transition-all duration-200 transform hover:scale-105"
                  >
                    <TrashIcon class="w-3 h-3 mr-1" />
                    Delete
                  </button>
                </td>
              </tr>
              <tr v-if="users.length === 0">
                <td colspan="4" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">
                  <div class="flex flex-col items-center space-y-2">
                    <UserGroupIcon class="w-12 h-12 text-gray-300 dark:text-gray-600" />
                    <p class="text-lg font-medium">No users found</p>
                    <p class="text-sm">Get started by adding your first user</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add User Modal -->
      <Modal :show="showAddModal" @close="closeAddModal">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl">
          <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
            <PlusIcon class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" />
            Add New User
          </h2>
          <form @submit.prevent="submitAddForm" class="space-y-6">
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Name</label>
              <input 
                v-model="addForm.name" 
                type="text" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors" 
              />
              <div v-if="addForm.errors.name" class="text-red-500 text-sm mt-1">{{ addForm.errors.name }}</div>
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Email</label>
              <input 
                v-model="addForm.email" 
                type="email" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors" 
              />
              <div v-if="addForm.errors.email" class="text-red-500 text-sm mt-1">{{ addForm.errors.email }}</div>
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Password</label>
              <input 
                v-model="addForm.password" 
                type="password" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors" 
              />
              <div v-if="addForm.errors.password" class="text-red-500 text-sm mt-1">{{ addForm.errors.password }}</div>
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Role</label>
              <select 
                v-model="addForm.role" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors"
              >
                <option value="agent">Agent</option>
                <option value="admin">Admin</option>
              </select>
              <div v-if="addForm.errors.role" class="text-red-500 text-sm mt-1">{{ addForm.errors.role }}</div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
              <button 
                type="button" 
                @click="closeAddModal" 
                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg font-semibold transition-colors"
              >
                Cancel
              </button>
              <button 
                type="submit" 
                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-semibold shadow-lg transition-all duration-200 transform hover:scale-105"
              >
                Create User
              </button>
            </div>
          </form>
        </div>
      </Modal>

      <!-- Edit User Modal -->
      <Modal :show="showEditModal" @close="closeEditModal">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl">
          <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
            <PencilIcon class="w-6 h-6 mr-2 text-yellow-600 dark:text-yellow-400" />
            Edit User
          </h2>
          <form @submit.prevent="submitEditForm" class="space-y-6">
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Name</label>
              <input 
                v-model="editForm.name" 
                type="text" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors" 
              />
              <div v-if="editForm.errors.name" class="text-red-500 text-sm mt-1">{{ editForm.errors.name }}</div>
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Email</label>
              <input 
                v-model="editForm.email" 
                type="email" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors" 
              />
              <div v-if="editForm.errors.email" class="text-red-500 text-sm mt-1">{{ editForm.errors.email }}</div>
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">
                Password 
                <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">(leave blank to keep current)</span>
              </label>
              <input 
                v-model="editForm.password" 
                type="password" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors" 
              />
              <div v-if="editForm.errors.password" class="text-red-500 text-sm mt-1">{{ editForm.errors.password }}</div>
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Role</label>
              <select 
                v-model="editForm.role" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors"
              >
                <option value="agent">Agent</option>
                <option value="admin">Admin</option>
              </select>
              <div v-if="editForm.errors.role" class="text-red-500 text-sm mt-1">{{ editForm.errors.role }}</div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
              <button 
                type="button" 
                @click="closeEditModal" 
                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg font-semibold transition-colors"
              >
                Cancel
              </button>
              <button 
                type="submit" 
                class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white rounded-lg font-semibold shadow-lg transition-all duration-200 transform hover:scale-105"
              >
                Update User
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
  </AuthenticatedLayout>
</template> 