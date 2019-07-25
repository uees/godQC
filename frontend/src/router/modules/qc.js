import Layout from '@/layout'

const qcRouter = {
  path: '/test',
  component: Layout,
  redirect: 'noRedirect',
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
      path: 'fqc/testing',
      component: () => import('@/views/fqc/Testing'),
      name: 'FQCTesting',
      meta: { roles: ['admin', 'fqc'], title: '在检产品', icon: 'example' }
    },
    {
      path: 'fqc/records',
      component: () => import('@/views/fqc/RecordList'),
      name: 'FQCRecordList',
      meta: { roles: ['admin', 'fqc'], title: '产品检测记录', icon: 'guide' }
    },
    {
      path: 'fqc/records/real',
      component: () => import('@/views/fqc/RealRecordList'),
      hidden: true,
      name: 'FQCRealRecordList',
      meta: { roles: ['admin', 'fqc'], title: '产品检测记录(真)' }
    },
    {
      path: 'iqc/testing',
      component: () => import('@/views/iqc/Testing'),
      name: 'IQCTesting',
      meta: { title: '在检材料', icon: 'example', roles: ['admin', 'iqc'] }
    },
    {
      path: 'iqc/records',
      component: () => import('@/views/iqc/RecordList'),
      name: 'IQCRecordList',
      meta: { title: '来料检测记录', icon: 'guide', roles: ['admin', 'iqc'] }
    },
    {
      path: 'iqc/records/real',
      component: () => import('@/views/iqc/RealRecordList'),
      hidden: true,
      name: 'IQCRealRecordList',
      meta: { title: '来料检测记录(真)', roles: ['admin', 'iqc'] }
    },
    {
      path: 'patterns/H-8100',
      component: () => import('@/views/fqc/PatternH9100'),
      name: 'PatternTestH8100',
      meta: { title: '型式检验H-8100/H-9100', icon: 'table' }
    },
    {
      path: 'patterns/A-9060',
      component: () => import('@/views/fqc/PatternA9060'),
      name: 'PatternTestA9060',
      meta: { title: '型式检验湿膜', icon: 'table' }
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
