<template>
  <div class="category-dialog">
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
        <el-table-column align="center" label="名称">
          <template slot-scope="scope">
            <el-input v-if="scope.row.is_edit" v-model="scope.row.name" class="edit-input" size="small"/>
            <span v-else>{{ scope.row.name }}</span>
          </template>
        </el-table-column>

        <el-table-column align="center" label="方法">
          <template slot-scope="scope">
            <el-autocomplete
              v-if="scope.row.is_edit"
              v-model="scope.row.method"
              :fetch-suggestions="queryMethods"
              :debounce="300"
              value-key="name"
              suffix-icon="el-icon-edit"
              placeholder="检测方法"
              @select="handleSelect"/>
            <span v-else>{{ scope.row.method }}</span>
          </template>
        </el-table-column>

        <el-table-column align="center" label="值类型">
          <template slot-scope="scope">
            <el-select v-if="scope.row.is_edit" v-model="scope.row.spec.value_type" placeholder="请选择">
              <el-option
                v-for="item in valueTypes"
                :key="item.code"
                :label="item.name"
                :value="item.code">
                <span style="float: left">{{ item.name }}</span>
                <span style="float: right; color: #8492a6; font-size: 13px">{{ item.code }}</span>
              </el-option>
            </el-select>
            <span v-else>{{ getTypeName(scope.row.spec.value_type) }}</span>
          </template>
        </el-table-column>

        <el-table-column align="center" label="值">
          <template slot-scope="scope">
            <template v-if="scope.row.is_edit">
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
            <template v-else>
              <span v-if="scope.row.spec.value_type === 'RANGE'">
                <i v-show="scope.row.spec.data.min">≥ {{ scope.row.spec.data.min }},</i>
                <i v-show="scope.row.spec.data.max">≤ {{ scope.row.spec.data.max }}</i>
              </span>
              <span v-else>{{ scope.row.spec.data.value }}</span>
            </template>
          </template>
        </el-table-column>

        <el-table-column align="center" label="操作">
          <template slot-scope="scope">
            <template v-if="scope.row.is_edit">
              <el-button class="cancel-btn" size="small" icon="el-icon-refresh" type="warning" @click="cancelEdit(scope.row)">取消</el-button>
              <el-button type="success" size="small" icon="el-icon-circle-check-outline" @click="confirmEdit(scope.row)">Ok</el-button>
            </template>
            <el-button v-else type="primary" size="small" icon="el-icon-edit" @click="scope.row.is_edit=!scope.row.is_edit">编辑</el-button>
          </template>
        </el-table-column>
      </el-table>

      <div slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" icon="el-icon-edit" @click="handleCreate">添加</el-button>
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
import { debounce } from '../../utils'

export function newWaysItem() {
  return {
    is_edit: true,
    name: '',
    method: '',
    method_id: 0,
    spec: {
      value_type: '', // range, info, value
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
          this.updateMethodId()
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
          this.updateMethodId()
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
    cancelEdit() {

    },
    confirmEdit() {

    },
    queryMethods(queryString, cb) {
      const fetch = () => {
        qcMethodApi.list({ params: { q: queryString } }).then(response => {
          const { data } = response.data
          // 调用 callback 返回建议列表的数据
          cb(data)
        })
      }

      debounce(fetch(), 200, true)
    },
    handleSelect(item) {
      this.selectTestMethods.push(item)
    },
    updateMethodId() {
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
