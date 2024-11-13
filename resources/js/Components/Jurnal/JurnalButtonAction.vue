<script setup lang="ts">
import { ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { Button } from "@/shadcn/ui/button";
import { FilePenLine, Trash2 } from "lucide-vue-next";
import type { IJurnal } from "@/types/response";
import ConfirmDialog from "../App/ConfirmDialog.vue";

const emits = defineEmits<{
  (e: "updated", value: string): void;
  (e: "deleted", value: boolean): void;
}>();

const props = defineProps<{
  jurnal: IJurnal;
}>();

const deleteForm = useForm({});
const openConfirmDialog = ref<boolean>(false);
const onDelete = () => {
  openConfirmDialog.value = false;
  deleteForm.delete(route("backoffice.jurnal.delete", props.jurnal.id), {
    onError: (error) => console.log(error),
    onSuccess: () => emits("deleted", true),
  });
};
const onUpdate = () => {
  emits("updated", props.jurnal.id);
};
</script>
<template>
  <div class="flex items-center justify-center gap-1 w-full">
    <Button
      type="button"
      variant="outline"
      size="icon"
      @click="onUpdate"
      :disabled="deleteForm.processing"
      v-if="jurnal.transaction_id === null"
    >
      <svg
        class="size-4 animate-spin"
        viewBox="0 0 100 100"
        v-if="deleteForm.processing"
      >
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
    <Button
      type="button"
      variant="outline"
      size="icon"
      @click="openConfirmDialog = true"
      :disabled="deleteForm.processing"
      v-if="jurnal.transaction_id === null"
    >
      <svg
        class="size-4 animate-spin"
        viewBox="0 0 100 100"
        v-if="deleteForm.processing"
      >
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
