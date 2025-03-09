import { defineStore } from "pinia"
import { ref } from "vue"
import axios from "axios"
import { useAuthStore } from "./auth"

export const useWorkOrdersStore = defineStore("workOrders", () => {
  const workOrders = ref([])
  const currentWorkOrder = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const authStore = useAuthStore()

  // Get base URL based on user role
  const getBaseUrl = () => {
    return authStore.userRole === "production_manager"
      ? "/api/production-manager/work-orders"
      : "/api/operator/assigned-work-orders"
  }

  async function fetchWorkOrders(filters = {}) {
    loading.value = true
    error.value = null

    try {
      const url = getBaseUrl()
      const response = await axios.get(url, { params: filters })

      if (!response.data.error) {
        workOrders.value = response.data.data
      } else {
        error.value = response.data.message
      }
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch work orders"
    } finally {
      loading.value = false
    }
  }

  // Update the fetchWorkOrder function to use the correct endpoint for operators
  async function fetchWorkOrder(id) {
    loading.value = true
    error.value = null
    currentWorkOrder.value = null

    try {
      // Use different endpoints based on user role
      const url =
        authStore.userRole === "production_manager"
          ? `/api/production-manager/work-orders/${id}`
          : `/api/operator/work-orders/${id}` // Changed from assigned-work-orders to work-orders

      const response = await axios.get(url)

      if (!response.data.error) {
        currentWorkOrder.value = response.data.data
      } else {
        error.value = response.data.message
      }
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch work order"
    } finally {
      loading.value = false
    }
  }

  async function createWorkOrder(workOrderData) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post("/api/production-manager/work-orders", workOrderData)

      if (!response.data.error) {
        return { success: true, data: response.data.data }
      } else {
        error.value = response.data.message
        return { success: false, message: response.data.message }
      }
    } catch (err) {
      const message = err.response?.data?.message || "Failed to create work order"
      error.value = message
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  async function updateWorkOrder(id, workOrderData) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.put(`/api/production-manager/work-orders/${id}`, workOrderData)

      if (!response.data.error) {
        return { success: true, data: response.data.data }
      } else {
        error.value = response.data.message
        return { success: false, message: response.data.message }
      }
    } catch (err) {
      const message = err.response?.data?.message || "Failed to update work order"
      error.value = message
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  async function deleteWorkOrder(id) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.delete(`/api/production-manager/work-orders/${id}`)

      if (!response.data.error) {
        return { success: true }
      } else {
        error.value = response.data.message
        return { success: false, message: response.data.message }
      }
    } catch (err) {
      const message = err.response?.data?.message || "Failed to delete work order"
      error.value = message
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  async function updateWorkOrderStatus(id, statusData) {
    loading.value = true
    error.value = null

    try {
      // Changed from POST to PUT to match the API endpoint
      const response = await axios.put(`/api/operator/update-status/${id}`, statusData)

      if (!response.data.error) {
        return { success: true, data: response.data.data }
      } else {
        error.value = response.data.message
        return { success: false, message: response.data.message }
      }
    } catch (err) {
      const message = err.response?.data?.message || "Failed to update work order status"
      error.value = message
      return { success: false, message }
    } finally {
      loading.value = false
    }
  }

  return {
    workOrders,
    currentWorkOrder,
    loading,
    error,
    fetchWorkOrders,
    fetchWorkOrder,
    createWorkOrder,
    updateWorkOrder,
    deleteWorkOrder,
    updateWorkOrderStatus,
  }
})

