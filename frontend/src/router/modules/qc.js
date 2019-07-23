import Layout from '@/layout'

const qcRouter = {
  path: '/test',
  component: Layout,
  redirect: 'noredirect',
  name: 'Test',
  meta: {
    title: '检测系统',
    icon: 'table'
  },
  children: [
    {
      path: 'disposes/:id',
      component: () => import('@/views/qcrecords/dispose'),
      hidden: true,
      props: true,
      name: 'Dispose',
      meta: { title: '处理意见' }
    },
    {
      path: 'records/:id',
      component: () => import('@/views/qcrecords/show'),
      hidden: true,
      props: true,
      name: 'ShowRecord',
      meta: { title: '检测记录', name: 'ShowRecord' }
    },
    {
      path: 'records/:id/real',
      component: () => import('@/views/qcrecords/show'),
      hidden: true,
      props: true,
      name: 'ShowRecordReal',
      meta: { title: '检测记录(真)', name: 'ShowRecord' }
    },
    {
      path: 'fqc',
      component: () => import('@/views/nested'),
      name: 'FQC',
      meta: {
        roles: ['admin', 'fqc'],
        title: '成品检测',
        icon: 'chart'
      },
      children: [
        {
          path: 'testing',
          component: () => import('@/views/fqc/Testing'),
          name: 'FQCTesting',
          meta: { title: '在检产品', icon: 'example' }
        },
        {
          path: 'index',
          component: () => import('@/views/fqc/index'),
          name: 'FQCIndex',
          meta: { title: '产品检测记录', icon: 'guide' }
        },
        {
          path: 'index/real',
          component: () => import('@/views/fqc/RealIndex'),
          hidden: true,
          name: 'FQCRealIndex',
          meta: { title: '产品检测记录(真)' }
        }
      ]
    },
    {
      path: 'iqc',
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
          name: 'IQCTesting',
          meta: { title: '在检材料', icon: 'example' }
        },
        {
          path: 'index',
          component: () => import('@/views/qcrecords/index'),
          name: 'IQCIndex',
          meta: { title: '来料检测记录', icon: 'guide' }
        },
        {
          path: 'index/real',
          component: () => import('@/views/qcrecords/index'),
          hidden: true,
          name: 'IQCIndexReal',
          meta: { title: '来料检测记录(真)' }
        }
      ]
    },
    {
      path: 'patterns',
      component: () => import('@/views/nested'), // Parent router-view
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
      path: 'methods',
      component: () => import('@/views/qcmethods/index'),
      name: 'TestMethods',
      meta: { title: '检测方法', icon: 'table' }
    },
    {
      path: 'ways',
      component: () => import('@/views/qcways/index'),
      name: 'TestWays',
      meta: { roles: ['admin'], title: '检测流程', icon: 'size' }
    }
  ]
}

export default qcRouter
