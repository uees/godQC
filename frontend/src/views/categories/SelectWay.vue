<template>
  <div class="select-way-dialog">
    <el-dialog
      :visible.sync="dialogFormVisible"
      title="选择检测流程"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="testWay"
        class="small-space"
        label-position="right"
        label-width="70px"
      >

        <el-form-item label="检测流程">
          <el-autocomplete
            v-model="testWay.name"
            :fetch-suggestions="queryWays"
            :debounce="300"
            value-key="name"
            suffix-icon="el-icon-edit"
            placeholder="检测流程"
            @select="handleSelect"
          />
        </el-form-item>
      </el-form>

      <div
        slot="footer"
        class="dialog-footer"
      >
        <el-button
          type="primary"
          @click="submit"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { qcWayApi, categorySelectTestWay } from '@/api/qc'
import Bus from '@/store/bus'
import { deepClone } from '@/utils'
import { TestWay } from '@/defines/models'

export default {
  name: 'SelectWay',
  data() {
    return {
      category: null,
      tableDataIndex: -1,
      testWay: this.newWay(),
      dialogFormVisible: false
    }
  },
  mounted() {
    Bus.$on('category-select-way', (scope) => {
      this.category = deepClone(scope.row)
      this.tableDataIndex = scope.$index
      if (this.category.testWay) {
        this.testWay = this.category.testWay
      } else {
        this.testWay = this.newWay()
      }
      this.dialogFormVisible = true
    })
  },
  methods: {
    newWay() {
      return TestWay()
    },
    resetWay() {
      this.testWay = this.newWay()
    },
    queryWays(queryString, cb) {
      qcWayApi.list({ params: { q: queryString }}).then(response => {
        const { data } = response.data
        // 调用 callback 返回建议列表的数据
        cb(data)
      })
    },
    handleSelect(testWay) {
      this.testWay = Object.assign({}, testWay)
    },
    submit() {
      categorySelectTestWay(this.category.id, this.testWay.id).then(response => {
        this.$emit('test-way-updated', this.tableDataIndex, this.testWay)
        this.done()
      })
    },
    done() {
      this.$notify({
        title: '成功',
        message: '操作成功',
        type: 'success',
        duration: 2000
      })
      this.close()
    },
    close() {
      this.dialogFormVisible = false
      this.resetWay()
    }
  }
}
</script>
