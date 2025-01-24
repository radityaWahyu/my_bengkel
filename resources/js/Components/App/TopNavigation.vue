<script setup lang="ts">
import {
  Bell,
  CircleUser,
  Home,
  LineChart,
  Menu,
  Package,
  Package2,
  Search,
  ShoppingCart,
  Users,
  Wrench,
} from "lucide-vue-next";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu";
import { Sheet, SheetContent, SheetTrigger } from "@/shadcn/ui/sheet";
import { router } from "@inertiajs/vue3";
import { Button } from "@/shadcn/ui/button";

function logout() {
  localStorage.setItem("isAuthenticated", `false`);
  router.post(route("backoffice.auth.logout"), {}, { replace: true });
}

function editProfil() {
  router.get(route("backoffice.profil.index"));
}
</script>
<template>
  <header
    class="flex h-14 items-center gap-4 border-b bg-white py-4 px-4 lg:px-6"
  >
    <!-- <Sheet>
      <SheetTrigger as-child>
        <Button variant="outline" size="icon" class="shrink-0 md:hidden">
          <Menu class="h-5 w-5" />
          <span class="sr-only">Toggle navigation menu</span>
        </Button>
      </SheetTrigger>
      <SheetContent side="left" class="flex flex-col">
        <nav class="grid gap-2 text-lg font-medium">
          <a href="#" class="flex items-center gap-2 text-lg font-semibold">
            <Package2 class="h-6 w-6" />
            <span class="sr-only">Acme Inc</span>
          </a>
          <a
            href="#"
            class="mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2 text-muted-foreground hover:text-foreground"
          >
            <Home class="h-5 w-5" />
            Dashboard
          </a>
          <a
            href="#"
            class="mx-[-0.65rem] flex items-center gap-4 rounded-xl bg-muted px-3 py-2 text-foreground hover:text-foreground"
          >
            <ShoppingCart class="h-5 w-5" />
            Orders
          </a>
          <a
            href="#"
            class="mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2 text-muted-foreground hover:text-foreground"
          >
            <Package class="h-5 w-5" />
            Products
          </a>
          <a
            href="#"
            class="mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2 text-muted-foreground hover:text-foreground"
          >
            <Users class="h-5 w-5" />
            Customers
          </a>
          <a
            href="#"
            class="mx-[-0.65rem] flex items-center gap-4 rounded-xl px-3 py-2 text-muted-foreground hover:text-foreground"
          >
            <LineChart class="h-5 w-5" />
            Analytics
          </a>
        </nav>
      </SheetContent>
    </Sheet> -->
    <div class="w-full flex-1"></div>
    <div class="flex items-center gap-3 bg-sky-50 px-2 py-1 rounded">
      <div class="text-right text-sky-900">
        <p class="text-sm capitalize font-medium">
          {{ $page.props.auth.user.name }}
        </p>
        <p class="text-xs capitalize">
          {{ $page.props.auth.user.username }} |
          {{ $page.props.auth.user.level }}
        </p>
      </div>
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button
            variant="secondary"
            size="icon"
            class="rounded-full bg-sky-200 text-sky-600"
          >
            <CircleUser class="h-5 w-5" />
            <span class="sr-only">Toggle user menu</span>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuItem @click="editProfil">Edit Profil</DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuItem @click="logout">Logout</DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </header>
</template>
