<script setup lang="ts">
import { Users } from "lucide-vue-next";

defineProps<{
  name: string;
}>();
</script>
<template>
  <div class="flex items-center gap-2 font-semibold text-sky-700 w-full capitalize">
    <div class="p-2 bg-sky-100 rounded-full"><Users class="size-4" /></div>
    <span>{{ name }}</span>
  </div>
</template>
