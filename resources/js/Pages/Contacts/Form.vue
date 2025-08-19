<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ formTitle }}</h1>
      <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Contact Details -->
          <div class="space-y-6">
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Full Name</label>
              <input v-model="form.full_name" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
              <div v-if="errors.full_name" class="text-red-500 text-xs mt-1">{{ errors.full_name }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Phone Number</label>
              <input v-model="form.phone_number" type="text" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="e.g. 22123456 or +21622123456" />
              <div v-if="errors.phone_number" class="text-red-500 text-xs mt-1">{{ errors.phone_number }}</div>
            </div>
            
            <!-- Group Selection -->
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Group Selection Method</label>
              <div class="flex space-x-4 mb-3">
                <label class="flex items-center">
                  <input type="radio" v-model="groupSelectionMethod" value="dropdown" class="mr-2" />
                  <span class="text-sm">Select from list</span>
                </label>
                <label class="flex items-center">
                  <input type="radio" v-model="groupSelectionMethod" value="map" class="mr-2" />
                  <span class="text-sm">Draw on map</span>
                </label>
              </div>
              
              <!-- Dropdown Selection -->
              <div v-if="groupSelectionMethod === 'dropdown'">
                <select v-model="form.group_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                  <option value="">Select a group</option>
                  <option v-for="group in groups" :key="group.id" :value="group.id">{{ group.name }}</option>
                </select>
                <div v-if="errors.group_id" class="text-red-500 text-xs mt-1">{{ errors.group_id }}</div>
              </div>
              
              <!-- Map Selection -->
              <div v-if="groupSelectionMethod === 'map'">
                <div class="mb-3">
                  <p class="text-sm text-gray-600 mb-2">Draw an area on the map to automatically create or select a group</p>
                  <div class="flex space-x-2 mb-3">
                    <button type="button" @click="startDrawing" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                      Start Drawing
                    </button>
                    <button type="button" @click="clearDrawing" class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                      Clear
                    </button>
                  </div>
                  <div v-if="selectedGroup" class="p-3 bg-green-50 border border-green-200 rounded">
                    <p class="text-sm font-medium text-green-800">Selected Group: {{ selectedGroup.name }}</p>
                    <p class="text-xs text-green-600">Area: {{ selectedGroup.area_name || 'Custom Area' }}</p>
                  </div>
                </div>
                <div id="map" class="w-full h-64 border rounded-lg"></div>
              </div>
            </div>
          </div>
          
          <!-- Map Preview (when using dropdown) -->
          <div v-if="groupSelectionMethod === 'dropdown' && form.group_id" class="space-y-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-1">Selected Group Area</label>
              <div id="preview-map" class="w-full h-64 border rounded-lg"></div>
            </div>
          </div>
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
import { computed, ref, onMounted, watch } from 'vue';
import { defineProps } from 'vue';
import L from 'leaflet';
import 'leaflet-draw';

const props = defineProps({
  contact: Object,
  groups: Array,
});

const isEdit = computed(() => !!props.contact);
const groupSelectionMethod = ref('dropdown');
const selectedGroup = ref(null);
let map = null;
let drawControl = null;
let drawnItems = null;
let previewMap = null;

const form = useForm({
  full_name: props.contact?.full_name || '',
  phone_number: props.contact?.phone_number || '',
  group_id: props.contact?.group_id || '',
});

const errors = form.errors;

const formTitle = computed(() => isEdit.value ? 'Edit Contact' : 'Add Contact');

// Initialize map for drawing
function initMap() {
  if (!map) {
    map = L.map('map').setView([36.8065, 10.1815], 10); // Tunisia center
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);
    
    drawControl = new L.Control.Draw({
      draw: {
        polygon: {
          allowIntersection: false,
          drawError: {
            color: '#e1e100',
            message: '<strong>Error:</strong> Shape edges cannot cross!'
          },
          shapeOptions: {
            color: '#3388ff'
          }
        },
        circle: false,
        rectangle: false,
        circlemarker: false,
        marker: false,
        polyline: false
      },
      edit: {
        featureGroup: drawnItems,
        remove: true
      }
    });
    
    map.addControl(drawControl);
    
    // Handle drawing events
    map.on('draw:created', function(e) {
      const layer = e.layer;
      drawnItems.addLayer(layer);
      
      // Get the drawn area coordinates
      const coordinates = layer.getLatLngs()[0];
      const center = layer.getBounds().getCenter();
      
      // Find or create group based on area
      const group = findOrCreateGroupByArea(coordinates, center);
      selectedGroup.value = group;
      form.group_id = group.id;
    });
    
    map.on('draw:deleted', function(e) {
      selectedGroup.value = null;
      form.group_id = '';
    });
  }
}

// Initialize preview map
function initPreviewMap() {
  if (!previewMap && form.group_id) {
    const selectedGroupData = props.groups.find(g => g.id == form.group_id);
    if (selectedGroupData && selectedGroupData.geometry) {
      previewMap = L.map('preview-map').setView([36.8065, 10.1815], 10);
      
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(previewMap);
      
      try {
        const geoJson = JSON.parse(selectedGroupData.geometry);
        L.geoJSON(geoJson, {
          style: {
            color: '#3388ff',
            fillColor: '#3388ff',
            fillOpacity: 0.3
          }
        }).addTo(previewMap);
      } catch (e) {
        console.error('Invalid GeoJSON:', e);
      }
    }
  }
}

// Find existing group by area or create new one
function findOrCreateGroupByArea(coordinates, center) {
  // Convert coordinates to GeoJSON
  const geoJson = {
    type: 'Polygon',
    coordinates: [coordinates.map(coord => [coord.lng, coord.lat])]
  };
  
  // Check if any existing group has similar geometry
  for (const group of props.groups) {
    if (group.geometry) {
      try {
        const existingGeoJson = JSON.parse(group.geometry);
        if (geometriesOverlap(geoJson, existingGeoJson)) {
          return group;
        }
      } catch (e) {
        console.error('Invalid GeoJSON for group:', group.id);
      }
    }
  }
  
  // Create new group based on area
  const areaName = generateAreaName(center);
  const newGroup = {
    id: 'temp_' + Date.now(),
    name: areaName,
    area_name: areaName,
    geometry: JSON.stringify(geoJson),
    user_id: null
  };
  
  return newGroup;
}

// Simple geometry overlap check
function geometriesOverlap(geo1, geo2) {
  // This is a simplified check - in production you might want more sophisticated overlap detection
  return true; // For now, assume they overlap if we're drawing in the same area
}

// Generate area name based on coordinates
function generateAreaName(center) {
  const lat = center.lat.toFixed(2);
  const lng = center.lng.toFixed(2);
  return `Area ${lat}, ${lng}`;
}

function startDrawing() {
  if (map && drawControl) {
    // Clear existing drawings
    drawnItems.clearLayers();
    selectedGroup.value = null;
    form.group_id = '';
  }
}

function clearDrawing() {
  if (drawnItems) {
    drawnItems.clearLayers();
    selectedGroup.value = null;
    form.group_id = '';
  }
}

// Watch for group selection method changes
watch(groupSelectionMethod, (newMethod) => {
  if (newMethod === 'map') {
    // Clear form group selection when switching to map
    form.group_id = '';
    selectedGroup.value = null;
    // Initialize map after DOM update
    setTimeout(() => {
      initMap();
    }, 100);
  } else {
    // Clear map when switching to dropdown
    if (map) {
      map.remove();
      map = null;
      drawControl = null;
      drawnItems = null;
    }
  }
});

// Watch for group_id changes in dropdown
watch(() => form.group_id, (newGroupId) => {
  if (groupSelectionMethod.value === 'dropdown' && newGroupId) {
    setTimeout(() => {
      initPreviewMap();
    }, 100);
  }
});

function submitForm() {
  console.log('Submitting form...');
  console.log('Form data:', form.data());
  console.log('Selected group:', selectedGroup.value);
  
  // If we have a temporary group (from map drawing), add the group data
  if (selectedGroup.value && selectedGroup.value.id.toString().startsWith('temp_')) {
    console.log('Adding new group data to form');
    form.new_group = {
      name: selectedGroup.value.name,
      area_name: selectedGroup.value.area_name,
      geometry: selectedGroup.value.geometry
    };
    console.log('New group data:', form.new_group);
  }
  
  if (isEdit.value) {
    console.log('Updating contact...');
    form.put(`/contacts/${props.contact.id}`);
  } else {
    console.log('Creating contact...');
    form.post('/contacts');
  }
}

function goBack() {
  router.visit('/contacts');
}

onMounted(() => {
  // Initialize map if starting with map method
  if (groupSelectionMethod.value === 'map') {
    setTimeout(() => {
      initMap();
    }, 100);
  }
});
</script>

<style>
/* Leaflet CSS */
@import 'leaflet/dist/leaflet.css';
@import 'leaflet-draw/dist/leaflet.draw.css';

/* Fix for Leaflet marker icons */
.leaflet-default-icon-path {
  background-image: url('https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png');
}
</style> 