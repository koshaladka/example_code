import {defineStore} from 'pinia';
import {BlogItem, PaginationData} from "~/types/blog";
import {integer} from "vscode-languageserver-types";
import {TagItem} from "~/types/common";

export const useBlogStore = defineStore('blog', () => {
  const config = useRuntimeConfig()
  const blogSection = ref();
  const blogTags = ref();
  const blogItems = ref();
  const page = ref(1);
  const lastPage = ref();
  const ceoData = ref();

  async function fetchBlogSection(quantity: integer, currentId?: integer) {
    const body = {
      number: quantity,
      currentId: currentId || null
    }
    const {data} = await useFetch<string, BlogItem[]>('/blog/section', {
      method: 'POST',
      baseURL:config.public.baseURL,
      body: body
    })
    if (!data) return;

    blogSection.value = toRaw(data.value);
  }
  async function getBlogSection(quantity: integer, currentId?: number) {
    if (!blogSection.value || blogSection.value.length < quantity || blogSection.value.some((item: BlogItem) => item.id === currentId) ) {
      await fetchBlogSection(quantity, currentId);
    }
    return blogSection.value;
  }

  async function fetchBlogById(id: string |  string[]) {
    const {data} = await useFetch<string, BlogItem[]>(`/blog/${id}`, {
      baseURL:config.public.baseURL,
    })
    if (!data) return;

    return toRaw(data.value);
  }

  async function fetchBlogTags() {
    const {data} = await useFetch<string, TagItem[]>(`/blog/tags`, {
      baseURL:config.public.baseURL,
    })
    if (!data) return;

    blogTags.value = toRaw(data.value);
  }
  async function getBlogTags() {
    if (!blogTags.value || blogTags.value.length === 0 ) {
      await fetchBlogTags();
    }
    return blogTags.value;
  }

  async function fetchBlogPaginate(per_page: integer, tag_code:  string | string[], loadMore: boolean ) {

    const body: {page: integer, per_page: integer, tag_code?: any} = {
      page: page.value,
      per_page,
    }
    if (tag_code != 'all') {
      body.tag_code = tag_code
    }
    const {data} = await useFetch<PaginationData>('/blog/paginate', {
      method: 'POST',
      baseURL:config.public.baseURL,
      body: body
    })
    if (!data) return;

    lastPage.value = data.value?.data.last_page;
    //loadMore = true - догружаем карточки
    if (loadMore === true) {
      blogItems.value = [...blogItems.value, ...toRaw(data.value?.data.data ?? [])]
    } else {
      blogItems.value = toRaw(data.value?.data.data  ?? [])
    };

    ceoData.value = data.value?.ceo;
    console.log(data.value);
    console.log(ceoData);

  }


  function incrementPage(){
    page.value++
  }
  function decrementPage(){
    page.value--
  }
  function setPage(number: number){
    page.value = number;
  }

  return {
    getBlogSection,
    fetchBlogById,
    getBlogTags,
    blogItems,
    fetchBlogPaginate,
    page,
    lastPage,
    incrementPage,
    decrementPage,
    setPage,
    ceoData,
  };
})
