<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import FormAlertiInfo from "@/Components/App/FormAlertiInfo.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, watch } from "vue";
import { Head, usePage, useForm as useInertiaForm, Link } from "@inertiajs/vue3";
import { ClipboardPen, ClipboardPenLine } from "lucide-vue-next";
import { Card, CardContent, CardDescription, CardHeader } from "@/shadcn/ui/card";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectGroup,
  SelectValue,
} from "@/shadcn/ui/select";
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
import type { ICustomer } from "@/types/response";
import * as zod from "zod";
import validator from "validator";
import FormAlertInfo from "@/Components/App/FormAlertiInfo.vue";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";

const props = defineProps<{
  customer?: ICustomer;
}>();

const page = usePage();

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      name: zod
        .string({ message: "Pegawai harus diisi" })
        .min(1, { message: "Pegawai harus diisi." }),
      address: zod
        .string({ message: "Alamat harus diisi" })
        .min(1, { message: "Alamat harus diisi." }),
      gender: zod.string({ message: "Jenis Kelamin harus dipilih." }),
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

const genders = ref([
  { value: "l", label: "Laki - laki" },
  { value: "p", label: "Perempuan" },
]);
const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const customerForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
  gender: "",
  address: "",
  phone: "",
  whatsapp: "",
});

watch(
  () => props.customer,
  (customer) => {
    console.log(customer);
    if (customer) {
      customerForm.name = customer.name;
      customerForm.gender = customer.gender;
      customerForm.address = customer.address;
      customerForm.phone = customer.phone;
      customerForm.whatsapp = customer.whatsapp;
      form.setFieldValue("name", customer.name);
      form.setFieldValue("gender", customer.gender);
      form.setFieldValue("address", customer.address);
      form.setFieldValue("phone", customer.phone);
      form.setFieldValue("whatsapp", customer.whatsapp);
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.customer) {
    customerForm.put(route("backoffice.customer.update", props.customer.id), {
      onError: (error) => console.log(error),
    });
  } else {
    customerForm.post(route("backoffice.customer.store"), {
      onError: (error) => console.log(error),
    });
  }
});
</script>
<template>
  <Head title="Form Pelanggan" />
  <div class="flex flex-1 flex-col gap-4 py-4 w-3/5 m-auto">
    <div class="flex items-center justify-between">
      <div class="flex items-center px-4 gap-4 text-primary">
        <ClipboardPenLine class="size-8" v-if="customer" />
        <ClipboardPen class="size-8" v-else />
        <h1 class="font-medium tracking-wider" v-if="customer">Edit Pelanggan</h1>
        <h1 class="font-medium tracking-wider" v-else>Tambah Pelanggan</h1>
      </div>

      <div class="space-x-2">
        <Link
          :href="route('backoffice.customer.index')"
          as="button"
          type="button"
          :disabled="customerForm.processing"
          class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
        >
          Batal
        </Link>
        <Button @click="onSubmit">
          <span v-if="customerForm.processing">Meyimpan data...</span>
          <span v-if="!customerForm.processing && customer">Simpan data</span>
          <span v-if="!customerForm.processing && !customer">
            Simpan dan Input Kendaraan
          </span>
        </Button>
      </div>
    </div>

    <Card class="shadow-none rounded">
      <CardHeader>
        <CardDescription>
          <FormAlertInfo v-if="!customer">
            Form Tambah Pegawai dipergunakan untuk menambah data pelanggan. Silahkan
            mengisi data sesuai dengan format yang diberikan oleh form ini tombol
          </FormAlertInfo>
          <FormAlertInfo v-else>
            Form Edit Pegawai dipergunakan untuk mengubah data pelanggan. Silahkan mengisi
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
                  'text-red-500': customerForm.errors.name,
                }"
              >
                <FormRequiredLabel>Nama Pelanggan</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="customerForm.name"
                  :class="{
                    'border border-red-500': customerForm.errors.name,
                  }"
                  :disabled="customerForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="customerForm.errors.name"
              >
                {{ customerForm.errors.name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="gender">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': customerForm.errors.gender,
                }"
              >
                <FormRequiredLabel>Jenis Kelamin</FormRequiredLabel>
              </FormLabel>
              <Select v-bind="componentField" v-model="customerForm.gender">
                <FormControl>
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih Jenis Kelamin" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  <SelectGroup>
                    <SelectItem
                      :value="gender.value"
                      v-for="(gender, index) in genders"
                      :key="index"
                    >
                      {{ gender.label }}
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="customerForm.errors.gender"
              >
                {{ customerForm.errors.gender }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <div class="grid grid-cols-2 items-center gap-2">
            <FormField v-slot="{ componentField }" name="phone">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': customerForm.errors.phone,
                  }"
                >
                  <FormRequiredLabel>No Telepon</FormRequiredLabel>
                </FormLabel>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="customerForm.phone"
                  :class="{
                    'border border-red-500': customerForm.errors.phone,
                  }"
                  :disabled="customerForm.processing"
                />
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="customerForm.errors.phone"
                >
                  {{ customerForm.errors.phone }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="whatsapp">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': customerForm.errors.whatsapp,
                  }"
                >
                  <FormRequiredLabel>No Whatsapp</FormRequiredLabel>
                </FormLabel>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="customerForm.whatsapp"
                  :class="{
                    'border border-red-500': customerForm.errors.whatsapp,
                  }"
                  :disabled="customerForm.processing"
                />
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="customerForm.errors.whatsapp"
                >
                  {{ customerForm.errors.whatsapp }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
          </div>
          <FormField v-slot="{ componentField }" name="address">
            <FormItem class="h-auto">
              <FormLabel
                :class="{
                  'text-red-500': customerForm.errors.address,
                }"
              >
                <FormRequiredLabel>Alamat</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Textarea
                  v-bind="componentField"
                  v-model="customerForm.address"
                  :class="{
                    'border border-red-500': customerForm.errors.name,
                  }"
                  :disabled="customerForm.processing"
                  rows="6"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="customerForm.errors.address"
              >
                {{ customerForm.errors.address }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
