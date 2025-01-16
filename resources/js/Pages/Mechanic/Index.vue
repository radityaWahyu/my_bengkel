<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { Wrench } from "lucide-vue-next";
</script>
<template>
  <Head title="Daftar Perbaikan Mekanik" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <Wrench class="size-10" />
        <h1 class="text-lg font-semibold">Daftar Perbaikan</h1>
      </div>
    </div>
  </div>
</template>
