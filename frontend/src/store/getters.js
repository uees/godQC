const getters = {
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  size: state => state.app.size,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  accessToken: state => state.user.accessToken,
  refreshToken: state => state.user.refreshToken,
  roles: state => state.user.roles,
  user: state => state.user,
  permission_routers: state => state.permission.routers,
  addedRouters: state => state.permission.addedRouters,
  errorLogs: state => state.errorLog.logs
}
export default getters
