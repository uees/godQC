import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

import qcRouter from './modules/qc'

/** note: Submenu only appear when children.length>=1
 *  detail see  https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 **/

/**
 * hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
 *                                if not set alwaysShow, only more than one route under the children
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: 'noRedirect'         if `redirect:'noRedirect'` will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['admin','editor']     will control the page roles (you can set multiple roles)
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
    noCache: true                if true ,the page will no be cached(default is false)
    affix: true                  if set true, the tag will affix in the tags-view
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 **/
export const constantRoutes = [
  {
    path: '/redirect',
    component: Layout,
    hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('@/views/redirect/index')
      }
    ]
  },
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/error',
    component: Layout,
    redirect: 'noRedirect',
    name: 'ErrorPages',
    hidden: true,
    children: [
      {
        path: '404',
        component: () => import('@/views/errorPage/404'),
        name: 'Page404',
        meta: { title: '404 未找到页面', noCache: true }
      },
      {
        path: '401',
        component: () => import('@/views/errorPage/401'),
        name: 'Page401',
        meta: { title: '401 无权限', noCache: true }
      }
    ]
  },
  {
    path: '/profile',
    component: Layout,
    redirect: 'index',
    hidden: true,
    children: [
      {
        path: 'index',
        component: () => import('@/views/errorPage/401'),
        name: 'Profile',
        meta: { title: '个人中心', icon: 'table', noCache: true }
      }
    ]
  },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index'),
        name: 'Dashboard',
        meta: { title: '主页', icon: 'dashboard', affix: true }
      }
    ]
  }
]

/**
 * asyncRoutes
 * the routes that need to be dynamically loaded based on user roles
 */
export const asyncRoutes = [
  qcRouter,
  {
    path: '/categories',
    component: Layout,
    children: [
      {
        path: 'index',
        component: () => import('@/views/categories/index'),
        name: 'Categories',
        meta: { title: '产品分类', icon: 'list' }
      }
    ]
  },
  {
    path: '/products',
    component: Layout,
    children: [
      {
        path: 'index',
        component: () => import('@/views/products/index'),
        name: 'Products',
        meta: { title: '产品', icon: 'star' }
      }
    ]
  },
  {
    path: '/customers',
    component: Layout,
    children: [
      {
        path: 'index',
        component: () => import('@/views/customers/index'),
        name: 'Customers',
        meta: { title: '客户', icon: 'tree' }
      }
    ]
  },
  {
    path: '/suggests',
    component: Layout,
    meta: {
      roles: ['admin']
    },
    children: [
      {
        path: 'index',
        component: () => import('@/views/suggests/index'),
        name: 'Suggests',
        meta: { title: '提示数据', icon: 'list' }
      }
    ]
  },
  // 404 要放最后
  { path: '*', redirect: '/error/404', hidden: true }
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
