<template>
  <nav class="bg-white shadow-md">
    <div class="container mx-auto px-4 max-w-7xl">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <div class="flex-shrink-0 flex items-center">
            <h1 class="text-xl font-bold text-blue-600">Work Order System</h1>
          </div>
          <div class="hidden md:ml-6 md:flex md:space-x-4">
            <router-link 
              to="/" 
              class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100"
              :class="{ 'text-blue-600 bg-blue-50': $route.path === '/' }"
            >
              Dashboard
            </router-link>
            
            <template v-if="isProductionManager">
              <router-link 
                to="/work-orders" 
                class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100"
                :class="{ 'text-blue-600 bg-blue-50': $route.path.includes('/work-orders') }"
              >
                Work Orders
              </router-link>
              <router-link 
                to="/reports" 
                class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-100"
                :class="{ 'text-blue-600 bg-blue-50': $route.path === '/reports' }"
              >
                Reports
              </router-link>
            </template>
          </div>
        </div>
        
        <div class="flex items-center">
          <div class="flex items-center">
            <span class="text-sm font-medium mr-4">{{ userName }}</span>
            <button 
              @click="logout" 
              class="px-3 py-2 rounded-md text-sm font-medium text-red-600 hover:bg-red-50"
            >
              Logout
            </button>
          </div>
          
          <!-- Mobile menu button -->
          <div class="md:hidden flex items-center ml-4">
            <button 
              @click="mobileMenuOpen = !mobileMenuOpen" 
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:bg-gray-100"
            >
              <svg 
                class="h-6 w-6" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path 
                  v-if="mobileMenuOpen" 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M6 18L18 6M6 6l12 12" 
                />
                <path 
                  v-else 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M4 6h16M4 12h16M4 18h16" 
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mobile menu -->
    <div v-if="mobileMenuOpen" class="md:hidden">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <router-link 
          to="/" 
          class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100"
          :class="{ 'text-blue-600 bg-blue-50': $route.path === '/' }"
          @click="mobileMenuOpen = false"
        >
          Dashboard
        </router-link>
        
        <template v-if="isProductionManager">
          <router-link 
            to="/work-orders" 
            class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100"
            :class="{ 'text-blue-600 bg-blue-50': $route.path.includes('/work-orders') }"
            @click="mobileMenuOpen = false"
          >
            Work Orders
          </router-link>
          <router-link 
            to="/reports" 
            class="block px-3 py-2 rounded-md text-base font-medium hover:bg-gray-100"
            :class="{ 'text-blue-600 bg-blue-50': $route.path === '/reports' }"
            @click="mobileMenuOpen = false"
          >
            Reports
          </router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const mobileMenuOpen = ref(false)

const userName = computed(() => authStore.user?.name || 'User')
const isProductionManager = computed(() => authStore.userRole === 'production_manager')

function logout() {
  authStore.logout()
}
</script>

