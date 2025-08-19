<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import Sidebar from '@/Components/Sidebar.vue';

const props = defineProps({
  user: Object,
});

const page = usePage();

// Get user data from props or page props
const user = computed(() => {
  return props.user || page.props.auth?.user || null;
});

const isSidebarOpen = ref(false);

// Close sidebar when clicking outside on mobile
const closeSidebar = () => {
  isSidebarOpen.value = false;
};

// Toggle sidebar
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

// Close sidebar on route change
onMounted(() => {
  // Close sidebar when route changes (for mobile)
  if (window.innerWidth < 1024) {
    isSidebarOpen.value = false;
  }
});
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Navigation -->
    <Navigation 
      :user="user" 
      @toggle-sidebar="toggleSidebar"
    />

    <!-- Sidebar and Main Content -->
    <div class="flex">
      <!-- Sidebar -->
      <Sidebar 
        :is-open="isSidebarOpen" 
        :user="user"
        @close="closeSidebar"
      />

      <!-- Main Content -->
      <main class="flex-1 transition-all duration-300 bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen p-6">
          <slot />
        </div>
      </main>
    </div>

    <!-- Mobile sidebar overlay -->
    <div
      v-if="isSidebarOpen"
      class="fixed inset-0 z-40 lg:hidden"
      @click="closeSidebar"
    >
      <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"></div>
    </div>
  </div>
</template>

<style scoped>
/* Smooth transitions for all elements */
* {
  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

/* Custom scrollbar for main content */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.dark ::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>
