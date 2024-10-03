<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { reactive, ref, computed } from "vue";
import {
  Head,
  usePage,
  useForm as useInertiaForm,
  Link,
} from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/shadcn/ui/card";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import { Input } from "@/shadcn/ui/input";
import { Textarea } from "@/shadcn/ui/textarea";
import { Label } from "@/shadcn/ui/label";
import { Button } from "@/shadcn/ui/button";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";
import {
  ClipboardPen,
  CarFront,
  TextSearch,
  PlusSquare,
  StickyNote,
} from "lucide-vue-next";

import type { IVehicle } from "@/types/response";
import VehicleList from "@/Components/Vehicle/VehicleList.vue";

const vehicle = reactive({
  id: "",
  name: "",
  plate_number: "",
  machine_frame: "",
  engine_volume: 0,
  engine_type: "",
  type: "",
  production_year: 0,
  brand: "",
  customer: "",
});

const vehicleDialogOpen = ref<boolean>(false);
const vehicleName = computed(() => {
  if (vehicle.id.length > 0) return `${vehicle.plate_number}-${vehicle.name}`;
});

const page = usePage();
const serviceForm = useInertiaForm({
  _token: page.props.csrf_token,
  vehicle_id: "",
  description: "",
});

const serviceSchema = () => {
  return toTypedSchema(
    zod.object({
      vehicle_name: zod
        .string({ message: "Data Kendaraan harus diisi" })
        .min(1, { message: "Data Kendaraan harus diisi" }),
      description: zod
        .string({ message: "Keluhan harus diisi" })
        .min(1, { message: "Keluhan harus diisi" }),
    })
  );
};

const validationSchema = serviceSchema();
const form = useForm({
  validationSchema,
});

const onVehicleSelected = (value: IVehicle) => {
  vehicleDialogOpen.value = false;
  vehicle.id = value.id;
  serviceForm.vehicle_id = value.id;
  vehicle.name = value.name;
  vehicle.plate_number = value.plate_number;
  vehicle.machine_frame = value.machine_frame;
  vehicle.engine_volume = value.engine_volume;
  vehicle.engine_type = value.engine_type === "petrol" ? "Bensin" : "Diesel";
  vehicle.type = value.type === "car" ? "Mobil" : "Sepeca Motor";
  vehicle.production_year = value.production_year;
  vehicle.brand = value.brand;
  vehicle.customer = value.customer;
  form.setFieldValue("vehicle_name", vehicleName.value);
};

const onSubmit = form.handleSubmit(() => {
  serviceForm.post(route("backoffice.service.store"), {
    onError: (error) => console.log(error),
  });
});
</script>
<template>
  <Head title="Form Transaksi Service" />
  <div class="p-4">
    <Card class="rounded shadow-none">
      <CardHeader>
        <div class="flex items-center justify-between">
          <div class="flex items-center px-2 gap-4 text-primary">
            <ClipboardPen class="size-8" />
            <h1 class="font-medium text-lg">Tambah Transaksi Service</h1>
          </div>

          <div class="space-x-2">
            <Link
              :href="route('backoffice.service.index')"
              as="button"
              type="button"
              :disabled="serviceForm.processing"
              class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
            >
              Batal
            </Link>
            <Button @click="onSubmit" :disabled="serviceForm.processing">
              <span v-if="serviceForm.processing">Meyimpan data...</span>
              <span v-else>Simpan data</span>
            </Button>
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="onSubmit" class="space-y-6 pb-4">
          <div class="space-y-3">
            <div
              class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
            >
              <CarFront class="size-8 text-blue-400" />
              <div>
                <h4 class="font-medium">Data Kendaraan</h4>
                <p class="text-sm text-gray-500">
                  Silahkan pilih salah satu kendaraan yang dimiliki pelanggan.
                  Apabila terdapat kendaraan baru atau pelanggan baru silahkan
                  klik tombol
                  <strong>Buat Baru</strong>
                </p>
              </div>
            </div>
            <div class="grid grid-cols-2 items-center gap-2">
              <div>
                <FormField v-slot="{ componentField }" name="vehicle_name">
                  <FormItem>
                    <FormLabel
                      :class="{
                        'text-red-500': serviceForm.errors.vehicle_id,
                      }"
                    >
                      <FormRequiredLabel>Nama Kendaraan</FormRequiredLabel>
                    </FormLabel>
                    <div class="grid grid-cols-[80%_20%] items-center gap-2">
                      <div>
                        <FormControl>
                          <Input
                            type="text"
                            v-bind="componentField"
                            v-model="vehicleName"
                            placeholder="Silhkan pilih kendaraan"
                            readonly
                          />
                        </FormControl>
                      </div>
                      <div class="space-x-1">
                        <Button
                          as="button"
                          size="icon"
                          id="search-vehicle"
                          @click="vehicleDialogOpen = true"
                          :disabled="serviceForm.processing"
                        >
                          <TextSearch class="size-5" />
                        </Button>
                        <Button as="button" size="icon">
                          <PlusSquare class="size-5" />
                        </Button>
                      </div>
                    </div>
                    <div
                      class="text-xs text-red-500 font-medium"
                      v-if="serviceForm.errors.vehicle_id"
                    >
                      {{ serviceForm.errors.vehicle_id }}
                    </div>
                    <FormMessage v-else />
                  </FormItem>
                </FormField>
              </div>
              <div class="space-y-1">
                <Label>Nama Pelanggan</Label>
                <Input
                  type="text"
                  v-model:model-value="vehicle.customer"
                  readonly
                />
              </div>
            </div>
            <div class="grid grid-cols-4 gap-2">
              <div class="space-y-1">
                <Label>CC Mesin</Label>
                <Input
                  type="number"
                  v-model:model-value="vehicle.engine_volume"
                  readonly
                />
              </div>
              <div class="space-y-1">
                <Label>Tipe Mesin</Label>
                <Input
                  type="text"
                  v-model:model-value="vehicle.engine_type"
                  readonly
                />
              </div>
              <div class="space-y-1">
                <Label>Jenis Kendaraan</Label>
                <Input
                  type="text"
                  v-model:model-value="vehicle.type"
                  readonly
                />
              </div>
              <div class="space-y-1">
                <Label>Tahun Pembuatan</Label>
                <Input
                  type="number"
                  v-model:model-value="vehicle.production_year"
                  readonly
                />
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <div
              class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
            >
              <StickyNote class="size-8 text-blue-400" />
              <div>
                <h4 class="font-medium">Keluhan Pelanggan</h4>
                <p class="text-sm text-gray-500">
                  Silahkan isi keluhan pelanggan terhadap kerusakan kendaraan
                  yang dimiliki oleh pelanggan tersebut.
                </p>
              </div>
            </div>
            <FormField v-slot="{ componentField }" name="description">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': serviceForm.errors.description,
                  }"
                >
                  <FormRequiredLabel>Deskripsi</FormRequiredLabel>
                </FormLabel>
                <FormControl>
                  <Textarea
                    cols="6"
                    v-bind="componentField"
                    v-model="serviceForm.description"
                    :class="{
                      'border border-red-500': serviceForm.errors.description,
                    }"
                    :disabled="serviceForm.processing"
                  />
                </FormControl>
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="serviceForm.errors.description"
                >
                  {{ serviceForm.errors.description }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
          </div>
        </form>
      </CardContent>
    </Card>
    <VehicleList v-model="vehicleDialogOpen" @selected="onVehicleSelected" />
  </div>
</template>
