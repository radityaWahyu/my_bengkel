<script setup lang="ts">
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import type { ISettingData } from "@/types/response";
import QrcodeVue from "qrcode.vue";
import type { Level, RenderAs, ImageSettings } from "qrcode.vue";

const props = defineProps<{
  setting: ISettingData;
  service: {
    id: string;
    customer: string;
    service_code: string;
    plate_number: string;
    vehicle_name: string;
    engine_volume: number;
    engine_type: string;
    type: string;
    production_year: number;
  };
}>();

const level = ref<Level>("M");
const renderAs = ref<RenderAs>("svg");
const background = ref("#ffffff");
const foreground = ref("#000000");
const margin = ref(0);
const imageSettings = ref<ImageSettings>({
  src: props.setting.logo_bengkel,
  width: 30,
  height: 30,
  // x: 10,
  // y: 10,
  excavate: true,
});
</script>
<template>
  <Head title="Cetak Receipt" />
  <div class="sheet-outer">
    <div class="sheet text-wrap space-y-3">
      <div class="border-b border-b-gray-800 border-dashed py-1">
        <h3 class="font-semibold">{{ setting.nama_bengkel }}</h3>
        <p class="text-[10px]">{{ setting.alamat_bengkel }}</p>
      </div>
      <div>
        <h3 class="font-semibold pb-2">{{ service.service_code }}</h3>
        <p>
          <span>Pelanggan : </span>
          <span>{{ service.customer }}</span>
        </p>
        <p>
          <span>No Kendaraan : </span>
          <span>{{ service.plate_number }}</span>
        </p>
        <p>
          <span>Kendaraan : </span>
          <span>{{ service.vehicle_name }}</span>
        </p>
        <p>
          <span>CC : </span>
          <span>{{ service.engine_volume }}</span>
        </p>
        <p>
          <span>Jenis Mesin : </span>
          <span>{{
            service.engine_type === "petrol" ? "Bensin" : "Diesel"
          }}</span>
        </p>
        <p>
          <span>Tahun Pembuatan : </span>
          <span>{{ service.production_year }}</span>
        </p>
        <p>
          <span>Jenis kendaraan : </span>
          <span>{{ service.type === "car" ? "Mobil" : "Sepeda Motor" }}</span>
        </p>
      </div>

      <div>
        <p class="text-[10px]">
          *Untuk keterangan lebih detil scan barcode dibawah ini.
        </p>
      </div>
      <div class="py-2">
        <qrcode-vue
          class="m-auto"
          :value="
            route('backoffice.service.customer-service-detail', service.id)
          "
          :level="level"
          :render-as="renderAs"
          :background="background"
          :foreground="foreground"
        />
      </div>
      <div
        class="py-1 text-[10px] text-center border-t border-dashed border-t-gray-700"
      >
        Terima kasih telah menggunakan jasa bengkel kami.
      </div>
    </div>
  </div>
</template>
<style lang="scss" scoped>
.sheet-outer {
  margin: 0;
  @apply text-[12px];
  @apply flex;
  @apply min-h-screen;
  @apply bg-gray-500;
}
.sheet {
  margin: 0;
  overflow: hidden;
  position: relative;
  box-sizing: border-box;
  page-break-after: always;
}

.sheet-outer .sheet {
  width: 56mm;
}
.sheet {
  padding: 2mm;
}

@media screen {
  .sheet {
    @apply bg-white;
    margin: auto;
  }
}

@media print {
  @page {
    size: 58mm 100mm;
    padding: 2mm;
    margin: auto;
  }
  .sheet-outer {
    width: 58mm;
    @apply bg-transparent;
  }
}
</style>
