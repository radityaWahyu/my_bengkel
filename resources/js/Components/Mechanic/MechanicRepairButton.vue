<script setup lang="ts">
import { ref, watch } from "vue";
import { useForm as useInertiaForm, usePage } from "@inertiajs/vue3";

import { Button } from "@/shadcn/ui/button";
import { IRepairList, ERepairStatus } from "@/types/response";
import { Play, Check, Loader2 } from "lucide-vue-next";

const props = defineProps<{
  repair: IRepairList;
}>();

const emits = defineEmits<{
  (e: "finished", value: boolean): void;
}>();

const isLoading = ref<boolean>(false);
const isStarted = ref<boolean>(true);

const page = usePage();

const serviceRepair = useInertiaForm({
  _token: page.props.csrf_token,
});

const doRepair = () => {
  if (isStarted.value) {
    serviceRepair.post(
      route("backoffice.service.start-repair", props.repair.repair_id),
      {
        onStart: () => {
          isLoading.value = true;
        },
        onSuccess: () => {
          isStarted.value = false;
        },
        onError: (error) => {
          console.log(error);
        },
        onFinish: () => {
          isLoading.value = false;
        },
      }
    );
  } else {
    serviceRepair.post(
      route("backoffice.service.finish-repair", props.repair.repair_id),
      {
        onStart: () => {
          isLoading.value = true;
        },
        onSuccess: () => {
          isStarted.value = true;
          emits("finished", true);
        },
        onError: (error) => {
          console.log(error);
        },
        onFinish: () => {
          isLoading.value = false;
        },
      }
    );
  }
};
const finishRepair = () => {};

watch(
  () => props.repair,
  (values) => {
    if (values.started_at === null && values.finished_at === null) {
      isStarted.value = true;
    }

    if (values.started_at !== null && values.finished_at === null) {
      isStarted.value = false;
    }
  },
  { immediate: true }
);
</script>

<template>
  <div>
    <Button class="capitalize font-semibold space-x-2" v-if="isLoading">
      <Loader2 class="w-4 h-4 mr-2 animate-spin" />
      Proses
    </Button>
    <Button class="capitalize font-semibold space-x-2" @click="doRepair" v-else>
      <template v-if="!isStarted">
        <Check class="size-4" />
        <span>Selesai</span>
      </template>
      <template v-if="isStarted">
        <Play class="size-4" />
        <span>Mulai</span>
      </template>
    </Button>
  </div>
</template>
