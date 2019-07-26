<template>
  <div>
    <el-autocomplete
      v-if="hasSuggest()"
      v-model="value"
      :fetch-suggestions="querySearch"
      :select-when-unmatched="true"
      :placeholder="placeholder"
      @blur="$emit('blur')"
      @input="$emit('input', value)"
      @select="handleSelect"
    />
    <el-input
      v-else
      v-model="value"
      :placeholder="placeholder"
      autocomplete="on"
      @blur="$emit('blur')"
      @input="$emit('input', value)"
    />
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: 'ValueInput',
  // 一个输入组件上的 v-model 默认的 prop 名称为 value，默认的事件名称为 input,
  // 但是像单选框、复选框等类型的输入控件可能会将 value 特性用于不同的目的。
  // model 选项可以用来避免这样的冲突
  model: {
    prop: 'dataValue',
    event: 'input'
  },
  props: {
    dataValue: {
      type: String,
      default: ''
    },
    item: { // 检测项目名称
      type: String,
      required: true
    },
    placeholder: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      value: ''
    }
  },
  computed: {
    ...mapState('basedata', {
      suggests: state => state.suggests
    }),
    itemSuggest() {
      // 获取 parent
      const parent = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测项目'
      })

      // 获取 children
      const testItems = this.suggests.filter(suggest => {
        return suggest.parent_id === parent.id
      })

      const itemSuggest = testItems.find(suggest => {
        return suggest.name === this.item
      })

      if (itemSuggest) {
        return itemSuggest.json_data
      }

      return null
    }
  },
  async created() {
    if (this.suggests.length === 0) {
      await this.$store.dispatch('basedata/fetchSuggest')
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.value = this.dataValue
    })
  },
  methods: {
    hasSuggest() {
      return Array.isArray(this.itemSuggest) && this.itemSuggest.length > 0
    },
    querySearch(queryString, cb) {
      // el-autocomplete 需要传入对象数组
      const values = this.itemSuggest.map(value => {
        return { value: value.toString(), label: value }
      })

      const results = queryString
        ? values.filter(element => element.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : values

      cb(results)
    },
    handleSelect(item) {
      // 延迟1s执行
      setTimeout(() => {
        this.$emit('blur')
      }, 1000)
    }
  }
}
</script>
