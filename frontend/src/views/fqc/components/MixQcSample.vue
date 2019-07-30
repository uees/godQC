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
        label-width="100px"
      >

        <el-form-item
          label="品名"
          prop="product_name"
        >
          <el-select
            v-model="selectProduct.internal_name"
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
            v-model="product_name_suffix"
            :fetch-suggestions="querySearchSuffix"
            placeholder="品名后缀"
          />
        </el-form-item>

        <el-form-item
          label="主剂批号"
          prop="record.part_a_batch"
        >
          <el-input v-model="record.part_a_batch" />
        </el-form-item>

        <el-form-item
          label="固化剂"
          prop="record.part_b_name"
        >
          <el-input v-model="record.part_b_name" />
        </el-form-item>

        <el-form-item
          label="固化剂批次"
          prop="record.part_b_batch"
        >
          <el-input v-model="record.part_b_batch" />
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
import { TestRecord, Product } from '@/defines/models'
import { mixQcSample } from '@/api/qc'
import { productApi } from '@/api/basedata'
import suffixSuggests from '@/views/mixins/suffixSuggests'

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
      selectProduct: Product(),
      product_name_suffix: '',
      products: [],
      batchRules: {
        product_name: { required: true, message: '必填项', trigger: 'blur' },
        'record.part_b_name': { required: true, message: '必填项', trigger: 'blur' },
        'record.part_a_batch': { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' },
        'record.part_b_batch': { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' }
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
          await this.qcSample()
        }
      })
    },
    async qcSample() {
      const { product_name, product_name_suffix, batch_number } = this.record.batch
      const type = this.type
      const { niandu, niandu60 } = this.postData

      const response = await mixQcSample({
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
    close() {
      this.dialogFormVisible = false
      this.record = this.newObj()
      this.postData = { niandu: '', niandu60: '' }
    }
  }
}
</script>
