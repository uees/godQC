<template>
  <div>
    <el-autocomplete
      v-if="hasSuggest()"
      v-model="value"
      :fetch-suggestions="querySearch"
      :select-when-unmatched="true"
      @blur="$emit('blur')"
      @select="handleSelect"
    />
    <el-input
      v-else
      v-model="value"
      @blur="$emit('blur')"
    />
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: 'ValueInput',
  // 一个组件上的 v-model 默认会利用名为 value 的 prop 和名为 input 的事件，
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
    item: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      value: '',
      testItems: [],
      suggest: null
    }
  },
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    })
  },
  watch: {
    value: function(val) {
      this.$emit('input', val)
    }
  },
  created() {
    if (this.suggests.length === 0) {
      this.$store.dispatch('basedata/FetchSuggest').then(() => {
        this.fetchTestItems()
      })
    }
  },
  mounted() {
    this.value = this.dataValue
    this.$nextTick(function() {
      this.fetchTestItems()
    })
  },
  methods: {
    fetchTestItems() {
      // 获取 parent
      const parent = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '检测项目'
      })

      // 获取 children
      this.testItems = this.suggests.filter(suggest => {
        return suggest.parent_id === parent.id
      })

      this.initSuggest()
    },
    initSuggest() {
      const suggestIndex = this.testItems.findIndex(suggest => {
        return suggest.name === this.item
      })

      if (suggestIndex >= 0) {
        this.suggest = this.testItems[suggestIndex].data
      }
    },
    hasSuggest() {
      return Array.isArray(this.suggest) && this.suggest.length > 0
    },
    querySearch(queryString, cb) {
      // el-autocomplete 需要传入对象数组
      const values = this.suggest.map(value => {
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
