<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Create Work Order</h1>
      
      <router-link to="/work-orders" class="btn btn-secondary">
        Cancel
      </router-link>
    </div>
    
    <div class="card">
      <form @submit.prevent="createWorkOrder" class="space-y-4">
        <div>
          <label for="product-name" class="form-label">Product Name</label>
          <input 
            id="product-name" 
            v-model="workOrderData.product_name" 
            type="text" 
            class="form-input" 
            required
          />
        </div>
        
        <div>
          <label for="quantity" class="form-label">Quantity</label>
          <input 
            id="quantity" 
            v-model.number="workOrderData.quantity" 
            type="number" 
            class="form-input" 
            required 
            min="1"
          />
        </div>
        
        <div>
          <label for="deadline" class="form-label">Deadline</label>
          <input 
            id="deadline" 
            v-model="workOrderData.deadline" 
            type="datetime-local" 
            class="form-input" 
            required
          />
        </div>
        
        <div>
          <label for="status" class="form-label">Status</label>
          <select id="status" v-model="workOrderData.status" class="form-input" required>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
            <option value="Canceled">Canceled</option>
          </select>
        </div>
        
        <div>
          <label for="operator" class="form-label">Assigned Operator</label>
          <select id="operator" v-model="workOrderData.assigned_operator_id" class="form-input" required>
            <option v-for="operator in operators" :key="operator.id" :value="operator.id">
              {{ operator.name }}
            </option>
          </select>
        </div>
        
        <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-md">
          {{ error }}
        </div>
        
        <div>
          <button type="submit" class="btn btn-primary" :disabled="submitting">
            <span v-if="submitting">Creating...</span>
            <span v-else>Create Work Order</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useWorkOrdersStore } from '../stores/workOrders'
import axios from 'axios'

const router = useRouter()
const workOrdersStore = useWorkOrdersStore()

const workOrderData = ref({
  product_name: '',
  quantity: 1,
  deadline: '',
  status: 'Pending',
  assigned_operator_id: ''
})

const operators = ref([])
const submitting = ref(false)
const error = ref('')
async function fetchOperators() {
  try {
    const response = await axios.get('/api/production-manager/list-operators')

    console.log('Response dari API:', response.data) // Debugging

    if (response.status === 200 && response.data && !response.data.error) {
      operators.value = response.data.data
    } else {
      throw new Error(response.data.message || 'Gagal mengambil data operator')
    }
  } catch (err) {
    console.error('Error fetching operators:', err.message || err)
    operators.value = [
      { id: 2, name: 'Operator 1' },
      { id: 3, name: 'Operator 2' }
    ]
  }
}


async function createWorkOrder() {
  if (!validateForm()) return
  
  submitting.value = true
  error.value = ''
  
  try {
    const result = await workOrdersStore.createWorkOrder(workOrderData.value)
    
    if (result.success) {
      router.push('/work-orders')
    } else {
      error.value = result.message
    }
  } catch (err) {
    error.value = 'An unexpected error occurred'
    console.error(err)
  } finally {
    submitting.value = false
  }
}

function validateForm() {
  if (!workOrderData.value.product_name) {
    error.value = 'Product name is required'
    return false
  }
  
  if (!workOrderData.value.quantity || workOrderData.value.quantity < 1) {
    error.value = 'Quantity must be at least 1'
    return false
  }
  
  if (!workOrderData.value.deadline) {
    error.value = 'Deadline is required'
    return false
  }
  
  if (!workOrderData.value.status) {
    error.value = 'Status is required'
    return false
  }
  
  if (!workOrderData.value.assigned_operator_id) {
    error.value = 'Assigned operator is required'
    return false
  }
  
  return true
}

onMounted(async () => {
  await fetchOperators()
  
  // Set default deadline to tomorrow
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  tomorrow.setHours(12, 0, 0, 0)
  
  workOrderData.value.deadline = tomorrow.toISOString().slice(0, 16)
  
  // Set default operator if available
  if (operators.value.length > 0) {
    workOrderData.value.assigned_operator_id = operators.value[0].id
  }
})
</script>

