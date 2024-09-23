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
  Contact,
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
import type { IPaginationMeta, ISupplier } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import SupplierButtonAction from "@/Components/Supplier/SupplierButtonAction.vue";
import SupplierNameBox from "@/Components/Supplier/SupplierNameBox.vue";
import ConfirmDialog from "@/Components/App/ConfirmDialog.vue";
import LinkButton from "@/Components/App/LinkButton.vue";

const props = defineProps<{
  suppliers: { data: ISupplier[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const openDeleteConfirm = ref<boolean>(false);
const search = ref(props.params?.search);
const perPage = ref(props.suppliers.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const supplierTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<ISupplier>[] = [
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
              selectedId.value = selectedId.value.filter((id) => id !== row.original.id);
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

            getSuppliers(props.suppliers.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h("div", { class: "gap-2 flex items-center font-semibold" }, [
            props.params?.sortType == "desc" && props.params?.sortName == "name"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Nama Pemasok",
          ]),
        ]
      );
    },
    cell: ({ row }) => h(SupplierNameBox, { name: row.original.name }),
  },
  {
    accessorKey: "contact_name",
    enableResizing: false,
    size: 200,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "contact_name";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getSuppliers(props.suppliers.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h("div", { class: "gap-2 flex items-center font-semibold" }, [
            props.params?.sortType == "desc" && props.params?.sortName == "contact_name"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Nama Kontak",
          ]),
        ]
      );
    },
    cell: ({ row }) => {
      return h("div", { class: "capitalize" }, row.original.contact_name);
    },
  },
  {
    accessorKey: "phone",
    enableResizing: false,
    size: 150,
    header: ({ column }) => {
      return h(
        "div",
        { class: "gap-2 flex items-center text-left font-semibold" },
        "No Telepon"
      );
    },
    cell: ({ row }) => h("div", {}, row.original.phone),
  },
  {
    accessorKey: "whatsapp",
    enableResizing: false,
    size: 150,
    header: ({ column }) => {
      return h(
        "div",
        { class: "gap-2 flex items-center text-left font-semibold" },
        "No Whatsapp"
      );
    },
    cell: ({ row }) => h("div", {}, row.original.whatsapp),
  },
  {
    id: "actions",
    enableHiding: false,
    size: 150,
    cell: ({ row }) =>
      h(SupplierButtonAction, {
        id: row.original.id,
        onDeleted: () => getSuppliers(props.suppliers.meta.current_page),
        onUpdated: () =>
          router.get(
            route("backoffice.supplier.edit", row.original.id),
            {},
            { replace: true }
          ),
      }),
  },
];

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getSuppliers(props.suppliers.meta.current_page);
};
// function get category data from database
const getSuppliers = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (search.value !== null) Object.assign(url.value, { search });

  router.get(route("backoffice.supplier.index"), url.value, {
    only: ["suppliers", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const onSaved = (value: boolean) => {
  // alert(value);
  getSuppliers(props.suppliers.meta.current_page);
};

const deleteAll = () => {
  router.post(
    route("backoffice.supplier.delete-all"),
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
        supplierTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  supplierTable.value?.resetTable();
};

watchDebounced(
  search,
  () => {
    getSuppliers(props.suppliers.meta.current_page);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Data Pemasok" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <Contact class="size-10" />
        <h1 class="text-lg font-semibold tracking-wider">Data Pemasok</h1>
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
            <svg class="size-4 animate-spin" viewBox="0 0 100 100" v-if="isLoading">
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
          :to="route('backoffice.supplier.create')"
          v-else
          class="-tracking-wider space-x-2"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Pemasok</span>
        </LinkButton>
      </div>
    </div>
    <HeaderInformation>
      Data Pemasok dipergunakan untuk memanjemen pemasok yang menyuplai sparepart pada
      sistem ini. Silahkan menambahkan data baru dengan mengklik tombol
      <strong>Tambah Pemasok</strong>
    </HeaderInformation>
    <div>
      <DataTable
        ref="supplierTable"
        :columns="columns"
        :data="suppliers.data"
        :pagination="suppliers.meta"
        :loading="isLoading"
        @change-limit="changeLimit"
        @change-page="getSuppliers"
      >
        <template #filter>
          <div class="relative w-1/2 items-center">
            <Input
              v-model="search"
              type="text"
              placeholder="Cari Pemasok..."
              class="pl-10 w-full bg-white"
            />
            <span class="absolute inset-y-0 flex items-center justify-center px-2">
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
        <div>Apakah anda ingin menghapus {{ selectedId.length }} data ini ?</div>
      </template>
    </ConfirmDialog>
  </div>
</template>
