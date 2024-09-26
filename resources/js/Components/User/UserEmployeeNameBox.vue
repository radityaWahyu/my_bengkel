<script setup lang="ts">
import { PersonStanding } from "lucide-vue-next";
import type { IUser } from "@/types/response";

defineProps<{
  user: IUser;
}>();
</script>
<template>
  <div class="font-semibold text-sky-700 capitalize space-y-1">
    <h4>{{ user.name }}</h4>
    <div
      class="inline-flex gap-2 font-medium text-xs px-2 py-1 bg-gray-200 rounded-full"
    >
      <PersonStanding class="size-4" />
      <span>{{ user.gender === "l" ? "Laki - laki" : "Perempuan" }}</span>
    </div>
  </div>
</template>
