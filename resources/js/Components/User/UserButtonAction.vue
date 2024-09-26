<script setup lang="ts">
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { Button } from "@/shadcn/ui/button";
import { FilePenLine, Trash2, Eye, EyeOff } from "lucide-vue-next";
import type { IUser } from "@/types/response";
import ConfirmDialog from "../App/ConfirmDialog.vue";

const emits = defineEmits<{
  (e: "updated", value: string): void;
  (e: "deleted", value: boolean): void;
  (e: "reload", value: boolean): void;
}>();

const props = defineProps<{
  user: IUser;
}>();

const page = usePage();
const userForm = useForm({});
const openConfirmDialog = ref<boolean>(false);
const onDelete = () => {
  openConfirmDialog.value = false;
  userForm.delete(route("backoffice.user.delete", props.user.id), {
    onError: (error) => console.log(error),
    onSuccess: () => emits("deleted", true),
  });
};
const onEnabled = (status: boolean) => {
  userForm
    .transform((data) => ({
      ...data,
      _token: page.props.csrf_token,
      status,
    }))
    .put(route("backoffice.user.enabled", props.user.id), {
      onError: (error) => console.log(error),
      onSuccess: () => emits("reload", true),
    });
};
</script>
<template>
  <div class="space-x-2 w-full text-center">
    <Button
      type="button"
      variant="outline"
      size="icon"
      :disabled="userForm.processing"
      @click="onEnabled(!user.enabled)"
    >
      <svg
        class="size-4 animate-spin"
        viewBox="0 0 100 100"
        v-if="userForm.processing"
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
      <span v-else>
        <EyeOff class="size-4 text-blue-500" v-if="user.enabled" />
        <Eye class="size-4 text-blue-500" v-else />
      </span>
    </Button>
    <Button
      type="button"
      variant="outline"
      size="icon"
      :disabled="userForm.processing"
      @click="emits('updated', user.id)"
    >
      <svg
        class="size-4 animate-spin"
        viewBox="0 0 100 100"
        v-if="userForm.processing"
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
      :disabled="userForm.processing"
    >
      <svg
        class="size-4 animate-spin"
        viewBox="0 0 100 100"
        v-if="userForm.processing"
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
