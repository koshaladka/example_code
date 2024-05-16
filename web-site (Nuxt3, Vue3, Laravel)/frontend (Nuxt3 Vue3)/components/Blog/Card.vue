<template>
  <div v-for="item in data"
       :key = item.id>
    <NuxtLink
              class="cursor-pointer"
              :to="`/blog/${item.tag.code}/${item.slug}`">
      <div class="card__image">
        <img :src="item.image_sm" :alt="item.title">
      </div>
      <NuxtLink
          :to="`/blog/${item.tag.code}`"
          class="card_tag mt-3.5 hover" >
        <p class="tag hover">{{ item.tag.name }}</p>
        <p class="date">{{ item.date }}</p>
      </NuxtLink>
      <div class='card-title mt-2.5 t_1 hover'>
        {{ item.title }}
      </div>
    </NuxtLink>
  </div>

</template>

<script setup lang="ts">

let props = defineProps({
  data: {
    type: Array,
    required: true,
  },
});

</script>

<style lang="scss" scoped>
$xlBreakpoint: 1280px;
$mdBreakpoint: 768px;
$smBreakpoint: 640px;
$mobBreakpoint: 410px;


  a {
    display: block;
  }
  .card_tag {
    display: flex;
    justify-content: space-between;
  }
  .card__image {
    border-radius: 10px;
    aspect-ratio: 16/9
  }
  .date {
    color: $grey_secondary;
  }


</style>