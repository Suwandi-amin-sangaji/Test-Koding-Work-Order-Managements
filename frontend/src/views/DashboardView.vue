<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="card bg-blue-50 border-blue-200">
        <h3 class="text-lg font-semibold mb-2 text-blue-800">Work Orders</h3>
        <div class="text-3xl font-bold text-blue-700">
          {{ workOrders.length }}
        </div>
        <p class="text-sm text-blue-600 mt-2">
          {{ isProductionManager ? "Total work orders" : "Assigned to you" }}
        </p>
      </div>

      <div class="card bg-yellow-50 border-yellow-200">
        <h3 class="text-lg font-semibold mb-2 text-yellow-800">Pending</h3>
        <div class="text-3xl font-bold text-yellow-700">{{ pendingCount }}</div>
        <p class="text-sm text-yellow-600 mt-2">Work orders waiting to start</p>
      </div>

      <div class="card bg-green-50 border-green-200">
        <h3 class="text-lg font-semibold mb-2 text-green-800">In Progress</h3>
        <div class="text-3xl font-bold text-green-700">
          {{ inProgressCount }}
        </div>
        <p class="text-sm text-green-600 mt-2">Work orders currently active</p>
      </div>
    </div>

    <div class="mt-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">
          {{ isProductionManager ? "Recent Work Orders" : "Your Work Orders" }}
        </h2>
        <div v-if="isProductionManager">
          <router-link to="/work-orders/create" class="btn btn-primary">
            Create Work Order
          </router-link>
        </div>
      </div>

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
              <th class="py-3 px-4 text-left font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in workOrders.slice(0, 5)" :key="order.id" class="border-t hover:bg-gray-50">
              <td class="py-3 px-4">{{ order.wo_number }}</td>
              <td class="py-3 px-4">{{ order.product_name }}</td>
              <td class="py-3 px-4">{{ order.quantity }}</td>
              <td class="py-3 px-4">
                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusClass(order.status)">
                  {{ order.status }}
                </span>
              </td>
              <td class="py-3 px-4">{{ formatDate(order.deadline) }}</td>
              <td class="py-3 px-4">
                <div class="flex space-x-2">
                  <router-link :to="`/work-orders/${order.id}`" class="text-blue-600 hover:underline">View</router-link>

                  <router-link
                    v-if="isProductionManager"
                    :to="`/work-orders/${order.id}/edit`"
                    class="text-blue-600 hover:underline"
                  >
                    Edit
                  </router-link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4 text-right">
          <router-link :to="isProductionManager ? '/work-orders' : '/'" class="text-blue-600 hover:underline">
            View all work orders
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "../stores/auth";
import { useWorkOrdersStore } from "../stores/workOrders";

const authStore = useAuthStore();
const workOrdersStore = useWorkOrdersStore();

const isProductionManager = computed(() => authStore.userRole === "production_manager");
const workOrders = computed(() => workOrdersStore.workOrders);
const loading = computed(() => workOrdersStore.loading);
const error = computed(() => workOrdersStore.error);

const pendingCount = computed(() => workOrders.value.filter((order) => order.status === "Pending").length);
const inProgressCount = computed(() => workOrders.value.filter((order) => order.status === "In Progress").length);

function getStatusClass(status) {
  switch (status) {
    case "Pending":
      return "bg-yellow-100 text-yellow-800";
    case "In Progress":
      return "bg-blue-100 text-blue-800";
    case "Completed":
      return "bg-green-100 text-green-800";
    case "Canceled":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("en-US", { year: "numeric", month: "short", day: "numeric" }).format(date);
}

function isOperatorAssigned(order) {
  return authStore.userRole === "operator" && order.assigned_to === authStore.userId;
}

onMounted(async () => {
  await workOrdersStore.fetchWorkOrders();
});
</script>
