<template>
  <div class="wrapper-btn">
    <v-btn
      position="absolute"
      class="header_btn ml-10 mt-2"
      color="grey"
      @pointerdown="$router.push(`/order`)"
    >
      Назад
    </v-btn>
    <div class="header_title">
      Заказ {{ order?.number }}<br />
      <!-- Стол {{ order?.table_id }} <br /> -->
      Стол {{ order?.table_id }}, {{ order?.customer?.name || "" }}
    </div>
  </div>

  <div class="d-flex page_wrapper">
    <div class="bg-white px-10 py-5 mb-10 d-flex justify-center order-1">
      <PinpadRub
        class="mx-auto"
        :sumFromClient="sumFromClient"
        :orderSum="orderSum"
        @update="updateSumFromClient"
      ></PinpadRub>
    </div>
    <div
      class="column_wrapper px-md-10 py-md-10 px-5 pt-10 mb-10 font-weight-bold d-flex justify-center"
    >
      <div class="content_wrapper text-h3">
        <div class="text-h5 d-flex align-center">
          Итого:
          <div class="total-sum ml-5">{{ getOrderAmount }} ₽</div>
        </div>
        <div
          v-show="payType.deposit.part"
          class="w-100 btn-custom--error text-body-2 px-3 py-2 text-center mt-3"
        >
          На депозитном счете недостаточно средств для закрытия заказа. Выберите
          дополнительный способ оплаты.
        </div>
        <button
          class="btn-custom mt-6 d-flex justify-space-between align-center elevation-1 px-4 py-2 text-h6 rounded"
          :class="{
            'btn-custom--active': (payType.cash && isEnoughSum) || change == 0,
            'btn-custom--error': payType.cash && !isEnoughSum,
          }"
          @pointerdown="choosePayType('cash')"
        >
          <div class="text-h6">Наличными</div>
          <div class="text-h5">
            <div>{{ sumFromClient }} ₽</div>
            <div v-show="payType.cash && change !== 0" class="text-subtitle-1">
              <div v-show="isEnoughSum">Сдача {{ change }} ₽</div>
              <div v-show="!isEnoughSum">Не хватает {{ change }} ₽</div>
            </div>
          </div>
        </button>
        <button
          class="btn-custom mt-5 d-flex justify-space-between align-center elevation-1 px-4 py-2 text-h6 rounded"
          :class="payType.card ? 'btn-custom--active' : ''"
          @pointerdown="choosePayType('card')"
        >
          Картой
          <div class="text-h5">{{ payType.card }} ₽</div>
        </button>
        <button
          class="btn-custom mt-5 d-flex justify-space-between align-center elevation-1 px-4 py-2 text-h6 rounded"
          :class="{
            'btn-custom--error': payType.deposit.part,
            'btn-custom--active': payWithDeposit,
          }"
        >
          <div class="deposit_title" @pointerdown="choosePayType('deposit')">
            Депозит ({{ order?.customer?.deposit }} ₽)
          </div>

          <div v-if="payType.deposit.part" class="text-h5 d-flex align-center">
            {{ payType.deposit.part }} ₽
            <v-icon
              icon="mdi-close-circle"
              color="grey-darken-3"
              class="ml-3"
              size="x-large"
              @pointerdown="choosePayType('depositCancel')"
            ></v-icon>
          </div>
          <div v-show="payType?.deposit.total" class="text-h5">
            {{ payType?.deposit.total }} ₽
            <v-icon
              icon="mdi-close-circle"
              color="red"
              class="ml-3"
              size="x-large"
              @pointerdown="choosePayType('depositCancel')"
            ></v-icon>
          </div>
        </button>
        <div class="d-flex justify-space-between mt-8">
          <v-btn class="w-50 btn-font" @click="dialogCancel = true">
            Закрыть без оплаты
          </v-btn>
          <v-btn
            @click="isFiscalNumber(), (isKKMPlaceholderPopupOpen = true)"
            color="success"
            class="w-50 ml-3"
            :disabled="isPayDisabled"
          >
            Оплата
          </v-btn>
        </div>
      </div>
    </div>
  </div>

  <v-dialog v-model="dialogCancel" width="auto">
    <div class="bg-white text-h6 px-10 py-10">
      Закрыть заказ без оплаты?
      <div class="d-flex justify-space-between mt-5">
        <v-btn @pointerdown="dialogCancel = false"> Отмена </v-btn>
        <v-btn @pointerdown="$router.push(`/order`)" color="red" class="ml-5">
          Подтвердить
        </v-btn>
      </div>
    </div>
  </v-dialog>

  <KKMPlaceholderPopup
    v-model="isKKMPlaceholderPopupOpen"
    :text="dialogKkmMessage"
    @close="isKKMPlaceholderPopupOpen = false"
  ></KKMPlaceholderPopup>
</template>

<script lang="ts" setup>
import PinpadRub from "@/components/orders/PinpadRub.vue";
import { ref, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";

import { useOrderStore } from "@/store/order";
import { useKkmStore } from "@/store/kkm";
import { storeToRefs } from "pinia";
import KKMPlaceholderPopup from "@/components/KKMPlaceholderPopup.vue";
import router from "@/router";
//TODO отправляет на страницу заказы из стор

const dialogCancel = ref(false);
const storeOrder = useOrderStore();
const storeKkm = useKkmStore();
const route = useRoute();
const id_order = +route.params.id;
const { order } = storeToRefs(storeOrder);
const { dialogKkmMessage } = storeToRefs(storeKkm);
const isKKMPlaceholderPopupOpen = ref(false);

let orderSum = 0;
let depositSum = 0;
const roundSum = ref(0);
const sumFromClient = ref(String(roundSum.value));

const getOrderAmount = computed(() => {
  if (!order.value) return 0;

  return order.value.amount - order.value.discount;
});

const isEnoughSum = ref(true);

//choose pay type
const payType = ref({
  cash: orderSum,
  card: 0,
  deposit: {
    part: 0,
    total: 0,
  },
});

const change = computed(() => {
  if (+sumFromClient.value - orderSum + payType.value.deposit.part <= 0) {
    return Math.abs(
      +sumFromClient.value - orderSum + payType.value.deposit.part
    );
  } else {
    return +sumFromClient.value - orderSum + payType.value.deposit.part;
  }
});

watch(change, () => {
  if (+sumFromClient.value - orderSum + payType.value.deposit.part <= 0) {
    isEnoughSum.value = false;
  } else {
    isEnoughSum.value = true;
  }
});

onMounted(async () => {
  await storeOrder.fetchOrderById(id_order);

  next();
  storeKkm.reVerifyServer();
});

function next() {
  orderSum = getOrderAmount.value;
  depositSum = order.value?.customer?.deposit || 0;
  countRoundSum(orderSum);
  updateSumFromClient(String(roundSum.value));
  choosePayType("cash");
  isEnoughSum.value = true;
}

function countRoundSum(n: number) {
  roundSum.value = Math.round(n / 100) * 100;
  if (+roundSum.value < orderSum) {
    roundSum.value = roundSum.value + 100;
  }
}

function updateSumFromClient(a: string) {
  sumFromClient.value = a;
}


const payWithDeposit = ref(false);

function choosePayType(type: string) {
  if (type === "cash") {
    if (payType.value.deposit.part || payType.value.deposit.total) {
      payType.value.card = 0;
      payType.value.cash =
        orderSum - payType.value.deposit.part - payType.value.deposit.total;
      if (payType.value.cash === 0) {
        return;
      }
      countRoundSum(payType.value.cash);
      sumFromClient.value = String(roundSum.value);
      payWithDeposit.value = true;
      return;
    }
    payType.value.cash = orderSum;
    payType.value.card = 0;
    sumFromClient.value = String(roundSum.value);
  }
  if (type === "card") {
    if (payType.value.deposit.part || payType.value.deposit.total) {
      payType.value.cash = 0;
      payType.value.card =
        orderSum - payType.value.deposit.part - payType.value.deposit.total;
      sumFromClient.value = "0";
      payWithDeposit.value = true;
      return;
    }
    payType.value.card = orderSum;
    payType.value.cash = 0;
    sumFromClient.value = "0";
  }
  if (type === "deposit") {
    if (depositSum >= orderSum) {
      payType.value.cash = 0;
      payType.value.card = 0;
      payType.value.deposit.total = orderSum;
      sumFromClient.value = "0";
      payWithDeposit.value = true;
      console.log(payWithDeposit.value);
    }
    if (depositSum < orderSum) {
      payType.value.cash = 0;
      payType.value.card = 0;
      payType.value.deposit.part = depositSum;
      sumFromClient.value = "0";
    }
  }
  if (type === "depositCancel") {
    payType.value.deposit.total = 0;
    payType.value.deposit.part = 0;
    payType.value.card = 0;
    payType.value.cash = orderSum;
    // countRoundSum(payType.value.cash);
    sumFromClient.value = String(roundSum.value);
    payWithDeposit.value = false;
    countRoundSum(payType.value.cash);
  }
}

/* Механика работы сервер-ккм
0.1 isFiscalNumber - Подготовка проверка в LS есть ли успешный чек по этому заказу с неудачным коммитом
0.2  createRequestPayments() - Подготовка: формирование запроса на сервер для оплаты
1 -> Работа Store. makePayment()
*/

function isFiscalNumber() {
  let validateId = false;
  try {
    const fiscalNumber = localStorage.getItem("fiscalNumber");
    if (fiscalNumber !== null) {
      const dataLS = JSON.parse(fiscalNumber);
      validateId = dataLS.some((item: { order_id: number }) => item.order_id === id_order);
    }
  } catch (error) {
    console.log(error);
  }

  if (validateId) {
    dialogKkmMessage.value = `Чек уже напечатан`;
  } else {
    //если чек не напечатан, формируем запрос на оплату
    createRequestPayments();

  }
}
//0.2 Формирование запроса на сервер для оплаты
async function createRequestPayments() {
  const bodyRequest: any = {
    order_id: id_order,
    payments: [],
  };
  const payments_item: any = {
    pay_type: "",
    amount: 0,
  };
  if (payType.value.cash) {
    const payments_item: any = {
      pay_type: "",
      amount: 0,
    };
    payments_item.pay_type = "cash";
    payments_item.amount = payType.value.cash;
    bodyRequest.payments.push(payments_item);
  }
  if (payType.value.card) {
    const payments_item: any = {
      pay_type: "",
      amount: 0,
    };
    payments_item.pay_type = "electronically";
    payments_item.amount = payType.value.card;
    bodyRequest.payments.push(payments_item);
  }
  if (payType.value.deposit.part) {
    const payments_item: any = {
      pay_type: "",
      amount: 0,
    };
    payments_item.pay_type = "prepaid";
    payments_item.amount = payType.value.deposit.part;
    bodyRequest.payments.push(payments_item);
  }
  if (payType.value.deposit.total) {
    const payments_item: any = {
      pay_type: "",
      amount: 0,
    };
    payments_item.pay_type = "prepaid";
    payments_item.amount = payType.value.deposit.total;
    bodyRequest.payments.push(payments_item);
  }

  await storeKkm.makePayment(
    bodyRequest,
    "/cashdesk/pay/start",
    "/cashdesk/pay/commit"
  );
  if (storeKkm.isKkmSuccess && storeKkm.isServerSuccess) {
    storeKkm.delay(3000);
    router.push(`/order`);
  }
  await storeOrder.fetchOrderById(id_order);
}

const isPayDisabled = computed(() => {
  if (payType.value.cash) return !isEnoughSum.value;

  if (payType.value.card) return !payType.value.card;

  if (payType.value.deposit) return false;

  return true;
});
</script>

<style lang="sass" scoped>
.wrapper-btn
  position: relative
  background-color: #ffffff
  display: flex
  justify-content: center
  padding: 10px

.header_btn
  left: 0
  width: 300px

.header_title
  text-align: center
  font-weight: 600
  font-size: 18px
  line-height: 22px


.column_wrapper
  flex-grow: 1
  order: 2

.total-sum
  font-size: 56px
  font-style: normal
  font-weight: 500
  line-height: normal

.content_wrapper
  padding: 40px
  background: #FFFFFF
  box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1)
  border-radius: 10px
  display: flex
  flex-direction: column
  flex-grow: 1
  max-width: 600px

.btn-custom
  height: 70px
  &:hover
    background-color: #F6F6F6

.btn-custom--active
  background: #C5FAC6 !important

.btn-custom--error
  background: #FDF2E7

.deposit_title
  flex-grow: 1
  text-align: start

@media (max-width: 850px)
  .page_wrapper
    flex-direction: column
  .column_wrapper
    order: 0
  .header_btn
    width: 200px

@media (max-width: 920px)
  .total-sum
    font-size: 40px

@media (max-width: 450px)
    .total-sum
      font-size: 30px
    .btn-font
      font-size: 13.5px !important
</style>
