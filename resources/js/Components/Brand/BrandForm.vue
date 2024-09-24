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
import type { IBrand } from "@/types/response";
import FormAlertiInfo from "../App/FormAlertiInfo.vue";

const formOpen = defineModel<boolean>();

const props = defineProps<{
  title: string;
  brand?: IBrand | undefined;
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
        .string({ message: "Nama Merk harus diisi" })
        .min(1, { message: "Nama Merk harus diisi." }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const brandForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
});

watch(
  () => props.brand,
  (values) => {
    if (values && props.edit) {
      id.value = values.id;
      form.setFieldValue("name", values.name);
      brandForm.name = values.name;
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.edit) {
    brandForm.put(route("backoffice.brand.update", id.value), {
      onSuccess: () => {
        emits("saved", true);
        brandForm.name = "";
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
    brandForm.post(route("backoffice.brand.store"), {
      onSuccess: () => {
        emits("saved", true);
        brandForm.name = "";
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
            Form ini dipergunakan untuk menambah atau mengubah data Merk.
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
                  'text-red-500': brandForm.errors.name,
                }"
                >Nama Kategori</FormLabel
              >
              <FormControl>
                <Input
                  type="text"
                  placeholder="nama merk..."
                  v-bind="componentField"
                  v-model="brandForm.name"
                  :class="{
                    'border border-red-500': brandForm.errors.name,
                  }"
                  :disabled="brandForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="brandForm.errors.name"
              >
                {{ brandForm.errors.name }}
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
          :disabled="brandForm.processing"
        >
          Batal
        </Button>
        <Button
          type="button"
          :disabled="brandForm.processing"
          @click="onSubmit"
        >
          <span v-if="brandForm.processing"> Menyimpan data... </span>
          <span v-else> Simpan data </span>
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
