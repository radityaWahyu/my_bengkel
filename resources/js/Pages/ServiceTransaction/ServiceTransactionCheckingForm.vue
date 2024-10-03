<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { reactive, ref, computed } from "vue";
import { Head, usePage, router, Link } from "@inertiajs/vue3";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/shadcn/ui/card";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import { Input } from "@/shadcn/ui/input";
import { Textarea } from "@/shadcn/ui/textarea";
import { Label } from "@/shadcn/ui/label";
import { Button } from "@/shadcn/ui/button";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";
import {
  ClipboardPen,
  CarFront,
  TextSearch,
  Wrench,
  PlusSquare,
  StickyNote,
  OctagonAlert,
  Package,
  X,
  ClipboardPenLine,
} from "lucide-vue-next";
import { usePrice } from "@/Plugin/useNumber";
import type {
  IRepair,
  IVehicle,
  IProduct,
  IServiceDetail,
} from "@/types/response";

import RepairList from "@/Components/Repair/RepairList.vue";
import ProductList from "@/Components/Product/ProductList.vue";

const props = defineProps<{
  service: IServiceDetail;
}>();

const repairDialogOpen = ref<boolean>(false);
const productDialogOpen = ref<boolean>(false);

const isLoading = ref<boolean>(false);

const price = usePrice();
const page = usePage();

const repairsSubTotal = computed(() => {
  return 0;
});

const productsSubTotal = computed(() => {
  return 0;
});

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      vehicle_id: zod
        .string({ message: "Nama Merk harus diisi" })
        .min(1, { message: "Nama Merk harus diisi." }),
      category_id: zod
        .string({ message: "Kategori harus diisi" })
        .min(1, { message: "Kategori harus diisi." }),
      rack_id: zod
        .string({ message: "Rak harus diisi" })
        .min(1, { message: "Rak harus diisi." }),
      stock: zod.number({ message: "Stok awal Produk harus diisi" }),
      buy_price: zod.number({ message: "Harga beli Produk harus diisi" }),
      sale_price: zod.number({ message: "Harga jual Produk harus diisi" }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const onRepairSelected = (value: IRepair) => {
  router.post(
    route("backoffice.service.add-repair", props.service.id),
    {
      repair_id: value.id,
      price: value.price,
      qty: 1,
    },
    {
      onStart: () => (isLoading.value = true),
      onError: (error) => console.log("error"),
      onSuccess: () => {
        repairDialogOpen.value = false;
        router.reload({
          only: ["service"],
        });
      },
      onFinish: () => (isLoading.value = false),
    }
  );
};

const onProductSelected = (value: IProduct) => {
  router.post(
    route("backoffice.service.add-product", props.service.id),
    {
      repair_id: value.id,
      price: value.sale_price,
      qty: 1,
    },
    {
      onStart: () => (isLoading.value = true),
      onError: (error) => console.log("error"),
      onSuccess: () => {
        router.reload({
          only: ["service"],
        });
      },
      onFinish: () => (isLoading.value = false),
    }
  );
};

const removeRepair = (index: number) => {};
const onSubmit = () => {};
</script>
<template>
  <Head title="Form Transaksi Service" />
  <div class="p-4">
    <Card class="rounded shadow-none">
      <CardHeader>
        <div class="flex items-center justify-between">
          <div class="flex items-center px-2 gap-4 text-primary">
            <ClipboardPenLine class="size-8" />
            <h1 class="font-medium text-lg">Form Transaksi Service</h1>
          </div>

          <div class="space-x-2">
            <Link
              :href="route('backoffice.service.index')"
              as="button"
              type="button"
              :disabled="isLoading"
              class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
            >
              Batal
            </Link>
            <Button @click="onSubmit">
              <span v-if="isLoading">Meyimpan data...</span>
              <span v-else>Simpan data</span>
            </Button>
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="onSubmit" class="space-y-6 pb-4">
          <div class="space-y-3">
            <div
              class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
            >
              <CarFront class="size-8 text-blue-400" />
              <div>
                <h4 class="font-medium">Data Kendaraan</h4>
                <p class="text-sm text-gray-500">
                  Silahkan cek data kendaraan apakah sesuai dengan yang dimiliki
                  oleh pelanggan.
                </p>
              </div>
            </div>
            <div class="grid grid-cols-2 items-center gap-2">
              <div>
                <Label>Kendaraan</Label>
                <Input
                  type="text"
                  :default-value="
                    service.vehicle.plate_number + '-' + service.vehicle.name
                  "
                  readonly
                />
              </div>
              <div class="space-y-1">
                <Label>Nama Pelanggan</Label>
                <Input
                  type="text"
                  :default-value="service.vehicle.customer"
                  readonly
                />
              </div>
            </div>
            <div class="grid grid-cols-4 gap-2">
              <div class="space-y-1">
                <Label>CC Mesin</Label>
                <Input
                  type="number"
                  :default-value="service.vehicle.engine_volume"
                  readonly
                />
              </div>
              <div class="space-y-1">
                <Label>Tipe Mesin</Label>
                <Input
                  type="text"
                  :default-value="
                    service.vehicle.engine_type === 'petrol'
                      ? 'Bensin'
                      : 'Diesel'
                  "
                  readonly
                />
              </div>
              <div class="space-y-1">
                <Label>Jenis Kendaraan</Label>
                <Input
                  type="text"
                  :default-value="
                    service.vehicle.type === 'car' ? 'Mobil' : 'Sepeda Motor'
                  "
                  readonly
                />
              </div>
              <div class="space-y-1">
                <Label>Tahun Pembuatan</Label>
                <Input
                  type="number"
                  :default-value="service.vehicle.production_year"
                  readonly
                />
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <div
              class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
            >
              <StickyNote class="size-8 text-blue-400" />
              <div>
                <h4 class="font-medium">Keluhan Pelanggan</h4>
                <p class="text-sm text-gray-500">
                  Silahkan isi keluhan pelanggan terhadap kerusakan kendaraan
                  yang dimiliki oleh pelanggan tersebut.
                </p>
              </div>
            </div>
            <div>
              <Textarea
                cols="6"
                :default-value="service.description"
                readonly
              />
            </div>
          </div>
          <div class="space-y-3">
            <div
              class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
            >
              <Wrench class="size-8 text-blue-400" />
              <div class="grow">
                <h4 class="font-medium">Daftar Perbaikan</h4>
                <p class="text-sm text-gray-500">
                  Silahkam masukan jenis jasa perbaikan pada kendaraan dengan
                  mengklik tombol
                  <strong>Pilih Perbaikan</strong>
                </p>
              </div>
              <Button
                as="button"
                variant="outline"
                class="text-primary space-x-2"
                @click="repairDialogOpen = true"
              >
                <PlusSquare class="size-5" />
                <span>Pilih Perbaikan</span>
              </Button>
            </div>
            <!-- repair table -->
            <Table class="border-b border-b-gray-200">
              <TableHeader class="border-t border-t-gray-200 shadow">
                <TableRow>
                  <TableHead class="w-[450px]"> Nama Perbaikan </TableHead>
                  <TableHead class="text-right w-32">Harga</TableHead>
                  <TableHead></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody v-if="!!service && service.repairs.length > 0">
                <TableRow
                  v-for="(repair, index) in service.repairs"
                  :key="index"
                >
                  <TableCell class="font-medium">
                    {{ repair.name }}
                  </TableCell>
                  <TableCell class="text-right">
                    {{ price.convertToRupiah(repair.price) }}
                  </TableCell>
                  <TableCell>
                    <Button
                      size="sm"
                      variant="outline"
                      @click="removeRepair(index)"
                    >
                      <X class="size-3" />
                    </Button>
                  </TableCell>
                </TableRow>
                <TableRow class="bg-sky-50 text-blue-700">
                  <TableCell class="text-right font-semibold py-2">
                    Sub Total
                  </TableCell>
                  <TableCell class="font-semibold text-right">
                    {{ price.convertToRupiah(repairsSubTotal) }}
                  </TableCell>
                  <TableCell></TableCell>
                </TableRow>
              </TableBody>
              <TableBody v-else>
                <TableRow>
                  <TableCell colspan="3">
                    <Alert class="bg-orange-50 border-none rounded w-full">
                      <OctagonAlert class="size-6" />
                      <AlertTitle class="ml-2">Keterangan</AlertTitle>
                      <AlertDescription class="ml-2">
                        Tidak terdapat data perbaikan silahkan menambahkan
                        terlebih dahulu
                      </AlertDescription>
                    </Alert>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
          <!-- item table -->
          <div class="space-y-3">
            <div
              class="flex items-center gap-4 border-b border-dashed border-b-gray-200 p-2"
            >
              <Package class="size-8 text-blue-400" />
              <div class="grow">
                <h4 class="font-medium">Daftar Spare Part</h4>
                <p class="text-sm text-gray-500">
                  Untuk masukan spare part perbaikan pada kendaraan dengan
                  silahkan mengklik tombol
                  <strong>Pilih Spare Part</strong>
                </p>
              </div>
              <Button
                as="button"
                variant="outline"
                class="text-primary space-x-2"
                @click="productDialogOpen = true"
              >
                <PlusSquare class="size-5" />
                <span>Pilih Spare Part</span>
              </Button>
            </div>
            <Table class="border-b border-b-gray-200">
              <TableHeader class="border-t border-t-gray-200 shadow">
                <TableRow>
                  <TableHead class="w-[250px]">Nama Barang </TableHead>
                  <TableHead class="w-[200px]">Harga</TableHead>
                  <TableHead class="w-24 text-center">Jumlah</TableHead>
                  <TableHead class="text-right w-[150px]">Total</TableHead>
                  <TableHead></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody v-if="service!! && service.products.length > 0">
                <TableRow
                  v-for="(product, index) in service.products"
                  :key="index"
                >
                  <TableCell class="font-medium">
                    {{ product.name }}
                  </TableCell>
                  <TableCell>{{
                    price.convertToRupiah(product.price)
                  }}</TableCell>
                  <TableCell>
                    <Input
                      type="number"
                      v-model="product.qty"
                      class="text-center"
                    />
                  </TableCell>
                  <TableCell class="text-right">
                    {{ price.convertToRupiah(product.total) }}
                  </TableCell>
                  <TableCell>
                    <Button
                      size="sm"
                      variant="outline"
                      @click="removeRepair(index)"
                    >
                      <X class="size-3" />
                    </Button>
                  </TableCell>
                </TableRow>
                <TableRow class="bg-sky-50 text-blue-700">
                  <TableCell class="text-right font-semibold py-2" colspan="3">
                    Sub Total
                  </TableCell>
                  <TableCell class="font-semibold text-right">
                    {{ price.convertToRupiah(productsSubTotal) }}
                  </TableCell>
                  <TableCell></TableCell>
                </TableRow>
              </TableBody>
              <TableBody v-else>
                <TableRow>
                  <TableCell colspan="3">
                    <Alert class="bg-orange-50 border-none rounded w-full">
                      <OctagonAlert class="size-6" />
                      <AlertTitle class="ml-2">Keterangan</AlertTitle>
                      <AlertDescription class="ml-2">
                        Tidak terdapat data barang silahkan menambahkan terlebih
                        dahulu
                      </AlertDescription>
                    </Alert>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </form>
      </CardContent>
    </Card>
    <RepairList v-model="repairDialogOpen" @selected="onRepairSelected" />
    <ProductList v-model="productDialogOpen" @selected="onProductSelected" />
  </div>
</template>
