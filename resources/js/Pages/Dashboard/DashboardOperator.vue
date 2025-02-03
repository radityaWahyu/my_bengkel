<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { onMounted, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { House, Activity } from "lucide-vue-next";
import DashboardCard from "@/Components/App/DashboardCard.vue";

type TMainDashboard = {
  saldo?: number;
  service_count?: number;
  service_finished?: number;
  customer_count?: number;
  income_now?: number;
  expense_now?: number;
};

const props = defineProps<TMainDashboard>();

const isLoading = ref<boolean>(false);

router.reload({
  only: [
    "saldo",
    "service_count",
    "service_finished",
    "customer_count",
    "income_now",
    "expense_now",
  ],
  onBefore: () => (isLoading.value = true),
  onFinish: () => (isLoading.value = false),
});
</script>

<template>
  <Head title="Dashboard" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <House class="size-8" />
        <h1 class="text-lg font-semibold">Dashboard</h1>
      </div>
    </div>
    <div class="px-4">
      <div class="flex flex-wrap gap-2">
        <DashboardCard
          title="Saldo"
          :content="saldo"
          description="Jumlah saldo yang telah masuk"
          :loading="isLoading"
        >
          <template #icon><Activity class="size-4" /></template>
        </DashboardCard>
        <DashboardCard
          title="Pemasukan"
          :content="income_now"
          description="Jumlah pemasukan bulan ini"
          :loading="isLoading"
        >
          <template #icon><Activity class="size-4" /></template>
        </DashboardCard>
        <DashboardCard
          title="Pengeluaran"
          :content="expense_now"
          description="Jumlah pengeluaran bulan ini"
          :loading="isLoading"
        >
          <template #icon><Activity class="size-4" /></template>
        </DashboardCard>
        <DashboardCard
          title="Perbaikan"
          :content="service_count"
          description="Jumlah perbaikan yang masuk"
          :loading="isLoading"
        >
          <template #icon><Activity class="size-4" /></template>
        </DashboardCard>
        <DashboardCard
          title="Perbaikan Selesai"
          :content="service_finished"
          description="Jumlah perbaikan yang selesai"
          :loading="isLoading"
        >
          <template #icon><Activity class="size-4" /></template>
        </DashboardCard>
        <DashboardCard
          title="Customer"
          :content="customer_count"
          description="Jumlah customer yang masuk"
          :loading="isLoading"
        >
          <template #icon><Activity class="size-4" /></template>
        </DashboardCard>
      </div>
    </div>
  </div>
</template>
