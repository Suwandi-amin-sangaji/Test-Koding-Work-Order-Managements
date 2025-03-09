<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar v-if="isAuthenticated" />
    <main class="container mx-auto px-4 py-6 max-w-7xl">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from './stores/auth'
import Navbar from './components/Navbar.vue'

const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.isAuthenticated)
</script>

