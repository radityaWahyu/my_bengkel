<script setup lang="ts">
import { BadgeInfo } from "lucide-vue-next";
</script>

<template>
  <div
    class="grid grid-cols-[15%_85%] bg-sky-100/70 text-gray-800 border-l-2 border-sky-200"
  >
    <div class="h-full flex items-center justify-center">
      <BadgeInfo class="size-8 text-sky-600" />
    </div>
    <div class="p-2 text-sky-600 text-xs tracking-wide">
      <slot />
    </div>
  </div>
</template>
