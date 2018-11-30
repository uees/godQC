import { mapGetters } from 'vuex'

export default {
  props: {
    action: {
      type: String,
      required: true
    },
    propObj: {
      type: Object,
      required: true
    }
  },

  data () {
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
    propObj: function (val) {
      if (val) {
        this.obj = Object.assign({}, val)
      }
    },
    action: function (val) {
      if (val === 'create' || val === 'update') {
        this.dialogFormVisible = true
      } else {
        this.dialogFormVisible = false
      }
    }
  },

  methods: {
    newObj () { // createObj 需要继承者实现
      return {}
    },
    resetObj () {
      this.obj = this.newObj()
    },
    create () {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          this.obj.user_id = this.user.id
          this.api.add(this.obj).then(response => {
            this.obj = response.data.data // 灰常重要！！！
            this.done()
          })
        } else {
          return false
        }
      })
    },
    update () {
      this.$refs['obj_form'].validate(valid => {
        if (valid) {
          this.obj.modified_user_id = this.user.id
          this.api.update(this.obj).then(response => {
            this.obj = response.data.data // 灰常重要！！！
            this.done()
          })
        } else {
          return false
        }
      })
    },
    done () {
      // 当一个子组件改变了一个带 .sync 的 prop 的值时，这个变化也会同步到父组件中所绑定的值
      this.$emit('update:updateObj', this.obj) // <comp :obj.sync="obj"></comp>
      if (this.action === 'create') {
        this.$emit('createDone')
      } else if (this.action === 'update') {
        this.$emit('updateDone')
      }
      this.$notify({
        title: '成功',
        message: '操作成功',
        type: 'success',
        duration: 2000
      })
      this.close()
    },
    close () {
      this.dialogFormVisible = false
      this.$emit('update:action', '')
      this.resetObj()
    }
  }

}
