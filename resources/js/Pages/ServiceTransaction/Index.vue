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
  CarFront,
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
import type { IPaginationMeta, IService } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import ServiceTransactionButtonAction from "@/Components/ServiceTransaction/ServiceTransactionButtonAction.vue";
import { usePrice } from "@/Plugin/useNumber";
import ConfirmDialog from "@/Components/App/ConfirmDialog.vue";
import LinkButton from "@/Components/App/LinkButton.vue";
import ServiceTransactionStatusBox from "@/Components/ServiceTransaction/ServiceTransactionStatusBox.vue";

const props = defineProps<{
  services: { data: IService[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const price = usePrice();
const openDeleteConfirm = ref<boolean>(false);
const search = ref(props.params?.search);
const perPage = ref(props.services.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const serviceTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IService>[] = [
  // {
  //   id: "select",
  //   size: 50,
  //   header: ({ table }) =>
  //     h(
  //       "div",
  //       {
  //         class: "text-center flex items-center justify-center h-full",
  //       },
  //       h(Checkbox, {
  //         checked:
  //           table.getIsAllPageRowsSelected() ||
  //           (table.getIsSomePageRowsSelected() && "indeterminate"),
  //         "onUpdate:checked": (value: any) => {
  //           console.log(value);
  //           if (!value) {
  //             selectedId.value = [];
  //             table.resetRowSelection();
  //           } else {
  //             const row = table.getRowModel();
  //             row.rows.forEach((rowData) => {
  //               selectedId.value.push(rowData.original.id);
  //             });
  //           }
  //           table.toggleAllPageRowsSelected(!!value);
  //         },
  //         ariaLabel: "Select all",
  //       })
  //     ),
  //   cell: ({ row }) =>
  //     h(
  //       "div",
  //       { class: "text-center flex items-center justify-center h-full" },
  //       h(Checkbox, {
  //         id: "check",
  //         checked: row.getIsSelected(),
  //         "onUpdate:checked": (value: any) => {
  //           if (value) {
  //             selectedId.value.push(row.original.id);
  //           } else {
  //             selectedId.value = selectedId.value.filter(
  //               (id) => id !== row.original.id
  //             );
  //           }

  //           row.toggleSelected(!!value);
  //         },
  //         ariaLabel: "Select row",
  //       })
  //     ),
  //   enableSorting: false,
  //   enableHiding: false,
  // },
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
    accessorKey: "service_code",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h(
        "div",
        { class: "gap-2 flex items-center font-semibold" },
        "Kode Service"
      ),
    cell: ({ row }) =>
      h(
        "div",
        {
          class: [
            "capitalize font-semibold px-2 py-1 inline-flex",
            {
              "bg-yellow-100 text-yellow-800":
                row.original.status === "waiting",
            },
          ],
        },
        row.original.service_code === null
          ? "Menunggu Pengecekan..."
          : row.original.service_code
      ),
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
    accessorKey: "status",
    enableResizing: false,
    size: 250,
    header: ({ column }) =>
      h("div", { class: "gap-2 flex items-center font-semibold" }, "Status"),
    cell: ({ row }) =>
      h(ServiceTransactionStatusBox, { service: row.original }),
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
        price.convertToRupiah(row.original.total)
      ),
  },
  {
    id: "actions",
    enableHiding: false,
    size: 350,
    cell: ({ row }) =>
      h(ServiceTransactionButtonAction, {
        service: row.original,
        onDeleted: () => getServices(props.services.meta.current_page),
        onUpdated: () =>
          router.get(
            route("backoffice.service.edit", row.original.id),
            {},
            { replace: true }
          ),
      }),
  },
];

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getServices(props.services.meta.current_page);
};
// function get category data from database
const getServices = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (search.value !== null) Object.assign(url.value, { search });

  router.get(route("backoffice.service.index"), url.value, {
    only: ["services", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const onSaved = (value: boolean) => {
  // alert(value);
  getServices(props.services.meta.current_page);
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
        serviceTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  serviceTable.value?.resetTable();
};

watchDebounced(
  search,
  () => {
    getServices(props.services.meta.current_page);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Transaksi Service Kendaraan" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <CarFront class="size-10" />
        <h1 class="text-lg font-semibold">Transaksi Service Kendaraan</h1>
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
          :to="route('backoffice.service.create')"
          v-else
          class="space-x-2"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Transaksi</span>
        </LinkButton>
      </div>
    </div>
    <HeaderInformation>
      Halaman ini dipergunakan untuk memanajemen transaksi service yang masuk
      dalam bengkel. Untuk menambah transaksi silahkan mengkilk tombol
      <strong>Buat Transaksi</strong>
    </HeaderInformation>
    <div>
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
