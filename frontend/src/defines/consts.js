export const VALUE_TYPES = [
  { value: 'RANGE', label: '范围' },
  { value: 'INFO', label: '信息' },
  { value: 'VALUE', label: '具体值' },
  { value: 'ONLY_SHOW', label: '仅展示' }
]

export const CONCLUSIONS = [
  { value: 'PASS', label: '合格' },
  { value: 'NG', label: '不合格' }
]

export const pickerOptions = {
  shortcuts: [{
    text: '最近一周',
    onClick(picker) {
      const end = new Date()
      const start = new Date()
      start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
      picker.$emit('pick', [start, end])
    }
  }, {
    text: '最近一个月',
    onClick(picker) {
      const end = new Date()
      const start = new Date()
      start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
      picker.$emit('pick', [start, end])
    }
  }, {
    text: '最近三个月',
    onClick(picker) {
      const end = new Date()
      const start = new Date()
      start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
      picker.$emit('pick', [start, end])
    }
  }]
}
