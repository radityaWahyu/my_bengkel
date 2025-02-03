<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { Sheet, SheetContent, SheetTrigger } from "@/shadcn/ui/sheet";
import { Button } from "@/shadcn/ui/button";
import { usePage } from "@inertiajs/vue3";
import {
  Home,
  Menu,
  ChevronRightIcon,
  ChevronDown,
  Bolt,
  Layers3,
  ClipboardPaste,
  FileText,
  Settings,
  HandCoins,
  ClipboardPenLine,
  History,
  Wrench,
  CheckCircle2,
} from "lucide-vue-next";
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from "@/shadcn/ui/collapsible";
import SubMenuNavigation from "./SubMenuNavigation.vue";
import MenuNavigation from "./MenuNavigation.vue";

const page = usePage();
const masterDataOpen = ref<boolean>(false);
const transaksiOpen = ref<boolean>(false);
const laporanOpen = ref<boolean>(false);
const pengaturanOpen = ref<boolean>(false);
const masterDataMenu = ref([
  "kategori",
  "rak",
  "merk",
  "perbaikan",
  "barang",
  "pegawai",
  "pemasok",
  "pelanggan",
]);

const transaksiMenu = ref(["service", "penjualan", "pembelian"]);

const laporanMenu = ref(["lbarangs", "lservice", "lpenjualan", "lpembelian"]);

const pengaturanMenu = ref(["user", "sistem", "pembayaran", "satuan"]);

const isAdminAndOperator = computed(
  () =>
    page.props.auth.user.level === "administrator" ||
    page.props.auth.user.level === "operator"
);

onMounted(() => {
  const fullUrl = page.url.split("/");
  console.log(fullUrl.length);
  const currentUrl = ref();

  if (fullUrl.length === 3) {
    currentUrl.value = fullUrl[2].split("?");
  } else if (fullUrl.length === 4) {
    currentUrl.value = fullUrl[2].split("?");
    if (
      currentUrl.value[0] === "pengaturan" ||
      currentUrl.value[0] === "transaksi" ||
      currentUrl.value[0] === "laporan"
    )
      currentUrl.value = fullUrl[3].split("?");
  } else if (fullUrl.length >= 5) {
    currentUrl.value = fullUrl[2].split("?");

    if (
      currentUrl.value[0] === "pengaturan" ||
      currentUrl.value[0] === "transaksi"
    )
      currentUrl.value = fullUrl[3].split("?");
  }

  console.log(currentUrl.value);

  if (masterDataMenu.value.includes(currentUrl.value[0])) {
    masterDataOpen.value = true;
  }
  if (transaksiMenu.value.includes(currentUrl.value[0])) {
    transaksiOpen.value = true;
  }
  if (pengaturanMenu.value.includes(currentUrl.value[0])) {
    pengaturanOpen.value = true;
  }
  if (laporanMenu.value.includes(currentUrl.value[0])) {
    laporanOpen.value = true;
  }
});
</script>
<template>
  <div>
    <Sheet>
      <SheetTrigger as-child>
        <Button variant="outline" size="icon" class="shrink-0 md:hidden">
          <Menu class="h-5 w-5" />
          <span class="sr-only">Toggle navigation menu</span>
        </Button>
      </SheetTrigger>
      <SheetContent side="left" class="flex flex-col">
        <div class="flex-1 overflow-y-auto scrollbar">
          <nav class="grid items-start text-sm font-medium">
            <MenuNavigation
              :active="page.url.startsWith('backoffice/dashboard', 1)"
            >
              <Home class="h-5 w-5" />
              Dashboard
            </MenuNavigation>
            <Collapsible
              v-model:open="masterDataOpen"
              v-if="isAdminAndOperator"
            >
              <CollapsibleTrigger
                class="flex w-full items-center text-sm font-medium px-4 py-3 border-l-[6px] border-l-sky-100 hover:bg-gray-100"
                @click="masterDataOpen != masterDataOpen"
              >
                <div
                  class="flex items-center gap-3 text-muted-foreground hover:text-primary"
                >
                  <Layers3 class="h-5 w-5" />
                  Master Data
                </div>
                <ChevronRightIcon
                  class="ml-auto h-5 w-5 transition-all"
                  v-if="masterDataOpen === false"
                />
                <ChevronDown class="ml-auto h-5 w-5 transition-all" v-else />
              </CollapsibleTrigger>
              <CollapsibleContent>
                <div class="">
                  <SubMenuNavigation
                    :active="page.url.startsWith('backoffice/kategori', 1)"
                    :to="route('backoffice.category.index')"
                  >
                    Data Kategori
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="page.url.startsWith('backoffice/rak', 1)"
                    :to="route('backoffice.rack.index')"
                  >
                    Data Rak
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="page.url.startsWith('backoffice/merk', 1)"
                    :to="route('backoffice.brand.index')"
                  >
                    Data Merk
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="page.url.startsWith('backoffice/barang', 1)"
                    :to="route('backoffice.product.index')"
                  >
                    Data Barang
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="page.url.startsWith('backoffice/perbaikan', 1)"
                    :to="route('backoffice.repair.index')"
                  >
                    Jenis Perbaikan
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="page.url.startsWith('backoffice/pegawai', 1)"
                    :to="route('backoffice.employee.index')"
                  >
                    Data Pegawai
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="page.url.startsWith('backoffice/pemasok', 1)"
                    :to="route('backoffice.supplier.index')"
                  >
                    Data Pemasok
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="page.url.startsWith('backoffice/pelanggan', 1)"
                    :to="route('backoffice.customer.index')"
                  >
                    Data Pelanggan
                  </SubMenuNavigation>
                </div>
              </CollapsibleContent>
            </Collapsible>
            <Collapsible v-model:open="transaksiOpen" v-if="isAdminAndOperator">
              <CollapsibleTrigger
                class="flex w-full items-center text-sm font-medium px-4 py-3 border-l-[6px] border-l-sky-100 hover:bg-gray-100"
                @click="transaksiMenu != transaksiMenu"
              >
                <div
                  class="flex items-center gap-3 text-muted-foreground hover:text-primary"
                >
                  <ClipboardPaste class="h-5 w-5" />
                  Transaksi
                </div>
                <ChevronRightIcon
                  class="ml-auto h-5 w-5 transition-all"
                  v-if="transaksiOpen === false"
                />
                <ChevronDown class="ml-auto h-5 w-5 transition-all" v-else />
              </CollapsibleTrigger>
              <CollapsibleContent>
                <div class="">
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/transaksi/service', 1)
                    "
                    :to="route('backoffice.service.index')"
                  >
                    Service Kendaraan
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/transaksi/penjualan', 1)
                    "
                    :to="route('backoffice.sale.index')"
                  >
                    Penjualan
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/transaksi/pembelian', 1)
                    "
                    :to="route('backoffice.purchase.index')"
                  >
                    Pembelian
                  </SubMenuNavigation>
                </div>
              </CollapsibleContent>
            </Collapsible>
            <MenuNavigation
              :active="page.url.startsWith('backoffice/history/service', 1)"
              :to="route('backoffice.history.service')"
            >
              <History class="h-5 w-5" />
              History Service
            </MenuNavigation>
            <MenuNavigation
              :active="page.url.startsWith('backoffice/mekanik/service', 1)"
              :to="route('backoffice.mechanic.service')"
              v-if="page.props.auth.user.level === 'mekanik'"
            >
              <Wrench class="h-5 w-5" />
              Daftar Perbaikan
            </MenuNavigation>
            <MenuNavigation
              :active="page.url.startsWith('backoffice/mekanik/finished', 1)"
              :to="route('backoffice.mechanic.finished')"
              v-if="page.props.auth.user.level === 'mekanik'"
            >
              <CheckCircle2 class="h-5 w-5" />
              Perbaikan Selesai
            </MenuNavigation>
            <Collapsible v-model:open="laporanOpen" v-if="isAdminAndOperator">
              <CollapsibleTrigger
                class="flex w-full items-center text-sm font-medium px-4 py-3 border-l-[6px] border-l-sky-100 hover:bg-gray-100"
                @click="laporanOpen != laporanOpen"
              >
                <div
                  class="flex items-center gap-3 text-muted-foreground hover:text-primary"
                >
                  <FileText class="h-5 w-5" />
                  Laporan
                </div>
                <ChevronRightIcon
                  class="ml-auto h-5 w-5 transition-all"
                  v-if="laporanOpen === false"
                />
                <ChevronDown class="ml-auto h-5 w-5 transition-all" v-else />
              </CollapsibleTrigger>
              <CollapsibleContent>
                <div class="">
                  <!-- <SubMenuNavigation> Laporan Barang </SubMenuNavigation> -->
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/laporan/lservice', 1)
                    "
                    :to="route('backoffice.report.service')"
                  >
                    Laporan Transaksi Service
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/laporan/lpenjualan', 1)
                    "
                    :to="route('backoffice.report.sale')"
                  >
                    Laporan Penjualan
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/laporan/lpembelian', 1)
                    "
                    :to="route('backoffice.report.purchase')"
                  >
                    Laporan Pembelian
                  </SubMenuNavigation>
                </div>
              </CollapsibleContent>
            </Collapsible>
            <MenuNavigation
              :active="page.url.startsWith('backoffice/stok-opname', 1)"
              :to="route('backoffice.stock-correction.index')"
              v-if="isAdminAndOperator"
            >
              <ClipboardPenLine class="size-5" />
              Stok Opname
            </MenuNavigation>
            <MenuNavigation
              :active="page.url.startsWith('backoffice/jurnal', 1)"
              :to="route('backoffice.jurnal.index')"
              v-if="isAdminAndOperator"
            >
              <HandCoins class="h-5 w-5" />
              Jurnal
            </MenuNavigation>
            <Collapsible
              v-model:open="pengaturanOpen"
              v-if="page.props.auth.user.level === 'administrator'"
            >
              <CollapsibleTrigger
                class="flex w-full items-center text-sm font-medium px-4 py-3 border-l-[6px] border-l-sky-100 hover:bg-gray-100"
                @click="pengaturanOpen != pengaturanOpen"
              >
                <div
                  class="flex items-center gap-3 text-muted-foreground hover:text-primary"
                >
                  <Settings class="h-5 w-5" />
                  Pengaturan
                </div>
                <ChevronRightIcon
                  class="ml-auto h-5 w-5 transition-all"
                  v-if="pengaturanOpen === false"
                />
                <ChevronDown class="ml-auto h-5 w-5 transition-all" v-else />
              </CollapsibleTrigger>
              <CollapsibleContent>
                <div class="">
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/pengaturan/user', 1)
                    "
                    :to="route('backoffice.user.index')"
                  >
                    User
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/pengaturan/sistem', 1)
                    "
                    :to="route('backoffice.setting.index')"
                  >
                    Sistem
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/pengaturan/pembayaran', 1)
                    "
                    :to="route('backoffice.payment.index')"
                  >
                    Jenis Pembayaran
                  </SubMenuNavigation>
                  <SubMenuNavigation
                    :active="
                      page.url.startsWith('backoffice/pengaturan/satuan', 1)
                    "
                    :to="route('backoffice.unit.index')"
                  >
                    Satuan
                  </SubMenuNavigation>
                </div>
              </CollapsibleContent>
            </Collapsible>
          </nav>
        </div>
      </SheetContent>
    </Sheet>
  </div>
</template>
