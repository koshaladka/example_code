<template>
  <div v-if="data">
    <p class="h_3 mb-section__title">
      <slot></slot>
    </p>

    <div class="news__wrapper-card">
      <BlogCard :data = 'filteredData'/>
    </div>

    <NuxtLink to="/blog/news?page=1" class="btn button_text">
      Все новости
    </NuxtLink>
  </div>
</template>

<script setup lang="ts">
import {useBlogStore} from "~/stores/blog";

let props = defineProps({
  currentId: {
    type: Number,
    required: false,
    default: undefined,
  },
});

const blogStore = useBlogStore();
const data = ref();
data.value = await blogStore.getBlogSection(3, props.currentId);
const filteredData = computed(() => {
    return data.value.slice(0, 3);
});

</script>

<style lang="scss" scoped>
$xlBreakpoint: 1280px;
$mdBreakpoint: 768px;
$smBreakpoint: 640px;
$mobBreakpoint: 410px;

.news__wrapper-card {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  padding-bottom: 30px;
  gap: 20px;
  @media (max-width: $mdBreakpoint) {
    grid-template-columns: 1fr;
    gap: 30px;
  }
}

.news__wrapper-card{
  a {
    display: block;
  }
  .card__image {
    border-radius: 10px;
  }
  .date {
    color: $grey_secondary;
  }
}

</style>