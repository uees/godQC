import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/views/layout/Layout'

/* Router Modules */
// import componentsRouter from './modules/components'
// import chartsRouter from './modules/charts'
// import tableRouter from './modules/table'
// import nestedRouter from './modules/nested'

/** note: Submenu only appear when children.length>=1
 *  detail see  https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 **/

/**
 * hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
 *                                if not set alwaysShow, only more than one route under the children
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['admin','editor']     will control the page roles (you can set multiple roles)
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
    noCache: true                if true ,the page will no be cached(default is false)
  }
 **/
export const constantRouterMap = [
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
    path: '/auth-redirect',
    component: () => import('@/views/login/authredirect'),
    hidden: true
  },
  {
    path: '/404',
    component: () => import('@/views/errorPage/404'),
    hidden: true
  },
  {
    path: '/401',
    component: () => import('@/views/errorPage/401'),
    hidden: true
  },
  {
    path: '',
    component: Layout,
    redirect: 'dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index'),
        name: 'Dashboard',
        meta: { title: 'dashboard', icon: 'dashboard' }
      },
      {
        path: 'disposes/:id',
        component: () => import('@/views/qcrecords/dispose'),
        hidden: true,
        props: true,
        name: 'disposes.show',
        meta: { title: '处理意见', name: 'Dispose' }
      },
      {
        path: 'records/:id',
        component: () => import('@/views/qcrecords/show'),
        hidden: true,
        props: true,
        name: 'records.show',
        meta: { title: '检测记录', name: 'ShowRecord' }
      },
      {
        path: 'records/:id/real',
        component: () => import('@/views/qcrecords/show'),
        hidden: true,
        props: true,
        name: 'records.show-real',
        meta: { title: '检测记录(真)', name: 'ShowRecord' }
      }
    ]
  }
]

export default new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})

export const asyncRouterMap = [
  {
    path: '/test/fqc',
    component: Layout,
    redirect: 'noredirect',
    name: 'FQC',
    meta: {
      roles: ['admin', 'fqc'],
      title: '成品检测',
      icon: 'chart'
    },
    children: [
      {
        path: 'testing',
        component: () => import('@/views/qcrecords/testing'),
        name: 'fqc-testing',
        meta: { title: '在检产品', icon: 'example', name: 'Testings' }
      },
      {
        path: 'list',
        component: () => import('@/views/qcrecords/index'),
        name: 'fqc-list',
        meta: { title: '产品检测记录', icon: 'guide', name: 'TestRecords' }
      },
      {
        path: 'list/real',
        component: () => import('@/views/qcrecords/index'),
        hidden: true,
        name: 'fqc-list-real',
        meta: { title: '产品检测记录(真)', name: 'TestRecords' }
      }
    ]
  },
  {
    path: '/test/iqc',
    component: Layout,
    redirect: 'noredirect',
    name: 'IQC',
    meta: {
      roles: ['admin', 'iqc'],
      title: '来料检测',
      icon: 'component'
    },
    children: [
      {
        path: 'testing',
        component: () => import('@/views/qcrecords/testing'),
        name: 'iqc-testing',
        meta: { title: '在检材料', icon: 'example', name: 'Testings' }
      },
      {
        path: 'list',
        component: () => import('@/views/qcrecords/index'),
        name: 'iqc-list',
        meta: { title: '来料检测记录', icon: 'guide', name: 'TestRecords' }
      },
      {
        path: 'list/real',
        component: () => import('@/views/qcrecords/index'),
        hidden: true,
        name: 'iqc-list-real',
        meta: { title: '来料检测记录(真)', name: 'TestRecords' }
      }
    ]
  },
  {
    path: '/test/patterns',
    component: Layout,
    children: [
      {
        path: 'index',
        component: () => import('@/views/qcrecords/patternTest'),
        name: 'PatternTest',
        meta: { title: '型式检验', icon: 'table' }
      }
    ]
  },
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
    path: '/test/methods',
    component: Layout,
    children: [
      {
        path: 'index',
        component: () => import('@/views/qcmethods/index'),
        name: 'TestMethods',
        meta: { title: '检测方法', icon: 'table' }
      }
    ]
  },
  {
    path: '/test/ways',
    component: Layout,
    meta: {
      roles: ['admin']
    },
    children: [
      {
        path: 'index',
        component: () => import('@/views/qcways/index'),
        name: 'TestWays',
        meta: { title: '检测流程', icon: 'size' }
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
  { path: '*', redirect: '/404', hidden: true }
]
