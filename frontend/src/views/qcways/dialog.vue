<template>
  <div class="qc-way-dialog">
    <el-dialog :title="dialogTitleMap[action]" :fullscreen="true" :visible.sync="dialogFormVisible" @close="close">
      <el-form
        ref="obj_form"
        :model="obj"
        :rules="objRules"
        class="small-space"
        label-position="right"
        label-width="70px">

        <el-form-item label="名称" prop="name">  <!--prop 属性设置为需校验的字段名-->
          <el-input v-model="obj.name"/>
        </el-form-item>
      </el-form>

      <el-table :data="obj.way" border fit highlight-current-row style="width: 100%">
        <el-table-column align="center" label="检测项目" width="180">
          <template slot-scope="scope">
            <el-input v-model="scope.row.name" class="edit-input" size="small"/>
          </template>
        </el-table-column>

        <el-table-column align="center" label="检测方法">
          <template slot-scope="scope">
            <el-autocomplete
              v-model="scope.row.method"
              :fetch-suggestions="queryMethods"
              :debounce="300"
              value-key="name"
              suffix-icon="el-icon-edit"
              placeholder="检测方法"
              @select="handleSelect"/>
          </template>
        </el-table-column>

        <el-table-column align="center" label="值类型">
          <template slot-scope="scope">
            <el-select v-model="scope.row.spec.value_type" placeholder="请选择">
              <el-option
                v-for="item in valueTypes"
                :key="item.code"
                :label="item.name"
                :value="item.code">
                <span style="float: left">{{ item.name }}</span>
                <span style="float: right; color: #8492a6; font-size: 13px">{{ item.code }}</span>
              </el-option>
            </el-select>
          </template>
        </el-table-column>

        <el-table-column align="center" label="值">
          <template slot-scope="scope">
            <template v-if="scope.row.spec.value_type === 'RANGE'">
              <el-row :gutter="20">
                <el-col :span="12">
                  <el-tooltip class="item" effect="dark" content="最小值, 留空为无下限" placement="top-start">
                    <el-input-number v-model="scope.row.spec.data.min" :precision="2" :step="0.1" size="mini"/>
                  </el-tooltip>
                </el-col>
                <el-col :span="12">
                  <el-tooltip class="item" effect="dark" content="最大值, 留空为无上限" placement="top-start">
                    <el-input-number v-model="scope.row.spec.data.max" :precision="2" :step="0.1" size="mini"/>
                  </el-tooltip>
                </el-col>
              </el-row>
            </template>
            <el-input v-else v-model="scope.row.spec.data.value" placeholder="要求值"/>
          </template>
        </el-table-column>
      </el-table>

      <div slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" icon="el-icon-edit" @click="handleCreate">添加项目</el-button>
        <el-button v-if="action==='create'" type="primary" @click="create()">确 定</el-button>
        <el-button v-else type="primary" @click="update()">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import dialog from '@/mixins/dialog'
import { valueTypes } from '@/mixins/const'
import { qcWayApi, qcMethodApi } from '@/api/qc'
// import { debounce } from '../../utils'

export function newWaysItem() {
  return {
    is_edit: true,
    name: '',
    method: '',
    method_id: 0,
    spec: {
      value_type: '', // RANGE, INFO, VALUE
      data: {
        min: undefined,
        max: undefined,
        value: ''
      }
    }
  }
}

export function newObj() {
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
}

export default {
  name: 'Dialog',
  mixins: [
    valueTypes,
    dialog
  ],
  data() {
    return {
      api: qcWayApi,
      selectTestMethods: [],
      objRules: {
        name: { required: true, message: '必填项', trigger: 'blur' }
      }
    }
  },
  methods: {
    newObj() {
      return Object.assign({}, newObj())
    },
    getTypeName(code) {
      const typeObj = this.valueTypes.find(element => element.code === code)
      return typeObj ? typeObj.name : ''
    },
    create() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          this.obj.user_id = this.user.id
          this.updateWay()
          this.api.add(this.obj).then(response => {
            const { data } = response.data
            this.obj = data // 灰常重要！！！
            this.done()
          })
        } else {
          return false
        }
      })
    },
    update() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          this.obj.modified_user_id = this.user.id
          this.updateWay()
          this.api.update(this.obj.id, this.obj).then(response => {
            const { data } = response.data
            this.obj = data // 灰常重要！！！
            this.done()
          })
        } else {
          return false
        }
      })
    },
    handleCreate() {
      this.obj.way.push(newWaysItem())
    },
    queryMethods(queryString, cb) {
      const fetch = () => {
        qcMethodApi.list({ params: { q: queryString } }).then(response => {
          const { data } = response.data
          // 调用 callback 返回建议列表的数据
          cb(data)
        })
      }

      fetch()
      // debounce(fetch(), 200, true) // element-ui 自带 debounce
    },
    handleSelect(item) {
      this.selectTestMethods.push(item)
    },
    updateWay() {
      this.obj.way = this.obj.way.map(element => {
        delete element.is_edit // 删除 is_edit 属性
        const method = this.selectTestMethods.find(method => method.name === element.method)
        if (method) {
          element.method_id = method.id
        }
        return element
      })
    }
  }
}
</script>

<style scoped>

</style>
