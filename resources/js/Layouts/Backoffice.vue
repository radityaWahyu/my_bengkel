<script setup lang="ts">
import { BadgeInfo, TriangleAlert } from "lucide-vue-next";
import { ref, watch } from "vue";

import LogoApp from "@/Components/backoffice/app/LogoApp.vue";
import LeftNavigationApp from "@/Components/backoffice/app/LeftNavigationApp.vue";
import RightHeaderApp from "@/Components/backoffice/app/RightHeaderApp.vue";
import Button from "@/shadcn/ui/button/Button.vue";

const props = defineProps<{ flash: any }>();
const showSuccessAlert = ref<boolean>(false);
const showErrorAlert = ref<boolean>(false);

watch(
    () => props.flash,
    (alert) => {
        console.log(!!alert.success);
        if (!!alert.success) {
            showSuccessAlert.value = !!alert.success;
            setTimeout(() => (showSuccessAlert.value = false), 3000);
        } else {
            showSuccessAlert.value = false;
        }

        if (!!alert.error) {
            showErrorAlert.value = !!alert.error;
        } else {
            showErrorAlert.value = false;
        }
    },
    { immediate: true }
);
</script>

<template>
    <div
        class="grid min-h-screen w-full md:grid-cols-[250px_1fr] lg:grid-cols-[250px_1fr] font-poppins antialiased bg-gray-50"
    >
        <!-- start left component -->
        <div class="hidden border-r-2 border-slate-400 bg-slate-800 md:block">
            <div class="flex h-full max-h-screen flex-col gap-2">
                <!-- start logo component -->
                <logo-app />
                <!-- end logo component -->
                <!-- start left navigation component -->
                <left-navigation-app />
                <!-- end left navigation component -->
            </div>
        </div>
        <!-- end left component -->
        <!-- start right component -->
        <div
            class="relative flex flex-col overflow-y-scroll h-screen bg-gray-50"
        >
            <!-- start header right component -->
            <right-header-app />
            <!-- end header right component -->
            <!-- start main content component -->
            <!-- start alert component -->
            <Transition>
                <div
                    class="absolute z-10 top-14 w-full inline-flex items-center justify-around py-3 px-4 bg-blue-100 border-b-2 border-blue-200"
                    v-if="showSuccessAlert"
                >
                    <div class="flex items-center gap-4">
                        <BadgeInfo class="w-6 h-6" />
                        <div class="text-sm w-[500px]">
                            <h1 class="font-normal">Informasi</h1>
                            <p class="font-medium">
                                {{ flash.success }}
                            </p>
                        </div>
                    </div>
                    <Button
                        variant="secondary"
                        size="sm"
                        @click="showSuccessAlert = false"
                    >
                        Tutup
                    </Button>
                </div>
            </Transition>
            <Transition>
                <div
                    class="absolute z-10 top-14 w-full inline-flex items-center justify-around py-3 px-4 bg-red-100 border-b-2 border-red-200"
                    v-if="showErrorAlert"
                >
                    <div class="flex items-center gap-4">
                        <TriangleAlert class="w-6 h-6" />
                        <div class="text-sm w-[500px]">
                            <h1 class="font-normal">Peringatan</h1>
                            <p class="font-medium">
                                {{ flash.error }}
                            </p>
                        </div>
                    </div>
                    <Button
                        variant="secondary"
                        size="sm"
                        @click="showErrorAlert = false"
                    >
                        Tutup
                    </Button>
                </div>
            </Transition>
            <!-- end alert component -->
            <main
                :class="{ 'mt-20': showErrorAlert || showSuccessAlert }"
                class="flex flex-1 flex-col gap-4 p-4 lg:gap-6 lg:p-6"
            >
                <slot />
            </main>
            <!-- end main content component -->
        </div>
        <!-- end right component -->
    </div>
</template>
<style lang="scss" scoped>
.v-enter-active,
.v-leave-active {
    transition: opacity 0.3s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
