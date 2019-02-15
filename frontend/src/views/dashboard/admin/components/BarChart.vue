<template>
  <div :class="className" :style="{height:height,width:width}"/>
</template>

<script>
import echarts from 'echarts'

require('echarts/theme/macarons') // echarts theme
import { debounce } from '@/utils'

const animationDuration = 6000

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
  computed: {
    data() {
      return this.failedStatistics.filter(el => el.category_id === 0 && el.qc_type === this.type)
        .map(el => {
          return { value: el.amount, name: el.item }
        }).sort((a, b) => {
          if (+a.value > +b.value) {
            return -1
          }
          if (+a.value < +b.value) {
            return 1
          }
          return 0
        })
    },
    labelData() {
      return this.data.map(el => el.name)
    },
    total() {
      let total = 0
      for (const el of this.data) {
        total += el.value
      }
      return total
    },
    rateData() {
      let totalRate = 0
      if (this.total) {
        return this.data.map(el => {
          totalRate += +(el.value / this.total * 100).toFixed(2)
          return totalRate
        })
      }
      return []
    }
  },
  watch: {
    failedStatistics() {
      this.chart.setOption({
        xAxis: [{
          data: this.labelData
        }],
        series: [{
          name: '不合格项数量',
          data: this.data
        }, {
          name: '累计百分比',
          data: this.rateData
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
          trigger: 'axis',
          axisPointer: { // 坐标轴指示器，坐标轴触发有效
            type: 'cross'
          }
        },
        grid: {
          top: 10,
          left: '2%',
          right: '2%',
          bottom: '3%',
          containLabel: true
        },
        toolbox: {
          feature: {
            dataView: { show: true, readOnly: false },
            restore: { show: true },
            saveAsImage: { show: true }
          }
        },
        legend: {
          data: ['不合格项数量', '累计百分比']
        },
        xAxis: [{
          type: 'category',
          data: [],
          axisTick: {
            alignWithLabel: true
          }
        }],
        yAxis: [{
          type: 'value',
          name: '不合格项数量',
          axisLabel: {
            formatter: '{value} 批'
          }
        }, {
          type: 'value',
          name: '累计百分比',
          min: 0,
          max: 100,
          position: 'right',
          axisLabel: {
            formatter: '{value} %'
          }
        }],
        series: [{
          name: '不合格项数量',
          type: 'bar',
          data: [],
          animationDuration
        }, {
          name: '累计百分比',
          type: 'line',
          yAxisIndex: 1,
          data: []
        }]
      })
    }
  }
}
</script>
