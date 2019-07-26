<template>
  <div>
    <el-dialog
      :title="titleMap[action]"
      :visible.sync="visible"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="item"
        :rules="rules"
        label-position="right"
        label-width="100px"
      >

        <el-form-item
          v-if="!tested"
          label="检测项目"
          prop="item"
        >
          <el-autocomplete
            v-model="item.item"
            :fetch-suggestions="querySearchItems"
            :select-when-unmatched="true"
            size="small"
          />
        </el-form-item>

        <el-form-item
          v-if="!tested"
          label="值类型"
          prop="spec.value_type"
        >
          <el-select
            v-model="item.spec.value_type"
            placeholder="请选择"
          >
            <el-option
              v-for="_item in valueTypes"
              :key="_item.value"
              :label="_item.label"
              :value="_item.value"
            >
              <span style="float: left">{{ _item.label }}</span>
              <span style="float: right; color: #8492a6; font-size: 13px">{{ _item.value }}</span>
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item
          v-if="!tested"
          label="单位"
          prop="spec.data.unit"
        >
          <el-input
            v-model="item.spec.data.unit"
            placeholder="单位"
          />
        </el-form-item>

        <el-form-item
          v-if="!tested"
          label="要求"
          prop="spec.data.value"
        >
          <template v-if="item.spec.value_type === 'RANGE'">
            <el-row :gutter="20">
              <el-col :span="12">
                <el-tooltip
                  class="item"
                  effect="dark"
                  content="最小值, 留空为无下限"
                  placement="top-start"
                >
                  <el-input-number
                    v-model="item.spec.data.min"
                    :precision="2"
                    :step="0.1"
                    size="mini"
                  />
                </el-tooltip>
              </el-col>
              <el-col :span="12">
                <el-tooltip
                  class="item"
                  effect="dark"
                  content="最大值, 留空为无上限"
                  placement="top-start"
                >
                  <el-input-number
                    v-model="item.spec.data.max"
                    :precision="2"
                    :step="0.1"
                    size="mini"
                  />
                </el-tooltip>
              </el-col>
            </el-row>
          </template>
          <el-input
            v-else
            v-model="item.spec.data.value"
          />
        </el-form-item>

        <el-form-item
          v-if="!tested"
          label="其他要求"
          prop="spec.data.memo"
        >
          <el-input
            v-model="item.spec.data.memo"
            :rows="2"
            type="textarea"
            placeholder="其他要求"
          />
        </el-form-item>

        <el-form-item
          v-if="tested"
          label="检测值"
          prop="value"
        >
          <el-input
            v-model="item.value"
            placeholder="检测值"
          />
        </el-form-item>

        <el-form-item
          v-if="tested"
          label="结论"
          prop="conclusion"
        >
          <el-select v-model="item.conclusion">
            <el-option
              v-for="_item in conclusions"
              :key="_item.value"
              :label="_item.label"
              :value="_item.value"
            />
          </el-select>
        </el-form-item>

        <el-form-item
          v-if="tested"
          label="备注"
        >
          <el-input
            v-model="item.memo"
            :rows="2"
            type="textarea"
            placeholder="备注"
          />
        </el-form-item>

        <el-form-item label="是否展示">
          <el-switch v-model="item.spec.is_show" />
        </el-form-item>
      </el-form>

      <div
        slot="footer"
        class="dialog-footer"
      >
        <el-button
          type="primary"
          @click="action==='create' ? create() : update()"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { TestRecordItem } from '@/defines/models'
import { VALUE_TYPES, CONCLUSIONS } from '@/defines/consts'
import testItemSuggestions from '@/views/mixins/testItemSuggestions'
import { updateRecordItem, addRecordItem } from '@/api/qc'

export default {
  name: 'ItemForm',
  mixins: [testItemSuggestions],
  props: {
    formInfo: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      action: '',
      recordIndex: -1,
      itemIndex: -1,
      record: undefined,
      item: this.newItem(),
      visible: false,
      tested: false,
      conclusions: CONCLUSIONS,
      valueTypes: VALUE_TYPES,
      rules: {
        item: { required: true, message: '必填项', trigger: 'blur' },
        'spec.value_type': { required: true, message: '必填项', trigger: 'blur' }
      },
      titleMap: {
        update: '编辑项目',
        create: '创建项目'
      }
    }
  },
  watch: {
    formInfo(val) {
      this.visible = val.visible
      this.record = val.record
      this.item = val.formData
      this.recordIndex = val.recordIndex
      this.itemIndex = val.itemIndex
      this.tested = val.tested
      if (val.tested) {
        this.action = 'update'
      } else {
        this.action = val.action
      }
    }
  },
  methods: {
    newItem() {
      return TestRecordItem()
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
            this.record.items.push(this.item)
            this.$emit('item-changed', this.record, this.recordIndex, this.item, this.itemIndex)
            this.done()
          })
        }
      })
    },
    update() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          updateRecordItem(this.record.id, this.item.id, this.item).then(response => {
            const { data } = response.data
            this.item = data
            this.record.items.splice(this.itemIndex, 1, this.item)
            this.$emit('item-changed', this.record, this.recordIndex, this.item, this.itemIndex)
            this.done()
          })
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
