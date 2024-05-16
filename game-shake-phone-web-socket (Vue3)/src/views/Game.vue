<template>
  <div class="main flex flex-col justify-start py-10 items-center relative">
    <div class="relative z-10">
      <p v-if="countdown !== 4" class="countdown text-center"> {{ countdown }}</p>
      <span  v-if="countdown === 1" class="custom_text text-center">На старт!</span>
      <span v-if="countdown === 2" class="custom_text text-center">Внимание!</span>
      <span v-if="countdown === 3" class="custom_text text-center">Марш!</span>
      <div v-if="countdown === 4" class="custom_text2 text-center mt-16">Тряси!</div>
    </div>

    <div class="text_on_hand mt-">Тряси телефон <br> как можно активней!</div>

    <img class="absolute hand" src="@/assets/images/hand.png" alt="Рука с телефоном">


  </div>
</template>

<script setup>
import router from "@/router";
import { ref, onMounted } from 'vue';
import { socket } from "@/socketIO";
import { useAppStore} from "@/stores/app";

const appStore = useAppStore();
const countdown = ref(1);

onMounted(() => {
  getId();
  setTimeout(() => {
    countdown.value = 2;
    regListener();
  }, 1000);

  setTimeout(() => {
    countdown.value = 3;
  }, 2000);

  setTimeout(() => {
    countdown.value = 4;
  }, 3000);



  socket.on('stop', function (data) {
    window.removeEventListener('devicemotion', handleMotionEvent, true);

    const results = data.body.places;
    console.log(results);
    results.forEach( res => {
        if (res.id === appStore.id) {
          appStore.setPlace(res.place);
          console.log(appStore.place)
        }
        if (res.id === "client1") {
          appStore.setPlace(1);
          console.log(appStore.place)
        }

    } )

    router.push({ name: "result" });
  })
});

const id = ref('');
function getId() {
  id.value = appStore.id;
  if (!id.value) {
    id.value = localStorage.getItem('id');
  }
}

function regListener() {
  window.addEventListener('devicemotion', handleMotionEvent, true);
  // console.log('2 window.addEventListener(\'devicemotion\', handleMotionEvent,')
  // var eventListeners = getEventListeners(document);
  // console.log(eventListeners);
}
function handleMotionEvent(event)
{
  console.log('function handleMotionEvent')
  var x = event.accelerationIncludingGravity.x;
  var y = event.accelerationIncludingGravity.y;
  var z = event.accelerationIncludingGravity.z;
  y = precisionRound(y, 1);

  if(y > 20 || y < -20) sendMove();
}

function precisionRound(number, precision)
{
  const factor = Math.pow(10, precision);
  return Math.round(number * factor) / factor;
}
function sendMove () {
  socket.emit('move', {body: {id: id.value}});
}


</script>

<style>
.main {
  background: url('@/assets/images/game1.png') center/cover no-repeat;
  min-height: 100vh;
}

.countdown {
  color: #7761FB;
  font-family: Montserrat;
  font-size: 128px;
  font-style: normal;
  font-weight: 800;
  line-height: normal;
}

.custom_text {
  color: #7761FB;
  font-family: Montserrat;
  font-size: 36px;
  font-style: normal;
  font-weight: 600;
  line-height: normal;
}

.custom_text2 {
  color: #7761FB;
  font-family: Montserrat;
  font-size: 64px;
  font-style: normal;
  font-weight: 600;
  line-height: normal;
}

.text_on_hand {
  position: relative;
  z-index: 1;
  margin-top: 180px;
  color: #FFF;
  text-align: center;
  font-size: 20px;
  font-style: normal;
  font-weight: 600;
  line-height: normal;
}

.hand {
  scale: 1.7;
  bottom: -3%;
  right: -20%;
  animation: swing 1s ease-in-out infinite; /* Настройка анимации */
  transform-origin: center; /* Ось вращения в центре элемента */
}

/* CSS ключевые кадры для анимации */
@keyframes swing {
  0% { transform: rotate(0deg); }
  50% { transform: rotate(20deg); } /* Угол наклона в середине анимации */
  100% { transform: rotate(0deg); }
}
</style>
