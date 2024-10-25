<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, h, computed } from "vue";

import { router, Head } from "@inertiajs/vue3";
import {
  ArrowUpDown,
  ArrowDownUp,
  FileSpreadsheet,
  Printer,
  HardDriveDownload,
} from "lucide-vue-next";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import { Label } from "@/shadcn/ui/label";
import type { ColumnDef } from "@tanstack/vue-table";
import type { IPaginationMeta, IPurchase } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import { usePrice } from "@/Plugin/useNumber";
import ReportPurchaseCodeBox from "@/Components/Report/ReportPurchaseCodeBox.vue";

const props = defineProps<{
  purchases: { data: IPurchase[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
    start_date: string;
    end_date: string;
  };
}>();

const price = usePrice();
const perPage = ref(props.purchases.meta.per_page);
const isLoading = ref<boolean>(false);
const purchaseTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IPurchase>[] = [
  {
    accessorKey: "sale_code",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h("div", { class: "px-2 font-semibold" }, "Kode Penjualan"),
    cell: ({ row }) => h(ReportPurchaseCodeBox, { purchase: row.original }),
  },
  {
    accessorKey: "created_at",
    enableResizing: false,
    size: 250,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "created_at";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getpurchases(props.purchases.meta.current_page);
          },
          class: "w-full flex items-center text-center px-0",
        },
        () => [
          h("div", { class: "gap-2 flex text-center font-semibold" }, [
            props.params?.sortType == "desc" &&
            props.params?.sortName == "stock"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Tanggal Transaksi",
          ]),
        ]
      );
    },
    cell: ({ row }) =>
      h("div", { class: "text-center" }, row.original.created_at),
  },
  {
    accessorKey: "payment_name",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h(
        "div",
        { class: "gap-2 text-center font-semibold px-4" },
        "Jenis Bayar"
      ),
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize font-semibold text-center px-4" },
        row.original.payment_type
      ),
  },
  {
    accessorKey: "extray_pay",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "gap-2 text-right font-semibold px-4" }, "Biaya Extra"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize font-semibold text-right px-4" },
        price.convertToRupiah(row.original.extra_pay)
      ),
  },
  {
    accessorKey: "total_sale",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "gap-2 text-right font-semibold px-4" }, "Sub Total"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize font-semibold text-right px-4" },
        price.convertToRupiah(row.original.total)
      ),
  },
  {
    accessorKey: "total",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "gap-2 text-right font-semibold px-4" }, "Total"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize font-semibold text-right px-4" },
        price.convertToRupiah(row.original.total + row.original.extra_pay)
      ),
  },
];

const totalMoneySaleTransaction = computed(() => {
  return props.purchases.data.reduce(
    (oldTotal, newTotal) => oldTotal + newTotal.total + newTotal.extra_pay,
    0
  );
});

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getpurchases(props.purchases.meta.current_page);
};

const dateFilter = ref({
  startDate: props.params.start_date,
  endDate: props.params.end_date,
});
// function get category data from database
const getpurchases = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (
    dateFilter.value.startDate &&
    dateFilter.value.endDate &&
    dateFilter.value.startDate.length > 0 &&
    dateFilter.value.endDate.length > 0
  ) {
    Object.assign(url.value, {
      start_date: dateFilter.value.startDate,
      end_date: dateFilter.value.endDate,
    });
  }

  router.get(route("backoffice.report.sale"), url.value, {
    only: ["purchases", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const printTransaction = () => {
  const url2 = ref({});

  if (
    dateFilter.value.startDate &&
    dateFilter.value.endDate &&
    dateFilter.value.startDate.length > 0 &&
    dateFilter.value.endDate.length > 0
  )
    Object.assign(url2.value, {
      start_date: dateFilter.value.startDate,
      end_date: dateFilter.value.endDate,
    });

  router.get(route("backoffice.report.purchase-print"), url2.value, {
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const exportTransaction = () => {
  const url2 = ref({});
  if (
    dateFilter.value.startDate &&
    dateFilter.value.endDate &&
    dateFilter.value.startDate.length > 0 &&
    dateFilter.value.endDate.length > 0
  ) {
    window.open(
      route("backoffice.report.sale-export") +
        `?start_date=${dateFilter.value.startDate}&&end_date=${dateFilter.value.endDate}`
    );
  } else {
    window.open(route("backoffice.report.purchase-export"));
  }
};

const resetDateFilter = () => {
  dateFilter.value.startDate = "";
  dateFilter.value.endDate = "";
  getpurchases(1);
};
</script>
<template>
  <Head title="Laporan Transaksi Penjualan" />
  <div>
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <FileSpreadsheet class="size-10" />
        <h1 class="text-lg font-semibold">Laporan Transaksi Pembelian</h1>
      </div>
    </div>
    <HeaderInformation>
      Halaman ini dipergunakan untuk memantau laporan setiap transaksi penjualan
      barang yang tersimpan ke dalam sistem.
    </HeaderInformation>
    <div class="px-2 py-6 inline-flex items-center gap-4 w-full">
      <div
        class="rounded border border-gray-200 px-2 py-2 text-center grow bg-gray-100"
      >
        <h4 class="text-sm font-medium text-muted-foreground">
          Jumlah Penjualan
        </h4>
        <div class="text-2xl font-bold text-primary">
          {{ purchases.data.length }} Data
        </div>
      </div>
      <div
        class="rounded border border-gray-200 px-2 py-2 text-center grow bg-gray-100"
      >
        <h4 class="text-sm font-medium text-muted-foreground">
          Total Transaksi Pembelian
        </h4>
        <div class="text-2xl font-bold text-primary">
          {{ price.convertToRupiah(totalMoneySaleTransaction) }}
        </div>
      </div>
    </div>
    <div class="bg-gray-100">
      <DataTable
        ref="purchaseTable"
        :columns="columns"
        :data="purchases.data"
        :pagination="purchases.meta"
        :loading="isLoading"
        @change-limit="changeLimit"
        @change-page="getpurchases"
      >
        <template #filter>
          <div class="inline-flex items-center gap-2">
            <div class="space-x-4">
              <div class="inline-flex items-center gap-2">
                <Label class="grow">Tanggal Awal</Label>
                <Input
                  type="date"
                  class="bg-white block w-40"
                  v-model="dateFilter.startDate"
                />
              </div>
              <div class="inline-flex items-center gap-2">
                <Label class="grow">Tanggal Akhir</Label>
                <Input
                  type="date"
                  class="bg-white block w-40"
                  v-model="dateFilter.endDate"
                />
              </div>
            </div>
            <div class="space-x-1">
              <Button variant="outline" @click="resetDateFilter" type="button">
                Reset
              </Button>
              <Button variant="default" @click="getpurchases(1)" type="button">
                Cari Transaksi
              </Button>
            </div>
            <div class="space-x-1">
              <Button
                variant="outline"
                size="icon"
                @click="exportTransaction"
                type="button"
              >
                <HardDriveDownload class="size-4 text-primary" />
              </Button>
              <Button
                variant="outline"
                size="icon"
                @click="printTransaction"
                type="button"
              >
                <Printer class="size-4 text-primary" />
              </Button>
            </div>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
