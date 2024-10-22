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
import type { IPaginationMeta, IService } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import { usePrice } from "@/Plugin/useNumber";
import { useHttpService } from "@/Services/useHttpServices";
import ReportServiceCodeBox from "@/Components/Report/ReportServiceCodeBox.vue";

const props = defineProps<{
  services: { data: IService[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
    start_date: string;
    end_date: string;
  };
}>();

const price = usePrice();
const http = useHttpService();
const perPage = ref(props.services.meta.per_page);
const isLoading = ref<boolean>(false);
const serviceTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IService>[] = [
  {
    accessorKey: "service_code",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h("div", { class: "px-2 font-semibold" }, "Kode Service"),
    cell: ({ row }) => h(ReportServiceCodeBox, { service: row.original }),
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

            getServices(props.services.meta.current_page);
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
    accessorKey: "customer",
    enableResizing: false,
    size: 250,
    header: ({ column }) =>
      h("div", { class: "gap-2 flex items-center font-semibold" }, "Pelanggan"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize font-semibold" },
        row.original.customer_name
      ),
  },
  {
    accessorKey: "vehicle_number",
    enableResizing: false,
    size: 250,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold" }, "No Plat"),
    cell: ({ row }) =>
      h("div", { class: "capitalize px-3" }, [
        h("p", { class: "font-semibold" }, row.original.vehicle_plate_number),
        h("p", { class: "font-medium" }, row.original.vehicle_name),
      ]),
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

const totalMoneyServiceTransaction = computed(() => {
  return props.services.data.reduce(
    (oldTotal, newTotal) => oldTotal + newTotal.total,
    0
  );
});

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getServices(props.services.meta.current_page);
};

const dateFilter = ref({
  startDate: props.params.start_date,
  endDate: props.params.end_date,
});
// function get category data from database
const getServices = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (
    dateFilter.value.startDate !== null &&
    dateFilter.value.endDate !== null &&
    dateFilter.value.startDate.length > 0 &&
    dateFilter.value.endDate.length > 0
  ) {
    Object.assign(url.value, {
      start_date: dateFilter.value.startDate,
      end_date: dateFilter.value.endDate,
    });
  }

  router.get(route("backoffice.report.service"), url.value, {
    only: ["services", "params"],
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
    dateFilter.value.startDate !== null &&
    dateFilter.value.endDate !== null &&
    dateFilter.value.startDate.length > 0 &&
    dateFilter.value.endDate.length > 0
  ) {
    Object.assign(url2.value, {
      start_date: dateFilter.value.startDate,
      end_date: dateFilter.value.endDate,
    });
  }

  router.get(route("backoffice.report.service-print"), url2.value, {
    only: ["services", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const exportTransaction = () => {
  const url2 = ref({});
  if (
    dateFilter.value.startDate !== null &&
    dateFilter.value.endDate !== null &&
    dateFilter.value.startDate.length > 0 &&
    dateFilter.value.endDate.length > 0
  ) {
    window.open(
      route("backoffice.report.service-export") +
        `?start_date=${dateFilter.value.startDate}&&end_date=${dateFilter.value.endDate}`
    );
  } else {
    window.open(route("backoffice.report.service-export"));
  }
};

const resetDateFilter = () => {
  dateFilter.value.startDate = "";
  dateFilter.value.endDate = "";
  getServices(1);
};
</script>
<template>
  <Head title="Laporan Transaksi Service" />
  <div>
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <FileSpreadsheet class="size-10" />
        <h1 class="text-lg font-semibold">Laporan Transaksi Service</h1>
      </div>
    </div>
    <HeaderInformation>
      Halaman ini dipergunakan untuk memanajemen transaksi service yang masuk
      dalam bengkel. Untuk menambah transaksi silahkan mengkilk tombol
      <strong>Buat Transaksi</strong>
    </HeaderInformation>
    <div class="px-2 py-6 inline-flex items-center gap-4 w-full">
      <div
        class="rounded border border-gray-200 px-2 py-2 text-center grow bg-gray-100"
      >
        <h4 class="text-sm font-medium text-muted-foreground">
          Jumlah Service
        </h4>
        <div class="text-2xl font-bold text-primary">
          {{ services.data.length }} Data
        </div>
      </div>
      <div
        class="rounded border border-gray-200 px-2 py-2 text-center grow bg-gray-100"
      >
        <h4 class="text-sm font-medium text-muted-foreground">
          Total Pemasukan Service
        </h4>
        <div class="text-2xl font-bold text-primary">
          {{ price.convertToRupiah(totalMoneyServiceTransaction) }}
        </div>
      </div>
    </div>
    <div class="bg-gray-100">
      <DataTable
        ref="serviceTable"
        :columns="columns"
        :data="services.data"
        :pagination="services.meta"
        :loading="isLoading"
        @change-limit="changeLimit"
        @change-page="getServices"
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
              <Button variant="outline" @click="resetDateFilter">Reset</Button>
              <Button variant="default" @click="getServices(1)"
                >Cari Transaksi</Button
              >
            </div>
            <div class="space-x-1">
              <Button variant="outline" size="icon" @click="exportTransaction">
                <HardDriveDownload class="size-4 text-primary" />
              </Button>
              <Button variant="outline" size="icon" @click="printTransaction">
                <Printer class="size-4 text-primary" />
              </Button>
            </div>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
