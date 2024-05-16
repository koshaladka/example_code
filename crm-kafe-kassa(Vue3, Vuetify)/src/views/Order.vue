<template>
  <div class="bg-white d-flex justify-center d-sm-block pl-sm-10">
    <v-btn
      class="mb-6 mt-6"
      color="red"
      width="300px"
      @pointerdown="$router.push(`/map`)"
    >
      Новый заказ
    </v-btn>
  </div>

  <div class="px-5 py-5">
    <TableOrders></TableOrders>
  </div>
</template>

<script lang="ts" setup>
import TableOrders from "@/components/orders/TableOrders.vue";
</script>

<style lang="sass" scoped></style>
