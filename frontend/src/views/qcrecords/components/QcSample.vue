<template>
  <div>
    <el-dialog
      :visible.sync="dialogFormVisible"
      title="取样"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="record.batch"
        :rules="batchRules"
        class="small-space"
        label-position="right"
        label-width="70px"
      >

        <el-form-item
          label="品名"
          prop="product_name"
        >
          <el-select
            v-model="record.batch.product_name"
            :loading="loading"
            :remote-method="queryProducts"
            remote
            filterable
            clearable
            default-first-option
            value-key="internal_name"
            suffix-icon="el-icon-edit"
            @change="handleSelect"
          >
            <el-option
              v-for="product in products"
              :key="product.id"
              :label="product.internal_name"
              :value="product.internal_name"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="品名后缀">
          <el-autocomplete
            v-model="record.batch.product_name_suffix"
            :fetch-suggestions="querySearchSuffix"
            placeholder="品名后缀"
          />
        </el-form-item>

        <el-form-item
          label="批号"
          prop="batch_number"
        >
          <el-input v-model="record.batch.batch_number" />
        </el-form-item>

        <el-form-item label="粘度">
          <el-input v-model="postData.niandu" />
        </el-form-item>

        <el-form-item label="60转粘度">
          <el-input v-model="postData.niandu60" />
        </el-form-item>
      </el-form>

      <div
        slot="footer"
        class="dialog-footer"
      >
        <el-button
          type="primary"
          @click="create"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { TestRecord } from '@/defines/models'
import { qcSample, disposeSample, getBatchDispose } from '@/api/qc'
import suffixSuggests from '@/views/mixins/suffixSuggests'
import { productApi } from '@/api/basedata'

export default {
  name: 'QCSample',
  mixins: [suffixSuggests],
  props: {
    formInfo: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      postData: {
        niandu: '',
        niandu60: ''
      },
      loading: false,
      products: [],
      batchRules: {
        product_name: { required: true, message: '必填项', trigger: 'blur' },
        batch_number: { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' }
      },
      type: '',
      record: TestRecord(),
      dialogFormVisible: false
    }
  },
  watch: {
    formInfo(val) {
      this.type = val.type
      this.record = val.formData
      this.dialogFormVisible = val.visible
    }
  },
  methods: {
    newObj() {
      return TestRecord()
    },
    queryProducts(queryString) {
      this.loading = true
      productApi.list({ params: { q: queryString, sort_by: 'internal_name', order: 'asc' }}).then(response => {
        this.loading = false
        const { data } = response.data
        this.products = data
      })
    },
    handleSelect(product) {
      //
    },
    create() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          const { product_name, batch_number } = this.record.batch
          const type = this.type

          const response = await getBatchDispose(product_name, batch_number, type)
          const { data } = response.data
          const dispose = data

          if (dispose.id) {
            try {
              await this.$confirm(`检测到此批号有返工处理记录:\n
                  品名：${dispose.batch.product_name} ${dispose.batch.product_name_suffix}\n
                  批号：${dispose.batch.batch_number}\n
                  处理方式：${dispose.method}\n\n
                  是否此批次?`, '提示', {
                confirmButtonText: '是',
                cancelButtonText: '否',
                type: 'info'
              })
            } catch (err) {
              await this.qcSample()
            }
            await this.disposeSample(dispose.id)
          } else {
            await this.qcSample()
          }
        }
      })
    },
    async qcSample() {
      const { product_name, product_name_suffix, batch_number } = this.record.batch
      const type = this.type
      const { niandu, niandu60 } = this.postData

      const response = await qcSample({
        product_name,
        product_name_suffix,
        batch_number,
        niandu,
        niandu60,
        type
      })
      const { data } = response.data

      this.record = data
      this.$emit('record-sampled', this.record)
      this.close()
    },
    async disposeSample(dispose_id) {
      const response = await disposeSample(dispose_id)
      const { data } = response.data

      this.record = data
      this.$emit('record-sampled', this.record)
      this.close()
    },
    close() {
      this.dialogFormVisible = false
      this.record = this.newObj()
      this.postData = { niandu: '', niandu60: '' }
    }
  }
}
</script>
