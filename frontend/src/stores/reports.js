import { defineStore } from "pinia"
import { ref } from "vue"
import axios from "axios"

export const useReportsStore = defineStore("reports", () => {
  const reports = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchReports() {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get("/api/production-manager/reports")

      if (!response.data.error) {
        // Store the array of work orders directly
        reports.value = response.data.data
      } else {
        error.value = response.data.message
      }
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch reports"
    } finally {
      loading.value = false
    }
  }

  return {
    reports,
    loading,
    error,
    fetchReports,
  }
})

