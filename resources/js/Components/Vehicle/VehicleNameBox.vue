<script setup lang="ts">
import { Button } from "@/shadcn/ui/button";
import { MoveRight } from "lucide-vue-next";
import type { IVehicle } from "@/types/response";
const props = defineProps<{
  vehicle: IVehicle;
}>();

const emits = defineEmits<{
  (e: "selected", value: IVehicle): void;
}>();

const onSelect = () => {
  emits("selected", props.vehicle);
};
</script>
<template>
  <div class="w-full flex items-center justify-between">
    <div class="py-3 space-y-2">
      <div>
        <h4 class="capitalize font-semibold">
          {{ vehicle.plate_number }} - {{ vehicle.name }}
        </h4>
        <p class="text-sm text-gray-400">{{ vehicle.customer }}</p>
      </div>
      <p class="space-x-1">
        <span class="px-2 py-1 bg-sky-100 rounded text-xs text-sky-600">
          {{ vehicle.engine_volume }}cc
        </span>
        <span class="px-2 py-1 bg-sky-100 rounded text-xs text-sky-600">{{
          vehicle.engine_type === "petrol" ? "Bensin" : "Diesel"
        }}</span>
        <span class="px-2 py-1 bg-sky-100 rounded text-xs text-sky-600">{{
          vehicle.type === "car" ? "Mobil" : "Sepeda Motor"
        }}</span>
      </p>
    </div>
    <div>
      <Button as="button" variant="outline" size="icon" @click="onSelect">
        <MoveRight class="size-4" />
      </Button>
    </div>
  </div>
</template>
