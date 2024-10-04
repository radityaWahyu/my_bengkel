<script setup lang="ts">
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader } from "@/shadcn/ui/card";

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import { Input } from "@/shadcn/ui/input";
import { Textarea } from "@/shadcn/ui/textarea";
import { Label } from "@/shadcn/ui/label";

import {
  CarFront,
  Wrench,
  StickyNote,
  OctagonAlert,
  Package,
  ClipboardPenLine,
} from "lucide-vue-next";
import { usePrice } from "@/Plugin/useNumber";
import type { IServiceDetail } from "@/types/response";

import Header from "@/Components/App/Header.vue";

const props = defineProps<{
  service: IServiceDetail;
  edit?: boolean;
}>();

const price = usePrice();

const repairsSubTotal = computed(() => {
  return props.service.repairs.reduce(
    (accumulator, current) => accumulator + current.total,
    0
  );
});

const productsSubTotal = computed(() => {
  return props.service.products.reduce(
    (accumulator, current) => accumulator + current.total,
    0
  );
});

const totalInvoice = computed(
  () => productsSubTotal.value + repairsSubTotal.value
);
</script>
<template>
  <Head title="Form Transaksi Service" />
  <div class="min-h-screen bg-slate-100">
    <Header />
    <div class="py-6">
      <Card class="rounded shadow-none w-3/5 m-auto">
        <CardHeader>
          <div class="flex items-center justify-between">
            <div class="flex items-center px-2 gap-4 text-primary">
              <ClipboardPenLine class="size-8" />
              <h1 class="font-medium text-lg">Detail Transaksi Service</h1>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <div class="space-y-3 pb-4">
            <div class="space-y-3">
              <div
                class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
              >
                <CarFront class="size-8 text-blue-400" />
                <div>
                  <h4 class="font-medium">Data Kendaraan</h4>
                  <p class="text-sm text-gray-500">
                    Silahkan cek data kendaraan apakah sesuai dengan yang
                    dimiliki oleh pelanggan.
                  </p>
                </div>
              </div>
              <div class="grid grid-cols-2 items-center gap-2">
                <div>
                  <Label>Kendaraan</Label>
                  <Input
                    type="text"
                    :default-value="
                      service.vehicle.plate_number + '-' + service.vehicle.name
                    "
                    readonly
                  />
                </div>
                <div class="space-y-1">
                  <Label>Nama Pelanggan</Label>
                  <Input
                    type="text"
                    :default-value="service.vehicle.customer"
                    readonly
                  />
                </div>
              </div>
              <div class="grid grid-cols-4 gap-2">
                <div class="space-y-1">
                  <Label>CC Mesin</Label>
                  <Input
                    type="number"
                    :default-value="service.vehicle.engine_volume"
                    readonly
                  />
                </div>
                <div class="space-y-1">
                  <Label>Tipe Mesin</Label>
                  <Input
                    type="text"
                    :default-value="
                      service.vehicle.engine_type === 'petrol'
                        ? 'Bensin'
                        : 'Diesel'
                    "
                    readonly
                  />
                </div>
                <div class="space-y-1">
                  <Label>Jenis Kendaraan</Label>
                  <Input
                    type="text"
                    :default-value="
                      service.vehicle.type === 'car' ? 'Mobil' : 'Sepeda Motor'
                    "
                    readonly
                  />
                </div>
                <div class="space-y-1">
                  <Label>Tahun Pembuatan</Label>
                  <Input
                    type="number"
                    :default-value="service.vehicle.production_year"
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
                    Keluhan kerusakan yang diberikan pelanggan terhadap
                    kendaraan.
                  </p>
                </div>
              </div>
              <div>
                <Textarea
                  cols="6"
                  :default-value="service.description"
                  readonly
                />
              </div>
            </div>
            <div class="space-y-3">
              <div
                class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
              >
                <Wrench class="size-8 text-blue-400" />
                <div class="grow">
                  <h4 class="font-medium">Daftar Perbaikan</h4>
                  <p class="text-sm text-gray-500">
                    Daftar jenis perbaikan yang dimasukkan dalam service
                    kendaraan anda.
                  </p>
                </div>
              </div>
              <!-- repair table -->
              <Table class="border-b border-b-gray-200">
                <TableHeader class="border-t border-t-gray-200 shadow">
                  <TableRow>
                    <TableHead class="w-[150px]"> Nama Perbaikan </TableHead>
                    <TableHead class="text-right w-32">Harga</TableHead>
                    <TableHead>Nama Mekanik</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody v-if="!!service && service.repairs.length > 0">
                  <TableRow
                    v-for="(repair, index) in service.repairs"
                    :key="index"
                  >
                    <TableCell class="font-medium">
                      {{ repair.name }}
                    </TableCell>
                    <TableCell class="text-right">
                      {{ price.convertToRupiah(repair.price) }}
                    </TableCell>
                    <TableCell class="capitalize">
                      <span
                        v-if="repair.employee_name"
                        class="bg-sky-100 px-2 py-1"
                        >{{ repair.employee_name }}</span
                      >
                      <span v-else class="bg-yellow-100 px-2 py-1"> - </span>
                    </TableCell>
                  </TableRow>
                  <TableRow class="bg-sky-50 text-blue-700">
                    <TableCell class="text-right font-semibold py-2">
                      Sub Total
                    </TableCell>
                    <TableCell class="font-semibold text-right">
                      {{ price.convertToRupiah(repairsSubTotal) }}
                    </TableCell>
                  </TableRow>
                </TableBody>

                <TableBody v-if="service.repairs.length === 0">
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
            <!-- item table -->
            <div class="space-y-3">
              <div
                class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
              >
                <Package class="size-8 text-blue-400" />
                <div class="grow">
                  <h4 class="font-medium">Daftar Spare Part</h4>
                  <p class="text-sm text-gray-500">
                    Daftar barang yang dipergunakan untuk menservice kendaraan
                    anda.
                  </p>
                </div>
              </div>
              <Table class="border-b border-b-gray-200">
                <TableHeader class="border-t border-t-gray-200 shadow">
                  <TableRow>
                    <TableHead class="w-[250px]">Nama Barang </TableHead>
                    <TableHead class="w-[200px]">Harga</TableHead>
                    <TableHead class="w-24 text-center">Jumlah</TableHead>
                    <TableHead class="text-right w-[150px]">Total</TableHead>
                    <TableHead></TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody v-if="service!! && service.products.length > 0">
                  <TableRow
                    v-for="(product, index) in service.products"
                    :key="index"
                  >
                    <TableCell class="font-medium">
                      {{ product.name }}
                    </TableCell>
                    <TableCell>{{
                      price.convertToRupiah(product.price)
                    }}</TableCell>
                    <TableCell>
                      {{ product.qty }}
                    </TableCell>
                    <TableCell class="text-right">
                      {{ price.convertToRupiah(product.total) }}
                    </TableCell>
                  </TableRow>
                  <TableRow class="bg-sky-50 text-blue-700">
                    <TableCell
                      class="text-right font-semibold py-2"
                      colspan="3"
                    >
                      Sub Total
                    </TableCell>
                    <TableCell class="font-semibold text-right">
                      {{ price.convertToRupiah(productsSubTotal) }}
                    </TableCell>
                    <TableCell></TableCell>
                  </TableRow>
                </TableBody>
                <TableBody v-else>
                  <TableRow>
                    <TableCell colspan="5">
                      <Alert class="bg-orange-50 border-none rounded w-full">
                        <OctagonAlert class="size-6" />
                        <AlertTitle class="ml-2">Keterangan</AlertTitle>
                        <AlertDescription class="ml-2">
                          Tidak terdapat data barang silahkan menambahkan
                          terlebih dahulu
                        </AlertDescription>
                      </Alert>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>

            <div
              class="flex items-center justify-end w-full py-4 px-4 bg-sky-50"
            >
              <div class="text-blue-500">
                <h2 class="text-lg">Total Invoice Service</h2>
                <h2 class="text-3xl font-semibold">
                  {{ price.convertToRupiah(totalInvoice) }}
                </h2>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
