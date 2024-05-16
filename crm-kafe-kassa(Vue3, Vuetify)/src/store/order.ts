//Order
import { defineStore } from "pinia";
import { ref } from "vue";
import type { TOrder } from "@/models/order";
import axios from "@/plugins/http";

export const useOrderStore = defineStore("order", () => {
  const orders = ref<TOrder[]>([]);
  const order = ref<TOrder>();
  const loading = ref(false);

  const ordersFiltered = ref<TOrder[]>([]);

  async function fetchOrders(): Promise<any> {
    loading.value = true;
    const { data } = await axios.get(`/order/unpaid`);
    if (!data) return;
    orders.value = data;
    loading.value = false;
    console.log(loading);

    ordersFiltered.value = [...orders.value];
  }

  function getOrder(id: number) {
    return orders.value.find((order) => order.id === id) || null;
  }

  async function fetchOrderById(id: number): Promise<any> {
    const { data } = await axios.get(`/order/unpaid/?id=${id}`);

    if (!data) return;

    order.value = data[0];
  }

  return {
    order,
    orders,
    loading,
    ordersFiltered,
    fetchOrders,
    getOrder,
    fetchOrderById,
  };
});
