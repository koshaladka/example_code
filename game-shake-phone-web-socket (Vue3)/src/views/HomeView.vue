<template>
  <main>
    <div v-if="isRegistration" class="main flex flex-col justify-end">

      <div v-if="isOpen"
          @click="getPermission"
          class="modal font-semibold text-white text-center bg-[#7761FB] py-32 px-5 w-2/3">
         Нажмите, <br> чтобы разрешить <br> браузеру <br> фиксировать <br> встряску
      </div>

      <div
          @click="$router.push('/gender')"
          class="font-semibold text-white text-center mb-28 rounded-3xl bg-[#7761FB] py-3 px-10 mx-auto">
        Начать
      </div>
    </div>
    <div v-if="!isRegistration" class="h-screen flex flex-col justify-between py-6 items-center nogame" >
<!--      {{ message1 }}-->
    </div>
  </main>
</template>


<script setup>
import router from "@/router";
import { ref, onMounted } from 'vue';
import { socket } from "@/socketIO";
import { useAppStore} from "@/stores/app";

onMounted(() => {
  socket.emit('status', {body: null}, (response)=>{
    if (response.body.status !== 1) {
      isRegistration.value = false;
    }
  })
  socket.on('init', function (data) {
    message1.value = 'Пришел Инит'
    isRegistration.value = true;
  });
  CheckPermission();
});
const isRegistration = ref(true);
const isOpen = ref(false);
const message1 = ref('пусто');
const message2 = ref('пусто');
const message3 = ref('пусто');
const message4 = ref('пусто');

function CheckPermission()
{
  if ( typeof( DeviceOrientationEvent ) === "undefined" ||
      typeof( DeviceOrientationEvent.requestPermission ) !== "function" )
  {
    if (window.DeviceOrientationEvent)
    {
      // window.addEventListener('devicemotion', handleMotionEvent, true);
      message1.value = 'первая проверка успешна';
      console.log('первая проверка успешна');

    }
    else
    {
      message1.value = 'первая проверка не успешна, надо зайти с другого браузера'
      router.push({ name: "browser_problem" });
      console.log('первая проверка не успешна, надо зайти с другого браузера')
    }
  }
  else
  {
    message2.value = 'НУЖНО РАЗРЕШЕНИЕ';
    isOpen.value = true;
    console.log('НУЖНО РАЗРЕШЕНИЕ');

  }
}

function getPermission(){
  setTimeout(()=> {
    isOpen.value = false;
  }, 1000)
  if ( typeof( DeviceOrientationEvent ) !== "undefined" && 
                typeof( DeviceOrientationEvent.requestPermission ) === "function" ) 
        {
            message3.value = "ПРОШУ РАЗРЕШЕНИЯ БРАУЗЕРА";

            DeviceOrientationEvent.requestPermission().then( response =>
            {
                console.log(response);
                // (optional) Do something after API prompt dismissed.
                if ( response == "granted" ) {
                  message4.value = "ЕСТЬ РАЗРЕШЕНИЕ";
                  // window.addEventListener('devicemotion', handleMotionEvent, true);
                }
                else  message4.value = "НЕТ РАЗРЕШЕНИЯ";
            }).catch( console.error )
          // window.addEventListener('devicemotion', handleMotionEvent, true);
        }
        else
        {
           message3.value = "зашли в else ";
            if (window.DeviceOrientationEvent)
            {
                message4.value = "регистрация из else ";
            }
            else 
            {        
                message4.value = "else не помог";
            }
        }

}

// function handleMotionEvent(event)
// {
//   console.log('1 экран function handleMotionEvent')
//   /*var x = event.acceleration.x;
//   var y = event.acceleration.y;
//   var z = event.acceleration.z;	*/
//   var x = event.accelerationIncludingGravity.x;
//   var y = event.accelerationIncludingGravity.y;
//   var z = event.accelerationIncludingGravity.z;
//
// }
</script>

<style lang="scss" scoped>
.main {
  background: url('@/assets/images/backgroundHome.png') center/cover no-repeat;
  min-height: 100vh;
}

.nogame {
  background: url('@/assets/images/no_game.png') center/cover no-repeat;
  min-height: 100vh;
}

.modal {
  position: absolute;
  width: 100%;
  height: 100vh;
  display: flex;
  align-content: center;
  font-size: 35px;
  line-height: 140%;
  letter-spacing: 1.16px;
}
</style>
