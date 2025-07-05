<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { BoltIcon, HomeIcon, UserGroupIcon, ChatBubbleLeftRightIcon, UsersIcon } from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);
</script>

<template>
  <div class="min-h-screen flex flex-col font-sans bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-30 bg-gradient-to-r from-blue-900 to-indigo-800 shadow-lg">
      <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link :href="route('dashboard')" class="flex items-center space-x-2">
            <BoltIcon class="h-8 w-8 text-yellow-400 drop-shadow" />
            <span class="text-2xl font-extrabold tracking-wide text-white drop-shadow">SwiftNotify</span>
                                </Link>
                            </div>
        <nav class="space-x-4 hidden sm:flex">
          <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="nav-link">
            <HomeIcon class="w-5 h-5 inline mr-1 -mt-1" /> Dashboard
          </NavLink>
          <NavLink :href="route('contacts.index')" :active="route().current('contacts.index')" class="nav-link">
            <UserGroupIcon class="w-5 h-5 inline mr-1 -mt-1" /> Contacts
          </NavLink>
          <NavLink :href="route('messages.index')" :active="route().current('messages.index')" class="nav-link">
            <ChatBubbleLeftRightIcon class="w-5 h-5 inline mr-1 -mt-1" /> Messages
          </NavLink>
          <NavLink v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'" :href="route('users.index')" :active="route().current('users.index')" class="nav-link">
            <UsersIcon class="w-5 h-5 inline mr-1 -mt-1" /> Users
                                </NavLink>
        </nav>
        <div class="flex items-center">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-blue-800 px-3 py-2 text-sm font-medium leading-4 text-white hover:bg-indigo-700 focus:outline-none">
                  {{ $page.props.auth.user?.name || 'User' }}
                  <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template #content>
              <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
              <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
    </header>

                <!-- Responsive Navigation Menu -->
    <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-blue-900 text-white">
      <div class="space-y-1 pb-3 pt-2 px-4">
        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
          <HomeIcon class="w-5 h-5 inline mr-1 -mt-1" /> Dashboard
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('contacts.index')" :active="route().current('contacts.index')">
          <UserGroupIcon class="w-5 h-5 inline mr-1 -mt-1" /> Contacts
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('messages.index')" :active="route().current('messages.index')">
          <ChatBubbleLeftRightIcon class="w-5 h-5 inline mr-1 -mt-1" /> Messages
        </ResponsiveNavLink>
        <ResponsiveNavLink v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'" :href="route('users.index')" :active="route().current('users.index')">
          <UsersIcon class="w-5 h-5 inline mr-1 -mt-1" /> Users
                        </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>

    <!-- Page Heading (only on dashboard) -->
    <div v-if="$page.component === 'Dashboard'" class="bg-white/80 shadow mt-2 rounded-b-xl">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex items-center">
        <h2 class="text-xl font-semibold text-gray-800">Welcome, {{ $page.props.auth.user?.name || 'User' }}!</h2>
                </div>
                </div>

            <!-- Page Content -->
    <main class="flex-1 mx-auto w-full max-w-7xl px-4 py-8">
                <slot />
            </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-blue-900 to-indigo-800 text-white text-center py-4 mt-8 shadow-inner border-t border-indigo-900">
      &copy; {{ new Date().getFullYear() }} SwiftNotify. All rights reserved.
    </footer>
    </div>
</template>

<style scoped>
.nav-link {
  @apply px-3 py-2 rounded font-medium transition-colors duration-200;
}
.nav-link:hover {
  @apply bg-indigo-700 text-white shadow;
}
.nav-link[aria-current="page"], .nav-link.active {
  @apply bg-white text-blue-900 shadow font-extrabold;
}
</style>
