import { createRouter, createWebHistory } from "vue-router"
import { useAuthStore } from "../stores/auth"
import LoginView from "../views/LoginView.vue"
import DashboardView from "../views/DashboardView.vue"
import WorkOrdersView from "../views/WorkOrdersView.vue"
import WorkOrderDetailView from "../views/WorkOrderDetailView.vue"
import CreateWorkOrderView from "../views/CreateWorkOrderView.vue"
import EditWorkOrderView from "../views/EditWorkOrderView.vue"
import ReportsView from "../views/ReportsView.vue"
import NotFoundView from "../views/NotFoundView.vue"

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/login",
      name: "login",
      component: LoginView,
      meta: { requiresAuth: false },
    },
    {
      path: "/",
      name: "dashboard",
      component: DashboardView,
      meta: { requiresAuth: true },
    },
    {
      path: "/work-orders",
      name: "work-orders",
      component: WorkOrdersView,
      meta: { requiresAuth: true, roles: ["production_manager"] },
    },
    {
      path: "/work-orders/create",
      name: "create-work-order",
      component: CreateWorkOrderView,
      meta: { requiresAuth: true, roles: ["production_manager"] },
    },
    {
      path: "/work-orders/:id",
      name: "work-order-detail",
      component: WorkOrderDetailView,
      meta: { requiresAuth: true },
    },
    {
      path: "/work-orders/:id/edit",
      name: "edit-work-order",
      component: EditWorkOrderView,
      meta: { requiresAuth: true, roles: ["production_manager"] },
    },
    {
      path: "/reports",
      name: "reports",
      component: ReportsView,
      meta: { requiresAuth: true, roles: ["production_manager"] },
    },
    {
      path: "/:pathMatch(.*)*",
      name: "not-found",
      component: NotFoundView,
    },
  ],
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated
  const userRole = authStore.userRole

  // Redirect to login if route requires auth and user is not authenticated
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: "login" })
    return
  }

  // Redirect to dashboard if user is authenticated and trying to access login
  if (to.name === "login" && isAuthenticated) {
    next({ name: "dashboard" })
    return
  }

  // Check role-based access
  if (to.meta.roles && !to.meta.roles.includes(userRole)) {
    next({ name: "dashboard" })
    return
  }

  next()
})

export default router

