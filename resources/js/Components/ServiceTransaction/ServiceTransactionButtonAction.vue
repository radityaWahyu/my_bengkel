<script setup lang="ts">
import { ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { Button } from "@/shadcn/ui/button";
import { FilePenLine, Trash2, QrCode, Printer } from "lucide-vue-next";
import type { IService } from "@/types/response";
import ConfirmDialog from "../App/ConfirmDialog.vue";

const emits = defineEmits<{
  (e: "updated", value: string): void;
  (e: "deleted", value: boolean): void;
}>();

const props = defineProps<{
  service: IService;
}>();

const deleteForm = useForm({});
const openConfirmDialog = ref<boolean>(false);
const onDelete = () => {
  openConfirmDialog.value = false;
  deleteForm.delete(route("backoffice.service.delete", props.service.id), {
    onError: (error) => console.log(error),
    onSuccess: () => emits("deleted", true),
  });
};
const serviceCheckingVehicle = () => {
  router.get(route("backoffice.service.create-invoice", props.service.id));
};
</script>
<template>
  <div class="flex items-center justify-center gap-1 w-full">
    <Button
      type="button"
      variant="outline"
      :disabled="deleteForm.processing"
      v-if="service.status === 'waiting'"
      @click="serviceCheckingVehicle"
    >
      <svg class="size-4 animate-spin" viewBox="0 0 100 100" v-if="deleteForm.processing">
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-current opacity-40"
          cx="50"
          cy="50"
          r="40"
        />
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-blue-500"
          stroke-dasharray="250"
          stroke-dashoffset="210"
          cx="50"
          cy="50"
          r="40"
        />
      </svg>
      <span v-else>Cek Kendaraan</span>
    </Button>
    <a
      v-if="service.status !== 'waiting'"
      :href="route('backoffice.service.receipt', props.service.id)"
      target="_blank"
      class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 w-9"
    >
      <svg class="size-4 animate-spin" viewBox="0 0 100 100" v-if="deleteForm.processing">
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-current opacity-40"
          cx="50"
          cy="50"
          r="40"
        />
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-blue-500"
          stroke-dasharray="250"
          stroke-dashoffset="210"
          cx="50"
          cy="50"
          r="40"
        />
      </svg>
      <QrCode class="size-4 text-blue-500" v-else />
    </a>
    <Button
      v-if="service.status !== 'waiting'"
      type="button"
      variant="outline"
      size="icon"
      @click="() => router.get(route('backoffice.service.edit', service.id))"
      :disabled="deleteForm.processing"
    >
      <svg class="size-4 animate-spin" viewBox="0 0 100 100" v-if="deleteForm.processing">
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-current opacity-40"
          cx="50"
          cy="50"
          r="40"
        />
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-blue-500"
          stroke-dasharray="250"
          stroke-dashoffset="210"
          cx="50"
          cy="50"
          r="40"
        />
      </svg>
      <FilePenLine class="size-4 text-blue-500" v-else />
    </Button>
    <a
      :href="route('backoffice.service.invoice', props.service.id)"
      target="_blank"
      class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 w-9"
      v-if="service.status === 'finish'"
    >
      <svg class="size-4 animate-spin" viewBox="0 0 100 100" v-if="deleteForm.processing">
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-current opacity-40"
          cx="50"
          cy="50"
          r="40"
        />
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-blue-500"
          stroke-dasharray="250"
          stroke-dashoffset="210"
          cx="50"
          cy="50"
          r="40"
        />
      </svg>
      <Printer class="size-4 text-blue-500" v-else />
    </a>
    <Button
      type="button"
      variant="outline"
      size="icon"
      @click="openConfirmDialog = true"
      :disabled="deleteForm.processing"
    >
      <svg class="size-4 animate-spin" viewBox="0 0 100 100" v-if="deleteForm.processing">
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-current opacity-40"
          cx="50"
          cy="50"
          r="40"
        />
        <circle
          fill="none"
          stroke-width="12"
          class="stroke-blue-500"
          stroke-dasharray="250"
          stroke-dashoffset="210"
          cx="50"
          cy="50"
          r="40"
        />
      </svg>
      <Trash2 class="size-4 text-red-500" v-else />
    </Button>
    <ConfirmDialog
      v-model="openConfirmDialog"
      cancel-text="Batalkan"
      ok-text="Hapus data"
      @cancel="openConfirmDialog = false"
      @ok="onDelete"
    >
      <template #title>
        <div>Konfirmasi Hapus Data</div>
      </template>
      <template #description>
        <div>Apakah anda ingin menghapus data ini ?</div>
      </template>
    </ConfirmDialog>
  </div>
</template>
