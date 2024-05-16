<template>
  <div class="breadcrumbs">
    <NuxtLink class="hover" to="/">Главная</NuxtLink> /
    <NuxtLink class="hover" to="/blog/all?page=1">Блог</NuxtLink> /
    <NuxtLink class="hover" :to="`/blog/${tag}?page=1`">
      {{breadCrumName}}
    </NuxtLink> /
    <span class="breadcrumbs_active">{{ data.title }}</span>
  </div>

    <div class="article__wrapper">

      <div class="image__wrapper article-image__wrapper">
        <img :src="data.image_lg" :alt="data.title">
      </div>

      <div class="promo__wrapper">
        <a v-if="data.is_promo_block" target="_blank"
           :href="data.promo_url"
           class="block image__wrapper promo__image cursor-pointer">
          <img :src="data.promo_image" :alt="data.promo_image_alt">
        </a>
      </div>

      <div>
        <div class="flex w-full justify-between gap-16 mt-2.5 md:mt-4 relative">
          <div class="flex w-full justify-between flex-col lg:flex-row">
            <div v-if="data.is_author">
              <div class="t_5 text-grey_secondary">Автор статьи</div>
              <div class="flex gap-2.5 mt-2.5">
                <div class="avatar w-12 h-full">
                  <img :src="data.author_avatar" :alt="data.author_name">
                </div>
                <div>
                  <div class="t_5 mt-1">{{ data.author_name }}</div>
                  <div class="t_5 text-grey">{{ data.author_position }}</div>
                  <div class="t_5 text-grey_secondary mt-2 tablet-show">{{ data.date }}</div>
                </div>
              </div>
            </div>
            <div class="t_5 text-grey tablet-none">
              {{ data.date }}
            </div>
          </div>

          <div class="article__social social__share flex gap-2.5 flex-col sm:flex-row">
            <div class="t_5 text-grey mob-none">Поделиться</div>
            <a :href="vkShareLink" target="_blank" rel="noopener noreferrer">
              <img src='@/assets/images/icons/social/vk.svg' alt="ВКонтакте">
            </a>
            <a :href="telegramShareLink" target="_blank" rel="noopener noreferrer">
              <img src="@/assets/images/icons/social/tg.svg" alt="Телеграм">
            </a>
            <a :href="twitterShareLink" target="_blank" rel="noopener noreferrer">
              <img src="@/assets/images/icons/social/x.svg" alt="Х">
            </a>

          </div>
        </div>

        <div class="article__text mt-10 md:mt-6">
          <h1 class="h_3">{{ data.title }}</h1>
          <div v-html="data.text" class="text_byTinyMce text_byTinyMce_blog"/>
          <form class="w-full mt-10 md:mt-14 lg:mt-20">
            <div class="input__wrapper flex justify-between relative">
              <label
                  for='blog-email'
                  class="text-sm text-red absolute -top-1/2 ml-4"
                  v-if="isSubmit &&  !EMAIL_REGEX.test(email)">
                Введите почту в формате example@example.com
              </label>
              <input
                  class="input w-full"
                  type="email"
                  required
                  placeholder="E-mail"
                  v-model="email"
                  id="blog-email"
              >
              <button
                  @click.prevent = 'onSubscribe'
                  class="target-btn-subscribe input__btn flex items-center content-center gap-2 shrink-0">
                Подписаться
              </button>
            </div>
            <div class="mt-5">
              <div
                  class="mb-1 text-sm text-red"
                  v-if="isSubmit && !isChecked">
                Необходимо согласие для отправки формы
              </div>
              <FormCheckboxText
                  :isChecked="isChecked"
                  @update="updateChecked"
              >
                "Подписаться"
              </FormCheckboxText>
            </div>
          </form>
        </div>
      </div>
    </div>




  <BlogNewsSection
      class="mt-section"
      :currentId="data.id"
  >
    Другие материалы
  </BlogNewsSection>
</template>

<script setup lang="ts">
import { EMAIL_REGEX } from "@/constants/regex";
import {useBlogStore} from "~/stores/blog";
import {TagItem} from "~/types/common";
import {computed} from "vue";

//инициализация Store
const blogStore = useBlogStore();

//получаем данные для страницы
const { slug, tag } = useRoute().params;
const data = ref();
data.value = await blogStore.fetchBlogById(slug);

//формирование Хлебных крошек
const blogTags = ref();
blogTags.value = await blogStore.getBlogTags();
const breadCrumName = computed(() => {
  if ( tag === 'all') return 'Все'
  const currentTag =  blogTags.value.find((item: TagItem) => item.code === tag);
  return currentTag.names
})

//форма подписки на рассылку
const isChecked = ref<boolean>(false);
function updateChecked(value: boolean){
  isChecked.value = value;
}
const email = ref<String>('');
const isSubmit = ref<boolean>(false);
function cleanForm() {
  email.value = '';
  isSubmit.value = false;
}
function onSubscribe () {
  isSubmit.value = true;
  if (!EMAIL_REGEX.test(email.value)) return;
  console.log(email.value, isChecked.value);
}

//шеринг в соц сетях
const currentURL = computed(() => {
  if (process.client) {
    return encodeURIComponent(window.location.href);
  }
  return '';
});
const titleShare = encodeURIComponent(data.value.title);
const telegramShareLink = computed(() => {
  return `https://t.me/share/url?url=${currentURL}&text=${titleShare}`;
});
const vkShareLink = computed(() => {
  return `https://vk.com/share.php?url=${currentURL}&title=${titleShare}&description=${'Статься на сайте atol.ru'}`;
});
const twitterShareLink = computed(() => {
  return `https://twitter.com/intent/tweet?url=${currentURL}&text=${titleShare}&hashtags=${'hashtags'}&via=${'via'}`;
});

//формирование мета тегов для СЕО
import {useCeoStore} from "~/stores/ceo";
const ceoStore = useCeoStore();

const meta_title = computed(() => {
  if (!data.value.is_custom_meta_title) {
    return data.value.title + ' | Новость';
  } else {
    return data.value.meta_title;
  }
});

const meta_description = computed(() => {
  if (!data.value.is_custom_meta_description && data.value.text && data.value.text.length > 4) {
    return data.value.text.slice(0, 250) || '';
  } else {
    return data.value.meta_description;
  }
});

const meta_robots_content = ceoStore.makeMetaRobots(data.value.meta_is_noindex, data.value.meta_is_nofollow);

useHead({
  title: meta_title,
  htmlAttrs: {
    prefix: `og: http://ogp.me/ns# http://ogp.me/ns/article#`,
  },
  meta: [
    { name: 'robots', content: meta_robots_content },
    { name: 'description', content: meta_description },
    { name: 'keywords', content: data.value.meta_keywords },
    { name: 'og:title', content: data.value.title},
    { name: 'og:type', content: "website" },
    { name: 'og:url', content: currentURL },
    { name: 'og:image', content: data.value.image_lg },
  ],
})



</script>

<style lang="scss" scoped>
$xlBreakpoint: 1280px;
$lgBreakpoint: 1024px;
$mdBreakpoint: 768px;
$smBreakpoint: 640px;
$mobBreakpoint: 410px;

.article__wrapper {
  display: grid;
  grid-template-columns: 3fr 1fr;
  column-gap: 60px;
  @media (max-width: $xlBreakpoint) {
    column-gap: 37px;
  }
  @media (max-width: $mdBreakpoint) {
    grid-template-columns: 1fr;
  }
}
.promo__wrapper {
  height: 100%;
  a {
    height: 100%;
    @media (max-width: $mdBreakpoint) {
      height: unset;
    }
  };

  @media (max-width: $mdBreakpoint) {
    margin-top: 90px;
    grid-row: 3;
  }
}

.image__wrapper {
  overflow: hidden;

  img {
    object-fit: cover;
    width: 100%;
    height: 100%;
  }
}
.promo__image {
  border-radius: 20px;
}
.article-image__wrapper {
  aspect-ratio: 21/9;
  border-radius: 6px;
}

.article__social {
  a {
    @media (max-width: $mdBreakpoint) {
      top: 0;
    }
  }
}

.tablet-none {
  @media (max-width: $xlBreakpoint) {
    display: none;
  }
}
.tablet-show {
  display: none;
  @media (max-width: $xlBreakpoint) {
    display: block;
  }
}

.mob-none {
  @media (max-width: $smBreakpoint) {
    display: none;
  }
}

.article__text {
  width: 80%;
  @media (max-width: $xlBreakpoint) {
    width: 100%;
  }
}

.input__wrapper {
  border-radius: 40px;
  border: 1px solid $grey_light_secondary;
}

.input {
  border-radius: 40px;
  padding: 10px 30px;
  outline: none;
  font-size: 18px;
  font-style: normal;
  font-weight: 400;
  line-height: 120%;
  letter-spacing: 0.18px;
  &:focus {
    outline: none;
  }
  @media (max-width: $mdBreakpoint){
    font-size: 16px;
    letter-spacing: 0.16px;
    padding: 8px 15px;
    line-height: 110%;
  }
}

.input__btn {
  border-radius: 40px;
  background: $red;
  padding: 15px 25px;
  color: $white;
  font-size: 20px;
  font-style: normal;
  font-weight: 500;
  line-height: 130%; /* 26px */
  letter-spacing: 0.2px;
  @media (max-width: $smBreakpoint){
    font-size: 15px;
    line-height: 110%;
    padding: 10px 15px;
  }
}


.label {
  position: absolute;
  top: -20px;
}

.text_byTinyMce_blog :deep(ul) {
  padding-left: 10px;
  list-style-type: none;
  position: relative;

  li::before {
    content: '\2022'; /* Устанавливаем вместо маркера нежирную точку */
    font-weight: normal; /* Делаем точку нежирной */
    margin-right: 0.5em; /* Добавляем небольшой отступ справа от точки */
    position: absolute;
    left: 3px;
  }
  li {
    margin-bottom: 0 !important;
    padding-left: 10px;
  }
}

</style>
