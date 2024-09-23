<script setup lang="ts">
import { SquareUser } from "lucide-vue-next";

defineProps<{
  name: string;
}>();
</script>
<template>
  <div class="flex items-center gap-2 font-semibold text-sky-700 w-full capitalize">
    <SquareUser class="size-4" />
    <span>{{ name }}</span>
  </div>
</template>
