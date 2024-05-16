<template>
  <div class="h-screen flex flex-col justify-start py-14 items-center" :class="{ boy2: !isGirl, girl2: isGirl }">
    <div class="w-full flex flex-col items-center">
      <div class="opacity-0">
        <img src="@/assets/images/progres2.png" alt="">
      </div>
      <div
          @click="$router.push('/')"
          class="self-start ml-1">
        <img src="@/assets/images/arrow_left.svg" alt="Назад">
      </div>
    </div>
  </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import router from "@/router";
import { useAppStore} from "@/stores/app";

const appStore = useAppStore();
const isGirl = computed(() => appStore.gender === 'girl');


</script>

<style>
.girl2 {
  background: url('@/assets/images/oops_boy.png') center/cover no-repeat;
  min-height: 100vh;
}

.boy2 {
  background: url('@/assets/images/oops_boy.png') center/cover no-repeat;
  min-height: 100vh;
}

</style>
