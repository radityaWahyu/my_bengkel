<script setup lang="ts">
import { computed, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { usePrice } from "@/Plugin/useNumber";
import { IServiceDetail, ISettingData } from "@/types/response";

const props = defineProps<{
  setting: ISettingData;
  service: IServiceDetail;
}>();

const price = usePrice();
const payCharge = computed(() => props.service.paid - props.service.total);
</script>
<template>
  <Head title="Cetak Invoice Service" />
  <div class="sheet-outer">
    <div class="sheet text-wrap space-y-3">
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
              <h4
                class="bg-gray-100 text-gray-600 px-2 py-1 text-sm capitalize"
              >
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
                {{
                  service.vehicle.engine_type === "petrol" ? "Bensin" : "Diesel"
                }}
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
                </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(service.extra_pay)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right">
                  Total Invoice
                </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(service.total + service.extra_pay)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right"> Dibayar </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(service.paid)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right">
                  Kembalian
                </TableCell>
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
              Garansi service diberikan selama 1 minggu setelah perbaikan,
              dengan cara menunjukan lembar invoice ini.
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
<style lang="scss" scoped>
.sheet-outer {
  margin: 0;
  @apply min-h-screen;
  @apply bg-gray-500;
  @apply p-10;
}
.sheet {
  margin: 0;
  overflow: hidden;
  position: relative;
  box-sizing: border-box;
}

.sheet-outer .sheet {
  width: 210mm;
}
.sheet {
  padding: 1cm;
}

@media screen {
  .sheet {
    @apply bg-white;
    margin: auto;
  }
}

@media print {
  .sheet-outer {
    width: 210mm;
    padding: 1cm;
    @apply bg-transparent;
  }
}
@page {
  size: 210mm 296mm;
  padding: 1cm;
  margin: auto;
}
</style>
