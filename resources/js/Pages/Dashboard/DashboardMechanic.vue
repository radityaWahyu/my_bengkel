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
import { Skeleton } from "@/shadcn/ui/skeleton";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/shadcn/ui/card";
import DashboardCard from "@/Components/App/DashboardCard.vue";

type TMechanicDashboard = {
  count_repair?: number;
  count_finished_repair?: number;
};

const props = defineProps<TMechanicDashboard>();

const isLoading = ref<boolean>(false);

router.reload({
  only: ["count_repair", "count_finished_repair"],
  onStart: () => (isLoading.value = true),
  onFinish: () => (isLoading.value = false),
});
</script>

<template>
  <Head title="Dashboard Mekanik" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <House class="size-8" />
        <h1 class="text-lg font-semibold">Dashboard Mekanik</h1>
      </div>
    </div>
    <div class="px-4">
      <div class="flex flex-wrap gap-2">
        <DashboardCard
          title="Jumlah Pekerjaan"
          :content="count_repair"
          description="Jumlah perbaikan mekanik"
          :loading="isLoading"
        >
          <template #icon><Activity class="size-4" /></template>
        </DashboardCard>
        <DashboardCard
          title="Jumlah Pekerjaan Selesai"
          :content="count_finished_repair"
          description="Jumlah perbaikan selesai"
          :loading="isLoading"
        >
          <template #icon><Activity class="size-4" /></template>
        </DashboardCard>
      </div>
    </div>
  </div>
</template>
