<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Button } from "@/shadcn/ui/button";
import { Dialog, DialogContent, DialogFooter } from "@/shadcn/ui/dialog";
import { Check } from "lucide-vue-next";

const props = defineProps<{
  saleId: string;
}>();

const dialogOpen = ref<boolean>(true);

const printInvoice = () => {
  window.open(route("backoffice.sale.invoice", props.saleId))?.focus();
  dialogOpen.value = false;
  backToSaleIndex();
};

const backToSaleIndex = () =>
  router.get(route("backoffice.sale.index"), {}, { replace: true });
</script>
<template>
  <div>
    <Dialog :open="dialogOpen">
      <DialogContent
        class="sm:max-w-[500px]"
        @escape-key-down="() => false"
        @interact-outside="() => false"
      >
        <div
          class="py-4 m-auto text-center space-y-3 border-b border-b-gray-400 border-dashed"
        >
          <div class="bg-green-200 rounded-full p-4 inline-flex">
            <Check class="size-14 text-green-700" />
          </div>
          <div class="space-y-2">
            <h3 class="text-xl font-semibold">Transaksi Penjualan Selesai</h3>
            <p class="font-medium text-sm">
              Transaksi penjualan telah berhasil di selesaikan. Silahkan untuk
              mencetak invoice transaksi dan berikan kepada pelanggan sebagai
              bukti telah melakukan pembelian.
            </p>
          </div>
        </div>
        <DialogFooter class="m-auto">
          <Button
            type="button"
            variant="outline"
            size="lg"
            @click="backToSaleIndex"
          >
            Ke Halaman Penjualan
          </Button>
          <Button type="button" size="lg" @click="printInvoice">
            Cetak Struk Penjualan
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
