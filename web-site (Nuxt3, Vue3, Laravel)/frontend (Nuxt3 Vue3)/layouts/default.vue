<template>
  <div class="app-container mx-auto flex flex-col justify-between pt-7">
    <div class="app-container__wrapper">
      <Header/>

      <main class="pb-[90px] sm:pb-[120px] lg:pb-[180px]">
        <slot />
      </main>
    </div>
  </div>
  <Footer/>
</template>

<script setup lang="ts">

</script>

<style scoped>

</style>