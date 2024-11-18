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
  History,
  Search,
} from "lucide-vue-next";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import type { ColumnDef } from "@tanstack/vue-table";
import type { IPaginationMeta, IService } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import { usePrice } from "@/Plugin/useNumber";
import ServiceDetailButton from "@/Components/History/ServiceDetailButton.vue";

const props = defineProps<{
  services: { data: IService[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const price = usePrice();
const perPage = ref(props.services.meta.per_page);
const search = ref(props.params?.search);
const isLoading = ref<boolean>(false);
const serviceTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IService>[] = [
  {
    accessorKey: "service_code",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h("div", { class: "px-2 font-semibold" }, "Kode Service"),
    cell: ({ row }) => h(ServiceDetailButton, { service: row.original }),
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
            "Tanggal Service",
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

  router.get(route("backoffice.history.service"), url.value, {
    only: ["services", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const resetFilter = () => {
  search.value = "";
  getServices(1);
};
</script>
<template>
  <Head title="History Service" />
  <div>
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <History class="size-10" />
        <h1 class="text-lg font-semibold">History Service Kendaraan</h1>
      </div>
    </div>
    <HeaderInformation>
      Halaman ini dipergunakan untuk melihat history service yang masuk dalam
      bengkel.
    </HeaderInformation>

    <div class="bg-gray-100 mt-5">
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
            <div class="relative items-center w-[400px]">
              <Input
                id="search"
                type="text"
                placeholder="Cari no plat / nama pelanggan..."
                class="pl-7 bg-white"
                v-model="search"
              />
              <span
                class="absolute start-0 inset-y-0 flex items-center justify-center px-2"
              >
                <Search class="size-4 text-muted-foreground" />
              </span>
            </div>
            <div class="space-x-1">
              <Button variant="outline" @click="resetFilter">Reset</Button>
              <Button variant="default" @click="getServices(1)"> Cari </Button>
            </div>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
