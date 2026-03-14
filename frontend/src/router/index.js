import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/components/Login.vue';
import Register from '@/components/Register.vue';
import QuestionFeed from '@/components/QuestionFeed.vue';
import QuestionDetail from '@/components/QuestionDetail.vue';
import Favorites from '@/components/Favorites.vue';
import PostQuestion from '../components/PostQuestion.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guestOnly: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guestOnly: true }
  },
  {
    path: '/',
    name: 'Feed',
    component: QuestionFeed,
    meta: { requiresAuth: true }
  },
  {
    path: '/favorites',
    name: 'Favorites',
    component: Favorites,
    meta: { requiresAuth: true }
  },
  {
    path: '/questions/create',
    name: 'PostQuestion',
    component: PostQuestion,
    meta: { requiresAuth: true }
  },
  {
    path: '/questions/:id',
    name: 'QuestionDetail',
    component: QuestionDetail,
    props: true,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});


router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('lm_token');

  // redirect to login if route requires auth and user is not authenticated
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  }
  // redirect to home if route is guest only and user is authenticated
  else if (to.meta.guestOnly && isAuthenticated) {
    next('/');
  }
  else {
    next();
  }
});

export default router;
