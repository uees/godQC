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
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" @click="submit">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { qcWayApi, categorySelectTestWay } from '@/api/qc'
import { categoryApi } from '@/api/basedata'
import Bus from '@/store/bus'

export default {
  name: 'SelectWay',
  data() {
    return {
      categoryId: 0,
      way: this.newWay(),
      ways: [],
      dialogFormVisible: false
    }
  },
  mounted() {
    Bus.$on('category-select-way', (categoryId) => {
      this.dialogFormVisible = true
      this.categoryId = categoryId
      // get the category way
      categoryApi.detail(categoryId, {params: {with: 'testWays'}}).then(response => {
        const { data } = response.data
        const ways = data.testWays
        // if way this.way = way
        if (ways && ways.length !== 0) {
          this.way = ways[0]
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
        id: 0,
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
    submit() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          categorySelectTestWay(this.categoryId, this.way.id).then(response => {
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

<style scoped>

</style>
