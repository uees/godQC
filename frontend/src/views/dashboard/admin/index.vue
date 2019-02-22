<template>
  <div class="dashboard-editor-container">
    <div class="filter-container">
      <el-select
        v-model="type"
        class="filter-item"
        placeholder="类别"
        @change="fetchData">
        <el-option label="成品检测" value="FQC"/>
        <el-option label="来料检测" value="IQC"/>
      </el-select>
      <el-date-picker
        v-model="date"
        type="month"
        class="filter-item"
        placeholder="选择月份"
        @change="fetchData"
      />
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-edit"
        @click="handleCreate">生成
      </el-button>
    </div>

    <panel-group
      :total-statistics="monthStatistics.totalStatistics"
      :type="type"
      @handleSetLineChartData="handleSetLineChartData"/>

    <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:32px;">
      <line-chart :chart-data="lineChartData"/>
    </el-row>

    <category-disqualification
      :total-statistics="monthStatistics.totalStatistics"
      :type="type"/>

    <el-row :gutter="32">
      <h3 style="padding-left: 16px">不合格项目统计</h3>
      <el-col :xs="24" :sm="24" :lg="16">
        <div class="chart-wrapper">
          <bar-chart :failed-statistics="monthStatistics.failedStatistics" :type="type"/>
        </div>
      </el-col>
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <pie-chart :failed-statistics="monthStatistics.failedStatistics" :type="type"/>
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="8">
      <h3 style="padding-left: 16px">不合格批次流水</h3>
      <el-col :xs="{span: 24}"
              :sm="{span: 24}"
              :md="{span: 24}"
              :lg="{span: 24}"
              :xl="{span: 24}"
              style="padding-right:8px;margin-bottom:30px;">
        <disqualification-records :failed-records="monthFailedRecords" :type="type"/>
      </el-col>
    </el-row>

  </div>
</template>

<script>
import GithubCorner from '@/components/GithubCorner'
import PanelGroup from './components/PanelGroup'
import CategoryDisqualification from './components/CategoryDisqualification'
import LineChart from './components/LineChart'
import PieChart from './components/PieChart'
import BarChart from './components/BarChart'
import DisqualificationRecords from './components/DisqualificationRecords'
import { showStatistics, showFailedAll, showStatisticsShape, makeTestStatistics, makeDisqualificationStatistics } from '@/api/qc'

export default {
  name: 'DashboardAdmin',
  components: {
    DisqualificationRecords,
    CategoryDisqualification,
    GithubCorner,
    PanelGroup,
    LineChart,
    PieChart,
    BarChart
  },
  data() {
    return {
      date: new Date(),
      type: 'FQC',
      monthStatistics: {
        totalStatistics: [],
        failedStatistics: []
      },
      monthFailedRecords: [],
      statisticsShape: [],
      lineChartData: {
        values: [],
        rates: [],
        type: ''
      }
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    handleSetLineChartData(type) {
      const data = this.statisticsShape.filter(el => el.qc_type === this.type && !el.category_id)
      if (type === 'tests_num') {
        this.lineChartData.values = data.map(el => el.tests_num)
        this.lineChartData.rates = data.map(el => this.oncePassRate(el))
      } else if (type === 'once_disqualification_num') {
        this.lineChartData.values = data.map(el => el.once_disqualification_num)
        this.lineChartData.rates = data.map(el => this.oncePassRate(el))
      } else if (type === 'disqualification_num') {
        this.lineChartData.values = data.map(el => el.disqualification_num)
        this.lineChartData.rates = data.map(el => this.passRate(el))
      } else if (type === 'force_accept_num') {
        this.lineChartData.values = data.map(el => el.force_accept_num)
        this.lineChartData.rates = data.map(el => this.forceRate(el))
      }

      this.lineChartData.values = this.lineChartData.values
        .concat(Array(12 - this.lineChartData.values.length).fill(0))
      this.lineChartData.rates = this.lineChartData.rates
        .concat(Array(12 - this.lineChartData.rates.length).fill(0))
      this.lineChartData.type = type
    },
    fetchData() {
      const date = this.date ? this.date : new Date()
      const year = date.getFullYear()
      const month = date.getMonth() + 1
      showStatistics(year, month, this.type).then(response => {
        const { data } = response.data
        this.monthStatistics = data
      })
      showFailedAll(year, month, this.type).then(response => {
        const { data } = response.data
        this.monthFailedRecords = data
      })
      showStatisticsShape(year).then(response => {
        const { data } = response.data
        this.statisticsShape = data
        this.handleSetLineChartData('tests_num')
      })
    },
    handleCreate() {
      if (this.date) {
        const year = this.date.getFullYear()
        const month = this.date.getMonth() + 1
        makeTestStatistics(year, month, this.type).then(() => {
          this.$message({ type: 'success', message: '统计报表完成' })
          makeDisqualificationStatistics(year, month, this.type).then(() => {
            this.$message({ type: 'success', message: '统计不合格记录完成' })
            this.fetchData()
          })
        })
      }
    },
    oncePassRate(st) {
      if (st.tests_num === 0) return 0
      const rate = 100 - st.once_disqualification_num / st.tests_num * 100
      return +rate.toFixed(2)
    },
    passRate(st) {
      if (st.tests_num === 0) return 0
      const rate = 100 - st.disqualification_num / st.tests_num * 100
      return +rate.toFixed(2)
    },
    forceRate(st) {
      if (st.once_disqualification_num === 0) return 0
      const rate = st.force_accept_num / st.once_disqualification_num * 100
      return +rate.toFixed(2)
    },
    dump(val) {
      console.log(val)
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .dashboard-editor-container {
    padding: 32px;
    background-color: rgb(240, 242, 245);

    .chart-wrapper {
      background: #fff;
      padding: 16px 16px 0;
      margin-bottom: 32px;
    }
  }
</style>
