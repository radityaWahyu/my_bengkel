<script setup lang="ts">
import { CircleUser } from "lucide-vue-next";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/shadcn/ui/dropdown-menu";
import { router } from "@inertiajs/vue3";
import { Button } from "@/shadcn/ui/button";
import MobileMainMenu from "./MobileMainMenu.vue";

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
    <div class="w-full flex-1">
      <MobileMainMenu />
    </div>
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
