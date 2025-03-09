<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Reports</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Summary Cards -->
      <div class="card bg-blue-50 border-blue-200">
        <h3 class="text-lg font-semibold mb-2 text-blue-800">Total</h3>
        <div class="text-3xl font-bold text-blue-700">{{ workOrders.length }}</div>
        <p class="text-sm text-blue-600 mt-2">Work orders</p>
      </div>
      
      <div class="card bg-yellow-50 border-yellow-200">
        <h3 class="text-lg font-semibold mb-2 text-yellow-800">Pending</h3>
        <div class="text-3xl font-bold text-yellow-700">{{ statusCounts.Pending || 0 }}</div>
        <p class="text-sm text-yellow-600 mt-2">Work orders</p>
      </div>
      
      <div class="card bg-blue-50 border-blue-200">
        <h3 class="text-lg font-semibold mb-2 text-blue-800">In Progress</h3>
        <div class="text-3xl font-bold text-blue-700">{{ statusCounts['In Progress'] || 0 }}</div>
        <p class="text-sm text-blue-600 mt-2">Work orders</p>
      </div>
      
      <div class="card bg-green-50 border-green-200">
        <h3 class="text-lg font-semibold mb-2 text-green-800">Completed</h3>
        <div class="text-3xl font-bold text-green-700">{{ statusCounts.Completed || 0 }}</div>
        <p class="text-sm text-green-600 mt-2">Work orders</p>
      </div>
    </div>
    
    <!-- Work Order Status Summary -->
    <div class="card mb-6">
      <h2 class="text-lg font-semibold mb-4">Work Order Report</h2>
      
      <div v-if="loading" class="text-center py-4">
        <p>Loading report data...</p>
      </div>
      
      <div v-else-if="error" class="bg-red-50 text-red-600 p-3 rounded-md">
        {{ error }}
      </div>
      
      <div v-else-if="!workOrders || workOrders.length === 0" class="text-center py-4">
        <p class="text-gray-500">No data available</p>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b bg-gray-50">
              <th class="py-3 px-4 text-left font-semibold text-gray-700">WO Number</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Product</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Quantity</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Operator</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Created</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Updated</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in workOrders" :key="order.wo_number" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4 font-medium">{{ order.wo_number }}</td>
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
              <td class="py-3 px-4">{{ order.assigned_operator?.name || 'Not assigned' }}</td>
              <td class="py-3 px-4">{{ formatDate(order.created_at) }}</td>
              <td class="py-3 px-4">{{ formatDate(order.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Operator Performance -->
    <div class="card">
      <h2 class="text-lg font-semibold mb-4">Operator Performance</h2>
      
      <div v-if="loading" class="text-center py-4">
        <p>Loading report data...</p>
      </div>
      
      <div v-else-if="error" class="bg-red-50 text-red-600 p-3 rounded-md">
        {{ error }}
      </div>
      
      <div v-else-if="!operatorStats || Object.keys(operatorStats).length === 0" class="text-center py-4">
        <p class="text-gray-500">No data available</p>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b bg-gray-50">
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Operator</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Total Work Orders</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Completed</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">In Progress</th>
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Total Quantity</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(stats, operatorId) in operatorStats" :key="operatorId" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4 font-medium">{{ stats.name }}</td>
              <td class="py-3 px-4">{{ stats.totalOrders }}</td>
              <td class="py-3 px-4">
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ stats.completed }}
                </span>
              </td>
              <td class="py-3 px-4">
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ stats.inProgress }}
                </span>
              </td>
              <td class="py-3 px-4">{{ stats.totalQuantity }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useReportsStore } from '../stores/reports'

const reportsStore = useReportsStore()

const workOrders = ref([])
const loading = computed(() => reportsStore.loading)
const error = computed(() => reportsStore.error)

// Computed properties for report statistics
const statusCounts = computed(() => {
  const counts = {}
  workOrders.value.forEach(order => {
    counts[order.status] = (counts[order.status] || 0) + 1
  })
  return counts
})

const operatorStats = computed(() => {
  const stats = {}
  
  workOrders.value.forEach(order => {
    if (!order.assigned_operator) return
    
    const operatorId = order.assigned_operator.id
    
    if (!stats[operatorId]) {
      stats[operatorId] = {
        id: operatorId,
        name: order.assigned_operator.name,
        totalOrders: 0,
        completed: 0,
        inProgress: 0,
        pending: 0,
        totalQuantity: 0
      }
    }
    
    stats[operatorId].totalOrders++
    stats[operatorId].totalQuantity += order.quantity
    
    if (order.status === 'Completed') {
      stats[operatorId].completed++
    } else if (order.status === 'In Progress') {
      stats[operatorId].inProgress++
    } else if (order.status === 'Pending') {
      stats[operatorId].pending++
    }
  })
  
  return stats
})

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

onMounted(async () => {
  await reportsStore.fetchReports()
  
  if (reportsStore.reports) {
    workOrders.value = reportsStore.reports
  }
})
</script>

