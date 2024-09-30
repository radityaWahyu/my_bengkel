<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { reactive, ref } from "vue";
import {
  Head,
  usePage,
  useForm as useInertiaForm,
  Link,
} from "@inertiajs/vue3";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/shadcn/ui/card";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
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
  Wrench,
  PlusSquare,
  StickyNote,
  OctagonAlert,
  Package,
} from "lucide-vue-next";
import type { IRepair, IProduct } from "@/types/response";

const vehicle = reactive({
  id: "",
  name: "",
  plate_number: "",
  machine_frame: "",
  engine_volume: "",
  engine_type: "",
  type: "",
  production_year: "",
  brand: "",
  customer: "",
});

const serviceForm = useInertiaForm({
  vehicle_id: "",
  description: "",
});

const page = usePage();
const repairs = ref<IRepair[]>();
const products = ref<IProduct[]>([]);

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      vehicle_id: zod
        .string({ message: "Nama Merk harus diisi" })
        .min(1, { message: "Nama Merk harus diisi." }),
      category_id: zod
        .string({ message: "Kategori harus diisi" })
        .min(1, { message: "Kategori harus diisi." }),
      rack_id: zod
        .string({ message: "Rak harus diisi" })
        .min(1, { message: "Rak harus diisi." }),
      stock: zod.number({ message: "Stok awal Produk harus diisi" }),
      buy_price: zod.number({ message: "Harga beli Produk harus diisi" }),
      sale_price: zod.number({ message: "Harga jual Produk harus diisi" }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const onSubmit = () => {};
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
            <Button @click="onSubmit">
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
                <FormField v-slot="{ componentField }" name="vehicle_id">
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
                        <Input
                          type="hidden"
                          v-bind="componentField"
                          v-model="serviceForm.vehicle_id"
                        />
                        <Input
                          type="text"
                          :default-value="vehicle.name"
                          placeholder="Silhkan pilih kendaraan"
                        />
                      </div>
                      <div class="space-x-1">
                        <Button as="button" size="icon">
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
                <Input type="text" :default-value="vehicle.customer" readonly />
              </div>
            </div>
            <div class="grid grid-cols-4 gap-2">
              <div class="space-y-1">
                <Label>CC Mesin</Label>
                <Input type="text" :default-value="vehicle.customer" readonly />
              </div>
              <div class="space-y-1">
                <Label>Tipe Mesin</Label>
                <Input type="text" :default-value="vehicle.customer" readonly />
              </div>
              <div class="space-y-1">
                <Label>Jenis Kendaraan</Label>
                <Input type="text" :default-value="vehicle.customer" readonly />
              </div>
              <div class="space-y-1">
                <Label>Tahun Pembuatan</Label>
                <Input type="text" :default-value="vehicle.customer" readonly />
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
          <div class="space-y-3">
            <div
              class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
            >
              <Wrench class="size-8 text-blue-400" />
              <div class="grow">
                <h4 class="font-medium">Daftar Perbaikan</h4>
                <p class="text-sm text-gray-500">
                  Silahkam masukan jenis jasa perbaikan pada kendaraan dengan
                  mengklik tombol
                  <strong>Pilih Perbaikan</strong>
                </p>
              </div>
              <Button
                as="button"
                variant="outline"
                class="text-primary space-x-2"
              >
                <PlusSquare class="size-5" />
                <span>Pilih Perbaikan</span>
              </Button>
            </div>
            <Table class="border-b border-b-gray-200">
              <TableHeader class="border-t border-t-gray-200 shadow">
                <TableRow>
                  <TableHead> Nama Perbaikan </TableHead>
                  <TableHead>Harga</TableHead>
                  <TableHead></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody v-if="repairs && repairs?.length > 0">
                <TableRow v-for="(repair, index) in repairs" :key="index">
                  <TableCell class="font-medium">
                    {{ repair.name }}
                  </TableCell>
                  <TableCell>{{ repair.price }}</TableCell>
                  <TableCell></TableCell>
                </TableRow>
                <TableRow>
                  <TableCell collspan="2">Sub Total</TableCell>
                  <TableCell> Rp.0</TableCell>
                </TableRow>
              </TableBody>
              <TableBody v-else>
                <TableRow>
                  <TableCell colspan="3">
                    <Alert class="bg-orange-50 border-none rounded w-full">
                      <OctagonAlert class="size-6" />
                      <AlertTitle class="ml-2">Keterangan</AlertTitle>
                      <AlertDescription class="ml-2">
                        Tidak terdapat data perbaikan silahkan menambahkan
                        terlebih dahulu
                      </AlertDescription>
                    </Alert>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
          <div class="space-y-3">
            <div
              class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
            >
              <Package class="size-8 text-blue-400" />
              <div class="grow">
                <h4 class="font-medium">Daftar Spare Part</h4>
                <p class="text-sm text-gray-500">
                  Untuk masukan spare part perbaikan pada kendaraan dengan
                  silahkan mengklik tombol
                  <strong>Pilih Spare Part</strong>
                </p>
              </div>
              <Button
                as="button"
                variant="outline"
                class="text-primary space-x-2"
              >
                <PlusSquare class="size-5" />
                <span>Pilih Spare Part</span>
              </Button>
            </div>
            <Table class="border-b border-b-gray-200">
              <TableHeader class="border-t border-t-gray-200 shadow">
                <TableRow>
                  <TableHead> Nama Perbaikan </TableHead>
                  <TableHead>Harga</TableHead>
                  <TableHead></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody v-if="products && products?.length > 0">
                <TableRow v-for="(product, index) in products" :key="index">
                  <TableCell class="font-medium">
                    {{ product.name }}
                  </TableCell>
                  <TableCell>{{ product.sale_price }}</TableCell>
                  <TableCell></TableCell>
                </TableRow>
                <TableRow>
                  <TableCell collspan="2">Sub Total</TableCell>
                  <TableCell> Rp.0</TableCell>
                </TableRow>
              </TableBody>
              <TableBody v-else>
                <TableRow>
                  <TableCell colspan="3">
                    <Alert class="bg-orange-50 border-none rounded w-full">
                      <OctagonAlert class="size-6" />
                      <AlertTitle class="ml-2">Keterangan</AlertTitle>
                      <AlertDescription class="ml-2">
                        Tidak terdapat data barang silahkan menambahkan terlebih
                        dahulu
                      </AlertDescription>
                    </Alert>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
