<script setup lang="ts">
import type { IService } from "@/types/response";

defineProps<{
  service: IService;
}>();
</script>
<template>
  <div class="inline-flex font-semibold">
    <span
      v-if="service.status === 'waiting'"
      class="bg-yellow-100 px-2 py-1 text-yellow-800"
    >
      Menunggu Pengecekan...
    </span>
    <span
      v-if="service.status === 'approved'"
      class="bg-blue-100 text-blue-500 px-2 py-1"
    >
      Menunggu proses service
    </span>
    <span
      v-if="service.status === 'process'"
      class="bg-blue-100 text-blue-500 px-2 py-1"
    >
      Proses Service...
    </span>
    <span
      v-if="service.status === 'pending'"
      class="bg-gray-100 text-yellow-500 px-2 py-1"
    >
      Service Pending
    </span>
    <span
      v-if="service.status === 'finish'"
      class="bg-green-100 text-green-500 px-2 py-1"
    >
      Service Selesai
    </span>
  </div>
</template>
