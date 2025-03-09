<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Work Orders</h1>
      
      <router-link to="/work-orders/create" class="btn btn-primary">
        Create Work Order
      </router-link>
    </div>
    
    <!-- Filters -->
    <div class="card mb-6">
      <h2 class="text-lg font-semibold mb-4">Filters</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label for="status-filter" class="form-label">Status</label>
          <select 
            id="status-filter" 
            v-model="filters.status" 
            class="form-input"
            @change="applyFilters"
          >
            <option value="">All Statuses</option>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
            <option value="Canceled">Canceled</option>
          </select>
        </div>
        
        <div>
          <label for="date-from" class="form-label">From Date</label>
          <input 
            id="date-from" 
            v-model="filters.dateFrom" 
            type="date" 
            class="form-input"
            @change="applyFilters"
          />
        </div>
        
        <div>
          <label for="date-to" class="form-label">To Date</label>
          <input 
            id="date-to" 
            v-model="filters.dateTo" 
            type="date" 
            class="form-input"
            @change="applyFilters"
          />
        </div>
      </div>
      
      <div class="mt-4 flex justify-end">
        <button @click="resetFilters" class="btn btn-secondary">
          Reset Filters
        </button>
      </div>
    </div>
    
    <!-- Work Orders Table -->
    <div v-if="loading" class="text-center py-8">
      <p>Loading work orders...</p>
    </div>
    
    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-md">
      {{ error }}
    </div>
    
    <div v-else-if="workOrders.length === 0" class="text-center py-8">
      <p class="text-gray-500">No work orders found</p>
    </div>
    
    <div v-else class="overflow-x-auto">
      <table class="min-w-full bg-white rounded-lg overflow-hidden shadow">
        <thead class="bg-gray-100">
          <tr>
            <th class="py-3 px-4 text-left font-semibold text-gray-700">WO Number</th>
            <th class="py-3 px-4 text-left font-semibold text-gray-700">Product</th>
            <th class="py-3 px-4 text-left font-semibold text-gray-700">Quantity</th>
            <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
            <th class="py-3 px-4 text-left font-semibold text-gray-700">Deadline</th>
            <th class="py-3 px-4 text-left font-semibold text-gray-700">Operator</th>
            <th class="py-3 px-4 text-left font-semibold text-gray-700">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in workOrders" :key="order.id" class="border-t hover:bg-gray-50">
            <td class="py-3 px-4">{{ order.wo_number }}</td>
            <td class="py-3 px-4">{{ order.product_name }}</td>
            <td class="py-3 px-4">{{ order.quantity }}</td>
            <td class="py-3 px-4">
              <span 
                class="px-2 py-1 rounded-full text-xs font-medium"
                :class="getStatusClass(order.status)"
              >
                {{ order.status }}
              </span>
            </td>
            <td class="py-3 px-4">{{ formatDate(order.deadline) }}</td>
            <td class="py-3 px-4">{{ order.assigned_operator?.name || 'Not assigned' }}</td>
            <td class="py-3 px-4">
              <div class="flex space-x-2">
                <router-link 
                  :to="`/work-orders/${order.id}`" 
                  class="text-blue-600 hover:underline"
                >
                  View
                </router-link>
                
                <router-link 
                  :to="`/work-orders/${order.id}/edit`" 
                  class="text-blue-600 hover:underline"
                >
                  Edit
                </router-link>
                
                <button 
                  @click="confirmDelete(order)" 
                  class="text-red-600 hover:underline"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
        <p>Are you sure you want to delete work order <strong>{{ workOrderToDelete?.wo_number }}</strong>?</p>
        <p class="text-sm text-red-600 mt-2">This action cannot be undone.</p>
        
        <div class="mt-6 flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="btn btn-secondary">
            Cancel
          </button>
          <button @click="deleteWorkOrder" class="btn btn-danger">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useWorkOrdersStore } from '../stores/workOrders'

const workOrdersStore = useWorkOrdersStore()

const workOrders = computed(() => workOrdersStore.workOrders)
const loading = computed(() => workOrdersStore.loading)
const error = computed(() => workOrdersStore.error)

const filters = ref({
  status: '',
  dateFrom: '',
  dateTo: ''
})

const showDeleteModal = ref(false)
const workOrderToDelete = ref(null)

function getStatusClass(status) {
  switch (status) {
    case 'Pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'In Progress':
      return 'bg-blue-100 text-blue-800'
    case 'Completed':
      return 'bg-green-100 text-green-800'
    case 'Canceled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date)
}

async function applyFilters() {
  const filterParams = {}
  
  if (filters.value.status) {
    filterParams.status = filters.value.status
  }
  
  if (filters.value.dateFrom) {
    filterParams.date_from = filters.value.dateFrom
  }
  
  if (filters.value.dateTo) {
    filterParams.date_to = filters.value.dateTo
  }
  
  await workOrdersStore.fetchWorkOrders(filterParams)
}

function resetFilters() {
  filters.value = {
    status: '',
    dateFrom: '',
    dateTo: ''
  }
  
  workOrdersStore.fetchWorkOrders()
}

function confirmDelete(order) {
  workOrderToDelete.value = order
  showDeleteModal.value = true
}

async function deleteWorkOrder() {
  if (!workOrderToDelete.value) return
  
  const result = await workOrdersStore.deleteWorkOrder(workOrderToDelete.value.id)
  
  if (result.success) {
    await workOrdersStore.fetchWorkOrders()
  }
  
  showDeleteModal.value = false
  workOrderToDelete.value = null
}

onMounted(async () => {
  await workOrdersStore.fetchWorkOrders()
})
</script>

