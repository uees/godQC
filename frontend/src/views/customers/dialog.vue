<template>
  <div class="customer-dialog">
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

        <el-form-item label="地址">
          <el-input v-model="obj.address" />
        </el-form-item>

        <el-form-item label="联系人">
          <el-input v-model="obj.contacts" />
        </el-form-item>

        <el-form-item label="电话">
          <el-input v-model="obj.tel" />
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
import { Customer } from '@/defines/models'
import { customerApi } from '@/api/basedata'

export default {
  name: 'Dialog',
  mixins: [
    DataFormDialog
  ],
  data() {
    return {
      api: customerApi,
      objRules: {
        name: { required: true, message: '必填项', trigger: 'blur' }
      }
    }
  },
  methods: {
    newObj() {
      return Customer()
    }
  }
}
</script>
