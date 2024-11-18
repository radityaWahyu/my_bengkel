<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import { readonly } from "vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, computed, defineAsyncComponent, watch } from "vue";
import {
  Head,
  usePage,
  useForm as useInertiaForm,
  router,
  Link,
} from "@inertiajs/vue3";
import { Card, CardContent, CardHeader } from "@/shadcn/ui/card";

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/shadcn/ui/select";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import { Input } from "@/shadcn/ui/input";
import { Textarea } from "@/shadcn/ui/textarea";
import { Label } from "@/shadcn/ui/label";
import { Button } from "@/shadcn/ui/button";
import { Skeleton } from "@/shadcn/ui/skeleton";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import {
  CarFront,
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
  IProduct,
  IServiceDetail,
  IUser,
  IServiceProduct,
  ICustomerPay,
} from "@/types/response";

// import RepairList from "@/Components/Repair/RepairList.vue";
// import ProductList from "@/Components/Product/ProductList.vue";
// import EmployeeList from "@/Components/Employee/EmployeeList.vue";
import ServiceTransactionFinishDialog from "@/Components/ServiceTransaction/ServiceTransactionFinishDialog.vue";
import TopAlert from "@/Components/App/TopAlert.vue";

const props = defineProps<{
  service: IServiceDetail;
  edit?: boolean;
}>();

const RepairList = defineAsyncComponent(
  () => import("@/Components/Repair/RepairList.vue")
);
const ProductList = defineAsyncComponent(
  () => import("@/Components/Product/ProductList.vue")
);
const EmployeeList = defineAsyncComponent(
  () => import("@/Components/Employee/EmployeeList.vue")
);
const ServiceTransactionPayment = defineAsyncComponent(
  () => import("@/Components/ServiceTransaction/ServiceTransactionPayment.vue")
);

const repairDialogOpen = ref<boolean>(false);
const productDialogOpen = ref<boolean>(false);
const employeeDialogOpen = ref<boolean>(false);
const paymentDialogOpen = ref<boolean>(false);
const finishDialogOpen = ref<boolean>(false);
const repairIdSelected = ref<string>("");

const isLoading = ref<boolean>(false);
const isLoadingProduct = ref<boolean>(false);
const isLoadingInvoice = ref<boolean>(false);
const notfinishService = ref<boolean>(false);

const statusList = ref([
  { label: "Pending", value: "pending" },
  { label: "Selesai", value: "finish" },
]);
const price = usePrice();
const page = usePage();

const serviceEditForm = useInertiaForm({
  _token: page.props.csrf_token,
  status: "",
  notes: "",
});

const serviceSchema = () => {
  if (props.service.status !== "finish") {
    return toTypedSchema(
      zod.object({
        status: zod
          .string({ message: "Status harus dipilih" })
          .min(1, { message: "Status harus dipilih" }),
        notes: zod
          .string({ message: "Catatan harus diisi" })
          .min(1, { message: "Catatan harus diisi" }),
      })
    );
  }
};

const validationSchema = serviceSchema();
const form = useForm({
  validationSchema,
});

const repairsSubTotal = computed(() => {
  return props.service.repairs.reduce(
    (accumulator, current) => accumulator + current.total,
    0
  );
});

const productsSubTotal = computed(() => {
  return props.service.products.reduce(
    (accumulator, current) => accumulator + current.total,
    0
  );
});

const totalInvoice = computed(
  () => productsSubTotal.value + repairsSubTotal.value
);

const onRepairSelected = (value: IRepair) => {
  repairDialogOpen.value = false;
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
        router.reload({
          only: ["service"],
        });
      },
      onFinish: () => (isLoading.value = false),
    }
  );
};

const deleteServiceRepair = (repairId: string) => {
  router.delete(route("backoffice.service.delete-repair", repairId), {
    preserveScroll: false,
    onStart: () => (isLoading.value = true),
    onError: (error) => console.log("error"),
    onSuccess: () => {
      router.reload({
        only: ["service"],
      });
    },
    onFinish: () => (isLoading.value = false),
  });
};

const deleteServiceProduct = (repairId: string) => {
  router.delete(route("backoffice.service.delete-product", repairId), {
    preserveScroll: false,
    onStart: () => (isLoadingProduct.value = true),
    onError: (error) => console.log("error"),
    onSuccess: () => {
      router.reload({
        only: ["service"],
      });
    },
    onFinish: () => (isLoading.value = false),
  });
};

const onProductSelected = (value: IProduct) => {
  productDialogOpen.value = false;
  router.post(
    route("backoffice.service.add-product", props.service.id),
    {
      product_id: value.id,
      price: value.sale_price,
      qty: 1,
    },
    {
      preserveScroll: false,
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

const updateQtyProduct = (serviceProduct: IServiceProduct) => {
  if (props.service.status === "finish") return true;
  router.post(
    route("backoffice.service.update-qty-product", serviceProduct.id),
    {
      qty: serviceProduct.qty,
    },
    {
      onError: (error) => console.log("error"),
      onSuccess: () => {
        router.reload({
          only: ["service"],
        });
      },
    }
  );
};

const selectEmployee = (repairId: string) => {
  repairIdSelected.value = repairId;
  employeeDialogOpen.value = true;
};
const onEmployeeSelected = (value: IUser) => {
  employeeDialogOpen.value = false;
  router.post(
    route("backoffice.service.add-employee", repairIdSelected.value),
    {
      employee_id: value.employee_id,
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

const onPaymentSelected = (payment: ICustomerPay) => {
  serviceEditForm
    .transform((data) => ({
      ...data,
      payment_id: payment.payment_id,
      extra_pay: payment.extra_pay,
      paid: payment.paid,
    }))
    .put(route("backoffice.service.update", props.service.id), {
      onError: (error) => console.log(error),
      onSuccess: () => {
        paymentDialogOpen.value = false;
        finishDialogOpen.value = true;
      },
    });
};

const startRepair = (repairId: string) => {
  router.post(
    route("backoffice.service.start-repair", repairId),
    {},
    {
      onError: (error) => console.log("error"),
      onSuccess: () => {
        router.reload({
          only: ["service"],
        });
      },
    }
  );
};

const finishRepair = (repairId: string) => {
  router.post(
    route("backoffice.service.finish-repair", repairId),
    {},
    {
      onError: (error) => console.log("error"),
      onSuccess: () => {
        router.reload({
          only: ["service"],
        });
      },
    }
  );
};
const updateStatusService = form.handleSubmit(() => {
  notfinishService.value = false;
  if (serviceEditForm.status === "finish") {
    props.service.repairs.forEach((value) => {
      if (value.finished_at === null) {
        notfinishService.value = true;
      }
    });

    if (!notfinishService.value) {
      paymentDialogOpen.value = true;
    }
  } else {
    serviceEditForm.put(route("backoffice.service.update", props.service.id), {
      onError: (error) => console.log(error),
    });
  }
});
const onSubmit = () => {
  if (serviceEditForm.status !== "") return updateStatusService();

  if (props.service.status === "waiting")
    return router.put(route("backoffice.service.approved", props.service.id), {
      total: totalInvoice.value,
    });

  return router.get(route("backoffice.service.index"), {}, { replace: true });
};

watch(
  () => props.service.status,
  (status) => {
    if (status === "finish") {
      serviceEditForm.status = status;
      serviceEditForm.notes = props.service.notes;
    }
  },
  { immediate: true }
);
</script>
<template>
  <Head title="Detail Service" />
  <div class="p-4">
    <Card class="rounded shadow-none">
      <CardHeader>
        <div class="flex items-center justify-between">
          <div class="flex items-center px-2 gap-4 text-primary">
            <ClipboardPenLine class="size-8" />
            <h1 class="font-medium text-lg">Detail Service</h1>
          </div>
        </div>
      </CardHeader>
      <CardContent>
        <div class="space-y-3">
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
              <div class="">
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
                  Keluhan pelanggan berisikan diagnosa awal yang diberikan oleh
                  pelanggan terhadap kerusakan pada kendaraan yang dimiliki
                  pelanggan.
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
                  Silahkan untuk memasukkan jenis jasa perbaikan pada kendaraan
                  dengan mengklik tombol
                  <strong>Pilih Perbaikan</strong>
                </p>
              </div>
              <Button
                as="button"
                variant="outline"
                class="text-primary space-x-2"
                @click="repairDialogOpen = true"
                :disabled="isLoading || isLoadingInvoice"
                v-if="service.status !== 'finish'"
              >
                <PlusSquare class="size-5" />
                <span>Pilih Perbaikan</span>
              </Button>
            </div>
            <!-- repair table -->
            <Table class="border-b border-b-gray-200">
              <TableHeader class="border-t border-t-gray-200 shadow">
                <TableRow>
                  <TableHead class="w-[150px]"> Nama Perbaikan </TableHead>
                  <TableHead class="text-center">Nama Mekanik</TableHead>
                  <TableHead v-if="edit" class="text-center">Status</TableHead>
                  <TableHead class="text-right w-32">Harga</TableHead>
                  <TableHead></TableHead>
                </TableRow>
              </TableHeader>
              <TableBody
                v-if="!!service && service.repairs.length > 0 && !isLoading"
              >
                <TableRow
                  v-for="(repair, index) in service.repairs"
                  :key="index"
                >
                  <TableCell class="font-medium">
                    {{ repair.name }}
                  </TableCell>
                  <TableCell class="capitalize text-center">
                    <div
                      v-if="repair.employee_name"
                      class="bg-sky-100 py-1 w-full"
                    >
                      {{ repair.employee_name }}
                    </div>
                    <div v-else class="bg-yellow-100 px-2 py-1 w-full">
                      Silahkan pilih mekanik
                    </div>
                  </TableCell>
                  <TableCell class="text-center" v-if="edit">
                    <div
                      class="w-full bg-yellow-50 py-1"
                      v-if="repair.started_at === null"
                    >
                      Menunggu Perbaikan
                    </div>
                    <div
                      class="w-full bg-blue-50 py-1"
                      v-if="
                        repair.started_at !== null &&
                        repair.finished_at === null
                      "
                    >
                      Proses Perbaikan
                    </div>
                    <div
                      class="w-full bg-green-50 py-1"
                      v-if="
                        repair.started_at !== null &&
                        repair.finished_at !== null
                      "
                    >
                      Perbaikan Selesai
                    </div>
                  </TableCell>
                  <TableCell class="text-right">
                    {{ price.convertToRupiah(repair.price) }}
                  </TableCell>

                  <TableCell class="flex items-center justify-center gap-1">
                    <Button
                      v-if="repair.started_at === null"
                      size="sm"
                      variant="destructive"
                      @click="deleteServiceRepair(repair.id)"
                      :disabled="isLoadingInvoice"
                    >
                      <X class="size-3" />
                    </Button>
                    <Button
                      v-if="repair.started_at === null"
                      size="sm"
                      variant="default"
                      :disabled="isLoadingInvoice"
                      @click="selectEmployee(repair.id)"
                    >
                      Pilih Mekanik
                    </Button>
                    <template v-if="edit">
                      <Button
                        size="sm"
                        variant="default"
                        :disabled="isLoadingInvoice"
                        @click="startRepair(repair.id)"
                        v-if="
                          repair.started_at === null &&
                          repair.finished_at === null
                        "
                      >
                        <span>Proses</span>
                      </Button>
                      <Button
                        v-if="
                          repair.started_at !== null &&
                          repair.finished_at === null
                        "
                        size="sm"
                        variant="default"
                        :disabled="isLoadingInvoice"
                        @click="finishRepair(repair.id)"
                        class="w-full"
                      >
                        <span>Selesai</span>
                      </Button>
                    </template>
                  </TableCell>
                </TableRow>
                <TableRow class="bg-sky-50 text-blue-700">
                  <TableCell class="text-right font-semibold py-2" colspan="3">
                    Sub Total
                  </TableCell>
                  <TableCell class="font-semibold text-right">
                    {{ price.convertToRupiah(repairsSubTotal) }}
                  </TableCell>
                  <TableCell></TableCell>
                  <TableCell></TableCell>
                  <TableCell></TableCell>
                </TableRow>
              </TableBody>
              <TableBody v-if="isLoading">
                <TableRow v-for="index in 3" :key="index">
                  <TableCell>
                    <Skeleton class="h-4 w-full" />
                  </TableCell>
                  <TableCell>
                    <Skeleton class="h-4 w-full" />
                  </TableCell>
                  <TableCell>
                    <Skeleton class="h-4 w-full" />
                  </TableCell>
                </TableRow>
              </TableBody>
              <TableBody v-if="!isLoading && service.repairs.length === 0">
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
                  Silahkan untuk memasukkan spare part perbaikan pada kendaraan
                  dengan silahkan mengklik tombol
                  <strong>Pilih Spare Part</strong>
                </p>
              </div>
              <Button
                as="button"
                variant="outline"
                class="text-primary space-x-2"
                :disabled="isLoadingInvoice"
                @click="productDialogOpen = true"
                v-if="service.status !== 'finish'"
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
                  <TableCell class="text-center">
                    {{ product.qty }}
                  </TableCell>
                  <TableCell class="text-right">
                    {{ price.convertToRupiah(product.total) }}
                  </TableCell>
                  <TableCell>
                    <Button
                      size="sm"
                      variant="destructive"
                      @click="deleteServiceProduct(product.id)"
                      :disabled="isLoadingInvoice"
                      v-if="service.status !== 'finish'"
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
                  <TableCell colspan="5">
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

          <TopAlert
            v-model="notfinishService"
            messages="Proses perbaikan masih belum selesai, silahkan di selesaiakn terlebih dahulu."
            type="error"
            class="flex-shrink w-full"
          />
          <div
            class="flex items-start justify-between w-full py-4 px-4 bg-sky-50"
          >
            <div class="space-y-2 grow h-auto relative">
              <FormField v-slot="{ componentField }" name="status">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': serviceEditForm.errors.status,
                    }"
                  >
                    <FormRequiredLabel>Status Perbaikan</FormRequiredLabel>
                  </FormLabel>
                  <Select
                    v-bind="componentField"
                    v-model="serviceEditForm.status"
                    v-if="service.status !== 'finish'"
                  >
                    <FormControl>
                      <SelectTrigger class="bg-white">
                        <SelectValue placeholder="Pilih status" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem
                          :value="status.value"
                          v-for="(status, index) in statusList"
                          :key="index"
                          :disabled="service.status === 'finish'"
                        >
                          {{ status.label }}
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <p class="px-2 py-1 bg-green-200 text-sm" v-else>
                    Transaksi Selesai
                  </p>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="serviceEditForm.errors.status"
                  >
                    {{ serviceEditForm.errors.status }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="notes">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': serviceEditForm.errors.notes,
                    }"
                  >
                    <FormRequiredLabel>Catatan</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Textarea
                      cols="6"
                      v-bind="componentField"
                      v-model="serviceEditForm.notes"
                      :class="{
                        'border border-red-500': serviceEditForm.errors.notes,
                      }"
                      class="bg-white"
                      :readonly="service.status === 'finish'"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="serviceEditForm.errors.notes"
                  >
                    {{ serviceEditForm.errors.notes }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
              <div class="space-x-2">
                <Link
                  :href="route('backoffice.history.service')"
                  as="button"
                  type="button"
                  :disabled="isLoadingInvoice"
                  class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
                >
                  <span v-if="service.status === 'finish'">Kembali</span>
                  <span v-else>Batal</span>
                </Link>
                <Button
                  @click="onSubmit"
                  :disabled="isLoadingInvoice"
                  v-if="service.status !== 'finish'"
                >
                  <span v-if="serviceEditForm.status === 'finish'">
                    Selesaikan Transaksi
                  </span>
                  <span v-else>Simpan data</span>
                </Button>
              </div>
            </div>
            <div class="text-blue-500 text-right grow">
              <h2 class="text-lg">Total Invoice Service</h2>
              <h2 class="text-3xl font-semibold">
                {{ price.convertToRupiah(totalInvoice) }}
              </h2>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
    <RepairList
      v-if="repairDialogOpen"
      @selected="onRepairSelected"
      @closed="() => (repairDialogOpen = false)"
    />
    <ProductList
      v-if="productDialogOpen"
      @selected="onProductSelected"
      @closed="() => (productDialogOpen = false)"
    />
    <EmployeeList
      v-if="employeeDialogOpen"
      @selected="onEmployeeSelected"
      role="mekanik"
    />
    <ServiceTransactionPayment
      :total="totalInvoice"
      v-if="paymentDialogOpen"
      @selected="onPaymentSelected"
      @closed="paymentDialogOpen = false"
    />
    <ServiceTransactionFinishDialog
      :service-id="service.id"
      v-if="finishDialogOpen"
    />
  </div>
</template>
