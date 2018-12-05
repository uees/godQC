export default {
  methods: {
    echoSpec(spec) {
      if (spec.value_type === 'info' || spec.value_type === 'number') {
        return spec.data.value
      } else if (spec.value_type === 'range') {
        let result = ''
        if (spec.data.min) {
          result += `>= ${spec.data.min}, `
        }
        if (spec.data.max) {
          result += `<= ${spec.data.max}, `
        }

        return result
      }
    }
  }
}
