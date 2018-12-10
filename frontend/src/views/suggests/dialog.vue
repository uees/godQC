<template>
  <div>
    <el-dialog :title="dialogTitleMap[action]" :visible.sync="dialogFormVisible" @close="close">
      <el-form
        ref="obj_form"
        :model="obj"
        :rules="objRules"
        class="small-space"
        label-position="right"
        label-width="70px">

        <el-form-item label="名称" prop="name">  <!--prop 属性设置为需校验的字段名-->
          <el-input v-model="obj.name"/>
        </el-form-item>

        <el-form-item label="型号" prop="父级">
          <el-select v-model="obj.parent_id">
            <el-option :value="0" label="无父类"/>
            <el-option
              v-for="item in topSuggests"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="数据">
          <json-editor v-model="obj.data"/>
        </el-form-item>

        <el-form-item label="备注">
          <el-input v-model="obj.memo"/>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button v-if="action==='create'" type="primary" @click="create()">确 定</el-button>
        <el-button v-else type="primary" @click="update()">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import dialog from '@/mixins/dialog'
import { suggestApi } from '@/api/basedata'
import JsonEditor from '../../components/JsonEditor/index'

export function newObj() {
  return {
    id: 0,
    parent_id: 0,
    name: '',
    data: [],
    memo: '',
    created_at: {
      date: '',
      timezone_type: '',
      timezone: ''
    },
    updated_at: {
      date: '',
      timezone_type: '',
      timezone: ''
    }
  }
}

export default {
  name: 'Dialog',
  components: { JsonEditor },
  mixins: [
    dialog
  ],
  data() {
    return {
      api: suggestApi,
      topSuggests: [],
      objRules: {
        name: { required: true, message: '必填项', trigger: 'blur' }
      }
    }
  },
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    })
  },
  created() {
    if (this.suggests.length === 0) {
      this.$store.dispatch('basedata/FetchSuggest').then(() => {
        this.initTopSuggests()
      })
    } else {
      this.initTopSuggests()
    }
  },
  methods: {
    newObj() {
      return newObj()
    },
    initTopSuggests() {
      this.topSuggests = this.suggests.filter(suggest => !suggest.parent_id)
    }
  }
}
</script>

<style scoped>

</style>
