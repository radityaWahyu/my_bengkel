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
  Wrench,
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
import type { IPaginationMeta, IRepair } from "@/types/response";
import { usePrice } from "@/Plugin/useNumber";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import RepairForm from "@/Components/Repair/RepairForm.vue";
import RepairButtonAction from "@/Components/Repair/RepairButtonAction.vue";
import { useHttpService } from "@/Services/useHttpServices";
import ConfirmDialog from "@/Components/App/ConfirmDialog.vue";

const price = usePrice();

const props = defineProps<{
  repairs: { data: IRepair[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const formState = reactive({
  open: false,
  title: "Tambah Perbaikan",
  edit: false,
});

const httpService = useHttpService();
const repair = ref<IRepair>();
const openDeleteConfirm = ref<boolean>(false);
const search = ref(props.params?.search);
const perPage = ref(props.repairs.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const repairTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IRepair>[] = [
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

            getrepairs(props.repairs.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h("div", { class: "gap-2 flex items-center font-semibold" }, [
            props.params?.sortType == "desc" && props.params?.sortName == "name"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Nama Perbaikan",
          ]),
        ]
      );
    },
    cell: ({ row }) => h("div", { class: "capitalize" }, row.getValue("name")),
  },
  {
    accessorKey: "price",
    enableResizing: false,
    size: 100,
    header: ({ column }) => {
      return h(
        Button,
        {
          variant: "ghost",
          onClick: () => {
            props.params.sortName = "price";
            if (props.params.sortType == "asc") {
              props.params.sortType = "desc";
            } else {
              props.params.sortType = "asc";
            }

            getrepairs(props.repairs.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h("div", { class: "gap-2 flex items-center font-semibold" }, [
            props.params?.sortType == "desc" &&
            props.params?.sortName == "price"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Harga",
          ]),
        ]
      );
    },
    cell: ({ row }) =>
      h(
        "div",
        { class: "capitalize" },
        price.convertToRupiah(row.getValue("price"))
      ),
  },
  {
    id: "actions",
    enableHiding: false,
    cell: ({ row }) => {
      const id: string = row.original.id as string;
      return h(RepairButtonAction, {
        id: row.original.id,
        onDeleted: () => getrepairs(props.repairs.meta.current_page),
        onUpdated,
      });
    },
  },
];

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getrepairs(props.repairs.meta.current_page);
};
// function get category data from database
const getrepairs = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (search.value !== null) Object.assign(url.value, { search });

  router.get(route("backoffice.repair.index"), url.value, {
    only: ["repairs", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};
const addrepair = () => {
  formState.open = true;
  formState.edit = false;
  formState.title = "Tambah Perbaikan";
};
const onSaved = (value: boolean) => {
  // alert(value);
  getrepairs(props.repairs.meta.current_page);
};
const onUpdated = async (id: string) => {
  const response = await httpService.get(route("backoffice.repair.edit", id));

  if (response) {
    repair.value = response.data;
    formState.title = "Edit Perbaikan";
    formState.edit = true;
    formState.open = true;
  }
};

const deleteAll = () => {
  router.post(
    route("backoffice.repair.delete-all"),
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
        repairTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  repairTable.value?.resetTable();
};

watchDebounced(
  search,
  () => {
    getrepairs(props.repairs.meta.current_page);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Data Perbaikan" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <Wrench class="size-10" />
        <h1 class="text-lg font-semibold tracking-wider">Jenis Perbaikan</h1>
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
          @click="addrepair"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Jenis Perbaikan</span>
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
        ref="repairTable"
        :columns="columns"
        :data="repairs.data"
        :pagination="repairs.meta"
        :loading="isLoading || httpService.processing.value"
        @change-limit="changeLimit"
        @change-page="getrepairs"
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
    <RepairForm
      v-model="formState.open"
      :title="formState.title"
      @saved="onSaved"
      :repair="repair"
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
