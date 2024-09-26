<script setup lang="ts">
import { User, CreditCard } from "lucide-vue-next";
const props = defineProps<{
  payment: {
    bank_name: string;
    account_name: string;
    account_number: string;
  };
}>();
</script>
<template>
  <div class="px-4 capitalize w-full space-y-1">
    <p class="text-base">{{ payment.bank_name }}</p>
    <p class="py-1 flex items-center gap-2">
      <span class="flex items-center gap-2 bg-gray-100 px-2 py-1 rounded">
        <User class="size-3" />
        {{ payment.account_name }}
      </span>
      <span class="flex items-center gap-2 bg-gray-100 px-2 py-1 rounded">
        <CreditCard class="size-3" />
        {{ payment.account_number }}
      </span>
    </p>
  </div>
</template>
