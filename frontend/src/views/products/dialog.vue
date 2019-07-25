<template>
  <div class="product-dialog">
    <el-dialog
      :title="dialogTitleMap[action]"
      :visible.sync="dialogFormVisible"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="obj"
        :rules="objRules"
        class="small-space"
        label-position="right"
        label-width="70px"
      >

        <el-form-item
          label="内部名"
          prop="internal_name"
        >
          <!--prop 属性设置为需校验的字段名-->
          <el-input v-model="obj.internal_name" />
        </el-form-item>

        <el-form-item
          label="销售名"
          prop="market_name"
        >
          <el-input v-model="obj.market_name" />
        </el-form-item>

        <el-form-item
          label="类别"
          prop="category_id"
        >
          <el-select
            v-model="obj.category_id"
            placeholder="请选择"
          >
            <el-option
              v-for="category in categories"
              :key="category.slug"
              :label="category.name"
              :value="category.id"
            >
              <span style="float: left">{{ category.name }}</span>
              <span style="float: right; color: #8492a6; font-size: 13px">{{ category.slug }}</span>
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="规格">
          <el-input v-model="obj.spec" />
        </el-form-item>

        <el-form-item label="A组分">
          <el-input v-model="obj.part_a" />
        </el-form-item>

        <el-form-item label="B组分">
          <el-input v-model="obj.part_b" />
        </el-form-item>

        <el-form-item label="A:B比例">
          <el-input v-model="obj.ab_ratio" />
        </el-form-item>

        <el-form-item label="颜色">
          <el-input v-model="obj.color" />
        </el-form-item>

        <el-form-item label="标签粘度">
          <el-input v-model="obj.label_viscosity" />
        </el-form-item>

        <el-form-item label="粘度浮动">
          <el-input v-model="obj.viscosity_width" />
        </el-form-item>

        <el-form-item label="Metas">
          <json-editor v-model="obj.metas" />
        </el-form-item>
      </el-form>

      <div
        slot="footer"
        class="dialog-footer"
      >
        <el-button @click="close">取 消</el-button>
        <el-button
          v-if="action==='create'"
          type="primary"
          @click="create()"
        >确 定</el-button>
        <el-button
          v-else
          type="primary"
          @click="update()"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import DataFormDialog from '@/views/mixins/DataFormDialog'
import queryCategory from '@/views/mixins/queryCategory'
import { productApi } from '@/api/basedata'
import { Product } from '@/defines/models'
import JsonEditor from '@/components/JsonEditor/index'

export default {
  name: 'Dialog',
  components: { JsonEditor },
  mixins: [
    DataFormDialog,
    queryCategory
  ],
  data() {
    return {
      api: productApi,
      objRules: {
        internal_name: { required: true, message: '必填项', trigger: 'blur' },
        market_name: { required: true, message: '必填项', trigger: 'blur' },
        category_id: { required: true, message: '必填项', trigger: 'blur' }
      }
    }
  },
  methods: {
    newObj() {
      const obj = Product()
      obj.with = 'category,testWay' // 加载关系
      return obj
    }
  }
}
</script>
