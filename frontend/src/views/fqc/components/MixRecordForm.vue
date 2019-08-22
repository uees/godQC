<template>
  <div>
    <el-dialog
      :visible="visible"
      title="编辑样品"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="record"
        :rules="rules"
        class="small-space"
        label-position="right"
        label-width="100px"
      >

        <el-form-item label="品名">
          <el-input
            :disabled="true"
            :value="record.part_a_name"
          />
        </el-form-item>

        <el-form-item
          label="主剂批号"
          prop="part_a_batch"
        >
          <el-input v-model="record.part_a_batch" />
        </el-form-item>

        <el-form-item
          label="固化剂"
          prop="part_b_name"
        >
          <el-input v-model="record.part_b_name" />
        </el-form-item>

        <el-form-item
          label="固化剂批次"
          prop="part_b_batch"
        >
          <el-input v-model="record.part_b_batch" />
        </el-form-item>

        <el-form-item label="是否展示">
          <el-switch v-model="record.show_reality" />
        </el-form-item>
      </el-form>

      <div
        slot="footer"
        class="dialog-footer"
      >
        <el-button
          type="primary"
          @click="update"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { MixTestRecord } from '@/defines/models'
import { mixQcRecordApi } from '@/api/qc'

export default {
  name: 'MixRecordForm',
  props: {
    formInfo: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      cachedItems: [],
      loading: false,
      rules: {
        part_a_batch: { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' },
        part_b_name: { required: true, message: '必填项', trigger: 'blur' },
        part_b_batch: { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' }
      },
      index: -1,
      record: MixTestRecord(),
      visible: false
    }
  },
  watch: {
    formInfo(val) {
      this.visible = val.visible
      this.record = val.formData
      this.cachedItems = this.record.items
      this.index = val.index
    }
  },
  methods: {
    newRecord() {
      return MixTestRecord()
    },
    update() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          const response = await mixQcRecordApi.update(this.record.id, this.record)
          const { data } = response.data
          this.record = data
          this.record.items = this.cachedItems // response 中无 items, 这里重新设置

          this.$emit('record-updated', this.record, this.index)
          this.$notify({ title: '成功', message: '操作成功', type: 'success', duration: 2000 })
          this.close()
        }
      })
    },
    close() {
      this.visible = false
    }
  }
}
</script>
