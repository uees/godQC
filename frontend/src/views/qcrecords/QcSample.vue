<template>
  <div>
    <el-dialog :visible.sync="dialogFormVisible" title="取样" @close="close">
      <el-form
        ref="obj_form"
        :model="record.batch"
        :rules="batchRules"
        class="small-space"
        label-position="right"
        label-width="70px">

        <el-form-item label="品名" prop="product_name">
          <el-select
            v-model="record.batch.product_name"
            :loading="loading"
            :remote-method="queryProducts"
            remote
            filterable
            clearable
            value-key="internal_name"
            suffix-icon="el-icon-edit"
            @change="handleSelect">
            <el-option
              v-for="product in products"
              :key="product.id"
              :label="product.internal_name"
              :value="product.internal_name"/>
          </el-select>
        </el-form-item>

        <el-form-item label="品名后缀">
          <el-input v-model="record.batch.product_name_suffix"/>
        </el-form-item>

        <el-form-item label="批号">
          <el-input v-model="record.batch.batch_number"/>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" @click="create">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Bus from '@/store/bus'
import { qcSample } from '@/api/qc'
import { productApi } from '@/api/basedata'
import { debounce } from '@/utils'

export default {
  name: 'QCSample',
  data() {
    return {
      type: '',
      record: this.newRecord(),
      loading: false,
      products: [],
      batchRules: {
        product_name: {required: true, message: '必填项', trigger: 'blur'},
        batch_number: {required: true, message: '必填项', trigger: 'blur'}
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
        productApi.list({params: {q: queryString, sort_by: 'internal_name', order: 'asc'}}).then(response => {
          this.loading = false
          const {data} = response.data
          this.products = data
        })
      }

      debounce(fetch(), 300, true)
    },
    handleSelect(product) {
      //
    },
    create() {
      const {product_name, product_name_suffix, batch_number} = this.record.batch
      const type = this.type
      qcSample({product_name, product_name_suffix, batch_number, type}).then(response => {
        const {data} = response.data
        this.record = data

        Bus.$emit('record-sampled', this.record)

        this.close()
      })
    },
    close() {
      this.dialogFormVisible = false
      this.record = this.newRecord()
    }
  }
}
</script>

<style scoped>

</style>
