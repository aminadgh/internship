<template>
  <AuthenticatedLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Group Management</h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your contact groups and organize your contacts by location and category</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                  <UserGroupIcon class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Groups</p>
                  <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats?.total_groups || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                  <UserIcon class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Contacts</p>
                  <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats?.total_contacts || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                  <CheckCircleIcon class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Groups</p>
                  <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats?.groups_with_contacts || 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                  <ExclamationTriangleIcon class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Empty Groups</p>
                  <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats?.empty_groups || 0 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Search and Actions -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
          <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <div class="flex-1 max-w-md">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Groups</label>
                <div class="relative">
                  <input
                    type="text"
                    v-model="search"
                    @input="debounceSearch"
                    placeholder="Search by name or area..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                  />
                  <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 dark:text-gray-500" />
                </div>
              </div>
              <button
                @click="navigateToCreate"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
              >
                <PlusIcon class="w-5 h-5 inline mr-1" />
                Create Group
              </button>
            </div>
          </div>
        </div>

        <!-- Groups List -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
          <div class="p-6">
            <div v-if="!groups?.data || groups.data.length === 0" class="text-center py-8">
              <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No groups found</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first group.</p>
              <div class="mt-6">
                <button
                  @click="navigateToCreate"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                >
                  <PlusIcon class="w-5 h-5 inline mr-1" />
                  Create Group
                </button>
              </div>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="group in groups.data"
                :key="group.id"
                class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                      <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ group.name }}</h3>
                      <span
                        :class="{
                          'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200': group.contacts_count > 0,
                          'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200': group.contacts_count === 0
                        }"
                        class="px-2 py-1 text-xs font-medium rounded-full"
                      >
                        {{ group.contacts_count || 0 }} contacts
                      </span>
                    </div>
                    
                    <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                      <p v-if="group.area_name">
                        <MapPinIcon class="w-4 h-4 inline mr-1" />
                        {{ group.area_name }}
                      </p>
                      <p>
                        <UserIcon class="w-4 h-4 inline mr-1" />
                        Created by {{ group.user?.name || 'Unknown' }}
                      </p>
                      <p>
                        <CalendarIcon class="w-4 h-4 inline mr-1" />
                        {{ new Date(group.created_at).toLocaleDateString() }}
                      </p>
                    </div>

                    <!-- Map Preview -->
                    <div class="mt-3">
                      <div v-if="group.geometry" class="h-32 bg-gray-100 rounded border border-gray-300 overflow-hidden">
                        <div :id="'map-preview-' + group.id" class="w-full h-full"></div>
                      </div>
                      <div v-else class="h-32 bg-gray-50 rounded border border-gray-200 flex items-center justify-center">
                        <div class="text-center text-gray-500">
                          <MapPinIcon class="w-8 h-8 mx-auto mb-2" />
                          <p class="text-sm">No map area defined</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="flex items-center space-x-2 ml-4">
                    <button
                      @click="navigateToEdit(group.id)"
                      class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 p-1 transition-colors"
                      title="Edit Group"
                    >
                      <PencilIcon class="w-5 h-5" />
                    </button>
                    <button
                      @click="deleteGroup(group)"
                      class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-1 transition-colors"
                      title="Delete Group"
                      :disabled="group.contacts_count > 0"
                    >
                      <TrashIcon class="w-5 h-5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="groups?.links && groups.links.length > 3" class="mt-6">
              <nav class="flex justify-center">
                <div class="flex space-x-1">
                  <a
                    v-for="(link, index) in groups.links"
                    :key="index"
                    :href="link.url"
                    :class="{
                      'px-3 py-2 text-sm font-medium rounded-md transition-colors': true,
                      'bg-indigo-600 text-white': link.active,
                      'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': !link.active && link.url,
                      'text-gray-300 dark:text-gray-600 cursor-not-allowed': !link.url
                    }"
                    v-html="link.label"
                  />
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
  UserGroupIcon,
  UserIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  MagnifyingGlassIcon,
  PlusIcon,
  MapPinIcon,
  CalendarIcon,
  PencilIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import L from 'leaflet'

const props = defineProps({
  groups: {
    type: Object,
    default: () => ({ data: [] })
  },
  stats: {
    type: Object,
    default: () => ({})
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const search = ref(props.filters?.search || '')

let searchTimeout = null

function debounceSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    try {
      router.get(route('groups.index'), { search: search.value }, {
        preserveState: true,
        preserveScroll: true
      })
    } catch (error) {
      console.error('Error in search:', error)
      // Fallback: use window.location
      const url = new URL(window.location)
      url.searchParams.set('search', search.value)
      window.location.href = url.toString()
    }
  }, 300)
}

function deleteGroup(group) {
  console.log('Delete group called:', group)
  
  if (group.contacts_count > 0) {
    alert('Cannot delete group with contacts. Please move or delete contacts first.')
    return
  }
  
  if (confirm(`Are you sure you want to delete "${group.name}"?`)) {
    console.log('Attempting to delete group:', group.id)
    
    try {
      const deleteUrl = route('groups.destroy', group.id)
      console.log('Delete URL:', deleteUrl)
      
      router.delete(deleteUrl, {
        onSuccess: () => {
          console.log('Group deleted successfully')
        },
        onError: (errors) => {
          console.error('Error deleting group:', errors)
          alert('Failed to delete group: ' + (errors.message || 'Unknown error'))
        }
      })
    } catch (error) {
      console.error('Exception during delete:', error)
      alert('An error occurred while trying to delete the group')
    }
  }
}

function navigateToCreate() {
  try {
    router.visit(route('groups.create'))
  } catch (error) {
    console.error('Error navigating to create:', error)
    window.location.href = '/groups/create'
  }
}

function navigateToEdit(groupId) {
  try {
    router.visit(route('groups.edit', groupId))
  } catch (error) {
    console.error('Error navigating to edit:', error)
    window.location.href = `/groups/${groupId}/edit`
  }
}

// Initialize map previews for groups with geometry
onMounted(() => {
  setTimeout(() => {
    if (props.groups?.data) {
      props.groups.data.forEach(group => {
        if (group.geometry) {
          initMapPreview(group)
        }
      })
    }
  }, 800)
})

function initMapPreview(group) {
  const mapElement = document.getElementById(`map-preview-${group.id}`)
  if (!mapElement) return
  
  try {
    // Handle different geometry formats
    let geoJson
    if (typeof group.geometry === 'string') {
      geoJson = JSON.parse(group.geometry)
    } else if (typeof group.geometry === 'object') {
      geoJson = group.geometry
    } else {
      console.warn('Invalid geometry format for group:', group.id)
      return
    }
    
    // Validate GeoJSON structure
    if (!geoJson || !geoJson.type || !geoJson.coordinates) {
      console.warn('Invalid GeoJSON structure for group:', group.id)
      return
    }
    
    const map = L.map(mapElement, {
      zoomControl: false,
      attributionControl: false,
      dragging: false,
      touchZoom: false,
      scrollWheelZoom: false,
      doubleClickZoom: false,
      boxZoom: false,
      keyboard: false
    }).setView([32.0, 10.0], 8) // Tataouine center
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map)
    
    const layer = L.geoJSON(geoJson, {
      style: {
        color: '#3388ff',
        fillColor: '#3388ff',
        fillOpacity: 0.3,
        weight: 2
      }
    }).addTo(map)
    
    // Fit map to the geometry bounds
    if (layer.getBounds) {
      map.fitBounds(layer.getBounds())
    }
    
    // Force map to refresh after initialization
    setTimeout(() => {
      map.invalidateSize()
    }, 50)
    
  } catch (e) {
    console.error('Error initializing map preview for group:', group.id, e)
    // Show a placeholder instead of broken map
    const mapElement = document.getElementById(`map-preview-${group.id}`)
    if (mapElement) {
      mapElement.innerHTML = `
        <div class="flex items-center justify-center h-full bg-gray-100 text-gray-500 text-sm">
          <MapPinIcon class="w-6 h-6 mr-2" />
          Map preview unavailable
        </div>
      `
    }
  }
}
</script>

<style>
/* Leaflet CSS for map previews */
@import 'leaflet/dist/leaflet.css';

/* Fix for Leaflet marker icons */
.leaflet-default-icon-path {
  background-image: url('https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png');
}

/* Ensure map preview containers have proper dimensions */
[id^="map-preview-"] {
  width: 100%;
  height: 100%;
  min-height: 128px; /* 8rem = 128px */
}

/* Hide map controls in previews for cleaner look */
[id^="map-preview-"] .leaflet-control-container {
  display: none;
}
</style>
