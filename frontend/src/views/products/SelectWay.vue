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
        <el-button
          type="primary"
          @click="submit"
        >确定</el-button>
        <el-button
          v-if="testWay.id"
          type="success"
          icon="el-icon-edit"
          @click="handleEditWay"
        >编辑</el-button>
        <el-button
          v-else
          type="warning"
          @click="handleCreateWay"
        >创建</el-button>
      </el-form>
    </el-dialog>

    <way-form
      :action="WayFormAction"
      :form-data="WayFormData"
      @action-done="onWayFormActionDone"
    />
  </div>
</template>

<script>
import { qcWayApi, productSelectTestWay } from '@/api/qc'
import { deepClone } from '@/utils'
import { TestWay } from '@/defines/models'
import Bus from '@/store/bus'
import WayForm from '../qcways/dialog'

export default {
  name: 'SelectWay',
  components: {
    WayForm
  },
  data() {
    return {
      product: undefined,
      productIndex: -1,
      testWay: this.newWay(),
      dialogFormVisible: false,

      // WayForm props
      WayFormAction: '',
      WayFormData: this.newWay()
    }
  },
  mounted() {
    Bus.$on('product-select-way', (scope) => {
      // 初始化
      this.product = deepClone(scope.row)
      this.productIndex = scope.$index
      if (this.product.testWay) {
        this.testWay = this.product.testWay
      } else {
        this.testWay = this.newWay()
      }
      // 显示
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
        cb(data)
      })
    },
    handleCreateWay() {
      this.WayFormAction = 'create'
      this.WayFormData = this.newWay()
    },
    handleEditWay() {
      this.WayFormAction = 'update'
      this.WayFormData = this.testWay
    },
    submit() {
      productSelectTestWay(this.product.id, this.testWay.id).then(() => {
        this.$emit('test-way-selected', this.productIndex, this.testWay)
        this.done()
      })
    },
    handleSelect(way) {
      this.testWay = Object.assign({}, way)
    },
    onWayFormActionDone(way) {
      this.testWay = way
    },
    done() {
      this.$notify({
        title: '成功',
        message: '操作成功',
        type: 'success',
        duration: 2000
      })
      this.resetWay()
      this.close()
    },
    close() {
      this.dialogFormVisible = false
      this.WayFormAction = ''
      this.WayFormData = undefined
    }
  }
}
</script>
