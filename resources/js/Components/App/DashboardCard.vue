<script setup lang="ts">
import { Skeleton } from "@/shadcn/ui/skeleton";
import { Card, CardContent, CardHeader, CardTitle } from "@/shadcn/ui/card";
import { useAttrs } from "vue";

const props = defineProps<{
  loading?: boolean;
  title: string;
  content: any;
  description: string;
}>();

const attrs = useAttrs();
</script>
<template>
  <Card class="lg:shrink lg:w-72 w-full" :class="attrs.class">
    <CardHeader
      class="flex flex-row items-center justify-between space-y-0 pb-1"
    >
      <CardTitle class="text-sm font-medium"> {{ title }} </CardTitle>
      <slot name="icon"></slot>
    </CardHeader>
    <CardContent>
      <div class="py-4" v-if="loading">
        <Skeleton class="h-2 w-full" />
      </div>
      <div class="text-2xl font-bold" v-else>{{ content }}</div>
      <p class="text-xs text-muted-foreground">{{ description }}</p>
    </CardContent>
  </Card>
</template>
