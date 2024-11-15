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
  Package,
  ArrowUpDown,
  ArrowDownUp,
  Search,
  X,
  Trash2,
} from "lucide-vue-next";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import { Checkbox } from "@/shadcn/ui/checkbox";
import type { ColumnDef } from "@tanstack/vue-table";
import type { IPaginationMeta, IProduct } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import ProductButtonAction from "@/Components/Product/ProductButtonAction.vue";
import { useHttpService } from "@/Services/useHttpServices";
import { usePrice } from "@/Plugin/useNumber";
import ConfirmDialog from "@/Components/App/ConfirmDialog.vue";
import LinkButton from "@/Components/App/LinkButton.vue";

const props = defineProps<{
  products: { data: IProduct[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const httpService = useHttpService();
const price = usePrice();
const openDeleteConfirm = ref<boolean>(false);
const search = ref(props.params?.search);
const perPage = ref(props.products.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const productTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IProduct>[] = [
  {
    id: "select",
    size: 50,
    header: ({ table }) =>
      h(
        "div",
        {
          class: "text-center flex items-center justify-center h-full",
        },
        h(Checkbox, {
          checked:
            table.getIsAllPageRowsSelected() ||
            (table.getIsSomePageRowsSelected() && "indeterminate"),
          "onUpdate:checked": (value: any) => {
            console.log(value);
            if (!value) {
              selectedId.value = [];
              table.resetRowSelection();
            } else {
              const row = table.getRowModel();
              row.rows.forEach((rowData) => {
                selectedId.value.push(rowData.original.id);
              });
            }
            table.toggleAllPageRowsSelected(!!value);
          },
          ariaLabel: "Select all",
        })
      ),
    cell: ({ row }) =>
      h(
        "div",
        { class: "text-center flex items-center justify-center h-full" },
        h(Checkbox, {
          id: "check",
          checked: row.getIsSelected(),
          "onUpdate:checked": (value: any) => {
            if (value) {
              selectedId.value.push(row.original.id);
            } else {
              selectedId.value = selectedId.value.filter(
                (id) => id !== row.original.id
              );
            }

            row.toggleSelected(!!value);
          },
          ariaLabel: "Select row",
        })
      ),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: "name",
    enableResizing: false,
    size: 300,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "name";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getProducts(props.products.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h("div", { class: "gap-2 flex items-center font-semibold" }, [
            props.params?.sortType == "desc" && props.params?.sortName == "name"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Nama Barang",
          ]),
        ]
      );
    },
    cell: ({ row }) =>
      h("div", { class: "capitalize font-semibold" }, row.original.name),
  },
  {
    accessorKey: "category",
    enableResizing: false,
    size: 200,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "category";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getProducts(props.products.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h("div", { class: "gap-2 flex items-center font-semibold" }, [
            props.params?.sortType == "desc" &&
            props.params?.sortName == "category"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Kategori",
          ]),
        ]
      );
    },
    cell: ({ row }) => h("div", { class: "capitalize" }, row.original.category),
  },
  {
    accessorKey: "rack",
    enableResizing: false,
    size: 150,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "rack";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getProducts(props.products.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h(
            "div",
            { class: "gap-2 flex items-center text-left font-semibold" },
            [
              props.params?.sortType == "desc" &&
              props.params?.sortName == "rack"
                ? h(ArrowUpDown, { class: "h-4 w-4" })
                : h(ArrowDownUp, { class: "h-4 w-4" }),
              "Rak",
            ]
          ),
        ]
      );
    },
    cell: ({ row }) => h("div", { class: "capitalize" }, row.original.rack),
  },
  {
    accessorKey: "stock",
    enableResizing: false,
    size: 100,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "stock";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getProducts(props.products.meta.current_page);
          },
          class: "w-full flex items-center text-center px-0",
        },
        () => [
          h("div", { class: "gap-2 flex text-center font-semibold" }, [
            props.params?.sortType == "desc" &&
            props.params?.sortName == "stock"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Stok",
          ]),
        ]
      );
    },
    cell: ({ row }) =>
      h(
        "div",
        { class: "text-center" },
        row.original.stock + " " + row.original.unit
      ),
  },
  {
    accessorKey: "buy_price",
    enableResizing: false,
    size: 200,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "buy_price";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getProducts(props.products.meta.current_page);
          },
          class: "w-full px-0",
        },
        h(
          "div",
          {
            class:
              "gap-2 w-full flex items-center justify-end font-semibold text-right",
          },
          [
            props.params?.sortType == "desc" &&
            props.params?.sortName == "buy_price"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Harga Beli",
          ]
        )
      );
    },
    cell: ({ row }) =>
      h(
        "div",
        { class: "text-right" },
        price.convertToRupiah(row.original.buy_price)
      ),
  },
  {
    accessorKey: "sale_price",
    enableResizing: false,
    size: 200,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "sale_price";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getProducts(props.products.meta.current_page);
          },
          class: "w-full px-0",
        },
        h(
          "div",
          {
            class:
              "gap-2 w-full flex items-center justify-end font-semibold text-right",
          },
          [
            props.params?.sortType == "desc" &&
            props.params?.sortName == "sale_price"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Harga Jual",
          ]
        )
      );
    },
    cell: ({ row }) =>
      h(
        "div",
        { class: "text-right" },
        price.convertToRupiah(row.original.sale_price)
      ),
  },
  {
    id: "actions",
    enableHiding: false,
    size: 150,
    cell: ({ row }) =>
      h(ProductButtonAction, {
        id: row.original.id,
        onDeleted: () => getProducts(props.products.meta.current_page),
        onUpdated: () =>
          router.get(
            route("backoffice.product.edit", row.original.id),
            {},
            { replace: true }
          ),
      }),
  },
];

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getProducts(props.products.meta.current_page);
};
// function get category data from database
const getProducts = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (search.value !== null) Object.assign(url.value, { search });

  router.get(route("backoffice.product.index"), url.value, {
    only: ["products", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const onSaved = (value: boolean) => {
  // alert(value);
  getProducts(props.products.meta.current_page);
};

const deleteAll = () => {
  router.post(
    route("backoffice.product.delete-all"),
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
        productTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  productTable.value?.resetTable();
};

watchDebounced(
  search,
  () => {
    getProducts(props.products.meta.current_page);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Data Barang" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <Package class="size-10" />
        <h1 class="text-lg font-semibold tracking-wider">Data Barang</h1>
      </div>

      <div class="px-4">
        <div class="flex items-center gap-1" v-if="selectedId.length > 0">
          <Button
            class="-tracking-wider space-x-2"
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
          :to="route('backoffice.product.create')"
          v-else
          class="-tracking-wider space-x-2"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Barang</span>
        </LinkButton>
      </div>
    </div>
    <HeaderInformation>
      Data barang dipergunakan untuk memanjemen barang yang akan dijual pada
      pelanggan. Silahkan menambahkan data baru dengan mengklik tombol
      <strong>Tambah Barang</strong>
    </HeaderInformation>
    <div>
      <DataTable
        ref="productTable"
        :columns="columns"
        :data="products.data"
        :pagination="products.meta"
        :loading="isLoading"
        @change-limit="changeLimit"
        @change-page="getProducts"
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
