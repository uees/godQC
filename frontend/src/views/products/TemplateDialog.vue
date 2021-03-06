<template>
  <div>
    <el-dialog
      :fullscreen="true"
      :visible.sync="visible"
      title="选择模板"
      @close="close"
    >

      <el-form
        class="small-space"
        label-position="right"
        label-width="120px"
      >
        <el-form-item label="取消分类模板">
          <el-switch v-model="cancelCategoryTemplate" />
        </el-form-item>
      </el-form>

      <el-table
        :data="templates"
        border
        fit
        highlight-current-row
        style="width: 100%"
      >
        <el-table-column label="模板">
          <template slot-scope="scope">
            <el-autocomplete
              v-model="scope.row.name"
              :fetch-suggestions="querySearchTemplates"
              style="width: 100%"
            />
          </template>
        </el-table-column>

        <el-table-column label="提示">
          <template slot-scope="scope">
            <json-editor v-model="scope.row.tips" />
          </template>
        </el-table-column>

        <el-table-column label="选项">
          <template slot-scope="scope">
            <json-editor v-model="scope.row.options" />
          </template>
        </el-table-column>

        <el-table-column
          align="center"
          label="操作"
          width="100"
          class-name="small-padding fixed-width"
        >
          <template slot-scope="scope">
            <el-button
              type="text"
              size="small"
              @click="insertTemplate(scope)"
            >插入</el-button>
            <el-button
              type="text"
              size="small"
              @click="deleteTemplate(scope)"
            >删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <div
        slot="footer"
        class="dialog-footer"
      >
        <el-button @click="close">取 消</el-button>
        <el-button
          type="primary"
          icon="el-icon-edit"
          @click="addTemplate"
        >添加模板</el-button>
        <el-button
          type="primary"
          @click="submit()"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { deepClone } from '@/utils'
import { productUpdateTemplates } from '@/api/qc'
import Bus from '@/store/bus'
import JsonEditor from '@/components/JsonEditor/index'
import templatesSuggestions from '@/views/mixins/templatesSuggestions'

export default {
  name: 'TemplateDialog',
  components: {
    JsonEditor
  },
  mixins: [
    templatesSuggestions
  ],
  data() {
    return {
      product: undefined,
      productIndex: -1,
      visible: false,
      templates: [], // will set to product.metas.templates
      cancelCategoryTemplate: false
    }
  },
  computed: {
    objectTemplates() {
      // json-editor 会把数据转为 json 字符串 这里需要转换回去
      return this.templates.map(template => {
        let tips, options
        if (typeof template.tips === 'string') {
          tips = JSON.parse(template.tips)
        }
        if (typeof template.options === 'string') {
          options = JSON.parse(template.options)
        }

        if (tips) {
          template.tips = tips
        }
        if (options) {
          template.options = options
        }
        return template
      })
    }
  },
  mounted() {
    Bus.$on('product-select-template', (scope) => {
      // 初始化
      this.product = deepClone(scope.row)
      this.productIndex = scope.$index
      if (this.product.metas && this.product.metas.templates) {
        this.templates = this.product.metas.templates
        this.cancelCategoryTemplate = !!this.product.metas.cancel_category_template
      } else {
        this.templates = []
        this.cancelCategoryTemplate = false
      }
      // 显示
      this.visible = true
    })
  },
  methods: {
    resetTemplates() {
      this.templates = []
    },
    newTemplate() {
      return {
        name: '',
        tips: [],
        options: {}
      }
    },
    addTemplate() {
      this.templates.push(this.newTemplate())
    },
    insertTemplate(scope) {
      this.templates.splice(scope.$index, 0, this.newTemplate())
    },
    deleteTemplate(scope) {
      this.templates.splice(scope.$index, 1)
    },
    submit() {
      // this.cleanData()
      productUpdateTemplates(this.product.id, this.objectTemplates, this.cancelCategoryTemplate).then(() => {
        this.$emit('template-updated', this.productIndex, this.objectTemplates)
        this.resetTemplates()
        this.close()
      })
    },
    close() {
      this.visible = false
    },
    cleanData() {
      // 保留有模板名称的项
      this.templates = this.templates.filter(element => {
        return element.name
      })
    }
  }
}
</script>
