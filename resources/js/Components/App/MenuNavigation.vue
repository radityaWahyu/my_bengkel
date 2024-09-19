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
    :class="{ 'bg-blue-100 text-primary': active }"
    class="flex items-center gap-3 px-4 py-4 text-muted-foreground transition-all hover:text-primary hover:bg-gray-100"
  >
    <slot />
  </Link>
</template>
