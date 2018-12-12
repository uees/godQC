<template>
  <div>
    <el-dialog :title="titleMap[action]" :visible.sync="visible" @close="close">
      <el-form
        ref="obj_form"
        :model="item"
        :rules="rules"
        label-position="right"
        label-width="100px">

        <el-form-item label="检测项目" prop="item">
          <el-autocomplete
            v-model="item.item"
            :fetch-suggestions="querySearchItems"
            :select-when-unmatched="true"
            size="small"
          />
        </el-form-item>

        <el-form-item label="值类型" prop="spec.value_type">
          <el-select v-model="item.spec.value_type" placeholder="请选择">
            <el-option
              v-for="item in valueTypes"
              :key="item.code"
              :label="item.name"
              :value="item.code">
              <span style="float: left">{{ item.name }}</span>
              <span style="float: right; color: #8492a6; font-size: 13px">{{ item.code }}</span>
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="要求" prop="spec.data.value">
          <template v-if="item.spec.value_type === 'RANGE'">
            <el-row :gutter="20">
              <el-col :span="12">
                <el-tooltip class="item" effect="dark" content="最小值, 留空为无下限" placement="top-start">
                  <el-input-number v-model="item.spec.data.min" :precision="2" :step="0.1" size="mini"/>
                </el-tooltip>
              </el-col>
              <el-col :span="12">
                <el-tooltip class="item" effect="dark" content="最大值, 留空为无上限" placement="top-start">
                  <el-input-number v-model="item.spec.data.max" :precision="2" :step="0.1" size="mini"/>
                </el-tooltip>
              </el-col>
            </el-row>
          </template>
          <el-input v-else v-model="item.spec.data.value"/>
        </el-form-item>

        <el-form-item label="是否展示">
          <el-switch v-model="item.spec.is_show"/>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button v-if="action==='create'" type="primary" @click="create">确 定</el-button>
        <el-button v-else type="primary" @click="update">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Bus from '@/store/bus'
import { valueTypes } from '@/mixins/const'
import testItemSuggestions from '@/mixins/testItemSuggestions'
import { updateRecordItem, addRecordItem } from '@/api/qc'

export default {
  name: 'ItemForm',
  mixins: [
    valueTypes,
    testItemSuggestions
  ],
  data() {
    return {
      action: 'update',
      record: null,
      recordIndex: -1,
      item: this.newItem(),
      itemIndex: -1,
      rules: {
        item: { required: true, message: '必填项', trigger: 'blur' },
        'spec.value_type': { required: true, message: '必填项', trigger: 'blur' }
      },
      visible: false,
      titleMap: {
        update: '编辑项目',
        create: '创建项目'
      }
    }
  },
  mounted() {
    Bus.$on('show-item-form', (scope, props) => {
      this.visible = true
      this.record = Object.assign({}, scope.row)
      this.recordIndex = scope.$index
      this.action = 'create'
      this.resetItem()

      if (props) {
        this.action = 'update'
        this.itemIndex = props.$index
        this.item = Object.assign({}, props.row)
      }
    })
  },
  methods: {
    newItem() {
      return {
        id: 0,
        test_record_id: 0,
        item: '',
        spec: {
          is_show: true,
          value_type: '', // RANGE, INFO, VALUE
          data: {
            min: undefined,
            max: undefined,
            value: ''
          }
        },
        value: '',
        fake_value: '',
        conclusion: '',
        tester: '',
        memo: ''
      }
    },
    resetItem() {
      this.item = this.newItem()
    },
    create() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          addRecordItem(this.record.id, this.item).then(response => {
            const { data } = response.data
            this.item = data
            this.record.push(this.item)
            this.$emit('item-created', this.record, this.recordIndex, this.item)
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
          updateRecordItem(this.record.id, this.item.id, this.item).then(response => {
            const { data } = response.data
            this.item = data
            this.record.splice(this.itemIndex, 1, this.item)
            this.$emit('item-updated', this.record, this.recordIndex, this.item, this.itemIndex)
            this.done()
          })
        } else {
          return false
        }
      })
    },
    done() {
      this.$notify({ title: '成功', message: '操作成功', type: 'success', duration: 2000 })
      this.close()
    },
    close() {
      this.visible = false
    }
  }
}
</script>
