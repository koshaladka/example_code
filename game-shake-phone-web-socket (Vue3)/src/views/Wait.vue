<template>
  <div class="h-screen flex flex-col justify-between py-12 items-center" :class="{ boy3: !isGirl, girl3: isGirl }">
    <div class="w-full flex flex-col items-center">
      <div>
        <img src="@/assets/images/progres3.png" alt="">
      </div>
      <div
          @click="stop"
          class="self-start ml-4">
        <img src="@/assets/images/arrow_left.svg" alt="Назад">
      </div>
      <div class="mt-7 custom_text text-center">
        Твой уникальный номер
      </div>
      <div class="number mt-2 flex items-center justify-center text-center"
           :class="{ 'n1': playerNumber == 1,
                     'n2': playerNumber == 2,
                     'n3': playerNumber == 3,
                     'n4': playerNumber == 4,
                     'n5': playerNumber == 5}">
        <p>№ {{ playerNumber }}</p>
      </div>
    </div>

    <div class="mb-10 w-full flex flex-col items-center justify-center">
      <div
          class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] text-neutral-100 motion-reduce:animate-[spin_1.5s_linear_infinite]"
          role="status">
        <span
            class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">
          Loading...
        </span>
      </div>
      <div class="mt-2 font-semibold text-white text-center">
        Ожидаем всех игроков
      </div>
    </div>
  </div>
</template>

<script setup>
import {computed, ref, onMounted} from 'vue';
import router from "@/router";
import { useAppStore} from "@/stores/app";
import { socket } from "@/socketIO";

const appStore = useAppStore();

onMounted(() => {
  socket.on('start', function (data) {
      appStore.setIsGame(true);
      router.push({ name: "game" });
    });
  getPlayerNumber();
});

const isGirl = computed(() => appStore.gender === 'girl');
const playerNumber = ref('');
function getPlayerNumber() {
  playerNumber.value = appStore.playerNumber;
  if (!playerNumber.value) {
    playerNumber.value = localStorage.getItem('playerNumber');
  }
}
function stop() {
  router.push({ name: "home" });
}

</script>

<style scoped>
.boy3 {
  background: url('@/assets/images/wait_boy.png') center/cover no-repeat;
  min-height: 100vh;
}

.girl3 {
  background: url('@/assets/images/wait_girl.png') center/cover no-repeat;
  min-height: 100vh;
}

.custom_text {
  color: #616161;
  font-family: Montserrat;
  font-size: 20px;
  font-style: normal;
  font-weight: 600;
  line-height: normal;
}

</style>
