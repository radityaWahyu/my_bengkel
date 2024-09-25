<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref } from "vue";
import { useForm, Link, Head, router } from "@inertiajs/vue3";
import type { ICustomerDetail } from "@/types/response";
import { Button } from "@/shadcn/ui/button";
import { CarFront } from "lucide-vue-next";
import { MoveLeft, PlusSquare } from "lucide-vue-next";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/shadcn/ui/card";
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import { FilePenLine, Trash2, List } from "lucide-vue-next";
import ConfirmDialog from "@/Components/App/ConfirmDialog.vue";

const props = defineProps<{
  customer: ICustomerDetail;
}>();

const deleteForm = useForm({});
const openConfirmDialog = ref<boolean>(false);
const onUpdate = (id: string) => {
  router.get(
    route("backoffice.vehicle.edit", id),
    { redirect: "customer-detail" },
    { replace: true }
  );
};
const onDelete = (id: string) => {
  openConfirmDialog.value = false;
  deleteForm.delete(route("backoffice.vehicle.delete", id), {
    onError: (error) => console.log(error),
  });
};
</script>
<template>
  <Head title="Detail Pelanggan" />
  <div class="container py-4 space-y-2">
    <Card class="shadow-none rounded">
      <CardHeader>
        <CardTitle class="flex items-center gap-3 font-medium text-lg">
          <Button size="icon" variant="outline" as-child>
            <Link :href="route('backoffice.customer.index')" replace>
              <MoveLeft class="size-4 text-blue-400" />
            </Link>
          </Button>
          <span>Data Pelanggan</span>
        </CardTitle>
        <CardDescription>
          Halaman ini menampilkan informasi mendetail dari pelanggan yang telah
          terdaftar dan melakukan service kendaraan di aplikasi ini.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div
          class="grid grid-cols-3 divide-x divide-gray-200 border border-gray-200 rounded-lg"
        >
          <div class="space-y-2 divide-y divide-gray-200">
            <div class="space-y-1 p-2">
              <p class="rounded bg-muted px-2 py-[0.2rem] text-xs">
                Nama Pelanggan
              </p>
              <h4 class="capitalize font-medium text-md px-2">
                {{ customer.name }}
              </h4>
            </div>
            <div class="space-y-1 p-2">
              <p class="rounded bg-muted px-2 py-[0.2rem] text-xs">
                Jenis Kelamin
              </p>
              <h4 class="capitalize font-medium text-md px-2">
                {{ customer.gender === "l" ? "Laki - laki" : "Perempuan" }}
              </h4>
            </div>
          </div>
          <div class="space-y-2 divide-y divide-gray-200">
            <div class="space-y-1 p-2">
              <p class="rounded bg-muted px-2 py-[0.2rem] text-xs">
                No Telepon
              </p>
              <h4 class="capitalize font-medium text-md px-2">
                {{ customer.phone }}
              </h4>
            </div>
            <div class="space-y-1 p-2">
              <p class="rounded bg-muted px-2 py-[0.2rem] text-xs">
                No Whatsapp
              </p>
              <h4 class="capitalize font-medium text-md px-2">
                {{ customer.whatsapp }}
              </h4>
            </div>
          </div>
          <div class="space-y-1 p-2">
            <p class="rounded bg-muted px-2 py-[0.3rem] text-xs">Alamat</p>
            <h4 class="capitalize font-medium text-sm px-2">
              {{ customer.address }}
            </h4>
          </div>
        </div>
      </CardContent>
    </Card>
    <Card class="shadow-none rounded">
      <CardHeader>
        <CardTitle
          class="flex items-center justify-between font-medium text-lg"
        >
          <div class="flex items-center gap-3">
            <CarFront class="size-7" />
            <span>Detail Kendaraan</span>
          </div>
          <Button size="sm" as-child>
            <Link
              :href="route('backoffice.vehicle.create')"
              replace
              class="flex items-center gap-2"
              as="button"
              method="get"
              :data="{ customer: customer.id, redirect: 'customer-detail' }"
            >
              <PlusSquare class="size-4" />
              <span>Tambah Kendaraan</span>
            </Link>
          </Button>
        </CardTitle>
        <CardDescription>
          Detail kendaraan berfungsi untuk menampilkan data kendaraan yang
          dimiliki oleh setiap pelanggan.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div class="max-w-full border border-gray-100">
          <Table
            class="table-fixed overflow-x-scroll whitespace-nowrap scrollbar [&::-webkit-scrollbar]:block"
          >
            <TableCaption>
              Pastikan data kendaraan pelanggan telah terinput dengan benar.
            </TableCaption>
            <TableHeader class="bg-gray-100">
              <TableRow>
                <TableHead class="w-[100px]"> No Plat </TableHead>
                <TableHead class="w-[300px]"> Nama Kendaraan </TableHead>
                <TableHead class="w-[150px]">Merk Kendaraan</TableHead>
                <TableHead class="text-center w-[150px]">
                  Tahun Pembuatan
                </TableHead>
                <TableHead class="text-center w-[100px]">CC Mesin</TableHead>
                <TableHead class="w-[100px]"> Tipe Mesin </TableHead>
                <TableHead class="w-[150px]"> Jenis Kendaraan </TableHead>
                <TableHead class="w-[100px]"></TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="(vehicle, index) in customer.vehicles"
                :key="index"
              >
                <TableCell class="font-medium">
                  {{ vehicle.plate_number }}
                </TableCell>
                <TableCell class="font-medium">
                  {{ vehicle.name }}
                </TableCell>
                <TableCell class="font-medium">
                  {{ vehicle.brand }}
                </TableCell>
                <TableCell class="font-medium text-center">
                  {{ vehicle.production_year }}
                </TableCell>
                <TableCell class="font-medium text-center">
                  {{ vehicle.engine_volume }}
                </TableCell>
                <TableCell class="font-medium capitalize">
                  {{ vehicle.engine_type === "petrol" ? "bensin" : "diesel" }}
                </TableCell>
                <TableCell class="font-medium">
                  {{ vehicle.type === "car" ? "Mobil" : "Sepeda Motor" }}
                </TableCell>
                <TableCell>
                  <div class="space-x-2 w-full text-center">
                    <Button
                      type="button"
                      variant="outline"
                      size="icon"
                      @click="onUpdate(vehicle.id)"
                    >
                      <svg
                        class="size-4 animate-spin"
                        viewBox="0 0 100 100"
                        v-if="deleteForm.processing"
                      >
                        <circle
                          fill="none"
                          stroke-width="12"
                          class="stroke-current opacity-40"
                          cx="50"
                          cy="50"
                          r="40"
                        />
                        <circle
                          fill="none"
                          stroke-width="12"
                          class="stroke-blue-500"
                          stroke-dasharray="250"
                          stroke-dashoffset="210"
                          cx="50"
                          cy="50"
                          r="40"
                        />
                      </svg>
                      <FilePenLine class="size-4 text-blue-500" v-else />
                    </Button>
                    <Button
                      type="button"
                      variant="outline"
                      size="icon"
                      @click="openConfirmDialog = true"
                      :disabled="deleteForm.processing"
                    >
                      <svg
                        class="size-4 animate-spin"
                        viewBox="0 0 100 100"
                        v-if="deleteForm.processing"
                      >
                        <circle
                          fill="none"
                          stroke-width="12"
                          class="stroke-current opacity-40"
                          cx="50"
                          cy="50"
                          r="40"
                        />
                        <circle
                          fill="none"
                          stroke-width="12"
                          class="stroke-blue-500"
                          stroke-dasharray="250"
                          stroke-dashoffset="210"
                          cx="50"
                          cy="50"
                          r="40"
                        />
                      </svg>
                      <Trash2 class="size-4 text-red-500" v-else />
                    </Button>

                    <ConfirmDialog
                      v-model="openConfirmDialog"
                      cancel-text="Batalkan"
                      ok-text="Hapus data"
                      @cancel="openConfirmDialog = false"
                      @ok="onDelete(vehicle.id)"
                    >
                      <template #title>
                        <div>Konfirmasi Hapus Data</div>
                      </template>
                      <template #description>
                        <div>Apakah anda ingin menghapus data ini ?</div>
                      </template>
                    </ConfirmDialog>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
