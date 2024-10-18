<script setup lang="ts">
import { IPurchase } from "@/types/response";

const props = defineProps<{
  purchase: IPurchase;
}>();
</script>
<template>
  <div class="inline-flex w-full">
    <span v-if="purchase.status === 'finish'">
      <h1 class="font-semibold">{{ purchase.supplier }}</h1>
      <p class="text-muted-foreground text-xs">
        No. Nota : {{ purchase.invoice_number }}
      </p>
    </span>
    <span
      v-else
      class="bg-yellow-100 px-2 py-1 text-yellow-800 font-semibold w-full"
    >
      Proses Transaksi...
    </span>
  </div>
</template>
