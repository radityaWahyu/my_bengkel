<script setup lang="ts">
import { watch, ref, onMounted } from "vue";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import { Textarea } from "@/shadcn/ui/textarea";
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
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import { usePage, useForm as useInertiaForm } from "@inertiajs/vue3";
import type { IJurnalForm, IPayment } from "@/types/response";
import { useHttpService } from "@/Services/useHttpServices";

const formOpen = ref<boolean>(true);

const props = defineProps<{
  title: string;
  id: string;
}>();

const emits = defineEmits<{
  (e: "saved", value: boolean): void;
  (e: "closed", value: boolean): void;
}>();

const page = usePage();
const paymentList = ref<IPayment[]>();
const http = useHttpService();
const id = ref<string>("");
const jurnalType = ref([
  { label: "Pengeluaran", value: "expense" },
  { label: "Pemasukan", value: "income" },
]);

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      transaction_date: zod
        .string({ message: "Tanggal transaksi harus diisi" })
        .min(1, { message: "Tanggal transaksi harus diisi." }),
      payment: zod
        .string({ message: "Jenis Pembayaran harus diisi" })
        .min(1, { message: "Jenis Pembayaran harus diisi." }),
      jurnal_type: zod
        .string({ message: "Tipe jurnal harus diisi" })
        .min(1, { message: "Tipe jurnal harus diisi." }),
      total: zod.number({ message: "Total harus diisi" }),
      description: zod
        .string({ message: "Deskripsi jurnal harus diisi" })
        .min(1, { message: "Deskripsi jurnal harus diisi" }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const jurnalForm = useInertiaForm({
  _token: page.props.csrf_token,
  transaction_date: "",
  payment: "",
  jurnal_type: "",
  total: 0,
  description: "",
});

const getPayment = async () => {
  const response = await http.get(route("backoffice.payment.list"));
  paymentList.value = response.data;
  // console.log(response);
};

const getJurnal = async () => {
  const { data } = await http.get(route("backoffice.jurnal.edit", id.value));
  // console.log(data);

  jurnalForm.transaction_date = data.transaction_date;
  form.setFieldValue("transaction_date", data.transaction_date);
  jurnalForm.payment = data.payment;
  form.setFieldValue("payment", data.payment);

  if (data.expense === 0) {
    jurnalForm.jurnal_type = "income";
    form.setFieldValue("jurnal_type", "income");
    jurnalForm.total = data.income;
    form.setFieldValue("total", data.income);
  } else {
    jurnalForm.jurnal_type = "expense";
    form.setFieldValue("jurnal_type", "expense");
    jurnalForm.total = data.expense;
    form.setFieldValue("total", data.expense);
  }

  jurnalForm.description = data.description;
  form.setFieldValue("description", data.description);
};

onMounted(() => {
  getPayment();
});

watch(
  () => props.id,
  (values) => {
    if (values || values !== null) {
      id.value = props.id;
      getJurnal();
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (id.value.length > 0) {
    jurnalForm.put(route("backoffice.jurnal.update", id.value), {
      onSuccess: () => {
        jurnalForm.reset();
        emits("saved", true);
      },
      onError: (error) => {
        console.log(error);
      },
      onFinish: () => {
        form.resetForm();
      },
    });
  } else {
    jurnalForm.post(route("backoffice.jurnal.store"), {
      onSuccess: () => {
        emits("saved", true);
        jurnalForm.reset();
      },
      onError: (error) => {
        console.log(error);
        emits("closed", true);
      },
      onFinish: () => {
        form.resetForm();
      },
    });
  }
});

const onClose = () => {
  form.resetForm();
  formOpen.value = false;
  emits("closed", true);
};
</script>

<template>
  <Sheet v-model:open="formOpen">
    <SheetContent @interact-outside="(e) => e.preventDefault()">
      <SheetHeader>
        <SheetTitle>{{ title }}</SheetTitle>
      </SheetHeader>
      <div class="grid gap-4 py-4">
        <form @submit="onSubmit" class="space-y-2">
          <FormField v-slot="{ componentField }" name="transaction_date">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': jurnalForm.errors.transaction_date,
                }"
              >
                Tanggal Transaksi
              </FormLabel>
              <FormControl>
                <Input
                  type="date"
                  v-bind="componentField"
                  v-model="jurnalForm.transaction_date"
                  :class="{
                    'border border-red-500': jurnalForm.errors.transaction_date,
                  }"
                  :disabled="jurnalForm.processing"
                  class="block"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="jurnalForm.errors.transaction_date"
              >
                {{ jurnalForm.errors.transaction_date }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <div class="grid grid-cols-2 gap-2">
            <FormField v-slot="{ componentField }" name="payment">
              <FormItem>
                <FormLabel>Jenis Pembayaran</FormLabel>

                <Select v-bind="componentField" v-model="jurnalForm.payment">
                  <FormControl>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih Pembayaran" />
                    </SelectTrigger>
                  </FormControl>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem
                        :value="payment.id"
                        v-for="payment in paymentList"
                        :key="payment.id"
                      >
                        {{ payment.name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <FormMessage />
              </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="jurnal_type">
              <FormItem>
                <FormLabel>Jenis Jurnal</FormLabel>

                <Select
                  v-bind="componentField"
                  v-model="jurnalForm.jurnal_type"
                >
                  <FormControl>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih Jenis" />
                    </SelectTrigger>
                  </FormControl>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem
                        :value="type.value"
                        v-for="(type, index) in jurnalType"
                        :key="index"
                      >
                        {{ type.label }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <FormMessage />
              </FormItem>
            </FormField>
          </div>

          <FormField v-slot="{ componentField }" name="total">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': jurnalForm.errors.total,
                }"
              >
                Total
              </FormLabel>
              <FormControl>
                <Input
                  type="number"
                  v-bind="componentField"
                  v-model="jurnalForm.total"
                  :class="{
                    'border border-red-500': jurnalForm.errors.total,
                  }"
                  :disabled="jurnalForm.processing"
                  class="block"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="jurnalForm.errors.total"
              >
                {{ jurnalForm.errors.total }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="description">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': jurnalForm.errors.description,
                }"
              >
                Deskripsi
              </FormLabel>
              <FormControl>
                <Textarea
                  placeholder="Deskripsi jurnal"
                  v-bind="componentField"
                  v-model="jurnalForm.description"
                  :class="{
                    'border border-red-500': jurnalForm.errors.description,
                  }"
                  :disabled="jurnalForm.processing"
                  rows="6"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="jurnalForm.errors.description"
              >
                {{ jurnalForm.errors.description }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
        </form>
      </div>
      <SheetFooter>
        <Button
          type="button"
          variant="ghost"
          @click="onClose"
          :disabled="jurnalForm.processing"
        >
          Batal</Button
        >
        <Button
          type="button"
          :disabled="jurnalForm.processing"
          @click="onSubmit"
        >
          <span v-if="jurnalForm.processing"> Menyimpan data... </span>
          <span v-else> Simpan data </span>
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
