<script setup lang="ts">
import { computed } from "vue";
import { ISale, ISettingData } from "@/types/response";
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

const props = defineProps<{
  sales: { data: ISale[] };
  setting: ISettingData;
}>();

const price = usePrice();
const totalSaleTransaction = computed(() =>
  props.sales.data.reduce(
    (oldValue, newValue) => oldValue + newValue.total + newValue.extra_pay,
    0
  )
);
</script>
<template>
  <Head title="Cetak Laporan Penjualan" />
  <div class="sheet-outer">
    <div class="sheet text-wrap space-y-6">
      <div class="flex items-center gap-3">
        <img :src="setting.logo_bengkel" alt="logo-bengkel" class="size-12" />
        <div>
          <h3 class="text-lg font-semibold uppercase">
            {{ setting.nama_bengkel }}
          </h3>
          <p class="text-xs w-3/5">{{ setting.alamat_bengkel }}</p>
        </div>
      </div>
      <div>
        <h2
          class="scroll-m-20 border-b text-xl font-semibold tracking-tight transition-colors first:mt-0 text-center"
        >
          LAPORAN TRANSAKSI PENJUALAN
        </h2>
      </div>
      <div>
        <Table class="border border-gray-400">
          <TableHeader>
            <TableRow class="border border-gray-400 divide-x divide-gray-400">
              <TableHead class="w-[20px] text-center">No.</TableHead>
              <TableHead class="w-[200px]">Kode Penjualan</TableHead>
              <TableHead class="text-center">Tanggal</TableHead>
              <TableHead>Jenis Bayar</TableHead>
              <TableHead class="text-right">Biaya Ekstra</TableHead>
              <TableHead class="text-right">Sub Total</TableHead>
              <TableHead class="text-right">Total</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow
              v-for="(sale, index) in sales.data"
              :key="sale.id"
              class="border border-gray-400 divide-x divide-gray-400"
            >
              <TableCell class="text-center"> {{ index + 1 }}. </TableCell>
              <TableCell>{{ sale.sale_code }}</TableCell>
              <TableCell class="text-center">{{ sale.created_at }}</TableCell>
              <TableCell class="text-center">{{ sale.payment_type }}</TableCell>
              <TableCell class="text-right">
                {{ price.convertToRupiah(sale.extra_pay) }}
              </TableCell>
              <TableCell class="text-right">
                {{ price.convertToRupiah(sale.total) }}
              </TableCell>
              <TableCell class="text-right">
                {{ price.convertToRupiah(sale.total + sale.extra_pay) }}
              </TableCell>
            </TableRow>
            <TableRow class="border border-gray-400 divide-x divide-gray-400">
              <TableCell colspan="6" class="text-right font-semibold">
                Total Transaksi Service
              </TableCell>
              <TableCell class="text-right">{{
                price.convertToRupiah(totalSaleTransaction)
              }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
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
  padding: 0.5cm;
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
    padding: 0;
    @apply bg-transparent;
  }
}
@page {
  size: 210mm 296mm;
  padding: 0.5cm;
  margin: auto;
}
</style>
