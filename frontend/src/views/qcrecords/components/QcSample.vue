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
import Bus from '@/store/bus'
import { qcSample, disposeSample, getBatchDispose } from '@/api/qc'
import suffixSuggests from '@/views/mixins/suffixSuggests'
import { productApi } from '@/api/basedata'
import { debounce } from '@/utils'

export default {
  name: 'QCSample',
  mixins: [suffixSuggests],
  data() {
    return {
      type: '',
      postData: {
        niandu: '',
        niandu60: ''
      },
      record: this.newRecord(),
      loading: false,
      products: [],
      batchRules: {
        product_name: { required: true, message: '必填项', trigger: 'blur' },
        batch_number: { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' }
      },
      dialogFormVisible: false
    }
  },
  mounted() {
    Bus.$on('show-record-sample-form', (type) => {
      this.type = type
      this.dialogFormVisible = true
    })
  },
  methods: {
    newBatch() {
      return {
        id: null,
        product_name: '',
        product_name_suffix: '',
        batch_number: '',
        type: this.type,
        amount: 0,
        tests_num: 1,
        memo: ''
      }
    },
    newRecord() {
      return {
        id: null,
        batch: this.newBatch(),
        test_times: 1,
        conclusion: '',
        testers: '',
        memo: '',
        items: []
      }
    },
    queryProducts(queryString) {
      const fetch = () => {
        this.loading = true
        productApi.list({ params: { q: queryString, sort_by: 'internal_name', order: 'asc' }}).then(response => {
          this.loading = false
          const { data } = response.data
          this.products = data
        })
      }

      debounce(fetch(), 300, true)
    },
    handleSelect(product) {
      //
    },
    create() {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          const { product_name, batch_number } = this.record.batch
          const type = this.type

          getBatchDispose(product_name, batch_number, type).then(response => {
            const { data } = response.data
            const dispose = data

            if (dispose.id) {
              this.$confirm(`检测到此批号有返工处理记录:\n
                  品名：${dispose.batch.product_name} ${dispose.batch.product_name_suffix}\n
                  批号：${dispose.batch.batch_number}\n
                  处理方式：${dispose.method}\n\n
                  是否此批次?`, '提示', {
                confirmButtonText: '是',
                cancelButtonText: '否',
                type: 'info'
              }).then(() => {
                this.disposeSample(dispose.id)
              }).catch(() => {
                this.qcSample()
              })
            } else {
              this.qcSample()
            }
          })
        } else {
          return false
        }
      })
    },
    close() {
      this.dialogFormVisible = false
      this.record = this.newRecord()
      this.postData = { niandu: '', niandu60: '' }
    },
    qcSample() {
      const { product_name, product_name_suffix, batch_number } = this.record.batch
      const type = this.type
      const { niandu, niandu60 } = this.postData

      qcSample({
        product_name,
        product_name_suffix,
        batch_number,
        niandu,
        niandu60,
        type
      }).then(response => {
        const { data } = response.data
        this.record = data

        Bus.$emit('record-sampled', this.record)

        this.close()
      })
    },
    disposeSample(dispose_id) {
      disposeSample(dispose_id).then(response => {
        const { data } = response.data
        this.record = data

        Bus.$emit('record-sampled', this.record)

        this.close()
      })
    }
  }
}
</script>
