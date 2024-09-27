<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import LinkButton from "@/Components/App/LinkButton.vue";
import { Wrench, BadgeInfo } from "lucide-vue-next";
import { Card, CardHeader, CardContent, CardDescription } from "@/shadcn/ui/card";
import { Label } from "@/shadcn/ui/label";
import { Input } from "@/shadcn/ui/input";
import type { ISetting } from "@/types/response";

const props = defineProps<{
  settings: { data: ISetting[] };
}>();

const settingForm = useForm<any>({
  settings: [],
});

watch(
  () => props.settings,
  (settings) => {
    if (settings.data) {
      settings.data.forEach((setting) => {
        settingForm.settings.push({
          id: setting.id,
          name: setting.name,
          value: setting.value,
        });
      });

      console.log(settingForm.settings);
    }
  },
  { immediate: true }
);

const onSubmit = () => {};
</script>
<template>
  <Head title="Pengaturan Sistem" />
  <div class="flex flex-1 flex-col gap-4 py-3 w-3/5 m-auto">
    <div class="flex items-center justify-between p-2">
      <div class="flex items-center gap-4 text-primary">
        <Wrench class="size-10" />
        <h1 class="text-lg font-semibold">Pengaturan Sistem</h1>
      </div>

      <div class="">
        <LinkButton :to="route('backoffice.user.create')" class="space-x-2">
          <span>Simpan Pengaturan</span>
        </LinkButton>
      </div>
    </div>

    <Card class="shadow-none rounded">
      <CardHeader>
        <CardDescription>
          <div
            class="inline-flex items-center gap-4 border-b border-dashed border-b-gray-200 pb-2"
          >
            <BadgeInfo class="size-16 text-blue-400" />
            <div>
              <h4 class="font-medium">Keterangan:</h4>
              <p class="text-sm text-gray-400">
                Halaman ini dipergunakan untuk mengatur data standart yang dibutuhkan oleh
                sistem. Dalam menjalankan setiap operasi yang berlangsung.
              </p>
            </div>
          </div>
        </CardDescription>
      </CardHeader>
      <CardContent class="h-auto relative">
        <form class="space-y-6" @submit="onSubmit">
          <div
            class="grid grid-cols-[30%_70%] gap-2"
            v-for="(setting, index) in settings.data"
            :key="index"
          >
            <div>
              <Label class="capitalize font-medium">{{ setting.name }}</Label>
            </div>
            <div class="space-y-1">
              <Input
                type="url"
                v-model="settingForm.settings[index].value"
                :class="{
                  'border-red-400 border': settingForm.errors[`settings.${index}.value`],
                }"
                class="bg-white text-slate-950 focus:border-none"
                :disabled="settingForm.processing"
              />
              <p
                v-if="settingForm.errors[`settings.${index}.value`]"
                class="text-xs text-red-400"
              >
                {{ settingForm.errors[`settings.${index}.value`] }}
              </p>
              <p class="text-xs text-gray-600">
                <strong>Keterangan :</strong> Pengaturan ini dipergunakan untuk
              </p>
            </div>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
