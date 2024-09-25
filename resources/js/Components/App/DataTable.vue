<script setup lang="ts">
import { ref, watch } from "vue";
import type { ColumnDef, ExpandedState } from "@tanstack/vue-table";
import { valueUpdater } from "@/shadcn/utils";
import { Skeleton } from "@/shadcn/ui/skeleton";
import { Label } from "@/shadcn/ui/label";
import { defineEmits, useSlots, defineExpose } from "vue";
import { OctagonAlert } from "lucide-vue-next";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  useVueTable,
  getExpandedRowModel,
} from "@tanstack/vue-table";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { Button } from "@/shadcn/ui/button";
import type { IPaginationMeta } from "@/types/response";

const props = defineProps<{
  columns: ColumnDef<any>[];
  data: any[];
  pagination: IPaginationMeta;
  loading: boolean;
  expanded?: boolean;
}>();

const slots = useSlots();
const emits = defineEmits<{
  (e: "changePage", page: number): void;
  (e: "changeLimit", limit: number): void;
}>();

watch(
  () => props.pagination.current_page,
  (values) => {
    pageFilter.value = values;
  }
);

const listPerPage = ref([
  { value: 10, label: "10" },
  { value: 25, label: "25" },
  { value: 50, label: "50" },
  { value: 100, label: "100" },
]);

const perPage = ref<number>(props.pagination.per_page);
const pageFilter = ref<number>(props.pagination.current_page);
const onChange = () => {
  emits("changePage", pageFilter.value);
};
const changePage = (page: number) => emits("changePage", page);
const expanded = ref<ExpandedState>({});

const table = useVueTable({
  defaultColumn: {
    size: 200, //starting column size
    minSize: 10, //enforced during column resizing
    maxSize: 500, //enforced during column resizing
  },
  get data() {
    return props.data;
  },
  get columns() {
    return props.columns;
  },
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onExpandedChange: (updaterOrValue) => {
    valueUpdater(updaterOrValue, expanded);
  },
  manualPagination: true,
  state: {
    get expanded() {
      return expanded.value;
    },
  },
});

const resetTable = () => {
  table.resetRowSelection();
};

defineExpose({
  resetTable,
});
</script>

<template>
  <div class="">
    <div class="px-4 py-2 flex items-center justify-between gap-3">
      <div class="grow w-full">
        <slot name="filter" />
      </div>
      <div v-if="pagination" class="grid grid-cols-2 gap-2 items-center w-44">
        <Label class="text-right">Filter Data</Label>
        <select
          v-model="perPage"
          class="grow bg-white border border-gray-200 text-gray-900 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
          @change="() => emits('changeLimit', perPage)"
        >
          <option
            :value="list.value"
            v-for="(list, index) in listPerPage"
            :key="index"
          >
            {{ list.label }}
          </option>
        </select>
      </div>
    </div>
    <div class="border border-l-0 border-r-0 bg-white max-w-full">
      <Table class="table-fixed scrollbar">
        <TableHeader class="bg-gray-100 shadow">
          <TableRow
            v-for="headerGroup in table.getHeaderGroups()"
            :key="headerGroup.id"
          >
            <TableHead
              v-for="header in headerGroup.headers"
              :key="header.id"
              :style="{ width: header.getSize() + 'px' }"
            >
              <FlexRender
                v-if="!header.isPlaceholder"
                :render="header.column.columnDef.header"
                :props="header.getContext()"
              />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody v-if="loading">
          <template v-if="table.getRowModel().rows?.length">
            <TableRow v-for="index in 6" :key="index">
              <TableCell
                v-for="index in columns.length"
                class="h-10"
                :key="index"
              >
                <Skeleton class="h-2 w-1/2" />
              </TableCell>
            </TableRow>
          </template>
          <template v-else>
            <TableRow v-for="index in 6" :key="index">
              <TableCell
                v-for="index in columns.length"
                class="h-10"
                :key="index"
              >
                <Skeleton class="h-2 w-1/2" />
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
        <TableBody v-else>
          <template v-if="table.getRowModel().rows?.length">
            <template v-for="row in table.getRowModel().rows" :key="row.id">
              <TableRow :data-state="row.getIsSelected() && 'selected'">
                <TableCell
                  v-for="cell in row.getVisibleCells()"
                  :key="cell.id"
                  :style="{ width: cell.column.getSize() + 'px' }"
                >
                  <FlexRender
                    :render="cell.column.columnDef.cell"
                    :props="cell.getContext()"
                  />
                </TableCell>
              </TableRow>
              <TableRow v-if="row.getIsExpanded()">
                <TableCell :colspan="row.getAllCells().length">
                  <slot name="expanded" :row="row" />
                </TableCell>
              </TableRow>
            </template>
          </template>
          <template v-else>
            <TableRow>
              <TableCell :colspan="columns.length" class="h-20 text-center">
                <div v-if="slots.empty">
                  <slot name="empty" />
                </div>
                <div v-else class="text-left">
                  <Alert class="bg-orange-50 border-none rounded">
                    <OctagonAlert class="size-6" />
                    <AlertTitle class="ml-2">Keterangan</AlertTitle>
                    <AlertDescription class="ml-2">
                      Tidak terdapat data pada halaman ini silahkan menambahkan
                      terlebih dahulu
                    </AlertDescription>
                  </Alert>
                </div>
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
    <div
      class="flex items-center justify-end space-x-2 py-2 px-4"
      v-if="pagination"
    >
      <div class="flex-1 text-xs text-muted-foreground">
        <span v-if="loading">
          <Skeleton class="h-2 w-1/4" />
        </span>
        <span v-else>
          Page {{ pagination?.current_page }} of
          {{ pagination?.last_page }} From {{ pagination?.total }} Data.
        </span>
      </div>
      <div class="flex items-center gap-2">
        <div
          class="grid grid-cols-2 gap-2 items-center"
          v-if="pagination.total > 0"
        >
          <Label class="text-right text-xs">Page</Label>
          <select
            v-model="pageFilter"
            class="bg-white text-center border border-gray-200 text-gray-900 text-xs rounded focus:ring-blue-500 focus:border-blue-500 p-1.5 shadow-sm w-14"
            @change="onChange"
          >
            <option
              :value="page"
              v-for="(page, index) in pagination.last_page"
              :key="index"
            >
              {{ page }}
            </option>
          </select>
        </div>
        <Button
          variant="outline"
          size="sm"
          :disabled="pagination?.current_page == 1 || loading"
          @click="changePage(pagination?.current_page - 1)"
        >
          <span>Previous</span>
        </Button>
        <Button
          variant="outline"
          size="sm"
          :disabled="
            pagination?.current_page == pagination?.last_page || loading
          "
          @click="changePage(pagination?.current_page + 1)"
        >
          <span>Next</span>
        </Button>
      </div>
    </div>
  </div>
</template>
