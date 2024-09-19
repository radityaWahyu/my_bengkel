<script setup lang="ts">
import { watch, ref } from "vue";
import { Info } from "lucide-vue-next";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
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
import type { ICategory } from "@/types/response";

const formOpen = defineModel<boolean>();

const props = defineProps<{
  title: string;
  category: ICategory | undefined;
  edit: boolean;
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
        .string({ message: "Nama Kategori harus diisi" })
        .min(1, { message: "Nama Kategori harus diisi." }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const categoryForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
});

watch(
  () => props.category,
  (values) => {
    if (values && props.edit) {
      id.value = values.id;
      form.setFieldValue("name", values.name);
      categoryForm.name = values.name;
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.edit) {
    categoryForm.put(route("backoffice.category.update", id.value), {
      onSuccess: () => {
        emits("saved", true);
        categoryForm.name = "";
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
    categoryForm.post(route("backoffice.category.store"), {
      onSuccess: () => {
        emits("saved", true);
        categoryForm.name = "";
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
          <Alert class="bg-blue-50">
            <Info class="h-4 w-4" />
            <AlertTitle>Keterangan</AlertTitle>
            <AlertDescription class="text-sm">
              Form ini dipergunakan untuk menambah atau mengubah data kategori.
              Silahkan isi data sesuai form dibawah.
            </AlertDescription>
          </Alert>
        </SheetDescription>
      </SheetHeader>
      <div class="grid gap-4 py-4">
        <form @submit="onSubmit">
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': categoryForm.errors.name,
                }"
                >Nama Kategori</FormLabel
              >
              <FormControl>
                <Input
                  type="text"
                  placeholder="nama kategori..."
                  v-bind="componentField"
                  v-model="categoryForm.name"
                  :class="{
                    'border border-red-500': categoryForm.errors.name,
                  }"
                  :disabled="categoryForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="categoryForm.errors.name"
              >
                {{ categoryForm.errors.name }}
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
          :disabled="categoryForm.processing"
        >
          Batal</Button
        >
        <Button
          type="button"
          :disabled="categoryForm.processing"
          @click="onSubmit"
        >
          <span v-if="categoryForm.processing"> Menyimpan data... </span>
          <span v-else> Simpan data </span>
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
