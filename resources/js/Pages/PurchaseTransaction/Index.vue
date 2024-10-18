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
  ShoppingBasket,
  ArrowUpDown,
  ArrowDownUp,
  Search,
  X,
  Trash2,
} from "lucide-vue-next";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import type { ColumnDef } from "@tanstack/vue-table";
import type { IPaginationMeta, IPurchase } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import { usePrice } from "@/Plugin/useNumber";
import ConfirmDialog from "@/Components/App/ConfirmDialog.vue";
import LinkButton from "@/Components/App/LinkButton.vue";
import PurchaseTransactionStatusBox from "@/Components/PurchaseTransaction/PurchaseTransactionStatusBox.vue";
import PurchaseTransactionButtonAction from "@/Components/PurchaseTransaction/PurchaseTransactionButtonAction.vue";
import PurchaseTransactionSupplierBox from "@/Components/PurchaseTransaction/PurchaseTransactionSupplierBox.vue";

const props = defineProps<{
  purchases: { data: IPurchase[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const price = usePrice();
const openDeleteConfirm = ref<boolean>(false);
const search = ref(props.params?.search);
const perPage = ref(props.purchases.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const purchaseTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IPurchase>[] = [
  {
    accessorKey: "purchase_code",
    enableResizing: false,
    size: 250,
    header: ({ column }) =>
      h(
        "div",
        { class: "gap-2 flex items-center font-semibold" },
        "Kode Pembelian"
      ),
    cell: ({ row }) =>
      h(
        "div",
        {
          class: [
            "capitalize font-semibold px-2 py-1 inline-flex w-full",
            {
              "bg-yellow-100 text-yellow-800": row.original.status === "create",
            },
          ],
        },
        row.original.purchase_code === null
          ? "Proses Transaksi..."
          : row.original.purchase_code
      ),
  },
  {
    accessorKey: "supplier",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h("div", { class: "gap-2 flex items-center font-semibold" }, "Pemasok"),
    cell: ({ row }) =>
      h(PurchaseTransactionSupplierBox, {
        purchase: row.original,
      }),
  },
  {
    accessorKey: "transaction_date",
    enableResizing: false,
    size: 250,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "transaction_date";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getPurchases(props.purchases.meta.current_page);
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
      h("div", { class: "text-center" }, row.original.transaction_date),
  },
  {
    accessorKey: "product_count",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "gap-2 text-center font-semibold" }, "Jumlah Barang"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize font-semibold text-center" },
        row.original.product_count
      ),
  },
  {
    accessorKey: "status",
    enableResizing: false,
    size: 180,
    header: ({ column }) =>
      h("div", { class: "gap-2 text-center font-semibold" }, "Status"),
    cell: ({ row }) =>
      h(PurchaseTransactionStatusBox, { purchase: row.original }),
  },
  {
    accessorKey: "employee",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "gap-2 text-center font-semibold" }, "Pegawai"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize font-semibold text-center" },
        row.original.employee
      ),
  },
  {
    accessorKey: "total",
    enableResizing: false,
    size: 150,
    header: ({ column }) =>
      h("div", { class: "gap-2 text-right font-semibold" }, "Total"),
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize font-semibold text-right" },
        price.convertToRupiah(row.original.total + row.original.extra_pay)
      ),
  },
  {
    id: "actions",
    enableHiding: false,
    size: 350,
    cell: ({ row }) =>
      h(PurchaseTransactionButtonAction, {
        purchase: row.original,
        onDeleted: () => getPurchases(props.purchases.meta.current_page),
      }),
  },
];

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getPurchases(props.purchases.meta.current_page);
};
// function get category data from database
const getPurchases = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (search.value !== null) Object.assign(url.value, { search });

  router.get(route("backoffice.service.index"), url.value, {
    only: ["purchases", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const onSaved = (value: boolean) => {
  // alert(value);
  getPurchases(props.purchases.meta.current_page);
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
        purchaseTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  purchaseTable.value?.resetTable();
};

watchDebounced(
  search,
  () => {
    getPurchases(props.purchases.meta.current_page);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Transaksi Pembelian" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <ShoppingBasket class="size-10" />
        <h1 class="text-lg font-semibold">Transaksi Pembelian</h1>
      </div>
      <div class="px-4">
        <LinkButton :to="route('backoffice.purchase.create')" class="space-x-2">
          <Plus class="w-4 h-4" />
          <span>Tambah Transaksi</span>
        </LinkButton>
      </div>
    </div>
    <HeaderInformation>
      Halaman ini dipergunakan untuk memanajemen transaksi penjualan yang masuk.
      Untuk menambah transaksi silahkan mengklik tombol
      <strong>Buat Transaksi</strong>
    </HeaderInformation>
    <div>
      <DataTable
        ref="purchaseTable"
        :columns="columns"
        :data="purchases.data"
        :pagination="purchases.meta"
        :loading="isLoading"
        @change-limit="changeLimit"
        @change-page="getPurchases"
      >
        <template #filter>
          <div class="relative w-1/2 items-center">
            <Input
              v-model="search"
              type="text"
              placeholder="Cari data..."
              class="pl-10 w-full bg-white"
            />
            <span
              class="absolute inset-y-0 flex items-center justify-center px-2"
            >
              <Search class="size-4 text-muted-foreground" />
            </span>
            <span
              class="absolute right-0 inset-y-0 flex items-center justify-center px-2"
              v-if="search !== null && search.length > 0"
            >
              <X class="size-4 text-muted-foreground" @click="search = ''" />
            </span></div
        ></template>
      </DataTable>
    </div>
    <ConfirmDialog
      v-model="openDeleteConfirm"
      cancel-text="Batalkan"
      ok-text="Hapus data"
      @cancel="openDeleteConfirm = false"
      @ok="deleteAll"
    >
      <template #title>
        <div>Konfirmasi Hapus Data</div>
      </template>
      <template #description>
        <div>
          Apakah anda ingin menghapus {{ selectedId.length }} data ini ?
        </div>
      </template>
    </ConfirmDialog>
  </div>
</template>
