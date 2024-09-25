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
import type { IRepair } from "@/types/response";
import FormAlertiInfo from "../App/FormAlertiInfo.vue";

const formOpen = defineModel<boolean>();

const props = defineProps<{
  title: string;
  repair?: IRepair | undefined;
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
      price: zod.number({ message: "Harga harus diisi angkat" }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const repairForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
  price: 0,
});

watch(
  () => props.repair,
  (values) => {
    if (values && props.edit) {
      id.value = values.id;
      form.setFieldValue("name", values.name);
      form.setFieldValue("price", values.price);
      repairForm.name = values.name;
      repairForm.price = values.price;
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.edit) {
    repairForm.put(route("backoffice.repair.update", id.value), {
      onSuccess: () => {
        emits("saved", true);
        repairForm.reset();
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
    repairForm.post(route("backoffice.repair.store"), {
      onSuccess: () => {
        emits("saved", true);
        repairForm.reset();
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
  repairForm.reset();
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
        <form @submit="onSubmit" class="space-y-8">
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': repairForm.errors.name,
                }"
              >
                Nama Perbaikan
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  placeholder="nama perbaikan..."
                  v-bind="componentField"
                  v-model="repairForm.name"
                  :class="{
                    'border border-red-500': repairForm.errors.name,
                  }"
                  :disabled="repairForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="repairForm.errors.name"
              >
                {{ repairForm.errors.name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="price">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': repairForm.errors.price,
                }"
              >
                Harga
              </FormLabel>
              <FormControl>
                <Input
                  type="number"
                  v-bind="componentField"
                  v-model="repairForm.price"
                  :class="{
                    'border border-red-500': repairForm.errors.price,
                  }"
                  :disabled="repairForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="repairForm.errors.price"
              >
                {{ repairForm.errors.price }}
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
          :disabled="repairForm.processing"
        >
          Batal
        </Button>
        <Button
          type="button"
          :disabled="repairForm.processing"
          @click="onSubmit"
        >
          <span v-if="repairForm.processing"> Menyimpan data... </span>
          <span v-else> Simpan data </span>
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
