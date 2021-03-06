<template>
  <div>
    <el-dialog
      :fullscreen="true"
      :visible.sync="visible"
      title="选择模板"
      @close="close"
    >

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
import { categoryUpdateTemplates } from '@/api/qc'
import JsonEditor from '@/components/JsonEditor/index'
import Bus from '@/store/bus'
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
      category: undefined,
      categoryIndex: -1,
      visible: false,
      templates: []
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
    Bus.$on('category-select-template', (scope) => {
      // 初始化
      this.category = deepClone(scope.row)
      this.categoryIndex = scope.$index
      if (this.category.metas && this.category.metas.templates) {
        this.templates = this.category.metas.templates
      } else {
        this.templates = []
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
      categoryUpdateTemplates(this.category.id, this.objectTemplates).then(response => {
        this.$emit('template-updated', this.categoryIndex, this.objectTemplates)
        this.resetTemplates()
        this.close()
      })
    },
    close() {
      this.visible = false
    }
  }
}
</script>
