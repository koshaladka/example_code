<template>
  <div>
    <div class="breadcrumbs">
      <NuxtLink class="hover" to="/">Главная</NuxtLink> /
      <NuxtLink class="hover" to="/blog/all?page=1">Блог</NuxtLink> /
      <span class="breadcrumbs_active">{{breadCrumName}}</span>
    </div>
    <h1 class="h_1">Блог</h1>
    <div class="flex blog__btn_wrapper">
      <NuxtLink to="/blog/all?page=1"
           :class="{'blog__btn_active': tag === 'all'}"
           class="blog__btn t_2">
        Все
      </NuxtLink>
      <NuxtLink v-for="blogTag in blogTags"
           :to="`/blog/${blogTag.code}?page=1`"
           :key="blogTag.id"
           class="blog__btn t_2"
           :class="{'blog__btn_active': blogTag.code === tag}">
        {{blogTag.names}}
      </NuxtLink>
      <div class="blog__btn w-full d_mob_none "></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-6">
      <BlogCard :data = 'data'/>
    </div>

    <div @click="loadMore"
        class="btn mt-5 lg:mt-10"
         :class="{'btn_not_active': blogStore.page === blogStore.lastPage}">
      Посмотреть еще
    </div>
    <div class="paginate flex gap-2.5 justify-center mt-7">
      <p
          @click="decrementPage()"
          class="hover mr-2.5"
          :class="{'not_active': blogStore.page === 1}"
      >
        Назад
      </p>
<!--      <p v-for="page in pages"-->
<!--         :key="page"-->
<!--         class="hover text-grey"-->
<!--         :class="{'page_active': page === blogStore.page}"-->
<!--         @click="setPage(page)"-->
<!--      >-->
<!--        {{ page }}-->
<!--      </p>-->
      <NuxtLink v-for="page in pages"
         :key="page"
         class="hover text-grey"
         :class="{'page_active': page === blogStore.page}"
          :to="`/blog/${tag}?page=${page}`"
          @click="setPage(page)"
      >
        {{ page }}
      </NuxtLink>
      <p
          @click="incrementPage()"
          class="hover ml-2.5"
          :class="{'not_active': blogStore.page === blogStore.lastPage}"
      >
        Вперед
      </p>
    </div>

  </div>
</template>

<script setup lang="ts">
import {useScreenSizeStore} from "~/stores/screenSize";
import {storeToRefs} from "pinia";
import {useBlogStore} from "~/stores/blog";
const blogStore = useBlogStore();

const screenSizeStore = useScreenSizeStore();
const { screenSize } = storeToRefs(screenSizeStore);

onMounted(() => {
  screenSizeStore.setObserverResize();
  blogStore.setPage(pageUrl);
  blogStore.fetchBlogPaginate(cardsPerPage.value, tag, false);
});
onUnmounted(() => {
  screenSizeStore.unSetObserverResize();
});


//настройка Тегов и хлебных крошек
const { tag } = useRoute().params;
const blogTags = ref();
blogTags.value = await blogStore.getBlogTags();
const breadCrumName = computed(() => {
      if ( tag === 'all') return 'Все'
      const currentTag =  blogTags.value.find(item => item.code === tag);
      return currentTag.names
})


//определение количества карточек на странице в зависимости от разрешения
const cardsPerPage = computed(() => {
  if (screenSize.value === 'small') {
    return 3;
  } else if (screenSize.value === 'medium') {
    return 4;
  } else {
    return 12;
  }
});

await blogStore.fetchBlogPaginate(cardsPerPage.value, tag, false);
const data = computed(() => blogStore.blogItems);

//получение страницы из query параметра в url
const pageUrl = parseInt(useRoute().query.page as string, 10) || 1;


async function setPage(number: number) {
  blogStore.setPage(number);
  await blogStore.fetchBlogPaginate(cardsPerPage.value, tag, false);
}
//получение кол-ва страниц
const pages = computed(() => {
  const data = [];
  for (let i = 1; i <= blogStore.lastPage; i++) {
    data.push(i)
  }
  return data;
})
async function loadMore() {
  if (blogStore.page === blogStore.lastPage) return;

  blogStore.incrementPage();
  await blogStore.fetchBlogPaginate(cardsPerPage.value, tag, true);;
}
async function decrementPage() {
  if (blogStore.page === 1) return;

  blogStore.decrementPage();
  await blogStore.fetchBlogPaginate(cardsPerPage.value, tag, false);
}
async function incrementPage() {
  if (blogStore.page === blogStore.lastPage) return;

  blogStore.incrementPage();
  await blogStore.fetchBlogPaginate(cardsPerPage.value, tag, false);
}

//формирование мета тегов для СЕО
import {useCeoStore} from "~/stores/ceo";
const ceoStore = useCeoStore();
const ceoData = computed(() => blogStore.ceoData);
const CEOforHead = ceoStore.generateCEOArray(ceoData.value);
useHead(CEOforHead);


</script>

<style lang="scss" scoped>
$xlBreakpoint: 1280px;
$mdBreakpoint: 768px;
$smBreakpoint: 640px;
$mobBreakpoint: 410px;

.blog__btn_wrapper {
  display: flex;
  margin-top: 20px;
}

.blog__btn {
  color: $grey-secondary;
  border-bottom: 2px solid $grey_light_secondary;
  cursor: pointer;
  padding:  0px 24px 6px 24px;
  &:first-child {
    padding-left: 0px;
  }
  @media (max-width: $mobBreakpoint) {
    padding-right: 10px;
    padding-left: 10px;
  }
}

.blog__btn_active {
  color: $black;
  border-bottom: 2px solid $red;
}

.d_mob_none {
  @media (max-width: 340px) {
    display: none;
  }
}

.paginate {
  font-size: 18px;
  font-style: normal;
  font-weight: 400;
  line-height: 100%;
}

.page_active {
  color: $red;
  text-decoration-line: underline;
}

.not_active {
  color: $grey;
  cursor: unset;

}
.btn_not_active {
  &:hover {
    background: white;
    cursor: unset;
    color: $red;
  }
}

</style>