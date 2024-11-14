<script lang="ts">
import Backoffice from "@/Layouts/Backoffice.vue";

export default {
  layout: Backoffice,
};
</script>
<script setup lang="ts">
import { ref, h } from "vue";
import { watchDebounced } from "@vueuse/core";
import { router, Head } from "@inertiajs/vue3";
import { Plus, UserCircle, Search, X, Trash2 } from "lucide-vue-next";
import { Button } from "@/shadcn/ui/button";
import { Input } from "@/shadcn/ui/input";
import type { ColumnDef } from "@tanstack/vue-table";
import type { IPaginationMeta, IUser } from "@/types/response";
import HeaderInformation from "@/Components/App/HeaderInformation.vue";
import DataTable from "@/Components/App/DataTable.vue";
import UserButtonAction from "@/Components/User/UserButtonAction.vue";
import UserNameBox from "@/Components/User/UserNameBox.vue";
import UserEmployeeNameBox from "@/Components/User/UserEmployeeNameBox.vue";
import ConfirmDialog from "@/Components/App/ConfirmDialog.vue";
import LinkButton from "@/Components/App/LinkButton.vue";

const props = defineProps<{
  users: { data: IUser[]; meta: IPaginationMeta };
  params: {
    sortName: string;
    sortType: string;
    search: string;
  };
}>();

const openDeleteConfirm = ref<boolean>(false);
const search = ref(props.params?.search);
const perPage = ref(props.users.meta.per_page);
const isLoading = ref<boolean>(false);
const selectedId = ref<string[]>([]);
const userTable = ref<InstanceType<typeof DataTable> | null>(null);
const columns: ColumnDef<IUser>[] = [
  {
    accessorKey: "username",
    enableResizing: false,
    size: 300,
    header: ({ column }) =>
      h("div", { class: "gap-2 flex items-center font-semibold" }, "Username"),
    cell: ({ row }) => h(UserNameBox, { user: row.original }),
  },
  {
    accessorKey: "name",
    enableResizing: false,
    size: 300,
    header: ({ column }) =>
      h(
        "div",
        { class: "gap-2 flex items-center font-semibold" },
        "Nama Pegawai"
      ),
    cell: ({ row }) => h(UserEmployeeNameBox, { user: row.original }),
  },
  {
    accessorKey: "whatsapp",
    enableResizing: false,
    size: 250,
    header: ({ column }) => {
      return h(
        "div",
        { class: "gap-2 flex items-center text-left font-semibold" },
        "No Whatsapp"
      );
    },
    cell: ({ row }) => h("div", {}, row.original.whatsapp),
  },
  {
    accessorKey: "role",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h("div", { class: "text-center font-semibold" }, "Role"),
    cell: ({ row }) =>
      h(
        "div",
        {
          class: "capitalize py-1 bg-sky-100 rounded-full w-full text-center",
        },
        row.original.role
      ),
  },
  {
    accessorKey: "enabled",
    enableResizing: false,
    size: 200,
    header: ({ column }) =>
      h("div", { class: "gap-2 text-center font-semibold" }, "Status"),
    cell: ({ row }) =>
      h(
        "div",
        {
          class: [
            "capitalize py-1  rounded-full text-center ",
            {
              "bg-sky-100 text-blue-600": row.original.enabled,
              "bg-yellow-100 text-yellow-800": !row.original.enabled,
            },
          ],
        },
        row.original.enabled ? "Aktif" : "Tidak Aktif"
      ),
  },

  {
    id: "actions",
    enableHiding: false,
    size: 150,
    cell: ({ row }) =>
      h(UserButtonAction, {
        user: row.original,
        onDeleted: () => getUsers(props.users.meta.current_page),
        onReload: () => getUsers(props.users.meta.current_page),
        onUpdated: () =>
          router.get(
            route("backoffice.user.edit", row.original.id),
            {},
            { replace: true }
          ),
      }),
  },
];

const changeLimit = (limit: number) => {
  perPage.value = limit;
  getUsers(props.users.meta.current_page);
};
// function get category data from database
const getUsers = (page: number) => {
  const url = ref({ page: page, perPage: perPage.value });

  if (props.params.sortName !== null && props.params.sortType !== null) {
    Object.assign(url.value, {
      sortName: props.params.sortName,
      sortType: props.params.sortType,
    });
  }
  if (search.value !== null) Object.assign(url.value, { search });

  router.get(route("backoffice.user.index"), url.value, {
    only: ["users", "params"],
    preserveState: true,
    preserveScroll: true,
    onError: (error) => console.log(error),
    onStart: () => (isLoading.value = true),
    onFinish: () => (isLoading.value = false),
  });
};

const onSaved = (value: boolean) => {
  // alert(value);
  getUsers(props.users.meta.current_page);
};

const deleteAll = () => {
  router.post(
    route("backoffice.user.delete-all"),
    {
      ids: selectedId.value,
    },
    {
      onStart: () => {
        openDeleteConfirm.value = false;
        isLoading.value = true;
      },
      onFinish: () => {
        isLoading.value = false;
        selectedId.value = [];
        userTable.value?.resetTable();
      },
    }
  );
};
const cancelDeleteAll = () => {
  selectedId.value = [];
  userTable.value?.resetTable();
};

watchDebounced(
  search,
  () => {
    getUsers(props.users.meta.current_page);
  },
  { debounce: 500, maxWait: 1000 }
);
</script>
<template>
  <Head title="Data User" />
  <div class="flex flex-1 flex-col gap-4 py-3">
    <div class="flex items-center divide-x divide-gray-300 p-2">
      <div class="flex items-center px-4 gap-4 text-primary">
        <UserCircle class="size-10" />
        <h1 class="text-lg font-semibold">Data User</h1>
      </div>

      <div class="px-4">
        <div class="flex items-center gap-1" v-if="selectedId.length > 0">
          <Button
            class="-tracking-wider space-x-2"
            type="button"
            variant="outline"
            :disabled="isLoading"
            @click="cancelDeleteAll"
          >
            <span>Batalkan</span>
          </Button>
          <Button
            class="-tracking-wider space-x-2"
            type="button"
            variant="destructive"
            :disabled="isLoading"
            @click="openDeleteConfirm = true"
          >
            <svg
              class="size-4 animate-spin"
              viewBox="0 0 100 100"
              v-if="isLoading"
            >
              <circle
                fill="none"
                stroke-width="12"
                class="stroke-white"
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
            <Trash2 class="size-4" v-else />
            <span v-if="isLoading">Menghapus data</span>
            <span v-else>Hapus data</span>
          </Button>
        </div>
        <LinkButton
          :to="route('backoffice.user.create')"
          v-else
          class="-tracking-wider space-x-2"
        >
          <Plus class="w-4 h-4" />
          <span>Tambah User</span>
        </LinkButton>
      </div>
    </div>
    <HeaderInformation>
      Data User dipergunakan untuk memanjemen user yang berkerja menggunakan
      sistem ini. Silahkan menambahkan data baru dengan mengklik tombol
      <strong>Tambah User</strong>
    </HeaderInformation>
    <div>
      <DataTable
        ref="userTable"
        :columns="columns"
        :data="users.data"
        :pagination="users.meta"
        :loading="isLoading"
        @change-limit="changeLimit"
        @change-page="getUsers"
      >
        <template #filter>
          <div class="relative w-1/2 items-center">
            <Input
              v-model="search"
              type="text"
              placeholder="Cari User..."
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
            </span></div
        ></template>
      </DataTable>
    </div>
    <ConfirmDialog
      v-model="openDeleteConfirm"
      cancel-text="Batalkan"
      ok-text="Hapus data"
      @cancel="openDeleteConfirm = false"
      @ok="deleteAll"
    >
      <template #title>
        <div>Konfirmasi Hapus Data</div>
      </template>
      <template #description>
        <div>
          Apakah anda ingin menghapus {{ selectedId.length }} data ini ?
        </div>
      </template>
    </ConfirmDialog>
  </div>
</template>
