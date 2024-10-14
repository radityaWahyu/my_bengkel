<script setup lang="ts">
import { ref } from "vue";
import { BadgeAlert, BadgeInfo } from "lucide-vue-next";
import { Dialog, DialogContent, DialogFooter } from "@/shadcn/ui/dialog";
import { Button } from "@/shadcn/ui/button";

const props = defineProps<{
  messages: string;
  alertType: "success" | "error";
}>();

const emits = defineEmits<{
  (e: "closed", value: boolean): void;
}>();

const dialogOpen = ref<boolean>(true);
const onClose = () => {
  dialogOpen.value = false;
  emits("closed", true);
};
</script>
<template>
  <div>
    <Dialog :open="dialogOpen">
      <DialogContent
        class="sm:max-w-[500px]"
        @escape-key-down="() => false"
        @interact-outside="() => false"
      >
        <div
          class="py-4 m-auto text-center space-y-3 border-b border-b-gray-400 border-dashed w-full"
        >
          <div
            class="rounded-full p-3 inline-flex"
            :class="{
              'bg-green-100': alertType === 'success',
              'bg-red-100': alertType === 'error',
            }"
          >
            <BadgeInfo
              class="size-14 text-green-700"
              v-if="alertType === 'success'"
            />
            <BadgeAlert
              class="size-14 text-red-700"
              v-if="alertType === 'error'"
            />
          </div>
          <div class="space-y-2">
            <h3 class="text-xl font-semibold" v-if="alertType === 'error'">
              Peringatan
            </h3>
            <h3 class="text-xl font-semibold" v-if="alertType === 'success'">
              Informasi
            </h3>
            <p class="font-medium text-sm" v-html="messages"></p>
          </div>
        </div>
        <DialogFooter class="flex">
          <Button type="button" @click="onClose" class="w-full"> OK </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
