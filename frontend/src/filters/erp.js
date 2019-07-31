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

// 输出注意事项
export function noteMatters(record) {
  let result = ''

  for (const item of record.items) {
    if (item.item === '桥线' && item.spec.data) {
      if ((item.spec.data.value && item.spec.data.value !== '做记录') || item.spec.data.max) {
        result += '桥线,'
      }
    } else if (item.item === '表面张力') {
      result += '测表面张力,'
    } else if (item.item === '黑点') {
      result += '测黑点,'
    } else if (item.item === '重检粘度') {
      result += '重检粘度,'
    } else if (item.item === '混合粘度') {
      result += '混合粘度,'
    } else if (item.item === '注意事项' && item.spec.data) {
      result += item.spec.data.value
    }
  }

  return result
}

export function mixNoteMatters(record) {
  let result = ''

  // only 配油 混合粘度
  if (record.items.length === 2) {
    result += '主剂已经配其他固化剂测过，现在只测混合粘度'
  }

  for (const item of record.items) {
    if (item.item === '桥线' && item.spec.data) {
      if ((item.spec.data.value && item.spec.data.value !== '做记录') || item.spec.data.max) {
        result += '桥线,'
      }
    } else if (item.item === '表面张力') {
      result += '测表面张力,'
    } else if (item.item === '黑点') {
      result += '测黑点,'
    } else if (item.item === '重检粘度') {
      result += '重检粘度,'
    } else if (item.item === '注意事项' && item.spec.data) {
      result += item.spec.data.value
    }
  }

  return result
}

// 输出配比要求
export function mixinTips(record) {
  const item = record.items.find(item => item.item === '配油')
  if (item && item.spec && item.spec.data) {
    return item.spec.data.value
  }

  return ''
}
