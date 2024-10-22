<script setup lang="ts">
import { IService } from "@/types/response";
import { Button } from "@/shadcn/ui/button";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
  service: IService;
}>();

const serviceDetail = () => {
  router.get(
    route("backoffice.service.detail", props.service.id),
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
      v-if="service.status === 'finish'"
    >
      {{ service.service_code }}
    </Button>
    <p class="bg-yellow-100 text-yellow-800 font-semibold" v-else>
      Proses Transaksi...
    </p>
  </div>
</template>
