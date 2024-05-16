<template>
  <div>
    <div class="d-flex justify-space-between mt-16 input-wrapper">
      <div>
        <div class="text-subtitle-2 input-text">Сумма к оплате</div>
        <div class="elevation-1 input-custom mt-1">
          {{ orderSum }}
        </div>
      </div>
      <div>
        <div class="text-subtitle-2">Сумма от гостя</div>
        <input class="elevation-1 input-custom mt-1" v-model="rub" />
      </div>
    </div>
    <Pinpad @update="onPinpadKey" class="mt-15"> </Pinpad>
  </div>
</template>

<script lang="ts" setup>
import Pinpad from "@/components/otp/Pinpad.vue";
import { ref, watch } from "vue";

let props = defineProps({
  sumFromClient: {
    type: String,
    required: true,
  },

  orderSum: {
    type: Number,
    default: 2,
    required: true,
  },
});

let rub = ref(props.sumFromClient);
watch(props, (newValue) => {
  rub.value = props.sumFromClient;
});

function onPinpadKey(key: number | string) {
  if (typeof key === "number") {
    rub.value = rub.value + key;
  }
  if (key === "backspace") {
    rub.value = rub.value.slice(0, -1);
  }
  if (key === ".") {
    if (rub.value.includes(".")) {
      return;
    }
    rub.value = rub.value + key;
  }
  if (rub.value.indexOf(".") != -1) {
    rub.value = rub.value.substring(0, rub.value.indexOf(".") + 3);
    return;
  }
}

const emit = defineEmits(["update"]);

watch(rub, (newValue) => {
  emit("update", newValue);
});
</script>

<style lang="sass" scoped>
.input-custom
  width: 150px
  height: 50px
  outline: none
  padding: 5px 10px
  font-size: 30px
  font-style: normal
  font-weight: 500
  text-align: center

.input-wrapper
  width: max-content
  gap: 20px
</style>
