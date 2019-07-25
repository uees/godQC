import { mapGetters } from 'vuex'
import { deepClone } from '@/utils'

export default {
  props: {
    action: {
      types: [String, undefined],
      required: true
    },
    formData: {
      types: [Object, undefined],
      required: true
    }
  },

  data() {
    return {
      api: undefined,
      obj: this.newObj(),
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
    formData(val) {
      if (val) {
        this.obj = deepClone(val)
      }
    },
    action(val) {
      this.dialogFormVisible = val === 'create' || val === 'update'
    }
  },

  methods: {
    newObj() { // newObj 需要继承者实现
      return {}
    },
    resetObj() {
      this.obj = this.newObj()
    },
    create() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          const formData = this.obj
          formData.user_id = this.user.id
          const response = await this.api.store(formData)
          const { data } = response.data
          this.done(data)
          this.resetObj()
        }
      })
    },
    update() {
      this.$refs['obj_form'].validate(async valid => {
        if (valid) {
          const formData = this.obj
          formData.modified_user_id = this.user.id
          const response = await this.api.update(formData.id, formData)
          const { data } = response.data
          this.done(data)
          this.resetObj()
        }
      })
    },
    done(data) {
      // 当一个子组件改变了一个带 .sync 的 prop 的值时，这个变化也会同步到父组件中所绑定的值
      // this.$emit('update:formData', this.obj) // <comp :obj.sync="obj"></comp>
      this.$emit('action-done', data)
      this.$notify({
        title: '成功',
        message: '操作成功',
        type: 'success',
        duration: 2000
      })
      this.close()
    },
    close() {
      this.$emit('close')
      this.dialogFormVisible = false
    }
  }
}
