import router from './router'
import store from './store'
import { Message } from 'element-ui'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css'
import { getToken } from '@/utils/auth' // get token from cookie
import getPageTitle from '@/utils/getPageTitle'

NProgress.configure({ showSpinner: false }) // NProgress Configuration

// permission judge function
function hasPermission(roles, permissionRoles) {
  if (!permissionRoles) {
    return true
  }

  return roles.some(role => {
    return permissionRoles.indexOf(role.name) >= 0 || role.name === 'admin'
  })
}

const whiteList = ['/login', '/auth-redirect'] // no redirect whitelist

router.beforeEach(async(to, from, next) => {
  NProgress.start() // start progress bar

  // set page title
  document.title = getPageTitle(to.meta.title)

  // determine whether the user has logged in
  const hasToken = getToken()

  if (hasToken) {
    // 初始化 store state
    if (store.state.basedata.suggests.length === 0) {
      await store.dispatch('basedata/fetchSuggest')
    }

    if (store.state.basedata.categories.length === 0) {
      await store.dispatch('basedata/fetchCategory')
    }

    if (to.path === '/login') {
      // if is logged in, redirect to the home page
      next({ path: '/' })
      NProgress.done()
    } else {
      // determine whether the user has obtained his permission roles through getInfo
      const hasRoles = store.getters.roles && store.getters.roles.length > 0
      if (hasRoles) {
        // 没有动态改变权限的需求可直接 next()
        if (hasPermission(store.getters.roles, to.meta.roles)) {
          next()
        } else {
          next({ path: '/401', replace: true, query: { noGoBack: true }})
        }
      } else {
        try {
          // note: roles must be a array!
          const { roles } = store.dispatch('user/getInfo')
          // generate accessible routes map based on roles
          const accessRoutes = await store.dispatch('permission/generateRoutes', roles)

          // dynamically add accessible routes
          router.addRoutes(accessRoutes)

          // hack method to ensure that addRoutes is complete
          // set the replace: true, so the navigation will not leave a history record
          next({ ...to, replace: true })
        } catch (err) {
          // remove token and go to login page to re-login
          await store.dispatch('user/resetToken')
          Message.error(err || 'Has Error')
          next(`/login?redirect=${to.path}`)
          NProgress.done()
        }
      }
    }
  } else {
    /* has no token*/
    if (whiteList.indexOf(to.path) !== -1) { // 在免登录白名单，直接进入
      next()
    } else {
      next(`/login?redirect=${to.path}`) // 否则全部重定向到登录页
      NProgress.done()
    }
  }
})

router.afterEach(() => {
  NProgress.done() // finish progress bar
})
