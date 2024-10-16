<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { reactive, ref, computed, defineAsyncComponent } from "vue";
import { watchDebounced } from "@vueuse/core";
import { Head, router } from "@inertiajs/vue3";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { ScrollArea } from "@/shadcn/ui/scroll-area";
import { Input } from "@/shadcn/ui/input";
import { Label } from "@/shadcn/ui/label";
import { Button } from "@/shadcn/ui/button";
import {
  ShoppingCart,
  Search,
  Package2,
  BadgePlus,
  Trash2,
  BadgeInfo,
} from "lucide-vue-next";
import {
  IPaginationMeta,
  IProduct,
  ISale,
  ITransactionProduct,
  ICustomerPay,
} from "@/types/response";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import { Skeleton } from "@/shadcn/ui/skeleton";
import { usePrice } from "@/Plugin/useNumber";
import SaleTransactionFinishDialog from "@/Components/SaleTransaction/SaleTransactionFinishDialog.vue";

const AlertDialog = defineAsyncComponent(
  () => import("@/Components/App/AlertDialog.vue")
);

const ServiceTransactionPayment = defineAsyncComponent(
  () => import("@/Components/ServiceTransaction/ServiceTransactionPayment.vue")
);

const props = defineProps<{
  sale: ISale;
  products: {
    data: IProduct[];
    meta: IPaginationMeta;
  };
  sale_product: {
    data: ITransactionProduct[];
  };
}>();

const price = usePrice();
const productsLoading = ref<boolean>(false);
const saleProductsLoading = ref<boolean>(false);
const showAlertDialog = ref<boolean>(false);
const paymentDialogOpen = ref<boolean>(false);
const finishDialogOpen = ref<boolean>(false);
const search = ref("");

const total = computed(() => {
  return props.sale_product.data.reduce(
    (accumulator, current) => accumulator + current.total,
    0
  );
});

const selectProduct = (product: IProduct) => {
  router.post(
    route("backoffice.sale.add-product", props.sale.id),
    {
      product_id: product.id,
      qty: 1,
      price: product.sale_price,
    },
    {
      onStart: () => (saleProductsLoading.value = true),
      onSuccess: () => {
        router.reload({
          only: ["products", "sale_product"],
          onSuccess: () => {
            saleProductsLoading.value = false;
          },
        });
      },
      onError: (error) => console.log(error),
    }
  );
};

const updateQtyProduct = (saleProduct: ITransactionProduct) => {
  if (props.sale.status === "finish") return true;
  if (saleProduct.qty <= 0) {
    router.reload();
  } else {
    router.post(
      route("backoffice.sale.update-qty-product", saleProduct.id),
      {
        qty: saleProduct.qty,
      },
      {
        onError: (error) => console.log("error"),
        onSuccess: () => {
          router.reload();
        },
      }
    );
  }
};

const deleteSaleProduct = (saleProductId: string) => {
  router.delete(route("backoffice.sale.delete-product", saleProductId), {
    preserveScroll: false,
    onStart: () => (saleProductsLoading.value = true),
    onError: (error) => console.log("error"),
    onSuccess: () => {
      router.reload();
    },
    onFinish: () => (saleProductsLoading.value = false),
  });
};

const onSubmit = () => {
  if (props.sale_product.data.length > 0) {
    paymentDialogOpen.value = true;
  } else {
    showAlertDialog.value = true;
  }
};

const onPaymentSelected = (payment: ICustomerPay) => {
  router.post(
    route("backoffice.sale.store", props.sale.id),
    {
      payment_id: payment.payment_id,
      extra_pay: payment.extra_pay,
      paid: payment.paid,
      total: payment.total_payment,
    },
    {
      onError: (error) => console.log(error),
      onSuccess: () => {
        paymentDialogOpen.value = false;
        finishDialogOpen.value = true;
      },
    }
  );
};

const getProducts = (page: number) => {
  const url = ref({ page: page });

  if (search.value !== "") Object.assign(url.value, { search });

  router.get(
    route("backoffice.sale.create-invoice", props.sale.id),
    url.value,
    {
      only: ["products"],
      preserveState: true,
      preserveScroll: true,
      onError: (error) => console.log(error),
      onStart: () => (productsLoading.value = true),
      onFinish: () => (productsLoading.value = false),
    }
  );
};

watchDebounced(
  search,
  () => {
    getProducts(props.products.meta.current_page);
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
          <span>Daftar Barang</span>
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
        <template v-if="products.data.length === 0 && !productsLoading">
          <Alert class="bg-orange-50 border-none rounded w-full">
            <BadgeInfo class="size-6" />
            <AlertTitle class="ml-2">Keterangan</AlertTitle>
            <AlertDescription class="ml-2">
              Data barang tidak ditemukan di dalam sistem
            </AlertDescription>
          </Alert>
        </template>
        <template v-if="productsLoading">
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
        <template v-if="products.data.length > 0 && !productsLoading">
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
              :disabled="products.meta.current_page == 1 || productsLoading"
              @click="getProducts(products.meta.current_page - 1)"
            >
              <span>Previous</span>
            </Button>
            <Button
              variant="outline"
              size="sm"
              :disabled="
                products.meta.current_page == products.meta.last_page ||
                productsLoading
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
    <div class="divide-y divide-gray-200">
      <div class="bg-gray-50">
        <h3
          class="py-3 px-3 text-sm font-medium flex items-center gap-2 text-primary"
        >
          <span><ShoppingCart class="size-5" /></span>
          <span>Transaksi Penjualan</span>
        </h3>
        <div
          class="relative w-full flex items-center justify-between px-4 py-2.5 bg-sky-200"
        >
          <h3 class="font-medium text-lg">TOTAL</h3>
          <h3 class="font-semibold text-xl text-blue-500">
            {{ price.convertToRupiah(total) }}
          </h3>
        </div>
      </div>
      <div class="bg-white">
        <ScrollArea class="h-[65vh] relative w-full">
          <div class="sticky top-0 z-10">
            <Table class="border-b border-b-gray-200 w-full">
              <TableHeader class="border-t border-t-gray-200">
                <TableRow class="bg-gray-100">
                  <TableHead class="w-[250px]">Nama Barang </TableHead>
                  <TableHead class="w-[200px]">Harga</TableHead>
                  <TableHead class="w-24 text-center">Jumlah</TableHead>
                  <TableHead class="text-right w-[150px]">Total</TableHead>
                  <TableHead class="w-20"></TableHead>
                </TableRow>
              </TableHeader>
            </Table>
          </div>
          <div>
            <Table>
              <TableBody class="mt-10">
                <template v-if="saleProductsLoading">
                  <TableRow v-for="index in 6" :key="index">
                    <TableCell class="py-3 w-[250px]"
                      ><Skeleton class="h-4 w-full"
                    /></TableCell>
                    <TableCell class="py-3 w-[200px]"
                      ><Skeleton class="h-4 w-full"
                    /></TableCell>
                    <TableCell class="py-3 w-24 text-center"
                      ><Skeleton class="h-4 w-full"
                    /></TableCell>
                    <TableCell class="py-3 text-right w-[150px]"
                      ><Skeleton class="h-4 w-full"
                    /></TableCell>
                    <TableCell></TableCell>
                  </TableRow>
                </template>
                <template
                  v-if="sale_product.data.length === 0 && !saleProductsLoading"
                >
                  <TableRow>
                    <TableCell colspan="4">
                      <Alert class="bg-orange-50 border-none rounded w-full">
                        <BadgeInfo class="size-6" />
                        <AlertTitle class="ml-2">Keterangan</AlertTitle>
                        <AlertDescription class="ml-2">
                          Tidak terdapat data barang silahkan menambahkan
                          terlebih dahulu
                        </AlertDescription>
                      </Alert>
                    </TableCell>
                  </TableRow>
                </template>
                <template
                  v-if="sale_product.data.length > 0 && !saleProductsLoading"
                >
                  <TableRow
                    v-for="(saleProduct, index) in sale_product.data"
                    :key="index"
                  >
                    <TableCell class="font-medium w-[250px]">
                      {{ saleProduct.name }}
                    </TableCell>
                    <TableCell class="w-[200px]">{{
                      price.convertToRupiah(saleProduct.price)
                    }}</TableCell>
                    <TableCell class="w-24 text-center">
                      <Input
                        type="number"
                        class="text-center"
                        @change="updateQtyProduct(saleProduct)"
                        v-model="saleProduct.qty"
                      />
                    </TableCell>
                    <TableCell class="text-right w-[150px]">
                      {{ price.convertToRupiah(saleProduct.total) }}
                    </TableCell>
                    <TableCell>
                      <div class="px-2">
                        <Button
                          size="sm"
                          variant="ghost"
                          class="px-2"
                          @click="deleteSaleProduct(saleProduct.id)"
                        >
                          <Trash2 class="size-4 text-red-500" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                </template>
              </TableBody>
            </Table>
          </div>
        </ScrollArea>
      </div>
      <div class="flex items-center gap-2 px-2 py-2">
        <Button
          as="button"
          size="lg"
          class="grow font-semibold text-md h-12"
          variant="outline"
        >
          Batalkan
        </Button>
        <Button
          as="button"
          size="lg"
          class="grow font-semibold text-md h-12"
          @click="onSubmit"
        >
          Bayar Sekarang
        </Button>
      </div>
    </div>
    <AlertDialog
      v-if="showAlertDialog"
      alert-type="error"
      messages="Silahkan memasukkan minimal <strong>1 Data barang</strong> dalam transaksi, agar transaksi dapat dilanjutkan."
      @closed="showAlertDialog = false"
    />
    <ServiceTransactionPayment
      :total="total"
      v-if="paymentDialogOpen"
      @selected="onPaymentSelected"
      @closed="paymentDialogOpen = false"
    />
    <SaleTransactionFinishDialog :sale-id="sale.id" v-if="finishDialogOpen" />
  </div>
</template>
