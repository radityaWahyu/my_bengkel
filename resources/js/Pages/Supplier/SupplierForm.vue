<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import FormAlertiInfo from "@/Components/App/FormAlertiInfo.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { watch } from "vue";
import { Head, usePage, useForm as useInertiaForm, Link } from "@inertiajs/vue3";
import { ClipboardPen, ClipboardPenLine } from "lucide-vue-next";
import { Card, CardContent, CardDescription, CardHeader } from "@/shadcn/ui/card";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import { Input } from "@/shadcn/ui/input";
import { Button } from "@/shadcn/ui/button";
import { Textarea } from "@/shadcn/ui/textarea";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import type { ISupplier } from "@/types/response";
import * as zod from "zod";
import validator from "validator";
import FormAlertInfo from "@/Components/App/FormAlertiInfo.vue";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";

const props = defineProps<{
  supplier?: ISupplier;
}>();

const page = usePage();

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      name: zod
        .string({ message: "Pemasok harus diisi" })
        .min(1, { message: "Pemasok harus diisi." }),
      contact_name: zod
        .string({ message: "Nama Kontak harus diisi" })
        .min(1, { message: "Nama Kontak harus diisi." }),
      address: zod
        .string({ message: "Alamat harus diisi" })
        .min(1, { message: "Alamat harus diisi." }),
      whatsapp: zod
        .string({ message: "No Whatsapp harus diisi dengan angka" })
        .refine(validator.isMobilePhone, {
          message: "No Whatsapp harus diisi dengan angka",
        }),
      phone: zod
        .string({ message: "No Telepon harus diisi dengan angka" })
        .refine(validator.isMobilePhone, {
          message: "No Telepone harus diisi dengan angka",
        }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const supplierForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
  contact_name: "",
  address: "",
  phone: "",
  whatsapp: "",
});

watch(
  () => props.supplier,
  (supplier) => {
    console.log(supplier);
    if (supplier) {
      supplierForm.name = supplier.name;
      supplierForm.contact_name = supplier.contact_name;
      supplierForm.address = supplier.address;
      supplierForm.phone = supplier.phone;
      supplierForm.whatsapp = supplier.whatsapp;
      form.setFieldValue("name", supplier.name);
      form.setFieldValue("contact_name", supplier.contact_name);
      form.setFieldValue("address", supplier.address);
      form.setFieldValue("phone", supplier.phone);
      form.setFieldValue("whatsapp", supplier.whatsapp);
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.supplier) {
    supplierForm.put(route("backoffice.supplier.update", props.supplier.id), {
      onError: (error) => console.log(error),
    });
  } else {
    supplierForm.post(route("backoffice.supplier.store"), {
      onError: (error) => console.log(error),
    });
  }
});
</script>
<template>
  <Head title="Form Pegawai" />
  <div class="flex flex-1 flex-col gap-4 py-4 w-3/5 m-auto">
    <div class="flex items-center justify-between">
      <div class="flex items-center px-4 gap-4 text-primary">
        <ClipboardPenLine class="size-8" v-if="supplier" />
        <ClipboardPen class="size-8" v-else />
        <h1 class="font-medium tracking-wider" v-if="supplier">Edit Pemasok</h1>
        <h1 class="font-medium tracking-wider" v-else>Tambah Pemasok</h1>
      </div>

      <div class="space-x-2">
        <Link
          :href="route('backoffice.supplier.index')"
          as="button"
          type="button"
          :disabled="supplierForm.processing"
          class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
        >
          Batal
        </Link>
        <Button @click="onSubmit">
          <span v-if="supplierForm.processing">Meyimpan data...</span>
          <span v-else>Simpan data</span>
        </Button>
      </div>
    </div>

    <Card class="shadow-none rounded">
      <CardHeader>
        <CardDescription>
          <FormAlertInfo v-if="!supplier">
            Form Tambah Pemasok dipergunakan untuk menambah data pemasok. Silahkan mengisi
            data sesuai dengan format yang diberikan oleh form ini tombol
          </FormAlertInfo>
          <FormAlertInfo v-else>
            Form Edit Pemasok dipergunakan untuk mengubah data pemasok. Silahkan mengisi
            data sesuai dengan format yang diberikan oleh form ini tombol
          </FormAlertInfo>
        </CardDescription>
      </CardHeader>
      <CardContent class="h-auto relative">
        <form @submit.prevent="onSubmit" class="space-y-6">
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': supplierForm.errors.name,
                }"
              >
                <FormRequiredLabel>Nama Pemasok</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="supplierForm.name"
                  :class="{
                    'border border-red-500': supplierForm.errors.name,
                  }"
                  :disabled="supplierForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="supplierForm.errors.name"
              >
                {{ supplierForm.errors.name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="contact_name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': supplierForm.errors.contact_name,
                }"
              >
                <FormRequiredLabel>Nama Kontak</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="supplierForm.contact_name"
                  :class="{
                    'border border-red-500': supplierForm.errors.contact_name,
                  }"
                  :disabled="supplierForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="supplierForm.errors.contact_name"
              >
                {{ supplierForm.errors.contact_name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <div class="grid grid-cols-2 items-center gap-2">
            <FormField v-slot="{ componentField }" name="phone">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': supplierForm.errors.phone,
                  }"
                >
                  <FormRequiredLabel>No Telepon</FormRequiredLabel>
                </FormLabel>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="supplierForm.phone"
                  :class="{
                    'border border-red-500': supplierForm.errors.phone,
                  }"
                  :disabled="supplierForm.processing"
                />
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="supplierForm.errors.phone"
                >
                  {{ supplierForm.errors.phone }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="whatsapp">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': supplierForm.errors.whatsapp,
                  }"
                >
                  <FormRequiredLabel>No Whatsapp</FormRequiredLabel>
                </FormLabel>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="supplierForm.whatsapp"
                  :class="{
                    'border border-red-500': supplierForm.errors.whatsapp,
                  }"
                  :disabled="supplierForm.processing"
                />
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="supplierForm.errors.whatsapp"
                >
                  {{ supplierForm.errors.whatsapp }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
          </div>
          <FormField v-slot="{ componentField }" name="address">
            <FormItem class="h-auto">
              <FormLabel
                :class="{
                  'text-red-500': supplierForm.errors.address,
                }"
              >
                <FormRequiredLabel>Alamat</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Textarea
                  v-bind="componentField"
                  v-model="supplierForm.address"
                  :class="{
                    'border border-red-500': supplierForm.errors.name,
                  }"
                  :disabled="supplierForm.processing"
                  rows="6"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="supplierForm.errors.address"
              >
                {{ supplierForm.errors.address }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
