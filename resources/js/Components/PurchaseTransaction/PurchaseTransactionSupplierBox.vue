<script setup lang="ts">
import { IPurchase } from "@/types/response";

const props = defineProps<{
  purchase: IPurchase;
}>();
</script>
<template>
  <div>
    <div v-if="purchase.status === 'finish'">
      <h1>{{ purchase.supplier }}</h1>
      <p>{{ purchase.invoice_number }}</p>
    </div>
    <div v-else class="bg-yellow-100 px-2 py-1 text-yellow-800 font-semibold">
      Proses Transaksi...
    </div>
  </div>
</template>
