<script setup lang="ts">
import { computed, ref } from "vue";
import { Head } from "@inertiajs/vue3";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { usePrice } from "@/Plugin/useNumber";
import { ISaleDetail, ISettingData } from "@/types/response";

const props = defineProps<{
  setting: ISettingData;
  sale: ISaleDetail;
}>();

const price = usePrice();
const totalPayment = computed(() => props.sale.total + props.sale.extra_pay);
const payCharge = computed(() => props.sale.paid - totalPayment.value);
</script>
<template>
  <Head title="Cetak Invoice Penjualan" />
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
          <p class="text-sm font-medium">{{ sale.sale_code }}</p>
          <p class="text-xs">Tanggal : {{ sale.created_at }}</p>
        </div>
      </div>
      <div class="space-y-4">
        <div>
          <div>
            <h2 class="text-sm">Daftar Barang</h2>
          </div>
          <Table class="border border-gray-500">
            <TableHeader>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableHead class="w-[50px] text-center">No</TableHead>
                <TableHead>sale Item</TableHead>
                <TableHead class="text-center">Jumlah</TableHead>
                <TableHead class="text-right">Harga Satuan</TableHead>
                <TableHead class="text-right">Sub Total</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="(product, index) in sale.products"
                :key="index"
                class="border border-gray-500 divide-x divide-gray-500"
              >
                <TableCell class="text-center">{{ index + 1 }}.</TableCell>
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
                  price.convertToRupiah(sale.total)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right">
                  Biaya Ekstra
                  <p class="text-xs">
                    *Pembayaran dengan meggunakan
                    <strong>{{ sale.payment_type }}</strong>
                  </p>
                </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(sale.extra_pay)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right">
                  Total Invoice
                </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(totalPayment)
                }}</TableCell>
              </TableRow>
              <TableRow class="border border-gray-500 divide-x divide-gray-500">
                <TableCell colspan="4" class="text-right"> Dibayar </TableCell>
                <TableCell class="text-right">{{
                  price.convertToRupiah(sale.paid)
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
            <p class="text-xs">
              <strong class="block capitalize">Keterangan :</strong>
              Barang telah di beli tidak dapat di tukar kembali, silahkan cek
              barang anda sebelum pembelian selesai.
            </p>
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
