<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, h, defineAsyncComponent, reactive } from "vue";
import { watchDebounced } from "@vueuse/core";
import { router, Head } from "@inertiajs/vue3";
import {
  Plus,
  HandCoins,
  ArrowUpDown,
  ArrowDownUp,
  Trash2,
} from "lucide-vue-next";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import { Label } from "@/shadcn/ui/label";
import type { ColumnDef } from "@tanstack/vue-table";
import type { IPaginationMeta, IJurnal } from "@/types/response";
import { usePrice } from "@/Plugin/useNumber";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import JurnalButtonAction from "@/Components/Jurnal/JurnalButtonAction.vue";

const JurnalForm = defineAsyncComponent(
  () => import("@/Components/Jurnal/JurnalForm.vue")
);

const props = defineProps<{
  jurnals: { data: IJurnal[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
    start_date: string;
    end_date: string;
  };
}>();

const price = usePrice();
const jurnalFormState = reactive({
  open: false,
  title: "Tambah Jurnal",
});
const openDeleteConfirm = ref<boolean>(false);
const perPage = ref(props.jurnals.meta.per_page);
const jurnalId = ref<string>("");
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const jurnalTable = ref<InstanceType<typeof DataTable> | null>(null);

const columns: ColumnDef<IJurnal>[] = [
  {
    accessorKey: "transaction_date",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold" }, "Tanggal Jurnal"),
    cell: ({ row }) =>
      h("div", { class: "px-3 font-medium" }, row.original.transaction_date),
  },
  {
    accessorKey: "jurnal_code",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold" }, "Kode Jurnal"),
    cell: ({ row }) =>
      h("div", { class: "px-3 font-medium" }, row.original.jurnal_code),
  },
  {
    accessorKey: "transaction_code",
    enableResizing: false,
    size: 250,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold text-center" }, "Kode Transaksi"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "px-3 font-medium text-center" },
        row.original.transaction_code
      ),
  },
  {
    accessorKey: "transaction_type",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold text-center" }, "Jenis Transaksi"),
    cell: ({ row }) =>
      h(
        "div",
        {
          class:
            "px-3 font-medium text-center w-full py-1 bg-blue-100 rounded-full",
        },
        row.original.transaction_type
      ),
  },
  {
    accessorKey: "income",
    enableResizing: false,
    size: 100,
    header: ({ column }) =>
      h("div", { class: "font-semibold text-right" }, "Pemasukan"),
    cell: ({ row }) =>
      h(
        "div",
        { class: " font-medium text-right w-full" },
        price.convertToRupiah(row.original.income)
      ),
  },
  {
    accessorKey: "expense",
    enableResizing: false,
    size: 100,
    header: ({ column }) =>
      h("div", { class: " font-semibold text-right" }, "Pengeluaran"),
    cell: ({ row }) =>
      h(
        "div",
        { class: " font-medium text-right w-full text-red-600" },
        price.convertToRupiah(row.original.expense)
      ),
  },
  {
    accessorKey: "payment",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold text-center" }, "Jenis Pembayaran"),
    cell: ({ row }) =>
      h("div", { class: "px-3 font-medium text-center" }, row.original.payment),
  },
  {
    accessorKey: "employee",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "px-3 font-semibold" }, "Pegawai"),
    cell: ({ row }) =>
      h("div", { class: "px-3 font-medium" }, row.original.employee),
  },
  {
    id: "actions",
    enableHiding: false,
    size: 150,
    cell: ({ row }) =>
      h(JurnalButtonAction, {
        jurnal: row.original,
        onDeleted: () => getJurnals(1),
        onUpdated: (id) => {
          jurnalId.value = id;
          jurnalFormState.title = "Edit Jurnal";
          jurnalFormState.open = true;
        },
      }),
  },
];

const dateFilter = ref({
  startDate: props.params.start_date,
  endDate: props.params.end_date,
});

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getJurnals(props.jurnals.meta.current_page);
};
// function get category data from database
const getJurnals = (page: number) => {
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

  router.get(route("backoffice.jurnal.index"), url.value, {
    only: ["jurnals", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const onSaved = (value: boolean) => {
  // alert(value);
  getJurnals(props.jurnals.meta.current_page);
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
        jurnalTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  jurnalTable.value?.resetTable();
};

const resetDateFilter = () => {
  dateFilter.value.startDate = "";
  dateFilter.value.endDate = "";
  getJurnals(1);
};

const jurnalFormSaved = () => {
  jurnalFormState.open = false;
  getJurnals(1);
};
</script>
<template>
  <Head title="Jurnal Keuangan" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <HandCoins class="size-10" />
        <h1 class="text-lg font-semibold">Jurnal</h1>
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
        <Button @click="jurnalFormState.open = true" class="space-x-2">
          <Plus class="w-4 h-4" />
          <span>Tambah Data</span>
        </Button>
      </div>
    </div>
    <HeaderInformation>
      Halaman ini dipergunakan untuk melihat laporan jurnal keuangan pengeluaran
      dan pemasukan yang diinputkan ke dalam sistem ini. Apabila ingin menambah
      jurnal baru silahkan klik tombol
      <strong>Tambah Data</strong>
    </HeaderInformation>
    <div>
      <DataTable
        ref="jurnalTable"
        :columns="columns"
        :data="jurnals.data"
        :pagination="jurnals.meta"
        :loading="isLoading"
        @change-limit="changeLimit"
        @change-page="getJurnals"
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
              <Button variant="default" @click="getJurnals(1)" type="button">
                Cari Data
              </Button>
            </div>
          </div>
        </template>
      </DataTable>
    </div>
    <JurnalForm
      v-if="jurnalFormState.open"
      :title="jurnalFormState.title"
      @closed="jurnalFormState.open = false"
      @saved="jurnalFormSaved"
      :id="jurnalId"
    />
  </div>
</template>
