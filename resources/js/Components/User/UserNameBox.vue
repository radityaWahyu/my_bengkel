<script setup lang="ts">
import { UserRound } from "lucide-vue-next";
import type { IUser } from "@/types/response";

defineProps<{
  user: IUser;
}>();
</script>
<template>
  <div
    :class="{ 'text-sky-700': user.enabled, 'text-yellow-700': !user.enabled }"
    class="flex items-center gap-2 font-semibold w-full capitalize"
  >
    <div
      :class="{
        'bg-sky-100': user.enabled,
        'bg-yellow-100': !user.enabled,
      }"
      class="p-2 bg-sky-100 rounded-full"
    >
      <UserRound class="size-4" />
    </div>
    <span>{{ user.username }}</span>
  </div>
</template>
