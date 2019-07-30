<template>
  <div>
    <el-dialog
      :visible="dialogFormVisible"
      title="取样"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="record"
        :rules="recordRules"
        class="small-space"
        label-position="right"
        label-width="100px"
      >

        <el-form-item
          label="品名"
          prop="product_name"
        >
          <el-select
            v-model="record.product_name"
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
            v-model="record.product_name_suffix"
            :fetch-suggestions="querySearchSuffix"
            placeholder="品名后缀"
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
import { MixTestRecord, Product } from '@/defines/models'
import { mixQcSample } from '@/api/qc'
import { productApi } from '@/api/basedata'
import suffixSuggests from '@/views/mixins/suffixSuggests'

export default {
  name: 'MixQCSample',
  mixins: [suffixSuggests],
  props: {
    formInfo: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      loading: false,
      selected_product: Product(),
      products: [],
      record: MixTestRecord(),
      recordRules: {
        product_name: { required: true, message: '必填项', trigger: 'blur' },
        part_b_name: { required: true, message: '必填项', trigger: 'blur' },
        part_a_batch: { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' },
        part_b_batch: { required: true, min: 5, message: '请填入完整的批号', trigger: 'blur' }
      },
      dialogFormVisible: false
    }
  },
  watch: {
    formInfo(val) {
      this.record = val.formData
      this.dialogFormVisible = val.visible
    }
  },
  methods: {
    newObj() {
      const record = MixTestRecord()
      record.product_name = ''
      record.product_name_suffix = ''
      return record
    },
    async queryProducts(queryString) {
      this.loading = true
      const response = await productApi.list({
        params: {
          q: queryString,
          sort_by: 'internal_name',
          category_id: '2,3,4,18',
          order: 'asc'
        }
      })
      this.loading = false
      const { data } = response.data
      this.products = data
    },
    handleSelect(product) {
      this.selected_product = product
    },
    create() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          await this.qcSample()
        }
      })
    },
    async qcSample() {
      const response = await mixQcSample(this.record)
      const { data } = response.data
      this.record = data
      this.$emit('record-sampled', this.record)
      this.close()
    },
    close() {
      this.dialogFormVisible = false
      this.record = this.newObj()
    }
  }
}
</script>
