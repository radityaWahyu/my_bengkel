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
  ShoppingBasket,
  Package2,
  Trash2,
  BadgeInfo,
  MoveRight,
} from "lucide-vue-next";
import {
  IPaginationMeta,
  IProduct,
  IPurchase,
  ICustomerPay,
  IPurchaseProduct,
} from "@/types/response";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import { Skeleton } from "@/shadcn/ui/skeleton";
import { usePrice } from "@/Plugin/useNumber";
import PurchaseTransactionFinishDialog from "@/Components/PurchaseTransaction/PurchaseTransactionFinishDialog.vue";

const AlertDialog = defineAsyncComponent(
  () => import("@/Components/App/AlertDialog.vue")
);

const ProductListTwo = defineAsyncComponent(
  () => import("@/Components/Product/ProductListTwo.vue")
);

const props = defineProps<{
  purchase: IPurchase;
  products: {
    data: IProduct[];
    meta: IPaginationMeta;
  };
  purchase_product: {
    data: IPurchaseProduct[];
  };
}>();

const price = usePrice();
const productsLoading = ref<boolean>(false);
const PurchaseProductsLoading = ref<boolean>(false);
const showAlertDialog = ref<boolean>(false);
const productDialogOpen = ref<boolean>(false);
const finishDialogOpen = ref<boolean>(false);
const search = ref("");

const total = computed(() => {
  return props.purchase_product.data.reduce(
    (accumulator, current) => accumulator + current.total,
    0
  );
});

const selectedProduct = (product: IProduct) => {
  router.post(
    route("backoffice.purchase.add-product", props.purchase.id),
    {
      product_id: product.id,
      qty: 1,
      price: product.buy_price,
      old_price: product.buy_price,
    },
    {
      onStart: () => (PurchaseProductsLoading.value = true),
      onSuccess: () => {
        router.reload({
          only: ["products", "purchase_product"],
          onSuccess: () => {
            PurchaseProductsLoading.value = false;
          },
        });
      },
      onError: (error) => console.log(error),
    }
  );
};

const updateQtyProduct = (PurchaseProduct: IPurchaseProduct) => {
  if (props.purchase.status === "finish") return true;
  if (PurchaseProduct.qty <= 0) {
    router.reload();
  } else {
    router.post(
      route("backoffice.purchase.update-qty-product", PurchaseProduct.id),
      {
        qty: PurchaseProduct.qty,
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

const deletePurchaseProduct = (PurchaseProductId: string) => {
  router.delete(
    route("backoffice.purchase.delete-product", PurchaseProductId),
    {
      preserveScroll: false,
      onStart: () => (PurchaseProductsLoading.value = true),
      onError: (error) => console.log("error"),
      onSuccess: () => {
        router.reload();
      },
      onFinish: () => (PurchaseProductsLoading.value = false),
    }
  );
};

const onSubmit = () => {
  if (props.purchase_product.data.length > 0) {
  } else {
    showAlertDialog.value = true;
  }
};

const onPaymentSelected = (payment: ICustomerPay) => {
  router.post(
    route("backoffice.purchase.store", props.purchase.id),
    {
      payment_id: payment.payment_id,
      extra_pay: payment.extra_pay,
      paid: payment.paid,
      total: payment.total_payment,
    },
    {
      onError: (error) => console.log(error),
      onSuccess: () => {
        finishDialogOpen.value = true;
      },
    }
  );
};

const getProducts = (page: number) => {
  const url = ref({ page: page });

  if (search.value !== "") Object.assign(url.value, { search });

  router.get(
    route("backoffice.purchase.create-invoice", props.purchase.id),
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
    <div class="">
      <div
        class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
      >
        <div>
          <ShoppingBasket class="size-8 text-blue-400" />
        </div>
        <div>
          <h4 class="font-medium">Transaksi Pembelian</h4>
          <p class="text-sm text-gray-500">
            Silahkan memilih pemasok dan memasukkan daftar barang yang dibeli.
          </p>
        </div>
      </div>
      <div class="px-4 py-1 space-y-2">
        <div class="space-y-1">
          <Label>Data Pemasok</Label>
          <Input type="text" readonly />
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div class="space-y-1">
            <Label>CC Mesin</Label>
            <Input type="number" readonly />
          </div>
          <div class="space-y-1">
            <Label>Tipe Mesin</Label>
            <Input type="text" readonly />
          </div>
        </div>
        <div class="space-y-1">
          <Label>No. Nota</Label>
          <Input type="text" readonly />
        </div>

        <div class="space-y-1">
          <Label>Jenis Pembayaran</Label>
          <Input type="text" readonly />
        </div>
        <div class="space-y-1">
          <Label>Biaya Extra</Label>
          <Input type="text" readonly />
        </div>
        <div class="space-y-1">
          <Label>Total Pembayaran</Label>
          <Input type="text" readonly />
        </div>
        <div class="flex">
          <Button
            as="button"
            size="lg"
            class="relative grow font-semibold text-md h-12 flex items-center"
            variant="default"
            @click="productDialogOpen = true"
          >
            Tambah Barang
            <span class="absolute right-4">
              <MoveRight class="size-5" />
            </span>
          </Button>
        </div>
      </div>
    </div>
    <!-- ----------right content -->
    <div class="divide-y divide-gray-200">
      <div class="bg-gray-50">
        <h3
          class="py-3 px-3 text-sm font-medium flex items-center gap-2 text-primary"
        >
          <span><Package2 class="size-5" /></span>
          <span>Daftar Barang</span>
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
                <template v-if="PurchaseProductsLoading">
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
                  v-if="
                    purchase_product.data.length === 0 &&
                    !PurchaseProductsLoading
                  "
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
                  v-if="
                    purchase_product.data.length > 0 && !PurchaseProductsLoading
                  "
                >
                  <TableRow
                    v-for="(PurchaseProduct, index) in purchase_product.data"
                    :key="index"
                  >
                    <TableCell class="font-medium w-[250px]">
                      {{ PurchaseProduct.name }}
                    </TableCell>
                    <TableCell class="w-[200px]">{{
                      price.convertToRupiah(PurchaseProduct.price)
                    }}</TableCell>
                    <TableCell class="w-24 text-center">
                      <Input
                        type="number"
                        class="text-center"
                        @change="updateQtyProduct(PurchaseProduct)"
                        v-model="PurchaseProduct.qty"
                      />
                    </TableCell>
                    <TableCell class="text-right w-[150px]">
                      {{ price.convertToRupiah(PurchaseProduct.total) }}
                    </TableCell>
                    <TableCell>
                      <div class="px-2">
                        <Button
                          size="sm"
                          variant="ghost"
                          class="px-2"
                          @click="deletePurchaseProduct(PurchaseProduct.id)"
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
          Simpan Transaksi
        </Button>
      </div>
    </div>
    <AlertDialog
      v-if="showAlertDialog"
      alert-type="error"
      messages="Silahkan memasukkan minimal <strong>1 Data barang</strong> dalam transaksi, agar transaksi dapat dilanjutkan."
      @closed="showAlertDialog = false"
    />
    <ProductListTwo
      v-if="productDialogOpen"
      @selected="selectedProduct"
      @closed="() => (productDialogOpen = false)"
    />
    <PurchaseTransactionFinishDialog
      :purchase-id="purchase.id"
      v-if="finishDialogOpen"
    />
  </div>
</template>
