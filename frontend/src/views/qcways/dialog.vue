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
            <el-autocomplete
              v-model="scope.row.name"
              :fetch-suggestions="querySearchItems"
              :select-when-unmatched="true"
              size="small"
            />
          </template>
        </el-table-column>

        <el-table-column align="center" label="检测方法">
          <template slot-scope="scope">
            <el-autocomplete
              v-model="scope.row.method"
              :fetch-suggestions="querySearchMethods"
              :debounce="300"
              value-key="name"
              suffix-icon="el-icon-edit"
              placeholder="检测方法"
              @select="handleSelect"/>
          </template>
        </el-table-column>

        <el-table-column align="center" label="检测值数据类型">
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

        <el-table-column align="center" label="要求">
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

        <el-table-column align="center" label="是否展示">
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.spec.is_show"
              active-text="是"
              inactive-text="否"/>
          </template>
        </el-table-column>

        <el-table-column align="center" label="操作" width="100" class-name="small-padding fixed-width">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="handleInsert(scope.row)">插入</el-button>
            <el-button type="text" size="small" @click="handleDelete(scope.row)">删除</el-button>
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
import testItemSuggestions from '@/mixins/testItemSuggestions'
import testMethodSuggestions from '@/mixins/testMethodSuggestions'
import { qcWayApi } from '@/api/qc'

export function newWaysItem() {
  return {
    name: '',
    method: '',
    method_id: 0,
    spec: {
      is_show: true,
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
    testItemSuggestions,
    testMethodSuggestions,
    dialog
  ],
  data() {
    return {
      api: qcWayApi,
      selectTestMethods: [],
      objRules: {
        name: { required: true, message: '必填项', trigger: 'blur' }
      },
      valueTypes: [
        { code: 'RANGE', name: '范围' },
        { code: 'INFO', name: '信息' },
        { code: 'VALUE', name: '具体值' },
        { code: 'ONLY_SHOW', name: '仅展示' }
      ]
    }
  },
  mounted() {
    this.$nextTick(function () {
      this.initSuggest()
    })
  },
  methods: {
    newObj() {
      return newObj()
    },
    create() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          this.obj.user_id = this.user.id
          this.updateWay()
          this.api.add(this.obj).then(response => {
            const { data } = response.data
            this.obj = data
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
            this.obj = data
            this.done()
          })
        } else {
          return false
        }
      })
    },
    handleSelect(item) {
      this.selectTestMethods.push(item)
    },
    handleCreate() {
      this.obj.way.push(newWaysItem())
    },
    handleInsert(row) {
      const index = this.obj.way.indexOf(row)
      this.obj.way.splice(index, 0, this.newWaysItem())
    },
    handleDelete(row) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const index = this.obj.way.indexOf(row)
        this.obj.way.splice(index, 1)
      })
    },
    updateWay() {
      this.obj.way = this.obj.way.map(element => {
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
