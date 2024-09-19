<script setup lang="ts">
import { MessageSquareText, MessageSquareX, X } from "lucide-vue-next";

const alertShow = defineModel<boolean>();
defineProps<{
  type: string;
  messages: string;
}>();

const onClose = () => (alertShow.value = false);
</script>
<template>
  <Transition>
    <div
      v-if="alertShow"
      class="grid grid-cols-[6%_88%_6%] items-center shadow-sm"
      :class="{
        'bg-blue-100': type === 'success',
        'bg-red-100': type === 'error',
      }"
    >
      <div class="px-2 h-full w-full flex items-center">
        <MessageSquareText
          class="size-6 text-blue-700 m-auto"
          v-if="type === 'success'"
        />
        <MessageSquareX
          class="size-6 text-red-700 m-auto"
          v-if="type === 'error'"
        />
      </div>
      <div
        class="py-2 text-sm"
        :class="{
          'text-blue-800': type === 'success',
          'text-red-800': type === 'error',
        }"
      >
        <strong class="block" v-if="type === 'success'">Informasi</strong>
        <strong class="block" v-if="type === 'error'">Kesalahan</strong>
        {{ messages }}
      </div>
      <button
        type="button"
        class="h-full flex items-center"
        :class="{
          'bg-blue-300': type === 'success',
          'bg-red-300': type === 'error',
        }"
        @click="onClose"
      >
        <X
          class="size-5 m-auto"
          :class="{
            'text-blue-700': type === 'success',
            'text-red-700': type === 'error',
          }"
        />
      </button>
    </div>
  </Transition>
</template>
<style lang="scss" scoped>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.3s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
