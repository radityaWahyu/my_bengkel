<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, h, reactive } from "vue";
import { watchDebounced } from "@vueuse/core";
import { router, Head } from "@inertiajs/vue3";
import {
  Plus,
  SwatchBook,
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
import type { IPaginationMeta, IRack } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import RackForm from "@/Components/Rack/RackForm.vue";
import RackButtonAction from "@/Components/Rack/RackButtonAction.vue";
import { useHttpService } from "@/Services/useHttpServices";
import ConfirmDialog from "@/Components/App/ConfirmDialog.vue";

const props = defineProps<{
  racks: { data: IRack[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const formState = reactive({
  open: false,
  title: "Tambah Rak",
  edit: false,
});

const httpService = useHttpService();
const rack = ref<IRack>();
const openDeleteConfirm = ref<boolean>(false);
const search = ref(props.params?.search);
const perPage = ref(props.racks.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const rackTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IRack>[] = [
  {
    id: "select",
    size: 20,
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
    size: 100,
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

            getRacks(props.racks.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h("div", { class: "gap-2 flex items-center font-semibold" }, [
            props.params?.sortType == "desc" && props.params?.sortName == "name"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Nama Rak",
          ]),
        ]
      );
    },
    cell: ({ row }) => h("div", { class: "capitalize" }, row.getValue("name")),
  },
  {
    id: "actions",
    enableHiding: false,
    cell: ({ row }) => {
      const id: string = row.original.id as string;
      return h(RackButtonAction, {
        id: row.original.id,
        onDeleted: () => getRacks(props.racks.meta.current_page),
        onUpdated,
      });
    },
  },
];

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getRacks(props.racks.meta.current_page);
};
// function get category data from database
const getRacks = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (search.value !== null) Object.assign(url.value, { search });

  router.get(route("backoffice.rack.index"), url.value, {
    only: ["racks", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};
const addRack = () => {
  formState.open = true;
  formState.edit = false;
  formState.title = "Tambah Rak";
};
const onSaved = (value: boolean) => {
  // alert(value);
  getRacks(props.racks.meta.current_page);
};
const onUpdated = async (id: string) => {
  const response = await httpService.get(route("backoffice.rack.edit", id));

  if (response) {
    rack.value = response.data;
    formState.title = "Edit Rak";
    formState.edit = true;
    formState.open = true;
  }
};

const deleteAll = () => {
  router.post(
    route("backoffice.rack.delete-all"),
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
        rackTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  rackTable.value?.resetTable();
};

watchDebounced(
  search,
  () => {
    getRacks(props.racks.meta.current_page);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Data Rak" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <SwatchBook class="w-10 h-10" />
        <h1 class="text-lg font-semibold tracking-wider">Data Rak</h1>
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
        <Button
          v-else
          class="-tracking-wider space-x-2"
          type="button"
          @click="addRack"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Rak</span>
        </Button>
      </div>
    </div>
    <HeaderInformation>
      Data rak dipergunakan untuk memanjemen penempatan barang berdasarkan rak
      yang akan dijual pada pelanggan. Silahkan menambahkan data baru dengan
      mengklik tombol
      <strong>Tambah Rak</strong>
    </HeaderInformation>
    <div>
      <DataTable
        ref="rackTable"
        :columns="columns"
        :data="racks.data"
        :pagination="racks.meta"
        :loading="isLoading || httpService.processing.value"
        @change-limit="changeLimit"
        @change-page="getRacks"
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
    <RackForm
      v-model="formState.open"
      :title="formState.title"
      @saved="onSaved"
      :rack="rack"
      :edit="formState.edit"
    />
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
