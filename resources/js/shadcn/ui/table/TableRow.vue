<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "@/shadcn/utils";

const props = defineProps<{
  class?: HTMLAttributes["class"];
}>();
</script>

<template>
  <tr
    :class="
      cn(
        'border-b border-b-gray-100 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted',
        props.class
      )
    "
  >
    <slot />
  </tr>
</template>
