<script setup lang="ts">
import type { IPurchase } from "@/types/response";

defineProps<{
  purchase: IPurchase;
}>();
</script>
<template>
  <div class="inline-flex font-semibold text-center">
    <span
      v-if="purchase.status === 'create'"
      class="bg-yellow-100 px-2 py-1 text-yellow-800"
    >
      Proses Transaksi...
    </span>
    <span
      v-if="purchase.status === 'finish'"
      class="bg-green-100 text-green-500 px-2 py-1"
    >
      Transaksi Selesai
    </span>
  </div>
</template>
