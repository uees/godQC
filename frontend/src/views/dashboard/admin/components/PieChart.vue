<template>
  <div
    :class="className"
    :style="{height:height,width:width}"
  />
</template>

<script>
import echarts from 'echarts'

require('echarts/theme/macarons') // echarts theme
import { debounce } from '@/utils'

export default {
  props: {
    className: {
      type: String,
      default: 'chart'
    },
    width: {
      type: String,
      default: '100%'
    },
    height: {
      type: String,
      default: '300px'
    },
    failedStatistics: {
      type: Array,
      required: true
    },
    type: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      chart: null
    }
  },
  watch: {
    failedStatistics(val) {
      this.chart.setOption({
        series: [{
          data: this.getPieData()
        }]
      })
    }
  },
  mounted() {
    this.initChart()
    this.__resizeHandler = debounce(() => {
      if (this.chart) {
        this.chart.resize()
      }
    }, 100)
    window.addEventListener('resize', this.__resizeHandler)
  },
  beforeDestroy() {
    if (!this.chart) {
      return
    }
    window.removeEventListener('resize', this.__resizeHandler)
    this.chart.dispose()
    this.chart = null
  },
  methods: {
    initChart() {
      this.chart = echarts.init(this.$el, 'macarons')

      this.chart.setOption({
        tooltip: {
          trigger: 'item',
          formatter: '{a} <br/>{b} : {c} ({d}%)'
        },
        legend: {
          left: 'center',
          bottom: '10'
        },
        calculable: true,
        series: [
          {
            name: '不合格分类',
            type: 'pie',
            radius: [0, 95],
            center: ['50%', '45%'],
            data: this.getPieData()
          }
        ]
      })
    },
    getPieData() {
      return this.failedStatistics.filter(el => el.category_id === 0 && el.qc_type === this.type)
        .map(el => Object.assign({}, { value: el.amount, name: el.item }))
        .sort((a, b) => +a.value - +b.value)
    }
  }
}
</script>
