<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import { IPurchaseDetail } from "@/types/response";

export default {
  layout: Backoffice,
};
</script>

<script setup lang="ts">
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
  purchase: IPurchaseDetail;
}>();

const price = usePrice();

const backToPurchase = () => {
  router.get(
    route("backoffice.purchase.index"),
    {},
    {
      replace: true,
    }
  );
};
</script>
<template>
  <Head title="Detail Pembelian" />
  <div class="p-4">
    <div class="bg-white rounded border border-gray-200 p-6 space-y-4">
      <div
        class="inline-flex items-center gap-4 border-b border-dashed border-b-gray-200"
      >
        <Button variant="outline" size="icon" @click="backToPurchase">
          <MoveLeft class="size-6 text-blue-400" />
        </Button>
        <div class="flex items-center gap-4 p-2">
          <div>
            <FileText class="size-8 text-blue-400" />
          </div>
          <div>
            <h4 class="font-medium">Detail Transaksi Pembelian</h4>
            <p class="text-sm text-gray-500">
              Halaman ini dipergunakan untuk melihat detail dari transaksi
              pembelian barang.
            </p>
          </div>
        </div>
      </div>
      <div>
        <div class="grid grid-cols-3 gap-[1px]">
          <div class="bg-gray-600 text-gray-50">
            <p class="px-2 py-1 text-sm">Kode Transaksi</p>
            <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm font-medium">
              {{ purchase.purchase_code }}
            </h4>
          </div>
          <div class="bg-gray-600 text-gray-50">
            <p class="px-2 py-1 text-sm">No. Nota</p>
            <h4
              class="bg-gray-100 text-gray-600 px-2 py-1 text-sm capitalize font-medium"
            >
              {{ purchase.invoice_number }}
            </h4>
          </div>
          <div class="bg-gray-600 text-gray-50">
            <p class="px-2 py-1 text-sm">Tanggal Pembelian</p>
            <h4
              class="bg-gray-100 text-gray-600 px-2 py-1 text-sm capitalize font-medium"
            >
              {{ purchase.transaction_date }}
            </h4>
          </div>
        </div>
      </div>
      <div>
        <div class="grid grid-cols-3 gap-[1px]">
          <div class="bg-gray-600 text-gray-50">
            <p class="px-2 py-1 text-sm">Pemasok</p>
            <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm font-medium">
              {{ purchase.supplier.name }}
            </h4>
          </div>
          <div class="bg-gray-600 text-gray-50">
            <p class="px-2 py-1 text-sm">Nama Kontak</p>
            <h4
              class="bg-gray-100 text-gray-600 px-2 py-1 text-sm capitalize font-medium"
            >
              {{ purchase.supplier.contact_name }}
            </h4>
          </div>
          <div class="bg-gray-600 text-gray-50">
            <p class="px-2 py-1 text-sm">No Whatsapp</p>
            <h4
              class="bg-gray-100 text-gray-600 px-2 py-1 text-sm capitalize font-medium"
            >
              {{ purchase.supplier.whatsapp }}
            </h4>
          </div>
        </div>
      </div>
      <div>
        <div class="bg-gray-600">
          <h2 class="text-sm font-medium text-white px-2 py-1">
            Daftar Barang
          </h2>
        </div>
        <Table class="border border-gray-500">
          <TableHeader>
            <TableRow class="border border-gray-500 divide-x divide-gray-500">
              <TableHead class="w-[50px] text-center">No</TableHead>
              <TableHead>Nama Barang</TableHead>
              <TableHead class="text-right">Harga Beli</TableHead>
              <TableHead class="text-center">Jumlah</TableHead>
              <TableHead class="text-right">Sub Total</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow
              v-for="(purchaseProduct, index) in purchase.purchase_products"
              :key="purchaseProduct.id"
              class="border border-gray-500 divide-x divide-gray-500"
            >
              <TableCell class="text-center">{{ index + 1 }}.</TableCell>
              <TableCell>{{ purchaseProduct.name }}</TableCell>
              <TableCell class="text-right">{{
                price.convertToRupiah(purchaseProduct.price)
              }}</TableCell>
              <TableCell class="text-center">{{
                purchaseProduct.qty
              }}</TableCell>
              <TableCell class="text-right">{{
                price.convertToRupiah(purchaseProduct.total)
              }}</TableCell>
            </TableRow>
            <TableRow class="border border-gray-500 divide-x divide-gray-500">
              <TableCell colspan="4" class="text-right">Total</TableCell>
              <TableCell class="text-right">{{
                price.convertToRupiah(purchase.total)
              }}</TableCell>
            </TableRow>
            <TableRow class="border border-gray-500 divide-x divide-gray-500">
              <TableCell colspan="4" class="text-right">
                Biaya Ekstra
                <p class="text-xs">
                  *Pembayaran dengan meggunakan
                  <strong>{{ purchase.payment_type }}</strong>
                </p>
              </TableCell>
              <TableCell class="text-right">{{
                price.convertToRupiah(purchase.extra_pay)
              }}</TableCell>
            </TableRow>
            <TableRow class="border border-gray-500 divide-x divide-gray-500">
              <TableCell colspan="4" class="text-right">
                Total Invoice
              </TableCell>
              <TableCell class="text-right">{{
                price.convertToRupiah(purchase.total + purchase.extra_pay)
              }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div class="w-full">
        <div class="bg-gray-600 text-gray-50 w-1/4">
          <p class="px-2 py-1 text-sm">Di Input Oleh :</p>
          <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm font-medium">
            {{ purchase.user.name }}
          </h4>
        </div>
        <div class="bg-gray-600 text-gray-50 w-1/4">
          <p class="px-2 py-1 text-sm">Tanggal Input :</p>
          <h4 class="bg-gray-100 text-gray-600 px-2 py-1 text-sm font-medium">
            {{ purchase.created_at }}
          </h4>
        </div>
      </div>
    </div>
  </div>
</template>
