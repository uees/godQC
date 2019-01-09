<template>
  <div class="select-way-dialog">
    <el-dialog :visible.sync="dialogFormVisible" title="选择检测流程" @close="close">
      <el-form
        ref="obj_form"
        :model="way"
        class="small-space"
        label-position="right"
        label-width="70px">

        <el-form-item label="检测流程">
          <el-autocomplete
            v-model="way.name"
            :fetch-suggestions="queryWays"
            :debounce="300"
            value-key="name"
            suffix-icon="el-icon-edit"
            placeholder="检测流程"
            @select="handleSelect"
          />
          <el-button
            v-show="!way.name"
            type="primary"
            icon="el-icon-edit"
            @click="handleCreateWay">添加
          </el-button>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" @click="submit">确 定</el-button>
      </div>
    </el-dialog>

    <way-form :action.sync="WayFormAction" :prop-obj.sync="WayFormObj"/>
  </div>
</template>

<script>
import { qcWayApi, productSelectTestWay } from '@/api/qc'
import { productApi } from '@/api/basedata'
import Bus from '@/store/bus'
import WayForm from '../qcways/dialog'

export default {
  name: 'SelectWay',
  components: {
    WayForm
  },
  data() {
    return {
      productId: 0,
      way: this.newWay(),
      ways: [],
      dialogFormVisible: false,
      WayFormAction: '',
      WayFormObj: undefined
    }
  },
  mounted() {
    Bus.$on('product-select-way', (productId) => {
      this.dialogFormVisible = true
      this.productId = productId
      // get the category way
      productApi.detail(productId, {params: {with: 'testWay'}}).then(response => {
        const { data } = response.data
        if (data.testWay) {
          this.way = data.testWay
        }
      })
    })
    this.$nextTick(function () {
      // this.way = category way
    })
  },
  methods: {
    newWay() {
      return {
        id: null,
        name: '',
        way: [],
        created_at: {
          date: '',
          timezone_type: '',
          timezone: ''
        },
        updated_at: {
          date: '',
          timezone_type: '',
          timezone: ''
        }
      }
    },
    resetWay() {
      this.way = this.newWay()
    },
    queryWays(queryString, cb) {
      qcWayApi.list({ params: { q: queryString } }).then(response => {
        const { data } = response.data
        // 调用 callback 返回建议列表的数据
        cb(data)
      })
    },
    handleSelect(way) {
      this.way = way
    },
    handleCreateWay() {
      this.WayFormAction = 'create'
    },
    submit() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          productSelectTestWay(this.productId, this.way.id).then(response => {
            this.done()
          })
        } else {
          return false
        }
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
