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
          <el-input v-model="record.batch.product_name_suffix"/>
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
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" @click="update">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Bus from '@/store/bus'
import { qcRecordApi, productBatchApi } from '@/api/qc'

export default {
  name: 'RecordForm',
  data() {
    return {
      record: this.newRecord(),
      loading: false,
      index: -1,
      rules: {
        'batch.batch_number': { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' }
      },
      visible: false
    }
  },
  mounted() {
    Bus.$on('show-update-record-form', (record, index) => {
      this.visible = true
      this.record = record
      this.index = index
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
          productBatchApi.update(this.record.batch.id, this.record.batch).then(response => {
            const { data } = response.data
            this.record.batch = data

            qcRecordApi.update(this.record.id, {
              show_reality: this.record.show_reality,
              memo: this.record.memo
            }).then(() => {
              this.$emit('record-updated', this.record, this.index)
              this.$notify({ title: '成功', message: '操作成功', type: 'success', duration: 2000 })
              this.visible = false
            })
          })
        } else {
          return false
        }
      })
    },
    close() {
      this.visible = false
      this.$emit('cancel', this.index)
    }
  }
}
</script>
