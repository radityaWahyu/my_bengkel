<script setup lang="ts">
import { ref, h, onMounted } from "vue";
import { watchDebounced } from "@vueuse/core";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import { X, Search } from "lucide-vue-next";
import {
  Sheet,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/shadcn/ui/sheet";
import DataTableDialog from "@/Components/App/DataTableDialog.vue";
import type { IUser, IPaginationMeta } from "@/types/response";
import type { ColumnDef } from "@tanstack/vue-table";
import EmployeeListNameBox from "./EmployeeListNameBox.vue";
import { useHttpService } from "@/Services/useHttpServices";

const formOpen = ref<boolean>(true);

const props = defineProps<{
  role: "mekanik" | "opeartor";
}>();

const emits = defineEmits<{
  (e: "selected", value: IUser): void;
  (e: "closed", value: boolean): void;
}>();

const httpService = useHttpService();
const employees = ref<IUser[]>();
const pagination = ref<IPaginationMeta>();
const search = ref("");
const perPage = ref(10);
const isLoading = ref<boolean>(false);
const brandTable = ref<InstanceType<typeof DataTableDialog> | null>(null);

const columns: ColumnDef<IUser>[] = [
  {
    accessorKey: "repair",
    enableResizing: false,
    header: ({ column }) =>
      h(
        "div",
        { class: "gap-2 flex items-center font-semibold" },
        "Daftar Mekanik"
      ),
    cell: ({ row }) =>
      h(EmployeeListNameBox, {
        user: row.original,
        onSelected: (user: IUser) => {
          search.value = "";
          emits("selected", user);
        },
      }),
  },
];

const getEmployees = async (page: number) => {
  const url = ref({ role: props.role, page: page, perPage: perPage.value });

  if (search.value !== null) Object.assign(url.value, { search });

  const response = await httpService.get(
    route("backoffice.employee.list", url.value)
  );
  employees.value = response.data;
  pagination.value = response.meta;
};
const onClose = () => {
  search.value = "";
  formOpen.value = false;
  emits("closed", true);
};

onMounted(() => {
  getEmployees(1);
});

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getEmployees(1);
};

watchDebounced(
  search,
  () => {
    getEmployees(1);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>

<template>
  <Sheet v-model:open="formOpen">
    <SheetContent @interact-outside="(e) => e.preventDefault()">
      <SheetHeader>
        <SheetTitle v-if="role === 'mekanik'">Pilih Mekanik</SheetTitle>
        <SheetTitle v-else>Pilih Operator</SheetTitle>
      </SheetHeader>
      <div>
        <DataTableDialog
          v-if="employees && pagination"
          class="py-4"
          :columns="columns"
          :data="employees"
          :pagination="pagination"
          :loading="isLoading || httpService.processing.value"
          @change-limit="changeLimit"
          @change-page="getEmployees"
        >
          <template #filter>
            <div class="relative w-full items-center">
              <Input
                v-model="search"
                type="text"
                placeholder="Cari data..."
                class="pl-10 w-full bg-white"
              />
              <span
                class="absolute inset-y-0 flex items-center justify-center px-2"
              >
                <Search class="size-4 text-muted-foreground" />
              </span>
              <span
                class="absolute right-0 inset-y-0 flex items-center justify-center px-2"
                v-if="search !== null && search.length > 0"
              >
                <X class="size-4 text-muted-foreground" @click="search = ''" />
              </span>
            </div>
          </template>
        </DataTableDialog>
      </div>
      <SheetFooter>
        <Button
          type="button"
          variant="default"
          size="lg"
          @click="onClose"
          class="w-full"
        >
          Tutup
        </Button>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
