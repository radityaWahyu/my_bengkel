<script setup lang="ts">
import { ref, h, onMounted } from "vue";
import { watchDebounced } from "@vueuse/core";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import { X, Search } from "lucide-vue-next";
import {
  Sheet,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/shadcn/ui/sheet";
import DataTableDialog from "@/Components/App/DataTableDialog.vue";
import type { IProduct, IPaginationMeta } from "@/types/response";
import type { ColumnDef } from "@tanstack/vue-table";
import ProductNameBox from "./ProductNameBox.vue";
import { useHttpService } from "@/Services/useHttpServices";

const formOpen = defineModel<boolean>();

const emits = defineEmits<{
  (e: "selected", value: IProduct): void;
  (e: "closed", value: boolean): void;
}>();

const httpService = useHttpService();
const products = ref<IProduct[]>();
const pagination = ref<IPaginationMeta>();
const search = ref("");
const perPage = ref(10);
const isLoading = ref<boolean>(false);
const brandTable = ref<InstanceType<typeof DataTableDialog> | null>(null);

const columns: ColumnDef<IProduct>[] = [
  {
    accessorKey: "product",
    enableResizing: false,
    header: ({ column }) =>
      h(
        "div",
        { class: "gap-2 flex items-center font-semibold" },
        "Detail Kendaraan"
      ),
    cell: ({ row }) =>
      h(ProductNameBox, {
        product: row.original,
        onSelected: (product: IProduct) => {
          search.value = "";
          emits("selected", product);
        },
      }),
  },
];

const getproducts = async (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (search.value !== null) Object.assign(url.value, { search });

  const response = await httpService.get(
    route("backoffice.product.list", url.value)
  );
  products.value = response.data;
  pagination.value = response.meta;
};
const onClose = () => {
  search.value = "";
  emits("closed", true);
};

onMounted(() => {
  getproducts(1);
});

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getproducts(1);
};

watchDebounced(
  search,
  () => {
    getproducts(1);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>

<template>
  <Sheet v-model:open="formOpen">
    <SheetContent @interact-outside="(e) => e.preventDefault()">
      <SheetHeader>
        <SheetTitle>Pilih Kendaraan Pelanggan</SheetTitle>
      </SheetHeader>
      <div>
        <DataTableDialog
          class="py-4"
          ref="brandTable"
          :columns="columns"
          :data="products"
          :pagination="pagination"
          :loading="isLoading || httpService.processing.value"
          @change-limit="changeLimit"
          @change-page="getproducts"
        >
          <template #filter>
            <div class="relative w-full items-center">
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
              </span>
            </div>
          </template>
        </DataTableDialog>
      </div>
      <SheetFooter>
        <Button
          type="button"
          variant="default"
          size="lg"
          @click="onClose"
          class="w-full"
        >
          Tutup
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
