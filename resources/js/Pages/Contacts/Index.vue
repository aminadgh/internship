<template>
  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto py-8">
      <h1 class="text-2xl font-bold mb-6">Contacts</h1>
      <div class="bg-white rounded shadow p-6">
        <div class="flex justify-between items-center mb-6">
          <div class="flex gap-2">
            <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold shadow transition">+ Add Contact</button>
            <button @click="openImportModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow transition">Import CSV</button>
          </div>
        </div>
        <div class="mb-4">
          <input v-model="searchInput" @input="searchContacts" type="text" placeholder="Search by name or phone..." class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="contact in contacts" :key="contact.id">
                <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ contact.full_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ contact.phone_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ contact.group?.name || '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                  <button @click="openEditModal(contact)" class="inline-flex items-center px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg font-semibold text-xs shadow transition">Edit</button>
                  <button @click="deleteContact(contact.id)" class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold text-xs shadow transition">Delete</button>
                </td>
              </tr>
              <tr v-if="contacts.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-gray-400">No contacts found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <Modal :show="showAddModal" @close="closeAddModal">
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4">Add Contact</h2>
          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Full Name</label>
              <input v-model="form.full_name" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="form.errors.full_name" class="text-red-500 text-xs mt-1">{{ form.errors.full_name }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Phone Number</label>
              <input v-model="form.phone_number" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="e.g. 22123456 or +21622123456" />
              <div v-if="form.errors.phone_number" class="text-red-500 text-xs mt-1">{{ form.errors.phone_number }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Group</label>
              <select v-model="form.group_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Select a group</option>
                <option v-for="group in props.groups" :key="group.id" :value="group.id">{{ group.name }}</option>
              </select>
              <div v-if="form.errors.group_id" class="text-red-500 text-xs mt-1">{{ form.errors.group_id }}</div>
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" @click="closeAddModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow">Create</button>
            </div>
          </form>
        </div>
      </Modal>
      <Modal :show="showEditModal" @close="closeEditModal">
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4">Edit Contact</h2>
          <form @submit.prevent="submitEditForm" class="space-y-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Full Name</label>
              <input v-model="editForm.full_name" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="editForm.errors.full_name" class="text-red-500 text-xs mt-1">{{ editForm.errors.full_name }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Phone Number</label>
              <input v-model="editForm.phone_number" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="e.g. 22123456 or +21622123456" />
              <div v-if="editForm.errors.phone_number" class="text-red-500 text-xs mt-1">{{ editForm.errors.phone_number }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Group</label>
              <select v-model="editForm.group_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Select a group</option>
                <option v-for="group in props.groups" :key="group.id" :value="group.id">{{ group.name }}</option>
              </select>
              <div v-if="editForm.errors.group_id" class="text-red-500 text-xs mt-1">{{ editForm.errors.group_id }}</div>
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" @click="closeEditModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow">Update</button>
            </div>
          </form>
        </div>
      </Modal>
      <Modal :show="showImportModal" @close="closeImportModal">
        <div class="p-6">
          <h2 class="text-xl font-bold mb-4">Import Contacts from CSV</h2>
          <div v-if="!importSummary">
            <div class="flex items-center gap-4 mb-4">
              <input type="file" accept=".csv,text/csv" @change="handleFileChange" />
              <button @click="downloadSampleCsv" class="text-blue-600 underline text-sm">Download sample CSV</button>
            </div>
            <div v-if="headerError" class="text-red-600 text-sm mb-4">{{ headerError }}</div>
            <div v-if="csvHeaders.length" class="mb-4">
              <h3 class="font-semibold mb-2">Map CSV columns to contact fields:</h3>
              <div class="mb-2">
                <label class="block text-gray-700 font-semibold mb-1">Full Name</label>
                <select v-model="mapping.full_name" class="w-full border rounded-lg px-3 py-2">
                  <option value="">Select column</option>
                  <option v-for="header in csvHeaders" :key="header" :value="header">{{ header }}</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="block text-gray-700 font-semibold mb-1">Phone Number</label>
                <select v-model="mapping.phone_number" class="w-full border rounded-lg px-3 py-2">
                  <option value="">Select column</option>
                  <option v-for="header in csvHeaders" :key="header" :value="header">{{ header }}</option>
                </select>
              </div>
              <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-1">Group</label>
                <select v-model="mapping.group_name" class="w-full border rounded-lg px-3 py-2">
                  <option value="">Select column</option>
                  <option v-for="header in csvHeaders" :key="header" :value="header">{{ header }}</option>
                </select>
              </div>
              <button @click="submitImport" :disabled="!mapping.full_name || !mapping.phone_number || !mapping.group_name" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold shadow">Import</button>
            </div>
          </div>
          <div v-else>
            <h3 class="font-semibold mb-2">Import Summary</h3>
            <div class="mb-2">Imported: <span class="font-bold text-green-700">{{ importSummary.imported }}</span></div>
            <div class="mb-2">Failed: <span class="font-bold text-red-700">{{ importSummary.failed }}</span></div>
            <ul v-if="importSummary.errors && importSummary.errors.length" class="text-red-600 text-xs list-disc pl-5">
              <li v-for="err in importSummary.errors" :key="err">{{ err }}</li>
            </ul>
            <button @click="closeImportModal" class="mt-4 bg-gray-200 hover:bg-gray-300 rounded-lg px-4 py-2 font-semibold">Close</button>
          </div>
        </div>
      </Modal>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, reactive } from 'vue';
import { router, usePage, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import Papa from 'papaparse';

const props = defineProps({
  contacts: Array,
  search: String,
  groups: Array,
});

const searchInput = ref(props.search || '');
const showAddModal = ref(false);
const showEditModal = ref(false);
const showImportModal = ref(false);
const editingContact = ref(null);
const importFile = ref(null);
const csvHeaders = ref([]);
const mapping = reactive({ full_name: '', phone_number: '', group_name: '' });
const importSummary = ref(null);
const headerError = ref('');

function searchContacts() {
  router.get('/contacts', { search: searchInput.value }, { preserveState: true, replace: true });
}

function openAddModal() {
  showAddModal.value = true;
}
function closeAddModal() {
  showAddModal.value = false;
  form.reset();
}

const form = useForm({
  full_name: '',
  phone_number: '',
  group_id: '',
});

function submitForm() {
  form.post('/contacts', {
    onSuccess: () => {
      closeAddModal();
      router.reload({ only: ['contacts'] });
    },
  });
}

const editForm = useForm({
  full_name: '',
  phone_number: '',
  group_id: '',
});

function openEditModal(contact) {
  editingContact.value = contact;
  editForm.reset();
  editForm.full_name = contact.full_name;
  editForm.phone_number = contact.phone_number;
  editForm.group_id = contact.group_id || '';
  showEditModal.value = true;
}
function closeEditModal() {
  showEditModal.value = false;
  editingContact.value = null;
  editForm.reset();
}
function submitEditForm() {
  if (!editingContact.value) return;
  editForm.put(`/contacts/${editingContact.value.id}`, {
    onSuccess: () => {
      closeEditModal();
      router.reload({ only: ['contacts'] });
    },
  });
}

function deleteContact(id) {
  if (confirm('Are you sure you want to delete this contact?')) {
    router.delete(`/contacts/${id}`);
  }
}

function openImportModal() {
  showImportModal.value = true;
  importFile.value = null;
  csvHeaders.value = [];
  mapping.full_name = '';
  mapping.phone_number = '';
  mapping.group_name = '';
  importSummary.value = null;
}
function closeImportModal() {
  showImportModal.value = false;
  importFile.value = null;
  csvHeaders.value = [];
  mapping.full_name = '';
  mapping.phone_number = '';
  mapping.group_name = '';
  importSummary.value = null;
}
function handleFileChange(e) {
  const file = e.target.files[0];
  console.log('File selected:', file);
  if (!file) return;
  importFile.value = file;
  headerError.value = '';
  Papa.parse(file, {
    header: true,
    preview: 1,
    complete: (results) => {
      console.log('PapaParse results:', results);
      csvHeaders.value = results.meta.fields || [];
      if (!csvHeaders.value.length) {
        headerError.value = 'No headers found in the CSV file. Please make sure the first row contains column names.';
      } else {
        console.log('Detected headers:', csvHeaders.value);
      }
    },
    error: (err) => {
      headerError.value = 'Failed to parse CSV file.';
      console.error('PapaParse error:', err);
    }
  });
}
function downloadSampleCsv() {
  const sample = 'Full Name,Phone Number,Group\nJohn Doe,+1234567890,Friends';
  const blob = new Blob([sample], { type: 'text/csv' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'sample_contacts.csv';
  a.click();
  URL.revokeObjectURL(url);
}
async function submitImport() {
  if (!importFile.value) return;
  const formData = new FormData();
  formData.append('file', importFile.value);
  formData.append('mapping[full_name]', mapping.full_name);
  formData.append('mapping[phone_number]', mapping.phone_number);
  formData.append('mapping[group_name]', mapping.group_name);
  importSummary.value = null;
  try {
    const response = await fetch(route('contacts.importCsv'), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
      body: formData,
    });
    const data = await response.json();
    importSummary.value = data;
    if (data.imported > 0) {
      router.reload({ only: ['contacts'] });
    }
  } catch (e) {
    importSummary.value = { imported: 0, failed: 0, errors: ['Import failed.'] };
  }
}
</script> 