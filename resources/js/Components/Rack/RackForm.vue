<script setup lang="ts">
import { watch, ref } from "vue";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/shadcn/ui/sheet";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import { usePage, useForm as useInertiaForm } from "@inertiajs/vue3";
import type { IRack } from "@/types/response";
import FormAlertiInfo from "../App/FormAlertiInfo.vue";

const formOpen = defineModel<boolean>();

const props = defineProps<{
  title: string;
  rack?: IRack | undefined;
  edit?: boolean;
}>();

const emits = defineEmits<{
  (e: "saved", value: boolean): void;
  (e: "closed", value: boolean): void;
}>();

const page = usePage();
const id = ref<string>("");

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      name: zod
        .string({ message: "Nama Rak harus diisi" })
        .min(1, { message: "Nama Rak harus diisi." }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const rackForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
});

watch(
  () => props.rack,
  (values) => {
    if (values && props.edit) {
      id.value = values.id;
      form.setFieldValue("name", values.name);
      rackForm.name = values.name;
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.edit) {
    rackForm.put(route("backoffice.rack.update", id.value), {
      onSuccess: () => {
        emits("saved", true);
        rackForm.name = "";
        formOpen.value = false;
      },
      onError: (error) => {
        console.log(error);
      },
      onFinish: () => {
        form.resetForm();
      },
    });
  } else {
    rackForm.post(route("backoffice.rack.store"), {
      onSuccess: () => {
        emits("saved", true);
        rackForm.name = "";
        formOpen.value = false;
      },
      onError: (error) => {
        console.log(error);
      },
      onFinish: () => {
        form.resetForm();
      },
    });
  }
});

const onClose = () => {
  form.resetForm();
  formOpen.value = false;
  emits("closed", true);
};
</script>

<template>
  <Sheet v-model:open="formOpen">
    <SheetContent @interact-outside="(e) => e.preventDefault()">
      <SheetHeader>
        <SheetTitle>{{ title }}</SheetTitle>
        <SheetDescription>
          <FormAlertiInfo>
            Form ini dipergunakan untuk menambah atau mengubah data rak.
            Silahkan isi data sesuai form dibawah.
          </FormAlertiInfo>
        </SheetDescription>
      </SheetHeader>
      <div class="grid gap-4 py-4">
        <form @submit="onSubmit">
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': rackForm.errors.name,
                }"
                >Nama Rak</FormLabel
              >
              <FormControl>
                <Input
                  type="text"
                  placeholder="nama rak..."
                  v-bind="componentField"
                  v-model="rackForm.name"
                  :class="{
                    'border border-red-500': rackForm.errors.name,
                  }"
                  :disabled="rackForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="rackForm.errors.name"
              >
                {{ rackForm.errors.name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
        </form>
      </div>
      <SheetFooter>
        <Button
          type="button"
          variant="ghost"
          @click="onClose"
          :disabled="rackForm.processing"
        >
          Batal</Button
        >
        <Button type="button" :disabled="rackForm.processing" @click="onSubmit">
          <span v-if="rackForm.processing"> Menyimpan data... </span>
          <span v-else> Simpan data </span>
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
