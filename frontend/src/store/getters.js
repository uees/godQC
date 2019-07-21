const getters = {
  sidebar: state => state.app.sidebar,
  size: state => state.app.size,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  token: state => state.user.token,
  roles: state => state.user.roles,
  user: state => state.user,
  permissionRouters: state => state.permission.routes,
  addedRouters: state => state.permission.addedRouters,
  errorLogs: state => state.errorLog.logs
}
export default getters
