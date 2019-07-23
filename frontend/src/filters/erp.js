import { parseTime as _parseTime } from '@/utils'

/**
  'spec' => [
      'is_show' => true,   // 是否展示
      'required' => true,  // 是否必须填值项
      'value_type' => '',  // RANGE, INFO, NUMBER, ONLY_SHOW
      'data' => [
          'min' => 0,
          'max' => 0,
          'value' => '',
          'memo' => '',
          'unit' => '',
      ],
  ],
 */
export function qcspec(spec) {
  if (!spec || !spec.data) {
    return ''
  }

  let result = ''
  if (spec.value_type === 'INFO' || spec.value_type === 'NUMBER' || !spec.value_type) {
    result = spec.data.value
    if (spec.data.unit) {
      result += spec.data.unit
    }
  } else if (spec.value_type === 'RANGE') {
    if (!spec.data.unit) {
      spec.data.unit = '' // null undefined to ''
    }
    if (spec.data.min) {
      result += `≥ ${spec.data.min}${spec.data.unit}, `
    }
    if (spec.data.max) {
      result += `≤ ${spec.data.max}${spec.data.unit}, `
    }
  } else if (spec.value_type === 'ONLY_SHOW') {
    // value is "要求|结果值"
    const tmpArr = spec.data.value.split('|')
    if (tmpArr.length > 0) {
      result = tmpArr[0]
    }
  }

  if (spec.data.memo) {
    result = `${result} (${spec.data.memo})`
  }

  return result
}

export function parseTime(dateObj) {
  if (dateObj && dateObj.date) {
    const date = new Date(dateObj.date.substring(0, 19).replace(/-/g, '/'))
    return _parseTime(date)
  }

  return dateObj
}

export function conclusionLabel(conclusion) {
  if (conclusion === 'PASS') {
    return '合格'
  }
  if (conclusion === 'NG') {
    return '不合格'
  }
  return conclusion
}
