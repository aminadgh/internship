<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ isEdit ? 'Edit Group' : 'Create Group' }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Form Fields -->
                <div class="space-y-6">
                  <!-- Group Name -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Group Name *
                    </label>
                    <input
                      type="text"
                      v-model="form.name"
                      required
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Enter group name"
                    />
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                  </div>

                  <!-- Area Name -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Area Name
                    </label>
                    <input
                      type="text"
                      v-model="form.area_name"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Enter area name (optional)"
                    />
                    <div v-if="form.errors.area_name" class="text-red-500 text-sm mt-1">{{ form.errors.area_name }}</div>
                  </div>

                  <!-- Map Drawing Instructions -->
                  <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Define Group Area</h3>
                    <p class="text-sm text-blue-700 mb-3">
                      Draw an area on the map to define the geographic boundaries for this group. 
                      This will help organize contacts by location.
                    </p>
                    <div class="flex space-x-2">
                      <button 
                        type="button" 
                        @click="startDrawing" 
                        class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      >
                        Start Drawing
                      </button>
                      <button 
                        type="button" 
                        @click="clearDrawing" 
                        class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                      >
                        Clear
                      </button>
                    </div>
                    <div v-if="drawnArea" class="mt-3 p-2 bg-green-50 border border-green-200 rounded">
                      <p class="text-sm font-medium text-green-800">Area Defined</p>
                      <p class="text-xs text-green-600">{{ drawnArea.name }}</p>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="flex justify-end space-x-3 pt-4">
                    <Link
                      :href="route('groups.index')"
                      class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    >
                      Cancel
                    </Link>
                    <button
                      type="submit"
                      :disabled="form.processing || !drawnArea"
                      class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      {{ form.processing ? 'Saving...' : (isEdit ? 'Update Group' : 'Create Group') }}
                    </button>
                  </div>
                </div>

                <!-- Map -->
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Draw Group Area
                    </label>
                    <div id="map" class="w-full h-96 border border-gray-300 rounded-lg"></div>
                  </div>
                  
                  <!-- Map Instructions -->
                  <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <h4 class="text-sm font-medium text-gray-900 mb-1">Instructions:</h4>
                    <ul class="text-xs text-gray-600 space-y-1">
                      <li>• Click "Start Drawing" to begin</li>
                      <li>• Click on the map to create polygon points</li>
                      <li>• Double-click to complete the area</li>
                      <li>• Use "Clear" to start over</li>
                    </ul>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import L from 'leaflet'
import 'leaflet-draw'

const props = defineProps({
  group: Object
})

const isEdit = computed(() => !!props.group)
const drawnArea = ref(null)

let map = null
let drawControl = null
let drawnItems = null

const form = useForm({
  name: props.group?.name || '',
  area_name: props.group?.area_name || '',
  geometry: props.group?.geometry || ''
})

// Initialize map for drawing
function initMap() {
  const mapElement = document.getElementById('map')
  if (!mapElement || map) return
  
  try {
    map = L.map('map').setView([36.8065, 10.1815], 10) // Tunisia center
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map)
    
    drawnItems = new L.FeatureGroup()
    map.addLayer(drawnItems)
    
    drawControl = new L.Control.Draw({
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
        featureGroup: drawnItems,
        remove: true
      }
    })
    
    map.addControl(drawControl)
    
    // Handle drawing events
    map.on('draw:created', function(e) {
      const layer = e.layer
      drawnItems.clearLayers() // Clear previous drawings
      drawnItems.addLayer(layer)
      
      // Get the drawn area coordinates
      const coordinates = layer.getLatLngs()[0]
      const center = layer.getBounds().getCenter()
      
      // Create area data
      const area = createAreaFromCoordinates(coordinates, center)
      drawnArea.value = area
      
      // Update form
      form.geometry = area.geometry
      if (!form.area_name) {
        form.area_name = area.name
      }
      
      // Fit map to the drawn area
      map.fitBounds(layer.getBounds())
    })
    
    map.on('draw:deleted', function(e) {
      drawnArea.value = null
      form.geometry = ''
    })
    
    // If editing and group has geometry, show it
    if (isEdit.value && props.group?.geometry) {
      try {
        const geoJson = JSON.parse(props.group.geometry)
        const layer = L.geoJSON(geoJson, {
          style: {
            color: '#3388ff',
            fillColor: '#3388ff',
            fillOpacity: 0.3,
            weight: 3
          }
        }).addTo(map)
        drawnItems.addLayer(layer)
        
        // Set drawn area data
        const center = layer.getBounds().getCenter()
        drawnArea.value = {
          name: props.group.area_name || generateAreaName(center),
          geometry: props.group.geometry
        }
        
        // Fit map to the existing geometry
        map.fitBounds(layer.getBounds())
      } catch (e) {
        console.error('Invalid GeoJSON:', e)
      }
    }
    
    // Force map to refresh after initialization
    setTimeout(() => {
      map.invalidateSize()
    }, 100)
    
  } catch (error) {
    console.error('Error initializing map:', error)
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
    name: areaName,
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
  if (map && drawControl) {
    // Clear existing drawings
    drawnItems.clearLayers()
    drawnArea.value = null
    form.geometry = ''
  }
}

function clearDrawing() {
  if (drawnItems) {
    drawnItems.clearLayers()
    drawnArea.value = null
    form.geometry = ''
  }
}

function submit() {
  if (isEdit.value) {
    form.put(route('groups.update', props.group.id))
  } else {
    form.post(route('groups.store'))
  }
}

onMounted(() => {
  // Wait for DOM to be fully rendered
  setTimeout(() => {
    initMap()
  }, 200)
})
</script>

<style>
/* Leaflet CSS */
@import 'leaflet/dist/leaflet.css';
@import 'leaflet-draw/dist/leaflet.draw.css';

/* Fix for Leaflet marker icons */
.leaflet-default-icon-path {
  background-image: url('https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png');
}

/* Ensure map container has proper dimensions */
#map {
  width: 100%;
  height: 100%;
  min-height: 384px; /* 24rem = 384px */
}

/* Fix for Leaflet controls */
.leaflet-control-draw {
  margin-top: 10px;
}

.leaflet-draw-toolbar .leaflet-draw-draw-polygon {
  background-position: -2px -2px;
}
</style> 