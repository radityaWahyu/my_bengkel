<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

withDefaults(
  defineProps<{
    active?: boolean;
    to?: any;
  }>(),
  {
    active: false,
    to: "/",
  }
);
</script>
<template>
  <Link
    :href="to"
    :class="{ 'bg-blue-100 text-primary border-l-sky-400': active }"
    class="flex items-center gap-3 pl-12 py-3 text-muted-foreground transition-all border-l-[6px] border-l-sky-100 hover:text-primary hover:bg-gray-100"
  >
    <slot />
  </Link>
</template>
