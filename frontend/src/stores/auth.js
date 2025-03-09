import { defineStore } from "pinia"
import { ref, computed } from "vue"
import axios from "axios"
import router from "../router"

export const useAuthStore = defineStore("auth", () => {
  const user = ref(JSON.parse(localStorage.getItem("user")) || null)
  const token = ref(localStorage.getItem("token") || null)

  const isAuthenticated = computed(() => !!token.value)

  const userRole = computed(() => {
    if (!user.value) return null
    return user.value.role_id === 1 ? "production_manager" : "operator"
  })

  async function login(email, password) {
    try {
      const response = await axios.post("/api/auth/login", { email, password })

      if (!response.data.error) {
        const userData = response.data.data.user
        const tokenValue = response.data.data.token

        user.value = userData
        token.value = tokenValue

        localStorage.setItem("user", JSON.stringify(userData))
        localStorage.setItem("token", tokenValue)

        // Set default Authorization header for all future requests
        axios.defaults.headers.common["Authorization"] = `Bearer ${tokenValue}`

        return { success: true }
      }

      return { success: false, message: response.data.message || "Login failed" }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || "An error occurred during login",
      }
    }
  }

  async function logout() {
    try {
      await axios.post("/api/auth/logout")
    } catch (error) {
      console.error("Logout error:", error)
    } finally {
      // Clear user data regardless of API response
      user.value = null
      token.value = null
      localStorage.removeItem("user")
      localStorage.removeItem("token")
      delete axios.defaults.headers.common["Authorization"]
      router.push("/login")
    }
  }

  // Initialize axios header if token exists
  if (token.value) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token.value}`
  }

  return {
    user,
    token,
    isAuthenticated,
    userRole,
    login,
    logout,
  }
})

