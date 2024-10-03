<script setup lang="ts">
import { Button } from "@/shadcn/ui/button";
import { MoveRight } from "lucide-vue-next";
import type { IRepair } from "@/types/response";
import { usePrice } from "@/Plugin/useNumber";

const price = usePrice();

const props = defineProps<{
  repair: IRepair;
}>();

const emits = defineEmits<{
  (e: "selected", value: IRepair): void;
}>();

const onSelect = () => {
  emits("selected", props.repair);
};
</script>
<template>
  <div class="w-full flex items-center justify-between">
    <div class="py-3 space-y-2">
      <div>
        <h4 class="capitalize font-semibold">
          {{ repair.name }}
        </h4>
        <p class="text-sm text-gray-400">
          {{ price.convertToRupiah(repair.price) }}
        </p>
      </div>
    </div>
    <div>
      <Button as="button" variant="outline" size="icon" @click="onSelect">
        <MoveRight class="size-4" />
      </Button>
    </div>
  </div>
</template>
