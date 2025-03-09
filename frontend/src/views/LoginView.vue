<template>
  <div class="flex min-h-[calc(100vh-2rem)] items-center justify-center">
    <div class="w-full max-w-md">
      <div class="card">
        <h2 class="text-2xl font-bold text-center mb-6">Work Order Management System</h2>
        
        <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-md mb-4">
          {{ error }}
        </div>
        
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label for="email" class="form-label">Email</label>
            <input 
              id="email" 
              v-model="email" 
              type="email" 
              class="form-input" 
              required 
              autocomplete="email"
            />
          </div>
          
          <div>
            <label for="password" class="form-label">Password</label>
            <input 
              id="password" 
              v-model="password" 
              type="password" 
              class="form-input" 
              required 
              autocomplete="current-password"
            />
          </div>
          
          <div>
            <button 
              type="submit" 
              class="btn btn-primary w-full"
              :disabled="loading"
            >
              <span v-if="loading">Logging in...</span>
              <span v-else>Login</span>
            </button>
          </div>
        </form>
        
        <div class="mt-6">
          <div class="text-sm text-gray-600">
            <p>Demo accounts:</p>
            <ul class="mt-2 space-y-1">
              <li>Production Manager: pm@example.com</li>
              <li>Password: password</li>
              <li>Operator: op1@example.com</li>
              <li>Password: password</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  if (!email.value || !password.value) {
    error.value = 'Please enter both email and password'
    return
  }
  
  loading.value = true
  error.value = ''
  
  try {
    const result = await authStore.login(email.value, password.value)
    
    if (result.success) {
      router.push('/')
    } else {
      error.value = result.message
    }
  } catch (err) {
    error.value = 'An unexpected error occurred'
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>

