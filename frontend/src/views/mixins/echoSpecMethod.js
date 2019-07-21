export default {
  methods: {
    echoSpec(spec) {
      if (spec.value_type === 'INFO' || spec.value_type === 'NUMBER') {
        return spec.data.value
      }

      if (spec.value_type === 'RANGE') {
        let result = ''
        if (spec.data.min) {
          result += `≥ ${spec.data.min}, `
        }
        if (spec.data.max) {
          result += `≤ ${spec.data.max}, `
        }

        return result
      }

      if (spec.value_type === 'ONLY_SHOW') {
        // value is "要求|结果值"
        const tmpArr = spec.data.value.split('|')
        if (tmpArr.length > 0) {
          return tmpArr[0]
        }
        return ''
      }
    }
  }
}
