<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Button } from "@/shadcn/ui/button";
import { Dialog, DialogContent, DialogFooter } from "@/shadcn/ui/dialog";
import { Check } from "lucide-vue-next";

const props = defineProps<{
  serviceId: string;
}>();

const dialogOpen = ref<boolean>(true);

const printInvoice = () => {
  window.open(route("backoffice.service.invoice", props.serviceId))?.focus();
  dialogOpen.value = false;
  backToServiceIndex();
};

const backToServiceIndex = () =>
  router.get(route("backoffice.service.index"), {}, { replace: true });
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
            <h3 class="text-xl font-semibold">Transaksi Service Selesai</h3>
            <p class="font-medium text-sm">
              Transaksi service telah berhasil di selesaikan. Silahkan untuk mencetak
              invoice transaksi dan berikan kepada pelanggan sebagai bukti telah melakukan
              service.
            </p>
          </div>
        </div>
        <DialogFooter class="m-auto">
          <Button type="button" variant="outline" size="lg" @click="backToServiceIndex">
            Ke Halaman Service
          </Button>
          <Button type="button" size="lg" @click="printInvoice">
            Cetak Invoice Service
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
