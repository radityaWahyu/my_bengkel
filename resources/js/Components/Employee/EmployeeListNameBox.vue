<script setup lang="ts">
import { Button } from "@/shadcn/ui/button";
import { MoveRight } from "lucide-vue-next";
import type { IUser } from "@/types/response";

const props = defineProps<{
  user: IUser;
}>();

const emits = defineEmits<{
  (e: "selected", value: IUser): void;
}>();

const onSelect = () => {
  emits("selected", props.user);
};
</script>
<template>
  <div class="w-full flex items-center justify-between">
    <div class="py-3 space-y-2">
      <div>
        <h4 class="capitalize font-semibold">
          {{ user.name }} <span class="font-medium">({{ user.whatsapp }})</span>
        </h4>
        <p class="uppercase text-sm text-gray-400">
          {{ user.role }} |
          <span class="capitalize">{{
            user.gender === "l" ? "Laki - laki" : "Perempuan"
          }}</span>
        </p>
      </div>
    </div>
    <div>
      <Button as="button" variant="outline" size="icon" @click="onSelect">
        <MoveRight class="size-4" />
      </Button>
    </div>
  </div>
</template>
