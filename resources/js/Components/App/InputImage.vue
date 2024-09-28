<script setup lang="ts">
import { Button } from "@/shadcn/ui/button";
import { ImageUp, X } from "lucide-vue-next";
import { ref, watch, defineModel } from "vue";

const inputFile = ref<HTMLInputElement | null>(null);

const previewImage = ref<any>(null);
const oldImage = ref<string>();

const image = defineModel<string>();

const props = withDefaults(defineProps<{ loading: boolean | null }>(), {
  loading: false,
});

const emits = defineEmits<{
  (e: "change", value: any): void;
}>();

const browseImage = () => {
  inputFile.value?.click();
};
const resetImage = () => {
  previewImage.value = oldImage.value;
  image.value = "";
};
const onChange = (e: any) => {
  let reader = new FileReader();
  reader.readAsDataURL(e.target.files[0]);
  reader.onload = (e) => {
    previewImage.value = e.target?.result;
  };

  image.value = e.target.files[0];
  // console.log(image.value);

  emits("change", e.target.files[0]);
};

watch(
  () => image,
  (image) => {
    // console.log(image.value);
    if (!!image.value) {
      previewImage.value = image.value;
      oldImage.value = image.value;
    }
  },
  { immediate: true }
);
</script>
<template>
  <div
    class="relative w-full border-2 border-gray-300 rounded-lg border-dashed overflow-hidden flex flex-col items-center"
  >
    <img
      :src="previewImage"
      class="w-full h-full object-cover"
      v-if="previewImage"
    />
    <div
      class="absolute top-0 space-y-4 p-4 bg-gray-900/20 w-full h-full flex flex-col justify-center items-center"
    >
      <div
        class="space-y-2 text-tomato flex flex-col items-center text-white p-2 bg-sky-900/50 rounded"
        v-if="!loading"
      >
        <ImageUp stroke-width="2px" class="h-10 w-10" />
        <div class="font-medium text-xs">Silahkan Pilih gambar</div>
      </div>

      <div
        class="flex items-center justify-center gap-2 w-full"
        v-if="!loading"
      >
        <Button
          type="button"
          v-if="!!oldImage"
          variant="secondary"
          size="sm"
          class="font-medium text-tomato"
          @click="resetImage"
        >
          <X class="w-4 h-4" stroke-width="4px" />
        </Button>
        <Button
          type="button"
          variant="secondary"
          size="sm"
          class="font-medium w-1/2 text-tomato"
          @click="browseImage"
        >
          Browse
        </Button>
      </div>
    </div>
    <input
      type="file"
      class="hidden"
      ref="inputFile"
      accept="image/*"
      @change="onChange"
    />
  </div>
</template>
