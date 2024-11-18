<script setup lang="ts">
import { IService } from "@/types/response";
import { Button } from "@/shadcn/ui/button";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
  service: IService;
}>();

const serviceDetail = () => {
  router.get(
    route("backoffice.history.service-detail", props.service.id),
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
    >
      {{ service.service_code }}
    </Button>
  </div>
</template>
