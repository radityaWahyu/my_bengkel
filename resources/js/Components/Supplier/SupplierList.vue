<script setup lang="ts">
import { ref, h, onMounted, watch } from "vue";
import { watchDebounced } from "@vueuse/core";
import { Button } from "@/shadcn/ui/button";
import { Label } from "@/shadcn/ui/label";
import { Input } from "@/shadcn/ui/input";
import { Skeleton } from "@/shadcn/ui/skeleton";
import { BadgePlus, Search, BadgeInfo } from "lucide-vue-next";
import { Alert, AlertTitle, AlertDescription } from "@/shadcn/ui/alert";
import { Sheet, SheetContent, SheetHeader, SheetTitle } from "@/shadcn/ui/sheet";

import type { ISupplier, IPaginationMeta } from "@/types/response";
import { usePrice } from "@/Plugin/useNumber";
import { useHttpService } from "@/Services/useHttpServices";

const formOpen = ref<boolean>(true);

const emits = defineEmits<{
  (e: "selected", value: ISupplier): void;
  (e: "closed", value: boolean): void;
}>();

const price = usePrice();
const httpService = useHttpService();
const suppliers = ref<ISupplier[]>();
const pagination = ref<IPaginationMeta>();
const search = ref("");
const perPage = ref(10);
const isLoading = ref<boolean>(false);

const getsuppliers = async (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (search.value !== null) Object.assign(url.value, { search });

  isLoading.value = true;
  const response = await httpService.get(route("backoffice.supplier.list", url.value));
  suppliers.value = response.data;
  pagination.value = response.meta;
  isLoading.value = false;
};
const onClose = () => {
  formOpen.value = false;
  search.value = "";
  emits("closed", true);
};

const selectsupplier = (supplier: ISupplier) => {
  formOpen.value = false;
  emits("selected", supplier);
};

onMounted(() => {
  getsuppliers(1);
});

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getsuppliers(1);
};

watch(
  () => formOpen.value,
  (value) => {
    if (value) {
      getsuppliers(1);
    }
  },
  { immediate: true }
);

watchDebounced(
  search,
  () => {
    getsuppliers(1);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>

<template>
  <Sheet :open="formOpen">
    <SheetContent @interact-outside="(e) => e.preventDefault()" class="px-3">
      <SheetHeader>
        <SheetTitle>Pilih Data Pemasok</SheetTitle>
      </SheetHeader>
      <div class="divide-y divide-gray-200">
        <div class="py-2 space-y-2">
          <div class="relative w-full items-center">
            <Input
              id="search"
              type="text"
              placeholder="Cari pemasok..."
              class="pl-10 bg-white rounded-xl"
              v-model="search"
            />
            <span
              class="absolute start-0 inset-y-0 flex items-center justify-center px-2"
            >
              <Search class="size-4 text-muted-foreground" />
            </span>
          </div>
        </div>
        <div class="py-2 h-[67vh] overflow-y-scroll space-y-2">
          <template v-if="suppliers?.length === 0 && !isLoading">
            <Alert class="bg-orange-50 border-none rounded w-full">
              <BadgeInfo class="size-6" />
              <AlertTitle class="ml-2">Keterangan</AlertTitle>
              <AlertDescription class="ml-2">
                Data pemasok tidak ditemukan di dalam sistem
              </AlertDescription>
            </Alert>
          </template>
          <template v-if="isLoading">
            <div
              class="border border-gray-200 bg-gray-50 px-3 py-2 cursor-pointer space-y-3"
              v-for="index in 3"
              :key="index"
            >
              <Skeleton class="h-4 w-3/5" />
              <Skeleton class="h-2 w-1/2" />
              <Skeleton class="h-2 w-1/2" />
            </div>
          </template>
          <template v-if="suppliers && !isLoading">
            <div
              class="flex items-center justify-between border border-gray-200 bg-gray-50 px-3 py-1 cursor-pointer rounded-xl hover:bg-sky-50"
              v-for="(supplier, index) in suppliers"
              :key="index"
              @click="selectsupplier(supplier)"
            >
              <div class="flex items-center justify-between w-full">
                <div>
                  <h4 class="capitalize font-medium">
                    <span> {{ supplier.name }} </span>
                  </h4>
                  <p class="text-sm text-muted-foreground font-medium space-x-2">
                    Kontak : {{ supplier.contact_name }} -
                    {{ supplier.whatsapp }}
                  </p>
                </div>
                <div class="flex items-center gap-4">
                  <div>
                    <BadgePlus class="size-5 text-primary" />
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
        <div class="flex items-center justify-between py-1" v-if="pagination">
          <!-- <div class="text-xs text-muted-foreground">
            Page {{ pagination.current_page }} of
            {{ pagination.last_page }} From {{ pagination.total }} Data.
          </div> -->

          <div class="grid grid-cols-2 gap-2 items-center">
            <Label class="text-right text-xs">Page</Label>
            <select
              class="bg-white text-center border border-gray-200 text-gray-900 text-xs rounded focus:ring-blue-500 focus:border-blue-500 p-1.5 shadow-sm w-14"
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
          <div class="space-x-2">
            <Button
              variant="outline"
              size="sm"
              :disabled="pagination.current_page == 1 || isLoading"
              @click="getsuppliers(pagination.current_page - 1)"
            >
              <span>Previous</span>
            </Button>
            <Button
              variant="outline"
              size="sm"
              :disabled="pagination.current_page == pagination.last_page || isLoading"
              @click="getsuppliers(pagination.current_page + 1)"
            >
              <span>Next</span>
            </Button>
          </div>
        </div>
        <div class="w-full py-2">
          <Button
            type="button"
            variant="default"
            size="lg"
            @click="onClose"
            class="w-full rounded-xl"
          >
            Tutup
          </Button>
        </div>
      </div>
    </SheetContent>
  </Sheet>
</template>
