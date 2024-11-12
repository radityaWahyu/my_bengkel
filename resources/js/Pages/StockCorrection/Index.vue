<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, h } from "vue";
import { watchDebounced } from "@vueuse/core";
import { router, Head } from "@inertiajs/vue3";
import {
  Plus,
  ClipboardPenLine,
  ArrowUpDown,
  ArrowDownUp,
  Trash2,
} from "lucide-vue-next";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import type { ColumnDef } from "@tanstack/vue-table";
import type { IPaginationMeta, IStockCorrection } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import LinkButton from "@/Components/App/LinkButton.vue";
import StockCorrectionButtonAction from "@/Components/StockCorrection/StockCorrectionButtonAction.vue";

const props = defineProps<{
  stock_corrections: { data: IStockCorrection[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
    start_date: string;
    end_date: string;
  };
}>();

const openDeleteConfirm = ref<boolean>(false);
const perPage = ref(props.stock_corrections.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const stockCorrectionTable = ref<InstanceType<typeof DataTable> | null>(null);

const columns: ColumnDef<IStockCorrection>[] = [
  {
    accessorKey: "created_at",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold" }, "Tanggal Input"),
    cell: ({ row }) =>
      h("div", { class: "px-3 font-medium" }, row.original.created_at),
  },
  {
    accessorKey: "product_name",
    enableResizing: false,
    size: 250,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold" }, "Nama Barang"),
    cell: ({ row }) =>
      h("div", { class: "px-3 font-medium" }, row.original.product_name),
  },
  {
    accessorKey: "old_stock",
    enableResizing: false,
    size: 100,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold text-center" }, "Stok Lama"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "px-3 font-medium text-center" },
        row.original.old_stock
      ),
  },
  {
    accessorKey: "new_stock",
    enableResizing: false,
    size: 100,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold text-center" }, "Stok Baru"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "px-3 font-medium text-center" },
        row.original.new_stock
      ),
  },
  {
    accessorKey: "deviation",
    enableResizing: false,
    size: 100,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold text-center" }, "Selisih"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "px-3 font-medium text-center" },
        row.original.old_stock - row.original.new_stock
      ),
  },
  {
    accessorKey: "employee",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold" }, "Pegawai"),
    cell: ({ row }) =>
      h("div", { class: "px-3 font-medium" }, row.original.employee),
  },
  {
    id: "actions",
    enableHiding: false,
    size: 60,
    cell: ({ row }) =>
      h(StockCorrectionButtonAction, { stockCorrection: row.original }),
  },
];

const dateFilter = ref({
  startDate: props.params.start_date,
  endDate: props.params.end_date,
});

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getStockCorrections(props.stock_corrections.meta.current_page);
};
// function get category data from database
const getStockCorrections = (page: number) => {
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

  router.get(route("backoffice.service.index"), url.value, {
    only: ["stock_corrections", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const onSaved = (value: boolean) => {
  // alert(value);
  getStockCorrections(props.stock_corrections.meta.current_page);
};

const deleteAll = () => {
  router.post(
    route("backoffice.service.delete-all"),
    {
      ids: selectedId.value,
    },
    {
      onStart: () => {
        openDeleteConfirm.value = false;
        isLoading.value = true;
      },
      onFinish: () => {
        isLoading.value = false;
        selectedId.value = [];
        stockCorrectionTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  stockCorrectionTable.value?.resetTable();
};

const resetDateFilter = () => {
  dateFilter.value.startDate = "";
  dateFilter.value.endDate = "";
  getStockCorrections(1);
};
</script>
<template>
  <Head title="Stok Opname" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <ClipboardPenLine class="size-10" />
        <h1 class="text-lg font-semibold">Stok Opname</h1>
      </div>

      <div class="px-4">
        <div class="flex items-center gap-1" v-if="selectedId.length > 0">
          <Button
            class="space-x-2"
            type="button"
            variant="outline"
            :disabled="isLoading"
            @click="cancelDeleteAll"
          >
            <span>Batalkan</span>
          </Button>
          <Button
            class="-tracking-wider space-x-2"
            type="button"
            variant="destructive"
            :disabled="isLoading"
            @click="openDeleteConfirm = true"
          >
            <svg
              class="size-4 animate-spin"
              viewBox="0 0 100 100"
              v-if="isLoading"
            >
              <circle
                fill="none"
                stroke-width="12"
                class="stroke-white"
                cx="50"
                cy="50"
                r="40"
              />
              <circle
                fill="none"
                stroke-width="12"
                class="stroke-blue-500"
                stroke-dasharray="250"
                stroke-dashoffset="210"
                cx="50"
                cy="50"
                r="40"
              />
            </svg>
            <Trash2 class="size-4" v-else />
            <span v-if="isLoading">Menghapus data</span>
            <span v-else>Hapus data</span>
          </Button>
        </div>
        <LinkButton
          :to="route('backoffice.stock-correction.create')"
          v-else
          class="space-x-2"
        >
          <Plus class="w-4 h-4" />
          <span>Buat Koreksi stok</span>
        </LinkButton>
      </div>
    </div>
    <HeaderInformation>
      Halaman ini dipergunakan untuk mengkoreksi stok barang yang terdapat dalam
      sistem dengan kenyataan yang terdapat pada setiap rak. Untuk menambah
      transaksi silahkan mengkilk tombol
      <strong>Buat Koreksi Stok</strong>
    </HeaderInformation>
    <div>
      <DataTable
        ref="stockCorrectionTable"
        :columns="columns"
        :data="stock_corrections.data"
        :pagination="stock_corrections.meta"
        :loading="isLoading"
        @change-limit="changeLimit"
        @change-page="getStockCorrections"
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
              <Button
                variant="default"
                @click="getStockCorrections(1)"
                type="button"
              >
                Cari Data
              </Button>
            </div>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
