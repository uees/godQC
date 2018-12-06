export default {
  methods: {
    echoSpec(spec) {
      if (spec.value_type === 'INFO' || spec.value_type === 'NUMBER') {
        return spec.data.value
      } else if (spec.value_type === 'RANGE') {
        let result = ''
        if (spec.data.min) {
          result += `≥ ${spec.data.min}, `
        }
        if (spec.data.max) {
          result += `≤ ${spec.data.max}, `
        }

        return result
      }
    }
  }
}
