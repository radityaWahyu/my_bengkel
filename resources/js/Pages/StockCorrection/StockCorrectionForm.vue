<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { reactive, ref, onMounted } from "vue";
import { watchDebounced } from "@vueuse/core";
import {
  Head,
  router,
  useForm as useInertiaForm,
  usePage,
} from "@inertiajs/vue3";

import {
  Search,
  Package2,
  BadgePlus,
  BadgeInfo,
  FilePenLine,
} from "lucide-vue-next";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import { Textarea } from "@/shadcn/ui/textarea";
import { Input } from "@/shadcn/ui/input";
import { Label } from "@/shadcn/ui/label";
import { Button } from "@/shadcn/ui/button";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";
import { IPaginationMeta, IProduct } from "@/types/response";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import { Skeleton } from "@/shadcn/ui/skeleton";
import { usePrice } from "@/Plugin/useNumber";
import { useHttpService } from "@/Services/useHttpServices";

const price = usePrice();
const http = useHttpService();
const products = reactive({
  loading: false,
  data: [] as IProduct[],
  meta: <IPaginationMeta>{},
});
const product = ref<IProduct>();

const search = ref("");

const page = usePage();

const stockCorrectionSchema = () => {
  return toTypedSchema(
    zod.object({
      product_name: zod
        .string({ message: "Data barang belum dipilih" })
        .min(1, { message: "Data barang belum dipilih" }),
      old_stock: zod.number({ message: "Stok awal harus diisi" }),
      new_stock: zod.number({ message: "Stok baru harus diisi" }),
      description: zod
        .string({ message: "Deskripsi harus diisi" })
        .min(1, { message: "deskripsi harus diisi" }),
    })
  );
};

const validationSchema = stockCorrectionSchema();
const form = useForm({
  validationSchema,
});

const stockCorrectionForm = useInertiaForm({
  _token: page.props.csrf_token,
  product_id: "",
  old_stock: 0,
  new_stock: 0,
  description: "",
});

const selectProduct = (product: IProduct) => {
  form.setFieldValue("product_name", product.name);
  form.setFieldValue("old_stock", product.stock);
  stockCorrectionForm.product_id = product.id;
  stockCorrectionForm.old_stock = product.stock;
};

const getProducts = async (page: number) => {
  let params = [`page=${page}`].join("&");

  if (search.value.length > 0) {
    params = `${params}&search=${search.value}`;
  }

  products.loading = true;
  const response = await http.get(
    route("backoffice.product.list") + "?" + params
  );
  products.data = response.data as IProduct[];
  products.meta = response.meta as IPaginationMeta;
  products.loading = false;
};

const onSubmit = form.handleSubmit((values) => {
  stockCorrectionForm.post(route("backoffice.stock-correction.store"), {
    onError: (error) => console.log(error),
  });
});

const backToIndex = () => {
  router.get(route("backoffice.stock-correction.index"));
};

onMounted(() => {
  getProducts(1);
});

watchDebounced(
  search,
  () => {
    getProducts(1);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Form Transaksi Penjualan" />
  <div
    class="grid grid-cols-[40%_60%] divide-x divide-gray-200 h-[calc(100vh-3.5rem)]"
  >
    <!-- ---------left content -->
    <div class="divide-y divide-gray-200">
      <div class="px-2 py-2 bg-gray-50 space-y-2">
        <h3
          class="text-sm font-medium flex items-center gap-2 py-1 text-primary"
        >
          <span><Package2 class="size-5" /></span>
          <span>Pilih Barang</span>
        </h3>
        <div class="relative w-full items-center">
          <Input
            id="search"
            type="text"
            placeholder="Cari barang..."
            class="pl-10 bg-white rounded-lg"
            v-model="search"
          />
          <span
            class="absolute start-0 inset-y-0 flex items-center justify-center px-2"
          >
            <Search class="size-5 text-muted-foreground" />
          </span>
        </div>
      </div>
      <div class="p-2 h-[70vh] bg-white overflow-y-scroll space-y-2">
        <template v-if="products.data.length === 0 && !products.loading">
          <Alert class="bg-orange-50 border-none rounded w-full">
            <BadgeInfo class="size-6" />
            <AlertTitle class="ml-2">Keterangan</AlertTitle>
            <AlertDescription class="ml-2">
              Data barang tidak ditemukan di dalam sistem
            </AlertDescription>
          </Alert>
        </template>
        <template v-if="products.loading">
          <div
            class="border border-gray-200 bg-gray-50 px-3 py-2 cursor-pointer space-y-3"
            v-for="index in 5"
            :key="index"
          >
            <Skeleton class="h-4 w-3/5" />
            <Skeleton class="h-2 w-1/2" />
            <Skeleton class="h-2 w-1/2" />
          </div>
        </template>
        <template v-if="products.data.length > 0 && !products.loading">
          <div
            class="flex items-center justify-between border border-gray-200 bg-gray-50 px-3 py-1 cursor-pointer rounded-xl hover:bg-sky-50"
            v-for="(product, index) in products.data"
            :key="index"
            @click="selectProduct(product)"
          >
            <div class="flex items-center justify-between w-full">
              <div>
                <h4 class="capitalize font-medium">
                  <span> {{ product.name }} </span>
                </h4>
                <p class="text-sm text-gray-600 font-medium space-x-2">
                  {{ price.convertToRupiah(product.sale_price) }}
                </p>
                <p class="space-x-2">
                  <span class="text-xs text-sky-600">{{
                    product.category
                  }}</span>
                  <span class="text-xs text-sky-600">{{ product.rack }}</span>
                </p>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-center">
                  <div class="text-xs font-semibold">STOK</div>
                  <div class="text-lg font-semibold">{{ product.stock }}</div>
                </div>
                <div>
                  <BadgePlus class="size-5 text-primary" />
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
      <div class="flex items-center justify-between py-1 px-2">
        <div class="text-xs text-muted-foreground">
          Page {{ products.meta.current_page }} of
          {{ products.meta.last_page }} From {{ products.meta.total }} Data.
        </div>
        <div class="flex items-center gap-2">
          <div class="grid grid-cols-2 gap-2 items-center">
            <Label class="text-right text-xs">Page</Label>
            <select
              class="bg-white text-center border border-gray-200 text-gray-900 text-xs rounded focus:ring-blue-500 focus:border-blue-500 p-1.5 shadow-sm w-14"
            >
              <option
                :value="page"
                v-for="(page, index) in products.meta.last_page"
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
              :disabled="products.meta.current_page == 1 || products.loading"
              @click="getProducts(products.meta.current_page - 1)"
            >
              <span>Previous</span>
            </Button>
            <Button
              variant="outline"
              size="sm"
              :disabled="
                products.meta.current_page == products.meta.last_page ||
                products.loading
              "
              @click="getProducts(products.meta.current_page + 1)"
            >
              <span>Next</span>
            </Button>
          </div>
        </div>
      </div>
    </div>
    <!-- ----------right content -->
    <div class="divide-y divide-gray-200 px-4 py-2">
      <form class="space-y-6 pb-4" @submit.prevent="onSubmit">
        <div class="space-y-3">
          <div
            class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
          >
            <div>
              <FilePenLine class="size-8 text-blue-400" />
            </div>
            <div>
              <h4 class="font-medium">Form Koreksi Stok Barang</h4>
              <p class="text-sm text-gray-500">
                Silahkan pilih data barang yang akan di koreksi stok. Dengan
                menuliskan stok awal dan memasukkan stok baru sesuai dengan data
                fisik yang ada.
              </p>
            </div>
          </div>
          <FormField v-slot="{ componentField }" name="product_name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': stockCorrectionForm.errors.product_id,
                }"
              >
                <FormRequiredLabel>Nama Barang</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Input
                  v-bind="componentField"
                  placeholder="Silahkan pilih barang"
                  class="bg-white"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="stockCorrectionForm.errors.product_id"
              >
                {{ stockCorrectionForm.errors.product_id }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <div class="grid grid-cols-2 items-center gap-2">
            <FormField v-slot="{ componentField }" name="old_stock">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': stockCorrectionForm.errors.old_stock,
                  }"
                >
                  <FormRequiredLabel>Stok Lama</FormRequiredLabel>
                </FormLabel>
                <FormControl>
                  <Input
                    type="number"
                    v-bind="componentField"
                    class="bg-white"
                  />
                </FormControl>
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="stockCorrectionForm.errors.old_stock"
                >
                  {{ stockCorrectionForm.errors.old_stock }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="new_stock">
              <FormItem>
                <FormLabel
                  :class="{
                    'text-red-500': stockCorrectionForm.errors.new_stock,
                  }"
                >
                  <FormRequiredLabel>Stok Baru</FormRequiredLabel>
                </FormLabel>
                <FormControl>
                  <Input
                    type="number"
                    v-bind="componentField"
                    v-model="stockCorrectionForm.new_stock"
                    class="bg-white"
                  />
                </FormControl>
                <div
                  class="text-xs text-red-500 font-medium"
                  v-if="stockCorrectionForm.errors.new_stock"
                >
                  {{ stockCorrectionForm.errors.new_stock }}
                </div>
                <FormMessage v-else />
              </FormItem>
            </FormField>
          </div>
          <FormField v-slot="{ componentField }" name="description">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': stockCorrectionForm.errors.description,
                }"
              >
                <FormRequiredLabel>Deskripsi</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Textarea
                  rows="6"
                  v-bind="componentField"
                  v-model="stockCorrectionForm.description"
                  :class="{
                    'border border-red-500':
                      stockCorrectionForm.errors.description,
                  }"
                  class="bg-white"
                  :disabled="stockCorrectionForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="stockCorrectionForm.errors.description"
              >
                {{ stockCorrectionForm.errors.description }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <div class="flex items-center gap-2">
            <Button
              size="lg"
              class="w-full"
              @click="backToIndex"
              variant="outline"
              type="button"
            >
              Batalkan
            </Button>
            <Button size="lg" class="w-full" @click="onSubmit" type="button">
              Perbarui Stok
            </Button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
