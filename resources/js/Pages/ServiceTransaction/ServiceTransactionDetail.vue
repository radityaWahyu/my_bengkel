<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import { IPurchaseDetail, IServiceDetail, ISettingData } from "@/types/response";

export default {
  layout: Backoffice,
};
</script>

<script setup lang="ts">
import { computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { FileText, MoveLeft } from "lucide-vue-next";
import { usePrice } from "@/Plugin/useNumber";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { Button } from "@/shadcn/ui/button";

const props = defineProps<{
  setting: ISettingData;
  service: IServiceDetail;
}>();

const price = usePrice();
const totalPayment = computed(() => props.service.total + props.service.extra_pay);
const payCharge = computed(() => props.service.paid - totalPayment.value);

const back = () => {
  window.history.back();
};
</script>
<template>
  <Head title="Detail Pembelian" />
  <div class="p-4">
    <div class="bg-white rounded border border-gray-200 p-6 space-y-4">
      <div
        class="inline-flex items-center gap-4 border-b border-dashed border-b-gray-200"
      >
        <Button variant="outline" size="icon" @click="back">
          <MoveLeft class="size-6 text-blue-400" />
        </Button>
        <div class="flex items-center gap-4 p-2">
          <div>
            <FileText class="size-8 text-blue-400" />
          </div>
          <div>
            <h4 class="font-medium">Detail Transaksi Pembelian</h4>
            <p class="text-sm text-gray-500">
              Halaman ini dipergunakan untuk melihat detail dari transaksi pembelian
              barang.
            </p>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 py-2">
          <img :src="setting.logo_bengkel" alt="logo-bengkel" class="size-12" />
          <div>
            <h3 class="text-lg font-semibold uppercase">
              {{ setting.nama_bengkel }}
            </h3>
            <p class="text-xs w-3/5">{{ setting.alamat_bengkel }}</p>
          </div>
        </div>
        <div>
          <h2 class="text-sm">No Invoice :</h2>
          <p class="text-sm font-medium">{{ service.service_code }}</p>
          <p class="text-xs">Tanggal : {{ service.created_at }}</p>
        </div>
      </div>
      <div class="space-y-4">
        <div>
          <div class="grid grid-cols-2 gap-[1px]">
            <div class="bg-gray-600 text-gray-50">
              <p class="px-2 py-1 text-xs">Data Kendaraan</p>
              <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm">
                {{ service.vehicle.plate_number }} - {{ service.vehicle.name }}
              </h4>
            </div>
            <div class="bg-gray-600 text-gray-50">
              <p class="px-2 py-1 text-xs">Nama Pelanggan</p>
              <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm capitalize">
                {{ service.vehicle.customer }}
              </h4>
            </div>
          </div>
          <div class="grid grid-cols-4 gap-[1px]">
            <div class="bg-gray-600 text-gray-50">
              <p class="px-2 py-1 text-xs">CC Mesin</p>
              <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm">
                {{ service.vehicle.engine_volume }}
              </h4>
            </div>
            <div class="bg-gray-600 text-gray-50">
              <p class="px-2 py-1 text-xs">Tipe Mesin</p>
              <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm">
                {{ service.vehicle.engine_type === "petrol" ? "Bensin" : "Diesel" }}
              </h4>
            </div>
            <div class="bg-gray-600 text-gray-50">
              <p class="px-2 py-1 text-xs">Jenis Kendaraan</p>
              <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm">
                {{ service.vehicle.type === "car" ? "Mobil" : "Sepeda Motor" }}
              </h4>
            </div>
            <div class="bg-gray-600 text-gray-50">
              <p class="px-2 py-1 text-xs">Tahun Pembuatan</p>
              <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm">
                {{ service.vehicle.production_year }}
              </h4>
            </div>
          </div>
        </div>
        <div class="bg-gray-600 text-gray-50">
          <p class="px-2 py-1 text-xs">Keluhan</p>
          <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm capitalize">
            {{ service.description }}
          </h4>
        </div>
        <div>
          <div>
            <h2 class="text-sm">Daftar Komponen service</h2>
          </div>
          <Table class="border border-gray-500">
            <TableHeader>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableHead class="w-[50px] text-center">No</TableHead>
                <TableHead>Service Item</TableHead>
                <TableHead class="text-center">Jumlah</TableHead>
                <TableHead class="text-right">Harga Satuan</TableHead>
                <TableHead class="text-right">Sub Total</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="(repair, index) in service.repairs"
                :key="index"
                class="border border-gray-500 divide-x divide-gray-500"
              >
                <TableCell class="text-center">{{ index + 1 }}.</TableCell>
                <TableCell>{{ repair.name }}</TableCell>
                <TableCell class="text-center">{{ repair.qty }}</TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(repair.price)
                }}</TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(repair.total)
                }}</TableCell>
              </TableRow>
              <TableRow
                v-for="(product, index) in service.products"
                :key="index"
                class="border border-gray-500 divide-x divide-gray-500"
              >
                <TableCell class="text-center"
                  >{{ index + service.repairs.length + 1 }}.</TableCell
                >
                <TableCell>{{ product.name }}</TableCell>
                <TableCell class="text-center">{{ product.qty }}</TableCell>
                <TableCell class="text-right">
                  {{ price.convertToRupiah(product.price) }}
                </TableCell>
                <TableCell class="text-right">
                  {{ price.convertToRupiah(product.total) }}
                </TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right">Total</TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(service.total)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right">
                  Biaya Ekstra
                  <p class="text-xs">
                    *Pembayaran dengan meggunakan
                    <strong>{{ service.payment_type }}</strong>
                  </p>
                </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(service.extra_pay)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right"> Total Invoice </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(totalPayment)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right"> Dibayar </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(service.paid)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right"> Kembalian </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(payCharge)
                }}</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <div class="grid grid-cols-2">
          <div class="grow space-y-2">
            <p class="text-sm">
              <strong class="block capitalize">Catatan :</strong>
              {{ service.notes }}
            </p>
            <p class="text-xs">
              <strong class="block capitalize">Keterangan :</strong>
              Garansi service diberikan selama 1 minggu setelah perbaikan, dengan cara
              menunjukan lembar invoice ini.
            </p>
          </div>
          <div class="grow text-sm text-center">
            <h4 class="mb-10">Dibayar Oleh</h4>
            <p class="font-semibold">{{ service.vehicle.customer }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
