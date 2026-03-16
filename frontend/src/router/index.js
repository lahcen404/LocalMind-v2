import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/components/Login.vue';
import Register from '@/components/Register.vue';
import QuestionFeed from '@/components/QuestionFeed.vue';
import QuestionDetail from '@/components/QuestionDetail.vue';
import Favorites from '@/components/Favorites.vue';
import PostQuestion from '@/components/PostQuestion.vue';
import AdminDashboard from '@/components/AdminDashboard.vue';
import Profile from '@/components/Profile.vue';

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
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});


router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('lm_token');

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
    return;
  }
  if (to.meta.requiresAdmin && isAuthenticated) {
    try {
      const saved = localStorage.getItem('lm_user');
      const user = saved ? JSON.parse(saved) : null;
      if (!user || user.role !== 'admin') {
        next('/');
        return;
      }
    } catch (_) {
      next('/');
      return;
    }
  }
  if (to.meta.guestOnly && isAuthenticated) {
    next('/');
    return;
  }
  next();
});

export default router;
