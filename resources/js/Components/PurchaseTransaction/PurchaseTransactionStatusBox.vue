<script setup lang="ts">
import type { ISale } from "@/types/response";

defineProps<{
  sale: ISale;
}>();
</script>
<template>
  <div class="inline-flex font-semibold">
    <span
      v-if="sale.status === 'create'"
      class="bg-yellow-100 px-2 py-1 text-yellow-800"
    >
      Proses Transaksi...
    </span>
    <span
      v-if="sale.status === 'finish'"
      class="bg-green-100 text-green-500 px-2 py-1"
    >
      Transaksi Selesai
    </span>
  </div>
</template>
