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
  Blocks,
  ArrowUpDown,
  ArrowDownUp,
  Search,
  X,
} from "lucide-vue-next";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import { Checkbox } from "@/shadcn/ui/checkbox";
import type { ColumnDef } from "@tanstack/vue-table";
import type { IPaginationMeta, ICategory } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import CategoryForm from "@/Components/Category/CategoryForm.vue";
import CategoryButtonAction from "@/Components/Category/CategoryButtonAction.vue";
import { useHttpService } from "@/Services/useHttpServices";

const props = defineProps<{
  categories: { data: ICategory[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const formState = reactive({
  open: false,
  title: "Tambah Kategori",
  edit: false,
});

const httpService = useHttpService();
const category = ref<ICategory>();
const search = ref(props.params?.search);
const perPage = ref(props.categories.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const categoryTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<ICategory>[] = [
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

            getCategories(props.categories.meta.current_page);
          },
          class: "w-full flex justify-between text-left px-0",
        },
        () => [
          h("div", { class: "gap-2 flex items-center font-semibold" }, [
            props.params?.sortType == "desc" && props.params?.sortName == "name"
              ? h(ArrowUpDown, { class: "h-4 w-4" })
              : h(ArrowDownUp, { class: "h-4 w-4" }),
            "Kategori",
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
      return h(CategoryButtonAction, {
        id: row.original.id,
        onDeleted: () => getCategories(props.categories.meta.current_page),
        onUpdated,
      });
    },
  },
];

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getCategories(props.categories.meta.current_page);
};
// function get category data from database
const getCategories = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (search.value !== null) Object.assign(url.value, { search });

  router.get(route("backoffice.category.index"), url.value, {
    only: ["categories", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};
const addCategory = () => {
  formState.open = true;
  formState.edit = false;
  formState.title = "Tambah Kategori";
};
const onSaved = (value: boolean) => {
  // alert(value);
  getCategories(props.categories.meta.current_page);
};
const onUpdated = async (id: string) => {
  const response = await httpService.get(route("backoffice.category.edit", id));

  if (response) {
    category.value = response.data;
    formState.title = "Edit Kategori";
    formState.edit = true;
    formState.open = true;
  }
};

watchDebounced(
  search,
  () => {
    getCategories(props.categories.meta.current_page);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Data Kategori" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <Blocks class="w-10 h-10" />
        <h1 class="text-lg font-semibold tracking-wider">Data Kategori</h1>
      </div>

      <div class="px-4">
        <Button
          class="-tracking-wider space-x-2"
          type="button"
          @click="addCategory"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah Kategori</span>
        </Button>
      </div>
    </div>
    <HeaderInformation>
      Data kategori dipergunakan untuk memanjemen kategori barang yang akan
      dijual pada pelanggan. Silahkan menambahkan data baru dengan mengklik
      tombol
      <strong>Tambah Kategori</strong>
    </HeaderInformation>
    <div>
      <DataTable
        ref="categoryTable"
        :columns="columns"
        :data="categories.data"
        :pagination="categories.meta"
        :loading="isLoading || httpService.processing.value"
        @change-limit="changeLimit"
        @change-page="getCategories"
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
    <CategoryForm
      v-model="formState.open"
      :title="formState.title"
      @saved="onSaved"
      :category="category"
      :edit="formState.edit"
    />
  </div>
</template>
