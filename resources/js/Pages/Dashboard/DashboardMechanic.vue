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

type TMechanicDashboard = {
  count_repair?: number;
  count_finished_repair?: number;
};

const props = defineProps<TMechanicDashboard>();

const isLoading = ref<boolean>(false);

onMounted(() => {
  router.get(
    route("backoffice.dashboard.index"),
    {},
    {
      only: ["count_repair", "count_finished_repair"],
      onStart: () => (isLoading.value = true),
      onFinish: () => (isLoading.value = false),
    }
  );
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
      <div class="flex gap-2">
        <Card class="grow">
          <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-1"
          >
            <CardTitle class="text-sm font-medium">
              Jumlah Perkerjaan
            </CardTitle>
            <Activity class="size-4" />
          </CardHeader>
          <CardContent>
            <div class="py-4" v-if="isLoading">
              <Skeleton class="h-2 w-full" />
            </div>
            <div class="text-2xl font-bold" v-else>{{ count_repair }}</div>
            <p class="text-xs text-muted-foreground">
              jumlah perbaikan mekanik
            </p>
          </CardContent>
        </Card>
        <Card class="grow">
          <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-1"
          >
            <CardTitle class="text-sm font-medium">
              Jumlah Perbaikan Selesai
            </CardTitle>
            <Activity class="size-4" />
          </CardHeader>
          <CardContent>
            <div class="py-4" v-if="isLoading">
              <Skeleton class="h-2 w-full" />
            </div>
            <div class="text-2xl font-bold" v-else>
              {{ count_finished_repair }}
            </div>
            <p class="text-xs text-muted-foreground">
              jumlah perbaikan selesai
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>
