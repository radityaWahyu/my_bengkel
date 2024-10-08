<script setup lang="ts">
import { IServiceDetail, ISettingData } from "@/types/response";

const props = defineProps<{
  setting: ISettingData;
  service: IServiceDetail;
}>();
</script>
<template>
  <Head title="Cetak Receipt" />
  <div class="sheet-outer">
    <div class="sheet text-wrap space-y-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 p-3">
          <img :src="setting.logo_bengkel" alt="logo-bengkel" class="size-16" />
          <div>
            <h3 class="text-xl font-semibold uppercase">
              {{ setting.nama_bengkel }}
            </h3>
            <p class="text-xs w-3/5">{{ setting.alamat_bengkel }}</p>
          </div>
        </div>
        <h2 class="text-xl font-semibold">INVOICE SERVICE</h2>
      </div>
      <div>
        <div class="grid grid-cols-2 gap-10 text-sm">
          <div class="border border-gray-400 p-2">
            <p>
              <span class="block">Kepada :</span>
              {{ service.vehicle.customer }}
            </p>
          </div>
          <div class="border border-gray-400 p-2">asdfasd</div>
        </div>
      </div>
    </div>
  </div>
</template>
<style lang="scss" scoped>
.sheet-outer {
  margin: 0;
  @apply min-h-screen;
  @apply bg-gray-500;
  @apply p-10;
}
.sheet {
  margin: 0;
  overflow: hidden;
  position: relative;
  box-sizing: border-box;
}

.sheet-outer .sheet {
  width: 210mm;
}
.sheet {
  padding: 0.5cm;
}

@media screen {
  .sheet {
    @apply bg-white;
    margin: auto;
  }
}

@media print {
  .sheet-outer {
    width: 210mm;
    padding: 1cm;
    @apply bg-transparent;
  }
}
@page {
  size: 210mm 296mm;
  padding: 1cm;
  margin: auto;
}
</style>
