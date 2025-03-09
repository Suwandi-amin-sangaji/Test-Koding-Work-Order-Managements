<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Work Order Details</h1>

      <div class="flex space-x-2">
        <router-link
          :to="isProductionManager ? '/work-orders' : '/'"
          class="btn btn-secondary"
        >
          Back
        </router-link>

        <router-link
          v-if="isProductionManager"
          :to="`/work-orders/${workOrderId}/edit`"
          class="btn btn-primary"
        >
          Edit
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="text-center py-8">
      <p>Loading work order details...</p>
    </div>

    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-md">
      {{ error }}
    </div>

    <div v-else-if="!workOrder" class="text-center py-8">
      <p class="text-gray-500">Work order not found</p>
    </div>

    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Work Order Details -->
        <div class="card">
          <h2 class="text-lg font-semibold mb-4">Work Order Information</h2>

          <div class="space-y-4">
            <div class="flex justify-between">
              <span class="text-gray-600">WO Number:</span>
              <span class="font-medium">{{ workOrder.wo_number }}</span>
            </div>

            <div class="flex justify-between">
              <span class="text-gray-600">Product:</span>
              <span class="font-medium">{{ workOrder.product_name }}</span>
            </div>

            <div class="flex justify-between">
              <span class="text-gray-600">Quantity:</span>
              <span class="font-medium">{{ workOrder.quantity }}</span>
            </div>

            <div class="flex justify-between">
              <span class="text-gray-600">Deadline:</span>
              <span class="font-medium">{{
                formatDate(workOrder.deadline)
              }}</span>
            </div>

            <div class="flex justify-between">
              <span class="text-gray-600">Status:</span>
              <span
                class="px-2 py-1 rounded-full text-xs font-medium"
                :class="getStatusClass(workOrder.status)"
              >
                {{ workOrder.status }}
              </span>
            </div>

            <div class="flex justify-between">
              <span class="text-gray-600">Assigned Operator:</span>
              <span class="font-medium">{{
                workOrder.assigned_operator?.name || "Not assigned"
              }}</span>
            </div>

            <div class="flex justify-between">
              <span class="text-gray-600">Created At:</span>
              <span class="font-medium">{{
                formatDate(workOrder.created_at)
              }}</span>
            </div>

            <div class="flex justify-between">
              <span class="text-gray-600">Last Updated:</span>
              <span class="font-medium">{{
                formatDate(workOrder.updated_at)
              }}</span>
            </div>
          </div>
        </div>

        <!-- Status Update (for Operator) -->
        <div v-if="isOperator && canUpdateStatus" class="card">
          <h2 class="text-lg font-semibold mb-4">Update Status</h2>

          <div
            v-if="updateSuccess"
            class="bg-green-50 text-green-600 p-3 rounded-md mb-4"
          >
            Status updated successfully!
          </div>

          <div
            v-if="updateError"
            class="bg-red-50 text-red-600 p-3 rounded-md mb-4"
          >
            {{ updateError }}
          </div>

          <form @submit.prevent="updateStatus" class="space-y-4">
            <div>
              <label for="new-status" class="form-label">New Status</label>
              <select
                id="new-status"
                v-model="statusUpdate.newStatus"
                class="form-input"
                required
              >
                <option
                  v-if="workOrder.status === 'Pending'"
                  value="In Progress"
                >
                  In Progress
                </option>
                <option
                  v-if="workOrder.status === 'In Progress'"
                  value="Completed"
                >
                  Completed
                </option>
              </select>
            </div>

            <div>
              <label for="quantity" class="form-label">Quantity</label>
              <input
                id="quantity"
                v-model.number="statusUpdate.quantity"
                type="number"
                class="form-input"
                required
                min="1"
                :max="workOrder.quantity"
              />
              <p class="text-sm text-gray-500 mt-1">
                Enter the quantity for this status update (max:
                {{ workOrder.quantity }})
              </p>
            </div>

            <div>
              <label for="notes" class="form-label">Notes</label>
              <textarea
                id="notes"
                v-model="statusUpdate.notes"
                class="form-input"
                rows="3"
                placeholder="Add any notes about this status update"
              ></textarea>
            </div>

            <div>
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="updating"
              >
                <span v-if="updating">Updating...</span>
                <span v-else>Update Status</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { useWorkOrdersStore } from "../stores/workOrders";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const workOrdersStore = useWorkOrdersStore();

const workOrderId = computed(() => route.params.id);
const workOrder = computed(() => workOrdersStore.currentWorkOrder);
const loading = computed(() => workOrdersStore.loading);
const error = computed(() => workOrdersStore.error);

const isProductionManager = computed(() => authStore.userRole === "production_manager");
const isOperator = computed(() => authStore.userRole === "operator");

const canUpdateStatus = computed(() => {
  if (!isOperator.value || !workOrder.value) return false;

  const isAssigned = workOrder.value.assigned_operator_id === authStore.user.id;
  const validStatus = ["Pending", "In Progress"].includes(workOrder.value.status);

  return isAssigned && validStatus;
});

const statusUpdate = ref({
  newStatus: "",
  quantity: 0,
  notes: "",
});

const updating = ref(false);
const updateError = ref("");
const updateSuccess = ref(false);

function getStatusClass(status) {
  switch (status) {
    case "Pending": return "bg-yellow-100 text-yellow-800";
    case "In Progress": return "bg-blue-100 text-blue-800";
    case "Completed": return "bg-green-100 text-green-800";
    case "Canceled": return "bg-red-100 text-red-800";
    default: return "bg-gray-100 text-gray-800";
  }
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }).format(date);
}

async function updateStatus() {
  if (!workOrder.value || !statusUpdate.value.newStatus || statusUpdate.value.quantity <= 0) {
    updateError.value = "Please fill in all required fields correctly";
    return;
  }

  updating.value = true;
  updateError.value = "";
  updateSuccess.value = false;

  try {
    const result = await workOrdersStore.updateWorkOrderStatus(workOrder.value.id, {
      new_status: statusUpdate.value.newStatus,
      quantity_updated: statusUpdate.value.quantity,
      notes: statusUpdate.value.notes,
    });

    if (result.success) {
      updateSuccess.value = true;
      await workOrdersStore.fetchWorkOrder(workOrderId.value);

      // Reset form
      statusUpdate.value = {
        newStatus: workOrder.value.status === "Pending" ? "In Progress" : "Completed",
        quantity: workOrder.value.quantity,
        notes: "",
      };
    } else {
      updateError.value = result.message || "Failed to update status";
    }
  } catch (err) {
    updateError.value = "An error occurred while updating the status";
    console.error("Error updating status:", err);
  } finally {
    updating.value = false;
  }
}

// Memantau perubahan workOrder untuk mengatur default status update
watch(workOrder, (newWorkOrder) => {
  if (newWorkOrder) {
    statusUpdate.value.newStatus = newWorkOrder.status === "Pending" ? "In Progress" : "Completed";
    statusUpdate.value.quantity = newWorkOrder.quantity;
  }
}, { immediate: true });

onMounted(() => {
  workOrdersStore.fetchWorkOrder(workOrderId.value);
});
</script>

