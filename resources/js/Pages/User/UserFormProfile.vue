<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import FormAlertiInfo from "@/Components/App/FormAlertiInfo.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, onMounted, reactive } from "vue";
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
import { Textarea } from "@/shadcn/ui/textarea";
import { Button } from "@/shadcn/ui/button";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import type { IUserProfile } from "@/types/response";
import * as zod from "zod";
import validator from "validator";
import FormAlertInfo from "@/Components/App/FormAlertiInfo.vue";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";

const props = defineProps<{
  user: IUserProfile;
}>();

const page = usePage();

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      username: zod
        .string({ message: "Username harus diisi" })
        .min(1, { message: "Username harus diisi." }),
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

const profileForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
  gender: "",
  address: "",
  phone: "",
  whatsapp: "",
  username: "",
  password: "",
});

const onSubmit = form.handleSubmit(() => {
  profileForm.put(route("backoffice.profil.update", props.user.user_id), {
    onError: (error) => console.log(error),
  });
});

onMounted(() => {
  profileForm.name = props.user.name;
  profileForm.gender = props.user.gender;
  profileForm.address = props.user.address;
  profileForm.phone = props.user.phone;
  profileForm.whatsapp = props.user.whatsapp;
  profileForm.username = props.user.username;

  form.setFieldValue("name", props.user.name);
  form.setFieldValue("gender", props.user.gender);
  form.setFieldValue("address", props.user.address);
  form.setFieldValue("phone", props.user.phone);
  form.setFieldValue("whatsapp", props.user.whatsapp);
  form.setFieldValue("username", props.user.username);
});
</script>
<template>
  <Head title="Edit Profil User" />
  <div class="flex flex-1 flex-col gap-4 py-4 w-3/5 m-auto">
    <div class="flex items-center justify-between">
      <div class="flex items-center px-4 gap-4 text-primary">
        <UserRoundPen class="size-8" v-if="user" />
        <UserRoundPlus class="size-8" v-else />
        <h1 class="font-medium tracking-wider" v-if="user">Edit Profil</h1>
        <h1 class="font-medium tracking-wider" v-else>Tambah User</h1>
      </div>

      <div class="space-x-2">
        <Link
          :href="route('backoffice.user.index')"
          as="button"
          type="button"
          :disabled="profileForm.processing"
          class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
        >
          Batal
        </Link>
        <Button @click="onSubmit">
          <span v-if="profileForm.processing">Meyimpan data...</span>
          <span v-else>Simpan data</span>
        </Button>
      </div>
    </div>

    <Card class="shadow-none rounded">
      <CardContent class="h-auto relative">
        <form @submit.prevent="onSubmit" class="space-y-6">
          <div class="space-y-5">
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

            <FormField v-slot="{ componentField }" name="name">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': profileForm.errors.name,
                  }"
                >
                  <FormRequiredLabel>Nama Pegawai</FormRequiredLabel>
                </FormLabel>
                <FormControl>
                  <Input
                    type="text"
                    v-bind="componentField"
                    v-model="profileForm.name"
                    :class="{
                      'border border-red-500': profileForm.errors.name,
                    }"
                    :disabled="profileForm.processing"
                    v-if="profileForm.name !== 'Administrator'"
                  />
                  <Input
                    type="text"
                    :default-value="profileForm.name"
                    :class="{
                      'border border-red-500': profileForm.errors.name,
                    }"
                    :disabled="profileForm.processing"
                    v-else
                    readonly
                  />
                </FormControl>
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="profileForm.errors.name"
                >
                  {{ profileForm.errors.name }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="gender">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': profileForm.errors.gender,
                  }"
                >
                  <FormRequiredLabel>Jenis Kelamin</FormRequiredLabel>
                </FormLabel>
                <Select v-bind="componentField" v-model="profileForm.gender">
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
                  v-if="profileForm.errors.gender"
                >
                  {{ profileForm.errors.gender }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
            <div class="grid grid-cols-2 items-center gap-2">
              <FormField v-slot="{ componentField }" name="phone">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': profileForm.errors.phone,
                    }"
                  >
                    <FormRequiredLabel>No Telepon</FormRequiredLabel>
                  </FormLabel>
                  <Input
                    type="text"
                    v-bind="componentField"
                    v-model="profileForm.phone"
                    :class="{
                      'border border-red-500': profileForm.errors.phone,
                    }"
                    :disabled="profileForm.processing"
                  />
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="profileForm.errors.phone"
                  >
                    {{ profileForm.errors.phone }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="whatsapp">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': profileForm.errors.whatsapp,
                    }"
                  >
                    <FormRequiredLabel>No Whatsapp</FormRequiredLabel>
                  </FormLabel>
                  <Input
                    type="text"
                    v-bind="componentField"
                    v-model="profileForm.whatsapp"
                    :class="{
                      'border border-red-500': profileForm.errors.whatsapp,
                    }"
                    :disabled="profileForm.processing"
                  />
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="profileForm.errors.whatsapp"
                  >
                    {{ profileForm.errors.whatsapp }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
            <FormField v-slot="{ componentField }" name="address">
              <FormItem class="h-auto">
                <FormLabel
                  :class="{
                    'text-red-500': profileForm.errors.address,
                  }"
                >
                  <FormRequiredLabel>Alamat</FormRequiredLabel>
                </FormLabel>
                <FormControl>
                  <Textarea
                    v-bind="componentField"
                    v-model="profileForm.address"
                    :class="{
                      'border border-red-500': profileForm.errors.name,
                    }"
                    :disabled="profileForm.processing"
                    rows="6"
                  />
                </FormControl>
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="profileForm.errors.address"
                >
                  {{ profileForm.errors.address }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
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
                      'text-red-500': profileForm.errors.username,
                    }"
                  >
                    <FormRequiredLabel>Username</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="text"
                      v-bind="componentField"
                      v-model="profileForm.username"
                      :class="{
                        'border border-red-500': profileForm.errors.username,
                      }"
                      :disabled="profileForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="profileForm.errors.username"
                  >
                    {{ profileForm.errors.username }}
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
                      'text-red-500': profileForm.errors.password,
                    }"
                  >
                    <FormRequiredLabel>Password</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="password"
                      v-bind="componentField"
                      v-model="profileForm.password"
                      :class="{
                        'border border-red-500': profileForm.errors.password,
                      }"
                      :disabled="profileForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="profileForm.errors.password"
                  >
                    {{ profileForm.errors.password }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
