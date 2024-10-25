<script setup lang="ts">
import { ISale } from "@/types/response";
import { Button } from "@/shadcn/ui/button";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
  sale: ISale;
}>();

const serviceDetail = () => {
  router.get(
    route("backoffice.sale.detail", props.sale.id),
    {},
    {
      replace: true,
    }
  );
};
</script>
<template>
  <div>
    <Button
      variant="link"
      class="capitalize font-semibold"
      @click="serviceDetail"
      v-if="sale.status === 'finish'"
    >
      {{ sale.sale_code }}
    </Button>
    <p class="bg-yellow-100 text-yellow-800 font-semibold" v-else>
      Proses Transaksi...
    </p>
  </div>
</template>
