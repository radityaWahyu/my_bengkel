<script lang="ts" setup>
import { type HTMLAttributes, provide } from "vue";
import { useId } from "radix-vue";
import { FORM_ITEM_INJECTION_KEY } from "./injectionKeys";
import { cn } from "@/shadcn/utils";

const props = defineProps<{
  class?: HTMLAttributes["class"];
}>();

const id = useId();
provide(FORM_ITEM_INJECTION_KEY, id);
</script>

<template>
  <div :class="cn('space-y-1 min-h-10', props.class)">
    <slot />
  </div>
</template>
