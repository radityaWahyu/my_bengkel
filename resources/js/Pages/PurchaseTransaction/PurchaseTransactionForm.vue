<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { reactive, ref, computed, defineAsyncComponent } from "vue";
import {
  Head,
  router,
  usePage,
  useForm as useInertiaForm,
} from "@inertiajs/vue3";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
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
import validator from "validator";
import { ScrollArea } from "@/shadcn/ui/scroll-area";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/shadcn/ui/select";
import { Input } from "@/shadcn/ui/input";
import { Label } from "@/shadcn/ui/label";
import { Button } from "@/shadcn/ui/button";
import {
  ShoppingBasket,
  Package2,
  Trash2,
  BadgeInfo,
  MoveRight,
  Contact,
} from "lucide-vue-next";
import {
  IPaginationMeta,
  IProduct,
  IPurchase,
  ICustomerPay,
  IPurchaseProduct,
  IPayment,
  ISupplier,
} from "@/types/response";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import { Skeleton } from "@/shadcn/ui/skeleton";
import { usePrice } from "@/Plugin/useNumber";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";

const AlertDialog = defineAsyncComponent(
  () => import("@/Components/App/AlertDialog.vue")
);

const ProductListTwo = defineAsyncComponent(
  () => import("@/Components/Product/ProductListTwo.vue")
);

const SupplierList = defineAsyncComponent(
  () => import("@/Components/Supplier/SupplierList.vue")
);

const props = defineProps<{
  purchase: IPurchase;
  payments: {
    data: IPayment[];
  };
  purchase_product: {
    data: IPurchaseProduct[];
  };
}>();

const price = usePrice();
const PurchaseProductsLoading = ref<boolean>(false);
const showAlertDialog = ref<boolean>(false);
const productDialogOpen = ref<boolean>(false);
const supplierDialogOpen = ref<boolean>(false);
const supplierData = reactive({
  name: "",
  contactName: "",
  whatsappNumber: "",
});

const page = usePage();

const purchaseSchema = () => {
  return toTypedSchema(
    zod.object({
      supplier_name: zod
        .string({ message: "Pemasok harus diisi" })
        .min(1, { message: "Pemasok harus diisi." }),
      invoice_number: zod
        .string({ message: "No Nota harus diisi" })
        .min(1, { message: "No Nota harus diisi." }),
      payment_id: zod.string({ message: "Jenis bayar harus dipilih." }),
      transaction_date: zod.string({
        message: "Tanggal transaksi harus diisi.",
      }),
    })
  );
};
const validationSchema = purchaseSchema();
const form = useForm({
  validationSchema,
});

const purchaseForm = useInertiaForm({
  _token: page.props.csrf_token,
  supplier_id: "",
  invoice_number: "",
  payment_id: "",
  transaction_date: "",
});

const selectedProduct = (product: IProduct) => {
  productDialogOpen.value = false;
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

const selectedSupplier = (supplier: ISupplier) => {
  supplierDialogOpen.value = false;
  purchaseForm.supplier_id = supplier.id;
  supplierData.name = supplier.name;
  supplierData.contactName = supplier.contact_name;
  supplierData.whatsappNumber = supplier.whatsapp;
  form.setFieldValue("supplier_name", supplier.name);
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

const updatePriceProduct = (PurchaseProduct: IPurchaseProduct) => {
  if (props.purchase.status === "finish") return true;

  router.post(
    route("backoffice.purchase.update-price-product", PurchaseProduct.id),
    {
      price: PurchaseProduct.price,
    },
    {
      onError: (error) => console.log("error"),
      onSuccess: () => {
        router.reload();
      },
    }
  );
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

const extraPay = computed(() => {
  let tax = 0;
  props.payments.data.forEach((value) => {
    if (value.id === purchaseForm.payment_id) tax = value.tax;
  });

  return tax;
});

const total = computed(() => {
  return props.purchase_product.data.reduce(
    (accumulator, current) => accumulator + current.total,
    0
  );
});

const totalInvoice = computed(() => {
  return total.value + extraPay.value;
});

const cancelPurchase = () => {
  router.delete(route("backoffice.purchase.delete", props.purchase.id), {
    onError: (error) => console.log(error),
  });
};

const onSubmit = form.handleSubmit(() => {
  if (props.purchase_product.data.length === 0) {
    showAlertDialog.value = true;
  } else {
    purchaseForm
      .transform((formData) => ({
        ...formData,
        extra_pay: extraPay.value,
        total: total.value,
      }))
      .post(route("backoffice.purchase.store", props.purchase.id), {
        onError: (error) => console.log(error),
      });
  }
});
</script>
<template>
  <Head title="Form Transaksi Pembelian" />
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
        <FormField v-slot="{ componentField }" name="supplier_name">
          <FormItem>
            <FormLabel>
              <FormRequiredLabel>Data Pemasok</FormRequiredLabel>
            </FormLabel>
            <FormControl>
              <div class="flex items-center">
                <Input
                  type="text"
                  readonly
                  class="bg-white rounded-r-none"
                  v-bind="componentField"
                  v-model="supplierData.name"
                />
                <Button
                  variant="outline"
                  class="border-l-0 rounded-r rounded-l-none"
                  @click="supplierDialogOpen = true"
                >
                  <Contact class="size-5" />
                </Button>
              </div>
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <Label>Nama Kontak</Label>
            <Input
              type="text"
              readonly
              class="bg-white"
              v-model="supplierData.contactName"
            />
          </div>
          <div>
            <Label>No Whatsapp</Label>
            <Input
              type="text"
              readonly
              class="bg-white"
              v-model="supplierData.whatsappNumber"
            />
          </div>
        </div>
        <FormField v-slot="{ componentField }" name="invoice_number">
          <FormItem>
            <FormLabel>
              <FormRequiredLabel>No Nota</FormRequiredLabel>
            </FormLabel>
            <FormControl>
              <Input
                type="text"
                class="bg-white block"
                v-bind="componentField"
                v-model="purchaseForm.invoice_number"
              />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="transaction_date">
          <FormItem>
            <FormLabel>
              <FormRequiredLabel>Tanggal Transaksi</FormRequiredLabel>
            </FormLabel>
            <FormControl>
              <Input
                type="date"
                class="bg-white block"
                v-bind="componentField"
                v-model="purchaseForm.transaction_date"
              />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="payment_id">
          <FormItem>
            <FormLabel>
              <FormRequiredLabel>Jenis Bayar</FormRequiredLabel>
            </FormLabel>
            <Select v-bind="componentField" v-model="purchaseForm.payment_id">
              <FormControl>
                <SelectTrigger class="bg-white">
                  <SelectValue placeholder="Pilih Jenis Pembayaran" />
                </SelectTrigger>
              </FormControl>
              <SelectContent>
                <SelectGroup>
                  <SelectItem
                    :value="payment.id"
                    v-for="payment in payments.data"
                    :key="payment.id"
                  >
                    {{ payment.name }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <FormMessage />
          </FormItem>
        </FormField>
        <div class="grid grid-cols-2 gap-2">
          <div class="space-y-1">
            <Label>Biaya Ekstra</Label>
            <Input type="number" readonly class="bg-white" v-model="extraPay" />
          </div>
          <div class="space-y-1">
            <Label>Total Pembayaran</Label>
            <Input
              type="text"
              readonly
              class="bg-white"
              v-model="totalInvoice"
            />
          </div>
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
                      <h3 class="font-medium">{{ PurchaseProduct.name }}</h3>
                      <p class="text-xs text-muted-foreground">
                        Harga lama :
                        {{ price.convertToRupiah(PurchaseProduct.old_price) }}
                      </p>
                    </TableCell>
                    <TableCell class="w-[200px] text-center">
                      <Input
                        type="number"
                        class="text-left block px-2"
                        @change="updatePriceProduct(PurchaseProduct)"
                        v-model="PurchaseProduct.price"
                      />
                    </TableCell>
                    <TableCell class="w-24 text-center">
                      <Input
                        type="number"
                        class="text-right px-2"
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
          @click="cancelPurchase"
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

    <SupplierList
      v-if="supplierDialogOpen"
      @selected="selectedSupplier"
      @closed="() => (supplierDialogOpen = false)"
    />
  </div>
</template>
