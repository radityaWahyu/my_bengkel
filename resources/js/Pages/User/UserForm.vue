<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import FormAlertiInfo from "@/Components/App/FormAlertiInfo.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, watch, reactive } from "vue";
import {
  Head,
  usePage,
  useForm as useInertiaForm,
  Link,
} from "@inertiajs/vue3";
import {
  UserRoundPlus,
  UserRoundPen,
  Users,
  UserCircle,
  BadgeInfo,
} from "lucide-vue-next";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
} from "@/shadcn/ui/card";
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
import { Label } from "@/shadcn/ui/label";
import { Button } from "@/shadcn/ui/button";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import type { IEmployee, IUserEdit } from "@/types/response";
import * as zod from "zod";
import validator from "validator";
import FormAlertInfo from "@/Components/App/FormAlertiInfo.vue";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";

const props = defineProps<{
  employees: { data: IEmployee[] };
  user?: IUserEdit;
}>();

const page = usePage();

const userSchema = () => {
  if (props.user) {
    return toTypedSchema(
      zod.object({
        username: zod
          .string({ message: "Username harus diisi" })
          .min(1, { message: "Username harus diisi." }),
        role: zod
          .string({ message: "Role harus dipilih" })
          .min(1, { message: "Role belum dipilih." }),
        employee_id: zod.string({ message: "Pegawai belum dipilih." }),
      })
    );
  }
  return toTypedSchema(
    zod.object({
      username: zod
        .string({ message: "Username harus diisi" })
        .min(1, { message: "Username harus diisi." }),
      role: zod
        .string({ message: "Role harus dipilih" })
        .min(1, { message: "Role belum dipilih." }),
      password: zod.string({ message: "Password harus diisi" }).min(5, {
        message: "Password harus diisi minimal 5 karakter.",
      }),
      employee_id: zod.string({ message: "Pegawai belum dipilih." }),
    })
  );
};

const employee = reactive({
  gender: "",
  phone: "",
  whatsapp: "",
});
const roles = ref([
  { value: "operator", label: "Operator" },
  { value: "mekanik", label: "Mekanik" },
]);
const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const userForm = useInertiaForm({
  _token: page.props.csrf_token,
  username: "",
  password: "",
  user_id: "",
  role: "",
  employee_id: "",
});

const onNameChange = () => {
  const response = props.employees.data.find(
    (employee) => employee.id === userForm.employee_id
  );

  // console.log(response);
  if (response) {
    employee.gender = response.gender === "l" ? "Laki - laki" : "Perempuan";
    employee.phone = response.phone;
    employee.whatsapp = response.whatsapp;
  }
};
watch(
  () => props.user,
  (user) => {
    // console.log(user);
    if (user) {
      userForm.employee_id = user.employee_id;
      userForm.username = user.username;
      userForm.role = user.role;
      form.setFieldValue("employee_id", user.employee_id);
      form.setFieldValue("username", user.username);
      form.setFieldValue("role", user.role);
      onNameChange();
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.user) {
    userForm.put(route("backoffice.user.update", props.user.id), {
      onError: (error) => console.log(error),
    });
  } else {
    userForm.post(route("backoffice.user.store"), {
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
        <UserRoundPen class="size-8" v-if="user" />
        <UserRoundPlus class="size-8" v-else />
        <h1 class="font-medium tracking-wider" v-if="user">Edit User</h1>
        <h1 class="font-medium tracking-wider" v-else>Tambah User</h1>
      </div>

      <div class="space-x-2">
        <Link
          :href="route('backoffice.user.index')"
          as="button"
          type="button"
          :disabled="userForm.processing"
          class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
        >
          Batal
        </Link>
        <Button @click="onSubmit">
          <span v-if="userForm.processing">Meyimpan data...</span>
          <span v-else>Simpan data</span>
        </Button>
      </div>
    </div>

    <Card class="shadow-none rounded">
      <CardHeader>
        <CardDescription>
          <FormAlertInfo v-if="!user">
            Form Tambah User dipergunakan untuk menambah data user. Silahkan
            mengisi data sesuai dengan format yang diberikan oleh form ini
          </FormAlertInfo>
          <FormAlertInfo v-else>
            Form Edit User dipergunakan untuk mengubah data user. Silahkan
            mengisi data sesuai dengan format yang diberikan oleh form ini
          </FormAlertInfo>
        </CardDescription>
      </CardHeader>
      <CardContent class="h-auto relative">
        <form @submit.prevent="onSubmit" class="space-y-6">
          <div class="">
            <div
              class="flex items-center gap-4 mb-5 border-b border-dashed border-b-gray-200 p-2"
            >
              <Users class="size-8 text-blue-400" />
              <div>
                <h4 class="font-medium">Data Pegawai</h4>
                <p class="text-sm text-gray-300">
                  Pastikan data pegawai sudah benar dan sesuai dengan kartu
                  identitas pegawai.
                </p>
              </div>
            </div>
            <FormField v-slot="{ componentField }" name="employee_id">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': userForm.errors.employee_id,
                  }"
                >
                  <FormRequiredLabel>Nama Pegawai</FormRequiredLabel>
                </FormLabel>
                <FormControl>
                  <Select
                    id="pegawai"
                    v-bind="componentField"
                    v-model="userForm.employee_id"
                    @update:model-value="onNameChange"
                    :disabled="!!user"
                    v-if="employees.data.length > 0"
                  >
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Pilih Pegawai" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem
                          :value="employee.id"
                          v-for="(employee, index) in employees.data"
                          :key="index"
                        >
                          {{ employee.name }}
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <div
                    class="bg-sky-100 flex items-center gap-2 px-2 py-2 text-sky-600 h-9 rounded"
                    v-else
                  >
                    <BadgeInfo class="size-5" />
                    <p class="text-xs">
                      Data Pegawai kosong silahkan input data pegawai terlebih
                      dahulu.
                    </p>
                  </div>
                </FormControl>
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="userForm.errors.employee_id"
                >
                  {{ userForm.errors.employee_id }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
            <template v-if="employees.data.length > 0">
              <div class="mt-4">
                <Label>Jenis Kelamin</Label>
                <Input v-model="employee.gender" readonly />
              </div>
              <div class="mt-4 grid grid-cols-2 items-center gap-2">
                <div>
                  <Label>No Telepon</Label>
                  <Input v-model="employee.phone" readonly />
                </div>
                <div>
                  <Label>No Whatsapp</Label>
                  <Input v-model="employee.whatsapp" readonly />
                </div>
              </div>
            </template>
          </div>
          <div
            class="flex items-center gap-4 mb-5 border-b border-dashed border-b-gray-200 p-2"
          >
            <UserCircle class="size-8 text-blue-400" />
            <div>
              <h4 class="font-medium">Form User</h4>
              <p class="text-sm text-gray-300">
                Pastikan isian data user telah terinput dengan benar sesuai
                dengan permintaan form.
              </p>
            </div>
          </div>
          <div class="grid grid-cols-2 item-center gap-2">
            <div>
              <FormField v-slot="{ componentField }" name="username">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': userForm.errors.username,
                    }"
                  >
                    <FormRequiredLabel>Username</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="text"
                      v-bind="componentField"
                      v-model="userForm.username"
                      :class="{
                        'border border-red-500': userForm.errors.username,
                      }"
                      :disabled="userForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="userForm.errors.username"
                  >
                    {{ userForm.errors.username }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
            <div>
              <FormField v-slot="{ componentField }" name="password">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': userForm.errors.password,
                    }"
                  >
                    <FormRequiredLabel>Password</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="password"
                      v-bind="componentField"
                      v-model="userForm.password"
                      :class="{
                        'border border-red-500': userForm.errors.password,
                      }"
                      :disabled="userForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="userForm.errors.password"
                  >
                    {{ userForm.errors.password }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
          </div>
          <FormField v-slot="{ componentField }" name="role">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': userForm.errors.role,
                }"
              >
                <FormRequiredLabel>Role User</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Select
                  v-bind="componentField"
                  v-model="userForm.role"
                  id="role"
                >
                  <FormControl>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih Role" />
                    </SelectTrigger>
                  </FormControl>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem
                        :value="role.value"
                        v-for="(role, index) in roles"
                        :key="index"
                      >
                        {{ role.label }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="userForm.errors.role"
              >
                {{ userForm.errors.role }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
