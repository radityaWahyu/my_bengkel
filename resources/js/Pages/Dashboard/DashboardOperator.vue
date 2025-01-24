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
        <Card class="shrink w-72">
          <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-1"
          >
            <CardTitle class="text-sm font-medium"> Saldo </CardTitle>
            <Activity class="size-4" />
          </CardHeader>
          <CardContent>
            <div class="py-4" v-if="isLoading">
              <Skeleton class="h-2 w-full" />
            </div>
            <div class="text-2xl font-bold" v-else>{{ saldo }}</div>
            <p class="text-xs text-muted-foreground">
              jumlah saldo yang telah masuk
            </p>
          </CardContent>
        </Card>
        <Card class="shrink w-72">
          <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-1"
          >
            <CardTitle class="text-sm font-medium"> Pemasukan </CardTitle>
            <Activity class="size-4" />
          </CardHeader>
          <CardContent>
            <div class="py-4" v-if="isLoading">
              <Skeleton class="h-2 w-full" />
            </div>
            <div class="text-2xl font-bold" v-else>{{ income_now }}</div>
            <p class="text-xs text-muted-foreground">
              Jumlah pemasukan bulan ini
            </p>
          </CardContent>
        </Card>
        <Card class="shrink w-72">
          <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-1"
          >
            <CardTitle class="text-sm font-medium"> Pengeluaran </CardTitle>
            <Activity class="size-4" />
          </CardHeader>
          <CardContent>
            <div class="py-4" v-if="isLoading">
              <Skeleton class="h-2 w-full" />
            </div>
            <div class="text-2xl font-bold" v-else>{{ expense_now }}</div>
            <p class="text-xs text-muted-foreground">
              Jumlah pengeluaran bulan ini
            </p>
          </CardContent>
        </Card>
        <Card class="shrink w-72">
          <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-1"
          >
            <CardTitle class="text-sm font-medium"> Perbaikan </CardTitle>
            <Activity class="size-4" />
          </CardHeader>
          <CardContent>
            <div class="py-4" v-if="isLoading">
              <Skeleton class="h-2 w-full" />
            </div>
            <div class="text-2xl font-bold" v-else>
              {{ service_count }}
            </div>
            <p class="text-xs text-muted-foreground">jumlah perbaikan</p>
          </CardContent>
        </Card>
        <Card class="shrink w-72">
          <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-1"
          >
            <CardTitle class="text-sm font-medium">
              Perbaikan Selesai
            </CardTitle>
            <Activity class="size-4" />
          </CardHeader>
          <CardContent>
            <div class="py-4" v-if="isLoading">
              <Skeleton class="h-2 w-full" />
            </div>
            <div class="text-2xl font-bold" v-else>
              {{ service_finished }}
            </div>
            <p class="text-xs text-muted-foreground">
              jumlah perbaikan selesai
            </p>
          </CardContent>
        </Card>
        <Card class="shrink w-72">
          <CardHeader
            class="flex flex-row items-center justify-between space-y-0 pb-1"
          >
            <CardTitle class="text-sm font-medium">
              Customer Terdaftar
            </CardTitle>
            <Activity class="size-4" />
          </CardHeader>
          <CardContent>
            <div class="py-4" v-if="isLoading">
              <Skeleton class="h-2 w-full" />
            </div>
            <div class="text-2xl font-bold" v-else>
              {{ customer_count }}
            </div>
            <p class="text-xs text-muted-foreground">
              jumlah customer yang terdaftar
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>
