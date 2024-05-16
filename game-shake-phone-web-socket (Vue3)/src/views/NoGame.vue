<template>
  <div class="h-screen flex flex-col justify-between py-6 items-center main" >

  </div>
</template>

<script setup>
import {computed, ref, onMounted} from 'vue';
import router from "@/router";
import { socket } from "@/socketIO";

// не используется страница
onMounted(() => {
    socket.on('init', function (data) {
    router.push({ name: "home" });
  });
});

</script>

<style scoped>
.main {
  background: url('@/assets/images/no_game.png') center/cover no-repeat;
  min-height: 100vh;
}

</style>
