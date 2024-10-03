<script setup lang="ts">
import { Button } from "@/shadcn/ui/button";
import { MoveRight } from "lucide-vue-next";
import type { IProduct } from "@/types/response";
import { usePrice } from "@/Plugin/useNumber";

const price = usePrice();

const props = defineProps<{
  product: IProduct;
}>();

const emits = defineEmits<{
  (e: "selected", value: IProduct): void;
}>();

const onSelect = () => {
  emits("selected", props.product);
};
</script>
<template>
  <div class="w-full flex items-center justify-between">
    <div class="py-3 space-y-2">
      <div>
        <h4 class="capitalize font-semibold space-x-2">
          <span>
            {{ product.name }}
          </span>
          <span
            :class="[
              'px-2 py-1 text-xs font-medium',
              {
                'bg-sky-100': product.stock > 0,
                'bg-yellow-100': product.stock === 0,
              },
            ]"
          >
            stok : {{ product.stock }}
          </span>
        </h4>
        <p class="text-sm text-gray-400">
          {{ price.convertToRupiah(product.sale_price) }}
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
