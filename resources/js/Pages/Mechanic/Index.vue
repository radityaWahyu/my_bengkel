<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import { ref } from "vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { IRepairList } from "@/types/response";
import { Wrench, OctagonAlert } from "lucide-vue-next";
import { Alert, AlertDescription, AlertTitle } from "@/shadcn/ui/alert";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/shadcn/ui/table";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import MechanicRepairButton from "@/Components/Mechanic/MechanicRepairButton.vue";

const props = defineProps<{
  repairs: { data: IRepairList[] };
}>();

const isLoading = ref<boolean>(false);

const getRepairs = () => {
  router.get(
    route("backoffice.mechanic.service"),
    {},
    {
      only: ["repairs", "params"],
      preserveState: true,
      preserveScroll: true,
      onError: (error) => console.log(error),
      onStart: () => (isLoading.value = true),
      onFinish: () => (isLoading.value = false),
    }
  );
};
</script>
<template>
  <Head title="Daftar Perbaikan Mekanik" />
  <div class="flex flex-1 flex-col gap-2 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <Wrench class="size-10" />
        <h1 class="text-lg font-semibold">Daftar Perbaikan</h1>
      </div>
    </div>
    <HeaderInformation>
      Halaman ini dipergunakan oleh parak mekanik untuk melihat daftar antrian
      kendaraan yang harus di kerjakan oleh setiap mekanik. Untuk memulai
      perbaikan silahkan klik tombol mulai.
    </HeaderInformation>
    <div>
      <!-- repair table -->
      <Table class="border-b border-b-gray-200 table-fixed scrollbar">
        <TableHeader class="border-t border-t-gray-200 shadow bg-gray-100">
          <TableRow>
            <TableHead class="px-5 w-[150px]"> Kode Service </TableHead>
            <TableHead class="text-center w-[150px]">No Plat</TableHead>
            <TableHead class="w-[200px]">Nama Pelanggan</TableHead>
            <TableHead class="w-[200px]">Nama Kendaraan</TableHead>
            <TableHead class="w-[250px]">Nama Perbaikan</TableHead>
            <TableHead></TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow
            v-for="(repair, index) in repairs.data"
            :key="index"
            class="bg-white"
          >
            <TableCell class="px-5 space-y-1">
              <h3 class="font-medium">{{ repair.service_code }}</h3>
              <p class="text-xs">Tanggal : {{ repair.created_at }}</p>
            </TableCell>
            <TableCell class="font-medium text-center">
              {{ repair.vehicle_plate_number }}
            </TableCell>
            <TableCell class="font-medium">
              {{ repair.customer_name }}
            </TableCell>
            <TableCell class="font-medium">
              {{ repair.vehicle_name }}
            </TableCell>
            <TableCell class="font-medium">
              {{ repair.repair_name }}
            </TableCell>
            <TableCell>
              <MechanicRepairButton :repair="repair" @finished="getRepairs" />
            </TableCell>
          </TableRow>
        </TableBody>

        <TableBody v-if="repairs.data.length === 0">
          <TableRow>
            <TableCell colspan="6">
              <Alert class="bg-orange-50 border-none rounded w-full">
                <OctagonAlert class="size-6" />
                <AlertTitle class="ml-2">Keterangan</AlertTitle>
                <AlertDescription class="ml-2">
                  Tidak terdapat data perbaikan pada akun anda
                </AlertDescription>
              </Alert>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </div>
</template>
