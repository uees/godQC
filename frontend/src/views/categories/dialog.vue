<template>
  <div class="category-dialog">
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
          label="名称"
          prop="name"
        >
          <!--prop 属性设置为需校验的字段名-->
          <el-input v-model="obj.name" />
        </el-form-item>

        <el-form-item
          label="型号"
          prop="slug"
        >
          <el-input v-model="obj.slug" />
        </el-form-item>

        <el-form-item label="备注">
          <el-input v-model="obj.memo" />
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
import dialog from '@/views/mixins/DataFormDialog'
import { categoryApi } from '@/api/basedata'
import { Category } from '@/defines/models'

export default {
  name: 'Dialog',
  mixins: [
    dialog
  ],
  data() {
    return {
      api: categoryApi,
      objRules: {
        name: { required: true, message: '必填项', trigger: 'blur' },
        slug: { required: true, message: '必填项', trigger: 'blur' }
      }
    }
  },
  methods: {
    newObj() {
      return Category()
    },
    update() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          const postData = Object.assign({}, this.obj, { with: 'testWay' })
          postData.modified_user_id = this.user.id
          const response = await this.api.update(this.obj.id, postData)
          const { data } = response.data
          this.done(data)
          this.resetObj()
        }
      })
    }
  }
}
</script>
