<template>
  <el-row :gutter="40" class="panel-group">
    <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('tests_num')">
        <div class="card-panel-icon-wrapper icon-people">
          <svg-icon icon-class="list" class-name="card-panel-icon"/>
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">检测数量</div>
          <count-to :start-val="0" :end-val="top.tests_num" :duration="200" class="card-panel-num"/>
        </div>
      </div>
    </el-col>
    <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('once_disqualification_num')">
        <div class="card-panel-icon-wrapper icon-message">
          <svg-icon icon-class="bug" class-name="card-panel-icon"/>
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">一次不合格</div>
          <count-to :start-val="0" :end-val="top.once_disqualification_num" :duration="5" class="card-panel-num"/>
          <span v-if="passRate > 95" style="color: green">一次合格率: {{ oncePassRate }}%</span>
          <span v-else style="color: red">一次合格率: {{ oncePassRate }}%</span>
        </div>
      </div>
    </el-col>
    <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('disqualification_num')">
        <div class="card-panel-icon-wrapper icon-money">
          <svg-icon icon-class="bug" class-name="card-panel-icon"/>
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">不合格</div>
          <count-to :start-val="0" :end-val="top.disqualification_num" :duration="2" class="card-panel-num"/>
          <span v-if="passRate > 99" style="color: green">合格率: {{ passRate }}%</span>
          <span v-else style="color: red">合格率: {{ passRate }}%</span>
        </div>
      </div>
    </el-col>
    <el-col :xs="12" :sm="12" :lg="6" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('force_accept_num')">
        <div class="card-panel-icon-wrapper icon-shopping">
          <svg-icon icon-class="people" class-name="card-panel-icon"/>
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">特采</div>
          <count-to :start-val="0" :end-val="top.force_accept_num" :duration="2" class="card-panel-num"/>
          <span style="color: #c26a3e">特采率: {{ forceRate }}%</span>
        </div>
      </div>
    </el-col>
  </el-row>
</template>

<script>
import CountTo from 'vue-count-to'

export default {
  components: {
    CountTo
  },
  props: {
    totalStatistics: {
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

    }
  },
  computed: {
    top() {
      const top = this.getTop()
      if (top) {
        return top
      }
      return {
        tests_num: 0,
        once_disqualification_num: 0,
        disqualification_num: 0,
        force_accept_num: 0
      }
    },
    oncePassRate() {
      if (this.top.tests_num === 0) return 100
      const rate = 100 - this.top.once_disqualification_num / this.top.tests_num * 100
      return +(rate.toFixed(2))
    },
    passRate() {
      if (this.top.tests_num === 0) return 100
      const rate = 100 - this.top.disqualification_num / this.top.tests_num * 100
      return +(rate.toFixed(2))
    },
    forceRate() {
      if (this.top.once_disqualification_num === 0) return 0
      const rate = this.top.force_accept_num / this.top.once_disqualification_num * 100
      return +(rate.toFixed(2))
    }
  },
  methods: {
    handleSetLineChartData(type) {
      this.$emit('handleSetLineChartData', type)
    },
    getTop() {
      return this.totalStatistics.find(el => el.category_id === 0 && el.qc_type === this.type)
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .panel-group {
    margin-top: 18px;

    .card-panel-col {
      margin-bottom: 32px;
    }

    .card-panel {
      height: 108px;
      cursor: pointer;
      font-size: 12px;
      position: relative;
      overflow: hidden;
      color: #666;
      background: #fff;
      box-shadow: 4px 4px 40px rgba(0, 0, 0, .05);
      border-color: rgba(0, 0, 0, .05);

      &:hover {
        .card-panel-icon-wrapper {
          color: #fff;
        }

        .icon-people {
          background: #40c9c6;
        }

        .icon-message {
          background: #36a3f7;
        }

        .icon-money {
          background: #f4516c;
        }

        .icon-shopping {
          background: #34bfa3
        }
      }

      .icon-people {
        color: #40c9c6;
      }

      .icon-message {
        color: #36a3f7;
      }

      .icon-money {
        color: #f4516c;
      }

      .icon-shopping {
        color: #34bfa3
      }

      .card-panel-icon-wrapper {
        float: left;
        margin: 14px 0 0 14px;
        padding: 16px;
        transition: all 0.38s ease-out;
        border-radius: 6px;
      }

      .card-panel-icon {
        float: left;
        font-size: 48px;
      }

      .card-panel-description {
        float: right;
        font-weight: bold;
        margin: 26px;
        margin-left: 0;

        .card-panel-text {
          line-height: 18px;
          color: rgba(0, 0, 0, 0.45);
          font-size: 16px;
          margin-bottom: 12px;
        }

        .card-panel-num {
          font-size: 20px;
        }
      }
    }
  }
</style>
