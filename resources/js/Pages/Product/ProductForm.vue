<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";
import FormAlertiInfo from "@/Components/App/FormAlertiInfo.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { reactive, watch } from "vue";
import { Head, usePage, useForm as useInertiaForm, router, Link } from "@inertiajs/vue3";
import { ClipboardPen, ClipboardPenLine } from "lucide-vue-next";
import { Card, CardContent, CardDescription, CardHeader } from "@/shadcn/ui/card";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectGroup,
  SelectValue,
} from "@/shadcn/ui/select";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/shadcn/ui/form";
import { Plus, BadgeInfo } from "lucide-vue-next";
import { Input } from "@/shadcn/ui/input";
import { Button } from "@/shadcn/ui/button";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import type { ICategory, IRack, IProductForm } from "@/types/response";
import * as zod from "zod";
import FormAlertInfo from "@/Components/App/FormAlertiInfo.vue";
import FormRequiredLabel from "@/Components/App/FormRequiredLabel.vue";
import CategoryForm from "@/Components/Category/CategoryForm.vue";
import RackForm from "@/Components/Rack/RackForm.vue";

const props = defineProps<{
  categories: { data: ICategory[] };
  racks: { data: IRack[] };
  product?: IProductForm;
}>();

const page = usePage();

const userSchema = () => {
  if (props.product)
    return toTypedSchema(
      zod.object({
        name: zod
          .string({ message: "Nama Merk harus diisi" })
          .min(1, { message: "Nama Merk harus diisi." }),
        category_id: zod
          .string({ message: "Kategori harus diisi" })
          .min(1, { message: "Kategori harus diisi." }),
        rack_id: zod
          .string({ message: "Rak harus diisi" })
          .min(1, { message: "Rak harus diisi." }),
        buy_price: zod.number({ message: "Harga beli Produk harus diisi" }),
        sale_price: zod.number({ message: "Harga jual Produk harus diisi" }),
      })
    );

  return toTypedSchema(
    zod.object({
      name: zod
        .string({ message: "Nama Merk harus diisi" })
        .min(1, { message: "Nama Merk harus diisi." }),
      category_id: zod
        .string({ message: "Kategori harus diisi" })
        .min(1, { message: "Kategori harus diisi." }),
      rack_id: zod
        .string({ message: "Rak harus diisi" })
        .min(1, { message: "Rak harus diisi." }),
      stock: zod.number({ message: "Stok awal Produk harus diisi" }),
      buy_price: zod.number({ message: "Harga beli Produk harus diisi" }),
      sale_price: zod.number({ message: "Harga jual Produk harus diisi" }),
    })
  );
};

const validationSchema = userSchema();
const form = useForm({
  validationSchema,
});

const categoryFormState = reactive({
  open: false,
  title: "Tambah Kategori",
  loading: false,
});

const rackFormState = reactive({
  open: false,
  title: "Tambah Rak",
  loading: false,
});

const productForm = useInertiaForm({
  _token: page.props.csrf_token,
  name: "",
  category_id: "",
  rack_id: "",
  stock: 0,
  buy_price: 0,
  sale_price: 0,
});

watch(
  () => props.product,
  (product) => {
    if (product) {
      productForm.name = product.name;
      productForm.category_id = product.category_id;
      productForm.rack_id = product.rack_id;
      productForm.buy_price = product.buy_price;
      productForm.sale_price = product.sale_price;
      form.setFieldValue("name", product.name);
      form.setFieldValue("category_id", product.category_id);
      form.setFieldValue("rack_id", product.rack_id);
      form.setFieldValue("buy_price", product.buy_price);
      form.setFieldValue("sale_price", product.sale_price);
    }
  },
  { immediate: true }
);

const onCategorySaved = () => {
  router.reload({
    only: ["categories"],
    onStart: () => (categoryFormState.loading = true),
    onFinish: () => (categoryFormState.loading = true),
  });
};
const onRackSaved = () => {
  router.reload({
    only: ["racks"],
    onStart: () => (rackFormState.loading = true),
    onFinish: () => (rackFormState.loading = true),
  });
};

const onSubmit = form.handleSubmit(() => {
  if (props.product) {
    productForm.put(route("backoffice.product.update", props.product.id), {
      onError: (error) => console.log(error),
    });
  } else {
    productForm.post(route("backoffice.product.store"), {
      onError: (error) => console.log(error),
    });
  }
});
</script>
<template>
  <Head title="Form Barang" />
  <div class="flex flex-1 flex-col gap-4 py-4 w-3/5 m-auto">
    <div class="flex items-center justify-between">
      <div class="flex items-center px-4 gap-4 text-primary">
        <ClipboardPenLine class="size-8" v-if="product" />
        <ClipboardPen class="size-8" v-else />
        <h1 class="font-medium tracking-wider" v-if="product">Edit Barang</h1>
        <h1 class="font-medium tracking-wider" v-else>Tambah Barang</h1>
      </div>

      <div class="space-x-2">
        <Link
          :href="route('backoffice.product.index')"
          as="button"
          type="button"
          :disabled="productForm.processing"
          class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2"
        >
          Batal
        </Link>
        <Button @click="onSubmit">
          <span v-if="productForm.processing">Meyimpan data...</span>
          <span v-else>Simpan data</span>
        </Button>
      </div>
    </div>

    <Card class="shadow-none rounded">
      <CardHeader>
        <CardDescription>
          <FormAlertInfo v-if="!product">
            Form barang dipergunakan untuk menambah data barang yang akan dijual pada
            pelanggan. Silahkan mengisi data sesuai dengan format yang diberikan oleh form
            ini tombol
          </FormAlertInfo>
          <FormAlertInfo v-else>
            Form barang dipergunakan untuk mengubah data barang yang akan dijual pada
            pelanggan. Silahkan mengisi data sesuai dengan format yang diberikan oleh form
            ini tombol
          </FormAlertInfo>
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="onSubmit" class="space-y-6 pb-4">
          <FormField v-slot="{ componentField }" name="name">
            <FormItem>
              <FormLabel
                :class="{
                  'text-red-500': productForm.errors.name,
                }"
              >
                <FormRequiredLabel>Nama Barang</FormRequiredLabel>
              </FormLabel>
              <FormControl>
                <Input
                  type="text"
                  v-bind="componentField"
                  v-model="productForm.name"
                  :class="{
                    'border border-red-500': productForm.errors.name,
                  }"
                  :disabled="productForm.processing"
                />
              </FormControl>
              <div
                class="text-xs text-red-500 font-medium"
                v-if="productForm.errors.name"
              >
                {{ productForm.errors.name }}
              </div>
              <FormMessage v-else />
            </FormItem>
          </FormField>
          <div class="grid grid-cols-2 items-center gap-2">
            <div class="flex items-end gap-1">
              <div class="grow w-full">
                <FormField v-slot="{ componentField }" name="category_id">
                  <FormItem>
                    <FormLabel
                      :class="{
                        'text-red-500': productForm.errors.category_id,
                      }"
                    >
                      <FormRequiredLabel>Kategori Barang</FormRequiredLabel>
                    </FormLabel>
                    <Select
                      v-bind="componentField"
                      v-model="productForm.category_id"
                      v-if="categories.data.length > 0"
                    >
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Pilih Kategori Barang" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectGroup>
                          <SelectItem
                            :value="category.id"
                            v-for="(category, index) in categories.data"
                            :key="index"
                          >
                            {{ category.name }}
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                    <div
                      v-else
                      class="bg-sky-100 flex items-center gap-2 px-2 py-2 text-sky-600 h-9 rounded"
                    >
                      <div>
                        <BadgeInfo class="size-5" v-if="!categoryFormState.loading" />
                        <svg class="size-4 animate-spin" viewBox="0 0 100 100" v-else>
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
                      </div>
                      <p class="text-xs" v-if="!categoryFormState.loading">
                        Data kategori kosong
                      </p>
                      <p class="text-xs" v-else>Ambil data kategori...</p>
                    </div>
                    <div
                      class="text-xs text-red-500 font-medium"
                      v-if="productForm.errors.category_id"
                    >
                      {{ productForm.errors.category_id }}
                    </div>
                    <FormMessage v-else />
                  </FormItem>
                </FormField>
              </div>
              <Button
                type="button"
                variant="outline"
                size="icon"
                class="mb-[2px]"
                @click="categoryFormState.open = true"
              >
                <Plus class="size-4" />
              </Button>
            </div>
            <div class="flex items-end gap-1">
              <div class="grow w-full">
                <FormField v-slot="{ componentField }" name="rack_id">
                  <FormItem>
                    <FormLabel
                      :class="{
                        'text-red-500': productForm.errors.rack_id,
                      }"
                    >
                      <FormRequiredLabel>Rak</FormRequiredLabel>
                    </FormLabel>
                    <Select
                      v-bind="componentField"
                      v-model="productForm.rack_id"
                      v-if="racks.data.length > 0"
                    >
                      <FormControl>
                        <SelectTrigger>
                          <SelectValue placeholder="Pilih Rak" />
                        </SelectTrigger>
                      </FormControl>
                      <SelectContent>
                        <SelectGroup>
                          <SelectItem
                            :value="rack.id"
                            v-for="(rack, index) in racks.data"
                            :key="index"
                          >
                            {{ rack.name }}
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                    <div
                      v-else
                      class="bg-sky-100 flex items-center gap-2 px-2 py-2 text-sky-600 h-9 rounded"
                    >
                      <div>
                        <BadgeInfo class="size-5" v-if="!rackFormState.loading" />
                        <svg class="size-4 animate-spin" viewBox="0 0 100 100" v-else>
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
                      </div>
                      <p class="text-xs" v-if="!rackFormState.loading">Data Rak kosong</p>
                      <p class="text-xs" v-else>Ambil data Rak...</p>
                    </div>
                    <div
                      class="text-xs text-red-500 font-medium"
                      v-if="productForm.errors.rack_id"
                    >
                      {{ productForm.errors.rack_id }}
                    </div>
                    <FormMessage v-else />
                  </FormItem>
                </FormField>
              </div>
              <Button
                type="button"
                variant="outline"
                size="icon"
                class="mb-[2px]"
                @click="rackFormState.open = true"
              >
                <Plus class="size-4" />
              </Button>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <div class="grow" v-if="!product">
              <FormField v-slot="{ componentField }" name="stock">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': productForm.errors.stock,
                    }"
                  >
                    <FormRequiredLabel>Stok Awal</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="number"
                      v-bind="componentField"
                      v-model="productForm.stock"
                      :class="{
                        'border border-red-500': productForm.errors.stock,
                      }"
                      :disabled="productForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="productForm.errors.stock"
                  >
                    {{ productForm.errors.stock }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
            <div class="grow">
              <FormField v-slot="{ componentField }" name="buy_price">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': productForm.errors.buy_price,
                    }"
                  >
                    <FormRequiredLabel>Harga Beli</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="number"
                      v-bind="componentField"
                      v-model="productForm.buy_price"
                      :class="{
                        'border border-red-500': productForm.errors.buy_price,
                      }"
                      :disabled="productForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="productForm.errors.buy_price"
                  >
                    {{ productForm.errors.buy_price }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
            <div class="grow">
              <FormField v-slot="{ componentField }" name="sale_price">
                <FormItem>
                  <FormLabel
                    :class="{
                      'text-red-500': productForm.errors.sale_price,
                    }"
                  >
                    <FormRequiredLabel>Harga Jual</FormRequiredLabel>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="number"
                      v-bind="componentField"
                      v-model="productForm.sale_price"
                      :class="{
                        'border border-red-500': productForm.errors.sale_price,
                      }"
                      :disabled="productForm.processing"
                    />
                  </FormControl>
                  <div
                    class="text-xs text-red-500 font-medium"
                    v-if="productForm.errors.sale_price"
                  >
                    {{ productForm.errors.sale_price }}
                  </div>
                  <FormMessage v-else />
                </FormItem>
              </FormField>
            </div>
          </div>
        </form>
      </CardContent>
    </Card>
    <CategoryForm
      v-model="categoryFormState.open"
      :title="categoryFormState.title"
      @saved="onCategorySaved"
    />
    <RackForm
      v-model="rackFormState.open"
      :title="rackFormState.title"
      @saved="onRackSaved"
    />
  </div>
</template>
