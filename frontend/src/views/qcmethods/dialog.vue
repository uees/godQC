<template>
  <div class="qc-method-dialog">
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

        <el-form-item label="文档">
          <el-input v-model="obj.file" />
        </el-form-item>

        <el-form-item label="内容">
          <el-input
            v-model="obj.content"
            :rows="10"
            type="textarea"
            placeholder="请输入内容"
          />
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
import { TestMethod } from '@/defines/models'
import { qcMethodApi } from '@/api/qc'

export default {
  name: 'Dialog',
  mixins: [
    dialog
  ],
  data() {
    return {
      api: qcMethodApi,
      objRules: {
        name: { required: true, message: '必填项', trigger: 'blur' }
      }
    }
  },
  methods: {
    newObj() {
      return TestMethod()
    }
  }
}
</script>

<style scoped>
</style>
