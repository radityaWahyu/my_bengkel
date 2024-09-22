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
import type { IEmployee } from "@/types/response";
import * as zod from "zod";
import validator from "validator";
import FormAlertInfo from "@/Components/App/FormAlertiInfo.vue";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";
import CategoryForm from "@/Components/Category/CategoryForm.vue";
import RackForm from "@/Components/Rack/RackForm.vue";

const props = defineProps<{
  employee?: IEmployee;
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

const employeeForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
  gender: "",
  address: "",
  phone: "",
  whatsapp: "",
});

watch(
  () => props.employee,
  (employee) => {
    console.log(employee);
    if (employee) {
      employeeForm.name = employee.name;
      employeeForm.gender = employee.gender;
      employeeForm.address = employee.address;
      employeeForm.phone = employee.phone;
      employeeForm.whatsapp = employee.whatsapp;
      form.setFieldValue("name", employee.name);
      form.setFieldValue("gender", employee.gender);
      form.setFieldValue("address", employee.address);
      form.setFieldValue("phone", employee.phone);
      form.setFieldValue("whatsapp", employee.whatsapp);
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.employee) {
    employeeForm.put(route("backoffice.employee.update", props.employee.id), {
      onError: (error) => console.log(error),
    });
  } else {
    employeeForm.post(route("backoffice.employee.store"), {
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
        <ClipboardPenLine class="size-8" v-if="employee" />
        <ClipboardPen class="size-8" v-else />
        <h1 class="font-medium tracking-wider" v-if="employee">Edit Pegawai</h1>
        <h1 class="font-medium tracking-wider" v-else>Tambah Pegawai</h1>
      </div>

      <div class="space-x-2">
        <Link
          :href="route('backoffice.employee.index')"
          as="button"
          type="button"
          :disabled="employeeForm.processing"
          class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
        >
          Batal
        </Link>
        <Button @click="onSubmit">
          <span v-if="employeeForm.processing">Meyimpan data...</span>
          <span v-else>Simpan data</span>
        </Button>
      </div>
    </div>

    <Card class="shadow-none rounded">
      <CardHeader>
        <CardDescription>
          <FormAlertInfo v-if="!employee">
            Form Tambah Pegawai dipergunakan untuk menambah data pegawai. Silahkan mengisi
            data sesuai dengan format yang diberikan oleh form ini tombol
          </FormAlertInfo>
          <FormAlertInfo v-else>
            Form Edit Pegawai dipergunakan untuk mengubah data pegawai. Silahkan mengisi
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
                  'text-red-500': employeeForm.errors.name,
                }"
              >
                <FormRequiredLabel>Nama Pegawai</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="employeeForm.name"
                  :class="{
                    'border border-red-500': employeeForm.errors.name,
                  }"
                  :disabled="employeeForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="employeeForm.errors.name"
              >
                {{ employeeForm.errors.name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="gender">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': employeeForm.errors.gender,
                }"
              >
                <FormRequiredLabel>Jenis Kelamin</FormRequiredLabel>
              </FormLabel>
              <Select v-bind="componentField" v-model="employeeForm.gender">
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
                v-if="employeeForm.errors.gender"
              >
                {{ employeeForm.errors.gender }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <div class="grid grid-cols-2 items-center gap-2">
            <FormField v-slot="{ componentField }" name="phone">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': employeeForm.errors.phone,
                  }"
                >
                  <FormRequiredLabel>No Telepon</FormRequiredLabel>
                </FormLabel>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="employeeForm.phone"
                  :class="{
                    'border border-red-500': employeeForm.errors.phone,
                  }"
                  :disabled="employeeForm.processing"
                />
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="employeeForm.errors.phone"
                >
                  {{ employeeForm.errors.phone }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="whatsapp">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': employeeForm.errors.whatsapp,
                  }"
                >
                  <FormRequiredLabel>No Whatsapp</FormRequiredLabel>
                </FormLabel>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="employeeForm.whatsapp"
                  :class="{
                    'border border-red-500': employeeForm.errors.whatsapp,
                  }"
                  :disabled="employeeForm.processing"
                />
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="employeeForm.errors.whatsapp"
                >
                  {{ employeeForm.errors.whatsapp }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
          </div>
          <FormField v-slot="{ componentField }" name="address">
            <FormItem class="h-auto">
              <FormLabel
                :class="{
                  'text-red-500': employeeForm.errors.address,
                }"
              >
                <FormRequiredLabel>Alamat</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Textarea
                  v-bind="componentField"
                  v-model="employeeForm.address"
                  :class="{
                    'border border-red-500': employeeForm.errors.name,
                  }"
                  :disabled="employeeForm.processing"
                  rows="6"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="employeeForm.errors.address"
              >
                {{ employeeForm.errors.address }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
