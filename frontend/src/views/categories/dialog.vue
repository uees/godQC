<template>
  <div class="category-dialog">
    <el-dialog :title="dialogTitleMap[action]" :visible.sync="dialogFormVisible" @close="close">
      <el-form
        ref="obj_form"
        :model="obj"
        :rules="objRules"
        class="small-space"
        label-position="right"
        label-width="70px">

        <el-form-item label="名称" prop="name">  <!--prop 属性设置为需校验的字段名-->
          <el-input v-model="obj.name" />
        </el-form-item>

        <el-form-item label="型号" prop="slug">
          <el-input v-model="obj.slug" />
        </el-form-item>

        <el-form-item label="备注">
          <el-input v-model="obj.memo" />
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button v-if="dialogStatus==='create'" type="primary" @click="create()">确 定</el-button>
        <el-button v-else type="primary" @click="update()">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import newObj from './index'
import queryCategory from '@/mixins/queryCategory'
// import categoryApi from '@/api/basedata'

export default {
  name: 'Dialog',
  mixins: [
    queryCategory
  ],
  props: {
    propObj: {
      required: true,
      type: Object
    },
    action: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      obj: newObj(),
      objHasChanged: false,
      objRules: {
        name: {required: true, message: '必填项'},
        slug: {required: true, message: '必填项'}
      },
      dialogFormVisible: false,
      dialogTitleMap: {
        update: '编辑',
        create: '创建'
      }
    }
  },
  computed: {
    ...mapGetters([
      'user'
    ])
  },
  watch: {
    action: function (val) {
      if (val === 'create') {
        this.obj = newObj()
        this.objHasChanged = false
        this.dialogFormVisible = true
      } else if (val === 'update') {
        if (this.propObj) {
          this.obj = Object.assign({}, this.propObj)
        }
        this.objHasChanged = false
        this.dialogFormVisible = true
      } else {
        this.dialogFormVisible = false
      }
    }
  },
  methods: {
    create() {

    },
    update() {

    },
    objChanged() {

    },
    done() {

    },
    close() {

    }
  }
}
</script>

<style scoped>

</style>
