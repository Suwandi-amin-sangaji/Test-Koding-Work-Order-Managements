<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Edit Work Order</h1>
      
      <div class="flex space-x-2">
        <router-link :to="`/work-orders/${workOrderId}`" class="btn btn-secondary">
          Cancel
        </router-link>
      </div>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <p>Loading work order...</p>
    </div>
    
    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-md">
      {{ error }}
    </div>
    
    <div v-else-if="!workOrder" class="text-center py-8">
      <p class="text-gray-500">Work order not found</p>
    </div>
    
    <div v-else class="card">
      <form @submit.prevent="updateWorkOrder" class="space-y-4">
        <div>
          <label for="wo-number" class="form-label">Work Order Number</label>
          <input 
            id="wo-number" 
            v-model="workOrderData.wo_number" 
            type="text" 
            class="form-input" 
            disabled
          />
          <p class="text-sm text-gray-500 mt-1">Work order number cannot be changed</p>
        </div>
        
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
        
        <div v-if="updateError" class="bg-red-50 text-red-600 p-3 rounded-md">
          {{ updateError }}
        </div>
        
        <div>
          <button type="submit" class="btn btn-primary" :disabled="submitting">
            <span v-if="submitting">Updating...</span>
            <span v-else>Update Work Order</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useWorkOrdersStore } from '../stores/workOrders'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const workOrdersStore = useWorkOrdersStore()

const workOrderId = computed(() => route.params.id)
const workOrder = computed(() => workOrdersStore.currentWorkOrder)
const loading = computed(() => workOrdersStore.loading)
const error = computed(() => workOrdersStore.error)

const workOrderData = ref({
  wo_number: '',
  product_name: '',
  quantity: 1,
  deadline: '',
  status: 'Pending',
  assigned_operator_id: ''
})

const operators = ref([])
const submitting = ref(false)
const updateError = ref('')

async function fetchOperators() {
  try {
    const response = await axios.get('/api/production-manager/list-operators')

    console.log('Response dari API:', response.data) // Debugging

    if (response.status === 200 && response.data && !response.data.error) {
      operators.value = response.data.data
      console.log('Operators:', operators.value) // Debugging
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


function formatDateForInput(dateString) {
  const date = new Date(dateString)
  return date.toISOString().slice(0, 16)
}

async function updateWorkOrder() {
  if (!validateForm()) return
  
  submitting.value = true
  updateError.value = ''
  
  try {
    const result = await workOrdersStore.updateWorkOrder(workOrderId.value, {
      product_name: workOrderData.value.product_name,
      quantity: workOrderData.value.quantity,
      deadline: workOrderData.value.deadline,
      status: workOrderData.value.status,
      assigned_operator_id: workOrderData.value.assigned_operator_id
    })
    
    if (result.success) {
      router.push(`/work-orders/${workOrderId.value}`)
    } else {
      updateError.value = result.message
    }
  } catch (err) {
    updateError.value = 'An unexpected error occurred'
    console.error(err)
  } finally {
    submitting.value = false
  }
}

function validateForm() {
  if (!workOrderData.value.product_name) {
    updateError.value = 'Product name is required'
    return false
  }
  
  if (!workOrderData.value.quantity || workOrderData.value.quantity < 1) {
    updateError.value = 'Quantity must be at least 1'
    return false
  }
  
  if (!workOrderData.value.deadline) {
    updateError.value = 'Deadline is required'
    return false
  }
  
  if (!workOrderData.value.status) {
    updateError.value = 'Status is required'
    return false
  }
  
  if (!workOrderData.value.assigned_operator_id) {
    updateError.value = 'Assigned operator is required'
    return false
  }
  
  return true
}

watch(workOrder, (newWorkOrder) => {
  if (newWorkOrder) {
    workOrderData.value = {
      wo_number: newWorkOrder.wo_number,
      product_name: newWorkOrder.product_name,
      quantity: newWorkOrder.quantity,
      deadline: formatDateForInput(newWorkOrder.deadline),
      status: newWorkOrder.status,
      assigned_operator_id: newWorkOrder.assigned_operator_id
    }
  }
})

onMounted(async () => {
  await Promise.all([
    workOrdersStore.fetchWorkOrder(workOrderId.value),
    fetchOperators()
  ])
})
</script>

