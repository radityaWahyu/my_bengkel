<script setup lang="ts">
import { watch, reactive } from "vue";
import TopAlert from "@/Components/App/TopAlert.vue";
import LeftNavigation from "@/Components/App/LeftNavigation.vue";
import TopNavigation from "@/Components/App/TopNavigation.vue";

const props = defineProps<{ flash: any }>();
const alert = reactive({
  show: false,
  type: "",
  message: "",
});

watch(
  () => props.flash,
  (value) => {
    console.log(!!value.error);
    if (!!value.success) {
      alert.show = !!value.success;
      alert.type = "success";
      alert.message = value.success;
      setTimeout(() => (alert.show = false), 2000);
    } else if (!!value.error) {
      alert.show = !!value.error;
      alert.type = "error";
      alert.message = value.error[2];
    }
  },
  { immediate: true }
);
</script>

<template>
  <div
    class="grid min-h-screen w-full md:grid-cols-[220px_1fr] lg:grid-cols-[260px_1fr] antialiased"
  >
    <LeftNavigation />
    <div class="h-screen flex flex-col overflow-y-auto scrollbar bg-[#F5FAFE]">
      <TopNavigation />
      <main class="h-auto">
        <TopAlert
          v-model="alert.show"
          :messages="alert.message"
          :type="alert.type"
        />
        <slot />
      </main>
    </div>
  </div>
</template>
