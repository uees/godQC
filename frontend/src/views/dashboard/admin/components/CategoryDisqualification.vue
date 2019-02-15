<template>
  <div class="category-disqualification">
    <h3>按产品类别统计的一次检验合格率</h3>
    <el-table :data="list" style="width: 100%; margin-bottom: 15px;">
      <el-table-column sortable sort-by="category.name" label="类别" min-width="150">
        <template slot-scope="scope">
          {{ scope.row.category.name }}
        </template>
      </el-table-column>
      <el-table-column prop="tests_num" sortable label="检测数" align="center"/>
      <el-table-column prop="once_disqualification_num" sortable label="一次不合格" align="center"/>
      <el-table-column :sort-method="sortRate" sortable label="一次合格率" align="center">
        <template slot-scope="scope">
          <el-tag :type="oncePassRate(scope.row) | statusFilter">
            {{ oncePassRate(scope.row) }}%
          </el-tag>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
export default {
  name: 'CategoryDisqualification',
  filters: {
    statusFilter(rate) {
      return rate > 95 ? 'success' : 'danger'
    }
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
  computed: {
    list() {
      return this.totalStatistics.filter(el => {
        return el.category_id !== 0 && el.tests_num > 0 && el.qc_type === this.type
      })
    }
  },
  methods: {
    oncePassRate(row) {
      if (row.tests_num === 0) return 100
      const rate = 100 - row.once_disqualification_num / row.tests_num * 100
      // toFixed 返回类型是 String, 这里转为数字
      return +(rate.toFixed(2))
    },
    sortRate(a, b) {
      const rateA = this.oncePassRate(a)
      const rateB = this.oncePassRate(b)

      // 比较数字而非字符串，比较函数可以简单的以 a 减 b
      return rateA - rateB
    }
  }
}
</script>
