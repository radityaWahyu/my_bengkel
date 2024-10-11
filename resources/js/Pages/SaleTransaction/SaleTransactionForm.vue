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
  usePage,
  useForm as useInertiaForm,
  Link,
} from "@inertiajs/vue3";
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
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import { Input } from "@/shadcn/ui/input";
import { Textarea } from "@/shadcn/ui/textarea";
import { Label } from "@/shadcn/ui/label";
import { Button } from "@/shadcn/ui/button";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";
import {
  ShoppingCart,
  Search,
  Package2,
  ChevronRight,
  X,
} from "lucide-vue-next";

const vehicle = reactive({
  id: "",
  name: "",
  plate_number: "",
  machine_frame: "",
  engine_volume: 0,
  engine_type: "",
  type: "",
  production_year: 0,
  brand: "",
  customer: "",
});

const vehicleDialogOpen = ref<boolean>(false);
const vehicleName = computed(() => {
  if (vehicle.id.length > 0) return `${vehicle.plate_number}-${vehicle.name}`;
});

const page = usePage();
const serviceForm = useInertiaForm({
  _token: page.props.csrf_token,
  vehicle_id: "",
  description: "",
});

const serviceSchema = () => {
  return toTypedSchema(
    zod.object({
      vehicle_name: zod
        .string({ message: "Data Kendaraan harus diisi" })
        .min(1, { message: "Data Kendaraan harus diisi" }),
      description: zod
        .string({ message: "Keluhan harus diisi" })
        .min(1, { message: "Keluhan harus diisi" }),
    })
  );
};

const validationSchema = serviceSchema();
const form = useForm({
  validationSchema,
});

const onSubmit = form.handleSubmit(() => {
  serviceForm.post(route("backoffice.service.store"), {
    onError: (error) => console.log(error),
  });
});
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
          />
          <span
            class="absolute start-0 inset-y-0 flex items-center justify-center px-2"
          >
            <Search class="size-5 text-muted-foreground" />
          </span>
        </div>
      </div>
      <div class="p-2 h-[70vh] bg-white overflow-y-scroll space-y-2">
        <div
          class="flex items-center justify-between border border-gray-200 bg-gray-100 px-3 py-1"
          v-for="index in 10"
          :key="index"
        >
          <div class="flex items-center justify-between w-full">
            <div>
              <h4 class="capitalize font-medium">
                <span> Produk 1 </span>
              </h4>
              <p class="text-sm text-gray-600 font-medium">Rp 500</p>
            </div>
            <div class="flex items-center gap-2">
              <div class="text-center">
                <div class="text-xs font-semibold">STOK</div>
                <div class="text-lg font-semibold">5</div>
              </div>
              <div>
                <ChevronRight class="size-5" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-between py-1 px-2">
        <div class="text-xs text-muted-foreground">
          <span> Page 5 of 4 From 1 Data. </span>
        </div>
        <div class="flex items-center gap-2">
          <div class="grid grid-cols-2 gap-2 items-center">
            <Label class="text-right text-xs">Page</Label>
            <select
              class="bg-white text-center border border-gray-200 text-gray-900 text-xs rounded focus:ring-blue-500 focus:border-blue-500 p-1.5 shadow-sm w-14"
            >
              <option>2</option>
            </select>
          </div>
          <div class="space-x-2">
            <Button variant="outline" size="sm">
              <span>Previous</span>
            </Button>
            <Button variant="outline" size="sm">
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
          <h3 class="font-medium">TOTAL</h3>
          <h3 class="font-semibold">Rp. 200.000</h3>
        </div>
      </div>
      <div class="bg-white h-[65vh] overflow-y-scroll">
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
          <TableBody class="mt-10">
            <TableRow v-for="index in 10" :key="index">
              <TableCell class="font-medium"> Produk 1 </TableCell>
              <TableCell>Rp 100000</TableCell>
              <TableCell>
                <Input type="number" class="text-center" />
              </TableCell>
              <TableCell class="text-right"> Rp.200000 </TableCell>
              <TableCell>
                <Button size="sm" variant="destructive">
                  <X class="size-3" />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div class="flex items-center gap-2 px-2 py-2">
        <Button as="button" size="lg" class="grow" variant="outline">
          Batalkan
        </Button>
        <Button as="button" size="lg" class="grow">Bayar Sekarang</Button>
      </div>
    </div>
  </div>
</template>
