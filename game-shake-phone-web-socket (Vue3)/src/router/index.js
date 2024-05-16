import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/gender',
      name: 'gender',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/Gender.vue')
    },
    {
      path: '/name',
      name: 'name',
      component: () => import('../views/Name.vue')
    },
    {
      path: '/oops',
      name: 'oops',
      component: () => import('../views/Oops.vue')
    },
    {
      path: '/wait',
      name: 'wait',
      component: () => import('../views/Wait.vue')
    },
    {
      path: '/game',
      name: 'game',
      component: () => import('../views/Game.vue')
    },
    {
      path: '/result',
      name: 'result',
      component: () => import('../views/Result.vue')
    },
    {
      path: '/nogame',
      name: 'nogame',
      component: () => import('../views/NoGame.vue')
    },
    {
      path: '/browser_problem',
      name: 'browser_problem',
      component: () => import('../views/BadBrouser.vue')
    }
  ]
})

export default router
