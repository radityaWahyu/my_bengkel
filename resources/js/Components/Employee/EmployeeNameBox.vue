<script setup lang="ts">
import { UserRound } from "lucide-vue-next";

defineProps<{
  name: string;
}>();
</script>
<template>
  <div class="flex items-center gap-2 font-semibold text-sky-700 w-full capitalize">
    <div class="p-2 bg-sky-100 rounded-full"><UserRound class="size-4" /></div>
    <span>{{ name }}</span>
  </div>
</template>
