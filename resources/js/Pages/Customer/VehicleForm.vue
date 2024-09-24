<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import FormAlertiInfo from "@/Components/App/FormAlertiInfo.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, reactive, watch } from "vue";
import { router } from "@inertiajs/vue3";
import {
  Head,
  usePage,
  useForm as useInertiaForm,
  Link,
} from "@inertiajs/vue3";
import { ClipboardPen, ClipboardPenLine, Plus } from "lucide-vue-next";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
} from "@/shadcn/ui/card";
import { Label } from "@/shadcn/ui/label";
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
import { Users, CarFront } from "lucide-vue-next";
import { toTypedSchema } from "@vee-validate/zod";
import type { ICustomer, IVehicleEdit, IBrand } from "@/types/response";
import * as zod from "zod";
import validator from "validator";
import FormAlertInfo from "@/Components/App/FormAlertiInfo.vue";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";
import BrandForm from "@/Components/Brand/BrandForm.vue";

const props = defineProps<{
  customers: { data: ICustomer[] };
  customer?: ICustomer;
  vehicle?: IVehicleEdit;
  brands: { data: IBrand[] };
  redirect: string;
}>();

const page = usePage();

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      name: zod
        .string({ message: "Nama Kendaraan harus diisi" })
        .min(1, { message: "Nama Kendaraan harus diisi." }),
      plate_number: zod
        .string({ message: "No Plat harus diisi" })
        .min(1, { message: "No Plat harus diisi." }),
      machine_frame: zod
        .string({ message: "Kerangka Mesin harus diisi" })
        .min(1, { message: "Kerangka Mesin harus diisi." }),
      engine_volume: zod.number({ message: "CC Mesin harus diisi" }),
      engine_type: zod.string({ message: "Jenis Mesin harus diisi" }),
      type: zod.string({ message: "Jenis Kendaraan harus diisi" }),
      brand_id: zod.string({ message: "Merk harus diisi" }),
      production_year: zod.number({
        message: "Tahun pembuatan harus diisi angka",
      }),
      customer_id: zod.string({ message: "Pelanggan harus diisi" }),
    })
  );
};

const vehicleType = ref([
  { value: "car", label: "Mobil" },
  { value: "motorcycle", label: "Sepeda Motor" },
]);

const machineType = ref([
  { value: "petrol", label: "Bensin" },
  { value: "diesel", label: "Diesel" },
]);

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const vehicleForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
  plate_number: "",
  machine_frame: "",
  engine_volume: 0,
  engine_type: "",
  type: "",
  brand_id: "",
  production_year: 0,
  customer_id: "",
  redirect: props.redirect,
});
const brandFormState = reactive({
  open: false,
  title: "Tambah Merk",
  loading: false,
});

watch(
  () => props,
  (prop) => {
    if (prop.customer) {
      vehicleForm.customer_id = prop.customer.id;
      form.setFieldValue("customer_id", prop.customer.id);
    }

    if (!!prop.vehicle) {
      vehicleForm.plate_number = prop.vehicle.plate_number;
      vehicleForm.name = prop.vehicle.name;
      vehicleForm.machine_frame = prop.vehicle.machine_frame;
      vehicleForm.engine_volume = prop.vehicle.engine_volume;
      vehicleForm.engine_type = prop.vehicle.engine_type;
      vehicleForm.type = prop.vehicle.type;
      vehicleForm.brand_id = prop.vehicle.brand_id;
      vehicleForm.production_year = prop.vehicle.production_year;

      form.setFieldValue("name", prop.vehicle.name);
      form.setFieldValue("plate_number", prop.vehicle.plate_number);
      form.setFieldValue("machine_frame", prop.vehicle.machine_frame);
      form.setFieldValue("engine_volume", prop.vehicle.engine_volume);
      form.setFieldValue("engine_type", prop.vehicle.engine_type);
      form.setFieldValue("type", prop.vehicle.type);
      form.setFieldValue("brand_id", prop.vehicle.brand_id);
      form.setFieldValue("production_year", prop.vehicle.production_year);
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.vehicle) {
    vehicleForm.put(route("backoffice.vehicle.update", props.vehicle.id), {
      onError: (error) => console.log(error),
    });
  } else {
    vehicleForm.post(route("backoffice.vehicle.store"), {
      onError: (error) => console.log(error),
    });
  }
});

const onBrandSaved = () => {
  router.reload({
    only: ["brands"],
    onStart: () => (brandFormState.loading = true),
    onFinish: () => (brandFormState.loading = true),
  });
};
</script>
<template>
  <Head title="Form Pelanggan" />
  <div class="flex flex-1 flex-col gap-4 py-4 w-3/5 m-auto">
    <div class="flex items-center justify-between">
      <div class="flex items-center px-4 gap-4 text-primary">
        <ClipboardPenLine class="size-8" v-if="vehicle" />
        <ClipboardPen class="size-8" v-else />
        <h1 class="font-medium tracking-wider" v-if="vehicle">
          Edit Kendaraan
        </h1>
        <h1 class="font-medium tracking-wider" v-else>Tambah Kendaraan</h1>
      </div>

      <div class="space-x-2">
        <Link
          v-if="vehicle"
          :href="route('backoffice.customer.show', customer?.id)"
          as="button"
          type="button"
          :disabled="vehicleForm.processing"
          class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
        >
          Batal
        </Link>
        <Button @click="onSubmit">
          <span v-if="vehicleForm.processing">Meyimpan data...</span>
          <span v-else>Simpan data</span>
        </Button>
      </div>
    </div>

    <Card class="shadow-none rounded">
      <CardHeader>
        <CardDescription>
          <FormAlertInfo v-if="!vehicle">
            Form Tambah Kendaraan dipergunakan untuk menambah data kendaraan
            yang dimiliki pelanggan. Silahkan mengisi data sesuai dengan format
            yang diberikan oleh form ini tombol
          </FormAlertInfo>
          <FormAlertInfo v-else>
            Form Edit Kendaraan dipergunakan untuk mengubah data kendaraan yang
            dimiliki pelanggan. Silahkan mengisi data sesuai dengan format yang
            diberikan oleh form ini tombol
          </FormAlertInfo>
        </CardDescription>
      </CardHeader>
      <CardContent class="h-auto pb-10 relative">
        <form @submit.prevent="onSubmit" class="space-y-6">
          <div class="">
            <div
              class="flex items-center gap-4 mb-5 border-b border-dashed border-b-gray-200 p-2"
            >
              <Users class="size-8 text-blue-400" />
              <div>
                <h4 class="font-medium">Data Pelanggan</h4>
                <p class="text-sm text-gray-300">
                  Pastikan data pelanggan sudah benar dan sesuai dengan kartu
                  identitas pelanggan.
                </p>
              </div>
            </div>
            <FormField v-slot="{ componentField }" name="customer_id">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': vehicleForm.errors.customer_id,
                  }"
                >
                  <FormRequiredLabel>Pelanggan</FormRequiredLabel>
                </FormLabel>
                <FormControl>
                  <Select
                    v-bind="componentField"
                    v-model="vehicleForm.customer_id"
                    :disabled="!!customer"
                  >
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Pilih Pelanggan" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem
                          :value="customer.id"
                          v-for="(customer, index) in customers.data"
                          :key="index"
                        >
                          {{ customer.name }}
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                </FormControl>
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="vehicleForm.errors.plate_number"
                >
                  {{ vehicleForm.errors.plate_number }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
            <div class="mt-4">
              <Label>Alamat</Label>
              <Textarea :default-value="customer?.address" rows="4" disabled />
            </div>
          </div>
          <div
            class="flex items-center gap-4 mb-5 border-b border-dashed border-b-gray-200 p-2"
          >
            <CarFront class="size-8 text-blue-400" />
            <div>
              <h4 class="font-medium">Form Kendaraan</h4>
              <p class="text-sm text-gray-300">
                Pastikan isian data kendaraan telah terinput dengan benar sesuai
                dengan STNK Pelanggan.
              </p>
            </div>
          </div>
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': vehicleForm.errors.name,
                }"
              >
                <FormRequiredLabel>Nama Kendaraan</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="vehicleForm.name"
                  :class="{
                    'border border-red-500': vehicleForm.errors.name,
                  }"
                  :disabled="vehicleForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="vehicleForm.errors.plate_number"
              >
                {{ vehicleForm.errors.plate_number }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <div class="grid grid-cols-2 item-center gap-2">
            <div>
              <FormField v-slot="{ componentField }" name="plate_number">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': vehicleForm.errors.plate_number,
                    }"
                  >
                    <FormRequiredLabel>No Plat</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="text"
                      v-bind="componentField"
                      v-model="vehicleForm.plate_number"
                      :class="{
                        'border border-red-500':
                          vehicleForm.errors.plate_number,
                      }"
                      :disabled="vehicleForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="vehicleForm.errors.plate_number"
                  >
                    {{ vehicleForm.errors.plate_number }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
            <div>
              <FormField v-slot="{ componentField }" name="machine_frame">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': vehicleForm.errors.plate_number,
                    }"
                  >
                    <FormRequiredLabel>No Kerangka Mesin</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="text"
                      v-bind="componentField"
                      v-model="vehicleForm.machine_frame"
                      :class="{
                        'border border-red-500':
                          vehicleForm.errors.machine_frame,
                      }"
                      :disabled="vehicleForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="vehicleForm.errors.machine_frame"
                  >
                    {{ vehicleForm.errors.machine_frame }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
          </div>
          <div class="grid grid-cols-3 items-center gap-2">
            <div>
              <FormField v-slot="{ componentField }" name="engine_volume">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': vehicleForm.errors.engine_volume,
                    }"
                  >
                    <FormRequiredLabel>CC Mesin</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="number"
                      v-bind="componentField"
                      v-model="vehicleForm.engine_volume"
                      :class="{
                        'border border-red-500':
                          vehicleForm.errors.engine_volume,
                      }"
                      :disabled="vehicleForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="vehicleForm.errors.engine_volume"
                  >
                    {{ vehicleForm.errors.engine_volume }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
            <div>
              <FormField v-slot="{ componentField }" name="engine_type">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': vehicleForm.errors.engine_type,
                    }"
                  >
                    <FormRequiredLabel>Jenis Mesin</FormRequiredLabel>
                  </FormLabel>
                  <Select
                    v-bind="componentField"
                    v-model="vehicleForm.engine_type"
                  >
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Pilih Jenis Mesin" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem
                          :value="machine.value"
                          v-for="(machine, index) in machineType"
                          :key="index"
                        >
                          {{ machine.label }}
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="vehicleForm.errors.engine_type"
                  >
                    {{ vehicleForm.errors.engine_type }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
            <div>
              <FormField v-slot="{ componentField }" name="type">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': vehicleForm.errors.type,
                    }"
                  >
                    <FormRequiredLabel>Jenis Kendaraan</FormRequiredLabel>
                  </FormLabel>
                  <Select v-bind="componentField" v-model="vehicleForm.type">
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Pilih Jenis Kendaraan" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem
                          :value="vehicletype.value"
                          v-for="(vehicletype, index) in vehicleType"
                          :key="index"
                        >
                          {{ vehicletype.label }}
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="vehicleForm.errors.type"
                  >
                    {{ vehicleForm.errors.type }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
          </div>
          <div class="grid grid-cols-2 items-center gap-2">
            <div>
              <FormField v-slot="{ componentField }" name="production_year">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': vehicleForm.errors.production_year,
                    }"
                  >
                    <FormRequiredLabel>Tahun Pembuatan</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="number"
                      v-bind="componentField"
                      v-model="vehicleForm.production_year"
                      :class="{
                        'border border-red-500':
                          vehicleForm.errors.production_year,
                      }"
                      :disabled="vehicleForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="vehicleForm.errors.production_year"
                  >
                    {{ vehicleForm.errors.production_year }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
            <div>
              <div class="flex items-end gap-1">
                <div class="grow w-full">
                  <FormField v-slot="{ componentField }" name="brand_id">
                    <FormItem>
                      <FormLabel
                        :class="{
                          'text-red-500': vehicleForm.errors.brand_id,
                        }"
                      >
                        <FormRequiredLabel>Merk Kendaraan</FormRequiredLabel>
                      </FormLabel>
                      <Select
                        v-bind="componentField"
                        v-model="vehicleForm.brand_id"
                        v-if="brands.data.length > 0"
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue placeholder="Pilih Merk" />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectGroup>
                            <SelectItem
                              :value="brand.id"
                              v-for="(brand, index) in brands.data"
                              :key="index"
                            >
                              {{ brand.name }}
                            </SelectItem>
                          </SelectGroup>
                        </SelectContent>
                      </Select>
                      <div
                        v-else
                        class="bg-sky-100 flex items-center gap-2 px-2 py-2 text-sky-600 h-9 rounded"
                      >
                        <div>
                          <BadgeInfo
                            class="size-5"
                            v-if="!brandFormState.loading"
                          />
                          <svg
                            class="size-4 animate-spin"
                            viewBox="0 0 100 100"
                            v-else
                          >
                            <circle
                              fill="none"
                              stroke-width="12"
                              class="stroke-current opacity-40"
                              cx="50"
                              cy="50"
                              r="40"
                            />
                            <circle
                              fill="none"
                              stroke-width="12"
                              class="stroke-blue-500"
                              stroke-dasharray="250"
                              stroke-dashoffset="210"
                              cx="50"
                              cy="50"
                              r="40"
                            />
                          </svg>
                        </div>
                        <p class="text-xs" v-if="!brandFormState.loading">
                          Data Merk kosong
                        </p>
                        <p class="text-xs" v-else>Ambil data Merk...</p>
                      </div>
                      <div
                        class="text-xs text-red-500 font-medium"
                        v-if="vehicleForm.errors.brand_id"
                      >
                        {{ vehicleForm.errors.brand_id }}
                      </div>
                      <FormMessage v-else />
                    </FormItem>
                  </FormField>
                </div>
                <Button
                  type="button"
                  variant="outline"
                  size="icon"
                  class="mb-[2px]"
                  @click="brandFormState.open = true"
                >
                  <Plus class="size-4" />
                </Button>
              </div>
            </div>
          </div>
        </form>
      </CardContent>
    </Card>
    <BrandForm
      v-model="brandFormState.open"
      :title="brandFormState.title"
      @saved="onBrandSaved"
    />
  </div>
</template>
