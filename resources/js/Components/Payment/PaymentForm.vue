<script setup lang="ts">
import { watch, ref } from "vue";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
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
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/shadcn/ui/sheet";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import * as zod from "zod";
import { usePage, useForm as useInertiaForm } from "@inertiajs/vue3";
import type { IPayment } from "@/types/response";
import FormAlertiInfo from "../App/FormAlertiInfo.vue";

const formOpen = defineModel<boolean>();

const props = defineProps<{
  title: string;
  payment?: IPayment | undefined;
  edit?: boolean;
}>();

const emits = defineEmits<{
  (e: "saved", value: boolean): void;
  (e: "closed", value: boolean): void;
}>();

const page = usePage();
const id = ref<string>("");

const userSchema = () => {
  return toTypedSchema(
    zod.object({
      name: zod
        .string({ message: "Nama Pembayaran harus diisi" })
        .min(1, { message: "Nama Pembayaran harus diisi." }),
      bank_name: zod
        .string({ message: "Nama Bank harus diisi" })
        .min(1, { message: "Nama Bank harus diisi." }),
      account_name: zod
        .string({ message: "Nama Akun harus diisi" })
        .min(1, { message: "Nama Akun harus diisi." }),
      account_number: zod
        .string({ message: "No Rekening harus diisi" })
        .min(1, { message: "No Rekening harus diisi." }),
      tax: zod.number({ message: "Harga harus diisi angkat" }).optional(),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const paymentForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
  bank_name: "",
  account_name: "",
  account_number: "",
  tax: 0,
});

watch(
  () => props.payment,
  (values) => {
    if (values && props.edit) {
      id.value = values.id;
      form.setFieldValue("name", values.name);
      form.setFieldValue("bank_name", values.bank_name);
      form.setFieldValue("account_name", values.account_name);
      form.setFieldValue("account_number", values.account_number);
      form.setFieldValue("tax", values.tax);
      paymentForm.name = values.name;
      paymentForm.bank_name = values.bank_name;
      paymentForm.account_name = values.account_name;
      paymentForm.account_number = values.account_number;
      paymentForm.tax = values.tax;
    }
  },
  { immediate: true }
);

const onSubmit = form.handleSubmit(() => {
  if (props.edit) {
    paymentForm.put(route("backoffice.payment.update", id.value), {
      onSuccess: () => {
        emits("saved", true);
        paymentForm.reset();
        formOpen.value = false;
      },
      onError: (error) => {
        console.log(error);
      },
      onFinish: () => {
        form.resetForm();
      },
    });
  } else {
    paymentForm.post(route("backoffice.payment.store"), {
      onSuccess: () => {
        emits("saved", true);
        paymentForm.reset();
        formOpen.value = false;
      },
      onError: (error) => {
        console.log(error);
      },
      onFinish: () => {
        form.resetForm();
      },
    });
  }
});

const onClose = () => {
  form.resetForm();
  paymentForm.reset();
  formOpen.value = false;
  emits("closed", true);
};
</script>

<template>
  <Sheet v-model:open="formOpen">
    <SheetContent @interact-outside="(e) => e.preventDefault()">
      <SheetHeader>
        <SheetTitle>{{ title }}</SheetTitle>
        <SheetDescription>
          <FormAlertiInfo>
            Form ini dipergunakan untuk menambah atau mengubah Jenis Pembayaran.
            Silahkan isi data sesuai form dibawah.
          </FormAlertiInfo>
        </SheetDescription>
      </SheetHeader>
      <div class="grid gap-4 py-4">
        <form @submit="onSubmit" class="space-y-8">
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': paymentForm.errors.name,
                }"
              >
                Jenis Pembayaran
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  placeholder="nama pembayaran..."
                  v-bind="componentField"
                  v-model="paymentForm.name"
                  :class="{
                    'border border-red-500': paymentForm.errors.name,
                  }"
                  :disabled="paymentForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="paymentForm.errors.name"
              >
                {{ paymentForm.errors.name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="bank_name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': paymentForm.errors.bank_name,
                }"
              >
                Nama Bank
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  placeholder="nama bank..."
                  v-bind="componentField"
                  v-model="paymentForm.bank_name"
                  :class="{
                    'border border-red-500': paymentForm.errors.bank_name,
                  }"
                  :disabled="paymentForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="paymentForm.errors.bank_name"
              >
                {{ paymentForm.errors.bank_name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="account_name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': paymentForm.errors.account_name,
                }"
              >
                Nama Akun
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  placeholder="nama akun..."
                  v-bind="componentField"
                  v-model="paymentForm.account_name"
                  :class="{
                    'border border-red-500': paymentForm.errors.account_name,
                  }"
                  :disabled="paymentForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="paymentForm.errors.account_name"
              >
                {{ paymentForm.errors.account_name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="account_number">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': paymentForm.errors.account_number,
                }"
              >
                No Rekening
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  placeholder="no rekening..."
                  v-bind="componentField"
                  v-model="paymentForm.account_number"
                  :class="{
                    'border border-red-500': paymentForm.errors.account_number,
                  }"
                  :disabled="paymentForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="paymentForm.errors.account_number"
              >
                {{ paymentForm.errors.account_number }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField }" name="tax">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': paymentForm.errors.tax,
                }"
              >
                PPN
              </FormLabel>
              <FormControl>
                <Input
                  type="number"
                  v-bind="componentField"
                  v-model="paymentForm.tax"
                  :class="{
                    'border border-red-500': paymentForm.errors.tax,
                  }"
                  :disabled="paymentForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="paymentForm.errors.tax"
              >
                {{ paymentForm.errors.tax }}
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
          :disabled="paymentForm.processing"
        >
          Batal
        </Button>
        <Button
          type="button"
          :disabled="paymentForm.processing"
          @click="onSubmit"
        >
          <span v-if="paymentForm.processing"> Menyimpan data... </span>
          <span v-else> Simpan data </span>
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
