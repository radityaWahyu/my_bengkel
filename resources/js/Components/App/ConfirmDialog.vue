<script setup lang="ts">
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/shadcn/ui/alert-dialog";

const open = defineModel<boolean>();

defineProps<{
  cancelText?: string;
  okText?: string;
}>();

const emits = defineEmits<{
  (e: "cancel", value: boolean): void;
  (e: "ok", value: boolean): void;
}>();
</script>
<template>
  <AlertDialog :open>
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>
          <slot name="title" />
        </AlertDialogTitle>
        <AlertDialogDescription>
          <slot name="description" />
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="emits('cancel', true)" class="border-none">
          <span v-if="cancelText">{{ cancelText }}</span>
          <span v-else>Batalkan</span>
        </AlertDialogCancel>
        <AlertDialogAction @click="emits('ok', true)" class="bg-primary">
          <span v-if="okText">{{ okText }}</span>
          <span v-else>Ok</span>
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
