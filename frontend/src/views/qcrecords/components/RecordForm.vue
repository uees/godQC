<template>
  <div>
    <el-dialog :visible.sync="visible" title="编辑样品" @close="close">
      <el-form
        ref="obj_form"
        :model="record"
        :rules="rules"
        class="small-space"
        label-position="right"
        label-width="70px">

        <el-form-item label="品名">
          <el-input :disabled="true" :value="record.batch.product_name"/>
        </el-form-item>

        <el-form-item label="品名后缀">
          <el-autocomplete
            v-model="record.batch.product_name_suffix"
            :fetch-suggestions="querySearchSuffix"
            placeholder="品名后缀"/>
        </el-form-item>

        <el-form-item label="批号" prop="batch.batch_number">
          <el-input v-model="record.batch.batch_number"/>
        </el-form-item>

        <el-form-item label="备注">
          <el-input v-model="record.memo"/>
        </el-form-item>

        <el-form-item label="是否展示">
          <el-switch v-model="record.show_reality"/>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="update">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Bus from '@/store/bus'
import { deepClone } from '@/utils'
import suffixSuggests from '@/mixins/suffixSuggests'
import { qcRecordApi } from '@/api/qc'

export default {
  name: 'RecordForm',
  mixins: [suffixSuggests],
  data() {
    return {
      record: this.newRecord(),
      cachedItems: [],
      loading: false,
      index: -1,
      rules: {
        'batch.batch_number': { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' }
      },
      visible: false
    }
  },
  mounted() {
    Bus.$on('show-update-record-form', (scope) => {
      this.visible = true
      this.record = deepClone(scope.row)
      this.cachedItems = this.record.items
      this.index = scope.$index
    })
  },
  methods: {
    newRecord() {
      return {
        id: 0,
        product_batch_id: 0,
        test_times: 0,
        conclusion: '',
        testers: '',
        completed_at: null,
        said_package_at: null,
        memo: '',
        show_reality: false,
        items: [],
        batch: {
          id: 0,
          product_name: '',
          product_name_suffix: '',
          batch_number: '',
          type: '',
          memo: ''
        }
      }
    },
    update() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          qcRecordApi.update(this.record.id, this.record).then((response) => {
            const { data } = response.data
            this.record = data
            this.record.items = this.cachedItems // response 中无 items, 这里重新设置

            this.$emit('record-updated', this.record, this.index)
            this.$notify({ title: '成功', message: '操作成功', type: 'success', duration: 2000 })
            this.close()
          })
        } else {
          return false
        }
      })
    },
    close() {
      this.visible = false
    }
  }
}
</script>
