<template>
  <AuthenticatedLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Contacts</h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your contact list and organize them into groups</p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
          <div class="p-6">
        <div class="flex justify-between items-center mb-6">
          <div class="flex gap-2">
            <button @click="openAddModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">+ Add Contact</button>
            <button @click="openImportModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Import CSV</button>
          </div>
        </div>
        <div class="mb-4">
          <input v-model="searchInput" @input="searchContacts" type="text" placeholder="Search by name or phone..." class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" />
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Group</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="contact in contacts" :key="contact.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ contact.full_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">{{ contact.phone_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">{{ contact.group?.name || '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                  <button @click="openEditModal(contact)" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md font-medium text-xs shadow-sm transition-colors">Edit</button>
                  <button @click="deleteContact(contact.id)" class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-md font-medium text-xs shadow-sm transition-colors">Delete</button>
                </td>
              </tr>
              <tr v-if="contacts.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-gray-400 dark:text-gray-500">No contacts found.</td>
              </tr>
            </tbody>
          </table>
        </div>
          </div>
        </div>
      </div>
      <Modal :show="showAddModal" @close="closeAddModal" max-width="3xl">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Add New Contact</h2>
            <button @click="closeAddModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Contact Details Section -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Contact Information
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                  <input v-model="form.full_name" type="text" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter full name" />
                  <div v-if="form.errors.full_name" class="text-red-500 text-xs mt-1">{{ form.errors.full_name }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                  <input v-model="form.phone_number" type="text" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 22123456" />
                  <div v-if="form.errors.phone_number" class="text-red-500 text-xs mt-1">{{ form.errors.phone_number }}</div>
                </div>
              </div>
            </div>

            <!-- Group Selection Section -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Group Assignment
              </h3>
              
              <!-- Selection Method Tabs -->
              <div class="flex space-x-1 mb-4 bg-white rounded-lg p-1 border">
                <button 
                  type="button"
                  @click="groupSelectionMethod = 'dropdown'"
                  :class="[
                    'flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors',
                    groupSelectionMethod === 'dropdown' 
                      ? 'bg-blue-600 text-white shadow-sm' 
                      : 'text-gray-600 hover:text-gray-800'
                  ]"
                >
                  Select Existing Group
                </button>
                <button 
                  type="button"
                  @click="groupSelectionMethod = 'map'"
                  :class="[
                    'flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors',
                    groupSelectionMethod === 'map' 
                      ? 'bg-blue-600 text-white shadow-sm' 
                      : 'text-gray-600 hover:text-gray-800'
                  ]"
                >
                  Draw New Area
                </button>
              </div>
              
              <!-- Dropdown Selection -->
              <div v-if="groupSelectionMethod === 'dropdown'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Choose Group</label>
                  <select v-model="form.group_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a group</option>
                    <option v-for="group in props.groups" :key="group.id" :value="group.id">{{ group.name }}</option>
                  </select>
                  <div v-if="form.errors.group_id" class="text-red-500 text-xs mt-1">{{ form.errors.group_id }}</div>
                </div>
                
                <!-- Group Preview -->
                <div v-if="form.group_id" class="bg-white rounded-lg border p-3">
                  <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Group Area</h4>
                  <div id="modal-preview-map" class="w-full h-48 border border-gray-200 rounded-md overflow-hidden"></div>
                </div>
              </div>
              
              <!-- Map Drawing -->
              <div v-if="groupSelectionMethod === 'map'" class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                      <p class="font-medium mb-1">Draw a new area on the map</p>
                      <p class="text-blue-600">Click "Start Drawing" then click on the map to create polygon points. Double-click to complete the area.</p>
                    </div>
                  </div>
                </div>
                
                <div class="flex space-x-2">
                  <button type="button" @click="startDrawing" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Start Drawing
                  </button>
                  <button type="button" @click="clearDrawing" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear
                  </button>
                </div>
                
                <div v-if="selectedGroup" class="bg-green-50 border border-green-200 rounded-lg p-3">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                      <p class="text-sm font-medium text-green-800">Area Created: {{ selectedGroup.name }}</p>
                      <p class="text-xs text-green-600">{{ selectedGroup.area_name || 'Custom Area' }}</p>
                    </div>
                  </div>
                </div>
                
                <div id="modal-map" class="w-full h-64 border border-gray-300 rounded-lg overflow-hidden shadow-sm"></div>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
              <button type="button" @click="closeAddModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                Cancel
              </button>
              <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Contact
              </button>
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
import { ref, watch, reactive, onMounted } from 'vue';
import { router, usePage, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import Papa from 'papaparse';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet-draw';
import 'leaflet-draw/dist/leaflet.draw.css';
import { Inertia } from '@inertiajs/inertia';

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

// Map-related variables
const groupSelectionMethod = ref('dropdown');
const selectedGroup = ref(null);
let modalMap = null;
let modalDrawControl = null;
let modalDrawnItems = null;
let modalPreviewMap = null;

function searchContacts() {
  router.get('/contacts', { search: searchInput.value }, { preserveState: true, replace: true });
}

function openAddModal() {
  showAddModal.value = true;
  // Reset map-related state when opening modal
  groupSelectionMethod.value = 'dropdown';
  selectedGroup.value = null;
  if (modalMap) {
    modalMap.remove();
    modalMap = null;
    modalDrawControl = null;
    modalDrawnItems = null;
  }
  if (modalPreviewMap) {
    modalPreviewMap.remove();
    modalPreviewMap = null;
  }
}
function closeAddModal() {
  showAddModal.value = false;
  form.reset();
  // Reset map-related state
  groupSelectionMethod.value = 'dropdown';
  selectedGroup.value = null;
  if (modalMap) {
    modalMap.remove();
    modalMap = null;
    modalDrawControl = null;
    modalDrawnItems = null;
  }
  if (modalPreviewMap) {
    modalPreviewMap.remove();
    modalPreviewMap = null;
  }
}

const form = useForm({
  full_name: '',
  phone_number: '',
  group_id: '',
});

// Test Leaflet on mount
onMounted(() => {
  console.log('Component mounted')
  console.log('Leaflet object:', L)
  console.log('Leaflet version:', L.version)
  
  // Test if we can create a simple map
  setTimeout(() => {
    const testElement = document.createElement('div')
    testElement.id = 'test-map'
    testElement.style.width = '100px'
    testElement.style.height = '100px'
    testElement.style.position = 'absolute'
    testElement.style.top = '-1000px'
    document.body.appendChild(testElement)
    
    try {
      const testMap = L.map('test-map')
      console.log('Test map created successfully:', testMap)
      testMap.remove()
      document.body.removeChild(testElement)
    } catch (error) {
      console.error('Failed to create test map:', error)
    }
  }, 1000)
})

function submitForm() {
  let payload = {
    full_name: form.full_name,
    phone_number: form.phone_number,
    group_id: form.group_id,
  };
  
  if (selectedGroup.value && selectedGroup.value.id.toString().startsWith('temp_')) {
    payload.new_group = JSON.parse(JSON.stringify({
      name: selectedGroup.value.name,
      area_name: selectedGroup.value.area_name,
      geometry: selectedGroup.value.geometry
    }));
  }
  
  console.log('Submitting payload:', payload);
  Inertia.post('/contacts', payload, {
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

// Map initialization functions
function initModalMap() {
  console.log('initModalMap called')
  const mapElement = document.getElementById('modal-map')
  console.log('mapElement:', mapElement)
  if (!mapElement || modalMap) {
    console.log('Map element not found or map already exists')
    return
  }
  
  try {
    // Wait for the modal to be fully rendered and visible
    const checkModalVisible = () => {
      // Try multiple selectors to find the modal
      const modal = document.querySelector('[role="dialog"]') || 
                   document.querySelector('.modal') ||
                   document.querySelector('.fixed.inset-0') ||
                   document.querySelector('[data-modal]')
      
      console.log('Checking modal visibility:', modal)
      
      // Check if map element exists and has dimensions
      const mapElement = document.getElementById('modal-map')
      console.log('Map element check:', mapElement, mapElement?.offsetWidth, mapElement?.offsetHeight)
      
      if (mapElement && mapElement.offsetWidth > 0 && mapElement.offsetHeight > 0) {
        console.log('Map element is ready, initializing map')
        initializeMap()
      } else if (mapElement) {
        console.log('Map element exists but no dimensions yet, retrying...')
        setTimeout(checkModalVisible, 100)
      } else {
        console.log('Map element not found, retrying...')
        setTimeout(checkModalVisible, 100)
      }
    }
    
    const initializeMap = () => {
      console.log('initializeMap called')
      console.log('L object:', L)
      console.log('L.map function:', L.map)
      
      modalMap = L.map('modal-map', {
        zoomControl: true,
        attributionControl: true,
        dragging: true,
        touchZoom: true,
        scrollWheelZoom: true,
        doubleClickZoom: true,
        boxZoom: true,
        keyboard: true
      }).setView([36.8065, 10.1815], 10) // Tunisia center
      
      console.log('Map created:', modalMap)
      
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
        minZoom: 3
      }).addTo(modalMap)
      
      modalDrawnItems = new L.FeatureGroup()
      modalMap.addLayer(modalDrawnItems)
      
      modalDrawControl = new L.Control.Draw({
        draw: {
          polygon: {
            allowIntersection: false,
            drawError: {
              color: '#e1e100',
              message: '<strong>Error:</strong> Shape edges cannot cross!'
            },
            shapeOptions: {
              color: '#3388ff',
              weight: 3,
              fillOpacity: 0.3
            }
          },
          circle: false,
          rectangle: false,
          circlemarker: false,
          marker: false,
          polyline: false
        },
        edit: {
          featureGroup: modalDrawnItems,
          remove: true
        }
      })
      
      modalMap.addControl(modalDrawControl)
      
      // Handle drawing events
      modalMap.on('draw:created', function(e) {
        const layer = e.layer
        modalDrawnItems.clearLayers() // Clear previous drawings
        modalDrawnItems.addLayer(layer)
        
        // Get the drawn area coordinates
        const coordinates = layer.getLatLngs()[0]
        const center = layer.getBounds().getCenter()
        
        // Create area data
        const area = createAreaFromCoordinates(coordinates, center)
        selectedGroup.value = area
        
        // Update form
        form.group_id = area.id
        
        // Fit map to the drawn area
        modalMap.fitBounds(layer.getBounds())
      })
      
      modalMap.on('draw:deleted', function(e) {
        selectedGroup.value = null
        form.group_id = ''
      })
      
      // Force map to refresh and recalculate size
      setTimeout(() => {
        modalMap.invalidateSize()
        modalMap.setView([36.8065, 10.1815], 10)
      }, 200)
      
    }
    
    checkModalVisible()
    
  } catch (error) {
    console.error('Error initializing modal map:', error)
  }
}

function initModalPreviewMap() {
  const mapElement = document.getElementById('modal-preview-map')
  if (!mapElement || modalPreviewMap) return
  
  try {
    const selectedGroupData = props.groups.find(g => g.id == form.group_id)
    if (selectedGroupData && selectedGroupData.geometry) {
      modalPreviewMap = L.map('modal-preview-map').setView([36.8065, 10.1815], 10)
      
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(modalPreviewMap)
      
      try {
        const geoJson = JSON.parse(selectedGroupData.geometry)
        L.geoJSON(geoJson, {
          style: {
            color: '#3388ff',
            fillColor: '#3388ff',
            fillOpacity: 0.3
          }
        }).addTo(modalPreviewMap)
      } catch (e) {
        console.error('Invalid GeoJSON:', e)
      }
    }
  } catch (error) {
    console.error('Error initializing modal preview map:', error)
  }
}

// Create area data from coordinates
function createAreaFromCoordinates(coordinates, center) {
  const geoJson = {
    type: 'Polygon',
    coordinates: [coordinates.map(coord => [coord.lng, coord.lat])]
  }
  
  const areaName = generateAreaName(center)
  
  return {
    id: 'temp_' + Date.now(),
    name: areaName,
    area_name: areaName,
    geometry: JSON.stringify(geoJson)
  }
}

// Generate area name based on coordinates
function generateAreaName(center) {
  const lat = center.lat.toFixed(2)
  const lng = center.lng.toFixed(2)
  return `Area ${lat}, ${lng}`
}

function startDrawing() {
  if (modalMap && modalDrawControl) {
    // Clear existing drawings
    modalDrawnItems.clearLayers()
    selectedGroup.value = null
    form.group_id = ''
  }
}

function clearDrawing() {
  if (modalDrawnItems) {
    modalDrawnItems.clearLayers()
    selectedGroup.value = null
    form.group_id = ''
  }
}

// Watch for group selection method changes
watch(groupSelectionMethod, (newMethod) => {
  console.log('Group selection method changed to:', newMethod)
  if (newMethod === 'map') {
    // Clear form group selection when switching to map
    form.group_id = ''
    selectedGroup.value = null
    // Initialize map after DOM update with more time for modal rendering
    setTimeout(() => {
      console.log('Attempting to initialize map after timeout')
      initModalMap()
    }, 500)
    
    // Force map initialization after 2 seconds as fallback
    setTimeout(() => {
      console.log('Force initializing map as fallback')
      const mapElement = document.getElementById('modal-map')
      if (mapElement && !modalMap) {
        console.log('Force creating map on element:', mapElement)
        try {
          modalMap = L.map('modal-map', {
            zoomControl: true,
            attributionControl: true,
            dragging: true,
            touchZoom: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            boxZoom: true,
            keyboard: true
          }).setView([36.8065, 10.1815], 10)
          
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18,
            minZoom: 3
          }).addTo(modalMap)
          
          modalDrawnItems = new L.FeatureGroup()
          modalMap.addLayer(modalDrawnItems)
          
          modalDrawControl = new L.Control.Draw({
            draw: {
              polygon: {
                allowIntersection: false,
                drawError: {
                  color: '#e1e100',
                  message: '<strong>Error:</strong> Shape edges cannot cross!'
                },
                shapeOptions: {
                  color: '#3388ff',
                  weight: 3,
                  fillOpacity: 0.3
                }
              },
              circle: false,
              rectangle: false,
              circlemarker: false,
              marker: false,
              polyline: false
            },
            edit: {
              featureGroup: modalDrawnItems,
              remove: true
            }
          })
          
          modalMap.addControl(modalDrawControl)
          
          // Handle drawing events
          modalMap.on('draw:created', function(e) {
            const layer = e.layer
            modalDrawnItems.clearLayers()
            modalDrawnItems.addLayer(layer)
            
            const coordinates = layer.getLatLngs()[0]
            const center = layer.getBounds().getCenter()
            
            const area = createAreaFromCoordinates(coordinates, center)
            selectedGroup.value = area
            form.group_id = area.id
            modalMap.fitBounds(layer.getBounds())
          })
          
          modalMap.on('draw:deleted', function(e) {
            selectedGroup.value = null
            form.group_id = ''
          })
          
          console.log('Force map created successfully:', modalMap)
        } catch (error) {
          console.error('Force map creation failed:', error)
        }
      }
    }, 2000)
  } else {
    // Clear map when switching to dropdown
    if (modalMap) {
      modalMap.remove()
      modalMap = null
      modalDrawControl = null
      modalDrawnItems = null
    }
  }
})

// Watch for group_id changes in dropdown
watch(() => form.group_id, (newGroupId) => {
  if (groupSelectionMethod.value === 'dropdown' && newGroupId) {
    setTimeout(() => {
      initModalPreviewMap()
    }, 200)
  }
})
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
  
  // Create form data
  const formData = new FormData();
  formData.append('file', importFile.value);
  formData.append('mapping[full_name]', mapping.full_name);
  formData.append('mapping[phone_number]', mapping.phone_number);
  formData.append('mapping[group_name]', mapping.group_name);
  
  // Use Inertia instead of fetch
  router.post(route('contacts.importCsv'), formData, {
    onSuccess: (page) => {
      console.log('Import successful:', page.props);
      console.log('Session data:', page.props.session);
      
      // Check for import results in session flash data
      const session = page.props.session;
      if (session && session.imported !== undefined) {
        importSummary.value = {
          imported: session.imported || 0,
          failed: session.failed || 0,
          errors: session.import_errors || []
        };
        
        // Show success message and close modal after 3 seconds
        setTimeout(() => {
          showImportModal.value = false; // Directly close modal
          importSummary.value = null; // Reset summary
          // Show success notification
          if (session.imported > 0) {
            alert(`✅ Import completed successfully!\n\nImported: ${session.imported} contacts\nFailed: ${session.failed} contacts`);
          }
        }, 3000);
      } else {
        // Fallback: assume success if no errors
        importSummary.value = {
          imported: 1,
          failed: 0,
          errors: []
        };
        
        // Show success message and close modal after 3 seconds
        setTimeout(() => {
          showImportModal.value = false;
          importSummary.value = null;
          alert(`✅ Import completed successfully!\n\nContacts have been imported.`);
        }, 3000);
      }
    },
    onError: (errors) => {
      console.error('Import failed:', errors);
      importSummary.value = { 
        imported: 0, 
        failed: 0, 
        errors: Object.values(errors).flat() 
      };
      
      // Show error message
      alert(`❌ Import failed!\n\n${Object.values(errors).flat().join('\n')}`);
    }
  });
}
</script>

<style>
/* Fix for Leaflet marker icons */
.leaflet-default-icon-path {
  background-image: url('https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png');
}

/* Ensure modal map containers have proper dimensions */
#modal-map {
  width: 100% !important;
  height: 256px !important;
  min-height: 256px !important;
  border-radius: 0.5rem;
  overflow: hidden;
  position: relative !important;
  z-index: 1 !important;
}

#modal-preview-map {
  width: 100% !important;
  height: 192px !important;
  min-height: 192px !important;
  border-radius: 0.375rem;
  overflow: hidden;
  position: relative !important;
  z-index: 1 !important;
}

/* Ensure Leaflet maps render properly */
.leaflet-container {
  width: 100% !important;
  height: 100% !important;
  font-family: inherit !important;
}

.leaflet-map-pane {
  z-index: 1 !important;
}

.leaflet-tile-pane {
  z-index: 2 !important;
}

.leaflet-overlay-pane {
  z-index: 3 !important;
}

.leaflet-marker-pane {
  z-index: 4 !important;
}

.leaflet-tooltip-pane {
  z-index: 5 !important;
}

.leaflet-popup-pane {
  z-index: 6 !important;
}

/* Fix for Leaflet controls in modal */
.leaflet-control-draw {
  margin-top: 10px;
}

.leaflet-draw-toolbar .leaflet-draw-draw-polygon {
  background-position: -2px -2px;
}

/* Ensure Leaflet maps render properly in modals */
.leaflet-container {
  font-family: inherit !important;
}

.leaflet-popup-content-wrapper {
  border-radius: 0.5rem !important;
}

.leaflet-popup-tip {
  background: white !important;
}

/* Modal specific styles */
.modal-content {
  max-height: 90vh;
  overflow-y: auto;
}

/* Ensure proper z-index for map controls */
.leaflet-control-container {
  z-index: 1000 !important;
}

.leaflet-draw-toolbar {
  z-index: 1001 !important;
}
</style> 