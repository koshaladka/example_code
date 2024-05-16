<template>
  <div>
    <div v-show="loading">
      <v-progress-circular
        v-show="loading"
        class="mx-auto my-10 d-block text-center"
        indeterminate
        size="64"
        color="red"
      ></v-progress-circular>
    </div>
    <div v-show="!loading">
      <div class="d-none d-sm-block mt-3 px-5">
        <!-- desktop -->
        <v-table fixed-header class="table_common" density="compact">
          <thead height="52" class="thead bg-white">
            <tr>
              <th class="text-left font-weight-bold">Открыт</th>
              <th class="text-left font-weight-bold">№ заказа, стол</th>
              <th class="text-left font-weight-bold">Статус</th>
              <th class="text-left font-weight-bold">
                <div class="ml-order-content title-order-content">
                  <div>Заказ</div>
                  <div>Сумма</div>
                </div>
              </th>
              <th class="text-left font-weight-bold"></th>
            </tr>
          </thead>
          <div class="mt-10"></div>
          <tbody>
            <tr v-for="item in orders" :key="item.id">
              <td>{{ item.created_at.substring(11, 16) }}</td>
              <td>{{ item.number }} / стол {{ item.table_id }}</td>
              <td>{{ item.status_name }}</td>
              <td class="expansion-column">
                <v-expansion-panels>
                  <v-expansion-panel elevation="0" class="bg-grey">
                    <v-expansion-panel-title>
                      <div class="d-flex justify-space-between order_content">
                        <div class="order_content-text">
                          Количество позиций: {{ item.products?.length }}
                        </div>
                        <div class="font_price">{{ item.amount }} ₽</div>
                      </div>
                    </v-expansion-panel-title>
                    <v-expansion-panel-text>
                      <div
                        v-for="product in item.products"
                        :key="product.id"
                        class="text-descr mt-1"
                      >
                        <div class="d-flex justify-space-between order_content">
                          <div class="order_content-text">
                            {{ product.name }} х
                            {{ product.pivot.quantity }} шт.
                          </div>
                          <div>{{ product.pivot.total }} ₽</div>
                        </div>
                      </div>
                    </v-expansion-panel-text>
                  </v-expansion-panel>
                </v-expansion-panels>
              </td>
              <td>
                <button
                  v-if="!isMobile"
                  class="mx-auto my-auto btn_custom bg-red rounded d-block text-center mb-8"
                  @pointerdown="$router.push(`/order/${item.id}`)"
                >
                  Перейти к оплате
                </button>
              </td>
            </tr>
          </tbody>
        </v-table>
      </div>
      <!-- mobile -->
      <div
        v-for="item in orders"
        :key="item.id"
        class="d-sm-none mb-3 order_item"
      >
        <div class="d-flex justify-space-between">
          <div class="order_title_mob">
            № {{ item.id }}, стол {{ item.table_id }} | {{ item.status_name }}
          </div>
          <div class="order_title_mob mb-3 text-end">
            {{ item.created_at.substring(11, 16) }}
          </div>
        </div>
        <div>
          <v-expansion-panels>
            <v-expansion-panel elevation="0" class="bg-grey">
              <v-expansion-panel-title>
                <div class="order_content d-flex justify-space-between">
                  <div class="mr-5">
                    Количество позиций: {{ item.products?.length }}
                  </div>
                  <div class="font_price">{{ item.amount }} ₽</div>
                </div>
              </v-expansion-panel-title>
              <v-expansion-panel-text>
                <div
                  v-for="product in item.products"
                  :key="product.id"
                  class="text-descr auto-width mt-1"
                >
                  <div class="d-flex justify-space-between order_content">
                    <div class="order_content-text">
                      {{ product.name }} х {{ product.pivot.quantity }} шт.
                    </div>
                    <div class="order_content__price">
                      {{ product.pivot.total }} ₽
                    </div>
                  </div>
                </div>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, computed } from "vue";
import { useOrderStore } from "@/store/order.js";
import { storeToRefs } from "pinia";

const store = useOrderStore();

onMounted(async () => {
  await store.fetchOrders();

  /* widthExpansionPanel(); */
});

const { orders } = storeToRefs(store);
const { loading } = storeToRefs(store);

const isMobile = computed(() => {
  return localStorage.getItem("is_mobile") === "true";
});
</script>

<style lang="sass" scoped>
.table_common, th
    background-color: transparent

th, td
  color: black !important
  font-size: 12px

td
  vertical-align: initial

.expansion-column
  @media (min-width: 1200px)
    width: 400px !important

.thead
  box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1)
.v-table > .v-table__wrapper > table > thead > tr > th
  padding: 0 5px

.v-table > .v-table__wrapper > table > thead > tr > th:first-child
    border-top-left-radius: 10px
    border-bottom-left-radius: 10px
    padding-left: 20px
.v-table > .v-table__wrapper > table > thead > tr > th:last-child
    border-top-right-radius: 10px
    border-bottom-right-radius: 10px
.v-table > .v-table__wrapper > table > tbody > tr > td
  padding: 0 5px

.v-table > .v-table__wrapper > table > tbody > tr > td:first-child
    padding-left: 20px

.v-expansion-panel-title
  padding: 0
  font-size: 14px

.v-expansion-panel-text :deep(.v-expansion-panel-text__wrapper)
  padding: 0 !important
  margin-bottom: 30px

.v-expansion-panel-title--active :deep(.v-expansion-panel-title__overlay)
  opacity: 0 !important

.v-expansion-panel-title--active :deep(.v-icon)
  color: #1AB900

.text-descr
  font-weight: 400
  line-height: 22px
  color: #212121

.font_price
  font-size: 14px

.order_content__price
  font-weight: 400
  font-size: 14px
  color: #575757

.btn_custom
  font-size: 12px
  cursor: pointer
  font-weight: 600
  width: max-content

.order_item
  border-bottom: 1px solid #E8E8E8

.product_item
  font-size: 12px !important

.order_title_mob
  font-weight: 600
  font-size: 16px
  line-height: 19px
  color: #212121

.order_content
  width: 270px

.order_content-text
  font-size: 14px

.title-order-content
  display: flex
  gap: 50px

@media (min-width: 340px)
  .font_price
    font-size: 16px

@media (min-width: 600px)
  td, th, .v-expansion-panel-title, .text-descr
    font-size: 14px
    color: #212121

  .order_content-text
    max-width: 90px
  .btn_custom
    padding: 5px

  .font_price
    min-width: 90px
  .order_content
    width: unset

@media (min-width: 700px)
  td, .v-expansion-panel-title, .text-descr
    font-size: 16px

  .order_content
    width: 260px
  .order_content-text
    max-width: 150px
  .title-order-content
    gap: 170px

@media (min-width: 750px)
  .order_content-text
    max-width: unset

@media (min-width: 950px)


  .order_content
    width: 300px
  .title-order-content
    gap: 200px


@media (min-width: 1050px)
  td, .font_price
    font-size: 20px
  .btn_custom
    font-size: 16px
    padding: 10px
  .text-descr, .v-expansion-panel-title, .order_content-text
    font-size: 18px
    color: #212121

  .ml-order-content, .v-expansion-panels
    margin-left: 80px
  .order_content
    width: 350px
  .v-expansion-panels
    max-width: 400px
  .title-order-content
    gap: 250px
</style>
