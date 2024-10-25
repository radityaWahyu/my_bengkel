<script setup lang="ts">
import { IPurchase } from "@/types/response";
import { Button } from "@/shadcn/ui/button";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
  purchase: IPurchase;
}>();

const serviceDetail = () => {
  router.get(
    route("backoffice.purchase.detail", props.purchase.id),
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
      v-if="purchase.status === 'finish'"
    >
      {{ purchase.purchase_code }}
    </Button>
    <p class="bg-yellow-100 text-yellow-800 font-semibold" v-else>
      Proses Transaksi...
    </p>
  </div>
</template>
