<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { watchDebounced } from "@vueuse/core";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import { HandCoins } from "lucide-vue-next";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import {
  Sheet,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/shadcn/ui/sheet";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/shadcn/ui/select";
import { Label } from "@/shadcn/ui/label";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import { usePage, useForm as useInertiaForm } from "@inertiajs/vue3";
import { usePrice } from "@/Plugin/useNumber";
import { useHttpService } from "@/Services/useHttpServices";
import { ICustomerPay, IPayment } from "@/types/response";

const dialogOpen = ref<boolean>(true);
const httpService = useHttpService();
const paymentList = ref<IPayment[]>();
const paymentSelected = ref<string>("");
const customerPay = ref<number>(0);
const price = usePrice();
// const formOpen = defineModel<boolean>();

const props = defineProps<{
  total: number;
}>();

const emits = defineEmits<{
  (e: "selected", value: ICustomerPay): void;
  (e: "closed", value: boolean): void;
}>();

const page = usePage();

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      payment_type: zod
        .string({ message: "Jenis Pembayaran harus dipilih" })
        .min(1, { message: "Jenis Pembayaran harus dipilih" }),
      total_paid: zod
        .number({ message: "Dibayar harus diisi" })
        .min(1, { message: "Dibayar harus diisi" }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const extraPay = computed(() => {
  let tax = 0;
  paymentList.value?.forEach((value) => {
    if (value.id === paymentSelected.value) tax = value.tax;
  });

  return tax;
});

const totalPayment = computed(() => props.total + extraPay.value);
const chargePayment = computed(() => {
  if (customerPay.value < totalPayment.value) {
    return 0;
  } else {
    return customerPay.value - totalPayment.value;
  }
});

const getPayment = async () => {
  const response = await httpService.get(route("backoffice.payment.list"));
  paymentList.value = response.data;
  // console.log(response);
};

onMounted(() => {
  getPayment();
});

const onSubmit = form.handleSubmit(() => {
  dialogOpen.value = false;
  emits("selected", {
    payment_id: paymentSelected.value,
    extra_pay: extraPay.value,
    paid: customerPay.value,
    total: props.total,
    payment_charge: chargePayment.value,
    total_payment: totalPayment.value,
  });
});
</script>

<template>
  <Sheet :open="dialogOpen">
    <SheetContent @interact-outside="(e) => e.preventDefault()">
      <SheetHeader>
        <SheetTitle class="py-4 text-muted-foreground flex items-center gap-2">
          <HandCoins class="size-6" />
          <span>Form Pembayaran</span>
        </SheetTitle>
      </SheetHeader>
      <div class="space-y-4">
        <FormField v-slot="{ componentField }" name="payment_type">
          <FormItem>
            <FormLabel>
              <FormRequiredLabel>Jenis Bayar</FormRequiredLabel>
            </FormLabel>
            <Select v-bind="componentField" v-model="paymentSelected">
              <FormControl>
                <SelectTrigger class="bg-white">
                  <SelectValue placeholder="pilih jenis" />
                </SelectTrigger>
              </FormControl>
              <SelectContent>
                <SelectGroup>
                  <SelectItem
                    :value="payment.id"
                    v-for="(payment, index) in paymentList"
                    :key="index"
                  >
                    {{ payment.name }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <FormMessage />
          </FormItem>
        </FormField>
        <div>
          <h4
            class="py-1 border-b border-dashed border-b-gray-200 text-muted-foreground text-sm"
          >
            Detail Pembayaran
          </h4>
          <div class="flex items-center">
            <h2 class="text-base grow">Total Invoice</h2>
            <h2 class="text-lg font-semibold grow text-right">
              {{ price.convertToRupiah(total) }}
            </h2>
          </div>
          <div class="flex items-center">
            <h2 class="text-base grow">Biaya Extra</h2>
            <h2 class="text-lg font-semibold grow text-right">
              {{ price.convertToRupiah(extraPay) }}
            </h2>
          </div>
          <div
            class="flex items-center py-1 border-t border-dashed border-t-gray-200"
          >
            <h2 class="text-base grow">Total Pembayaran</h2>
            <h2 class="text-lg font-semibold grow text-right">
              {{ price.convertToRupiah(totalPayment) }}
            </h2>
          </div>
        </div>
        <FormField v-slot="{ componentField }" name="total_paid">
          <FormItem>
            <FormLabel> Jumlah Di Bayar </FormLabel>
            <FormControl>
              <Input
                type="number"
                placeholder="jumlah bayar"
                v-bind="componentField"
                v-model="customerPay"
                class="text-right"
              />
            </FormControl>

            <FormMessage />
          </FormItem>
        </FormField>
        <div class="text-right grow">
          <h2 class="text-lg">Kembalian</h2>
          <h2 class="text-3xl font-semibold">
            {{ price.convertToRupiah(chargePayment) }}
          </h2>
        </div>
      </div>
      <SheetFooter>
        <div class="w-full py-6 space-y-2">
          <Button
            type="button"
            variant="default"
            size="lg"
            @click="onSubmit"
            class="w-full"
          >
            Bayar Sekarang
          </Button>
          <Button
            type="button"
            variant="outline"
            size="lg"
            @click="emits('closed', true)"
            class="w-full"
          >
            Batalkan
          </Button>
        </div>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
