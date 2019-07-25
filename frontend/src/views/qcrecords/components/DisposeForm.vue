<template>
  <div>
    <el-dialog
      :visible="visible"
      title="处理方法"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="formData"
        :rules="rules"
        class="small-space"
        label-position="right"
        label-width="100px"
      >

        <el-form-item
          label="处理方法"
          prop="method"
        >
          <el-autocomplete
            v-model="formData.method"
            :fetch-suggestions="queryMethods"
            placeholder="请输入内容"
          />
        </el-form-item>

        <el-form-item
          label="处理人"
          prop="author"
        >
          <el-autocomplete
            v-model="formData.author"
            :fetch-suggestions="queryAuthors"
            value-key="name"
          />
        </el-form-item>

        <el-form-item label="备注">
          <el-input
            v-model="formData.memo"
            :rows="2"
            type="textarea"
            placeholder="请输入内容"
          />
        </el-form-item>
      </el-form>

      <div
        slot="footer"
        class="dialog-footer"
      >
        <el-button
          type="primary"
          @click="disposeForm.action==='create' ? create() : update()"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { ProductDispose } from '@/defines/models'
import { productDisposeApi } from '@/api/qc'

export default {
  name: 'DisposeForm',
  props: {
    formInfo: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      loading: false,
      authors: [],
      methods: [],
      rules: {
        method: { required: true, message: '必填项', trigger: 'blur' },
        author: { required: true, message: '必填项', trigger: 'blur' }
      },
      recordIndex: -1,
      visible: false,
      formData: ProductDispose(),
      action: ''
    }
  },
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    })
  },
  watch: {
    formInfo(val) {
      this.recordIndex = val.index
      this.visible = val.visible
      this.formData = val.formData
      this.action = val.action
    }
  },
  async created() {
    if (this.suggests.length === 0) {
      await this.$store.dispatch('basedata/fetchSuggest')
    }
    this.initAuthors()
    this.initMethods()
  },
  methods: {
    newDispose() {
      return ProductDispose()
    },
    initAuthors() {
      const suggest = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '处理人'
      })

      if (suggest) {
        this.authors = suggest.data
      }
    },
    initMethods() {
      const suggest = this.suggests.find(suggest => {
        return suggest.parent_id === 0 && suggest.name === '处理方法'
      })

      if (suggest) {
        this.methods = suggest.data
      }
    },
    queryAuthors(queryString, cb) {
      const users = this.authors.map(name => {
        return { name: name, label: name }
      })
      const results = queryString
        ? users.filter(user => user.name.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : users
      cb(results)
    },
    queryMethods(queryString, cb) {
      const methods = this.methods.map(method => {
        return { value: method, label: method }
      })
      const results = queryString
        ? methods.filter(method => method.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : methods
      cb(results)
    },
    create() {
      productDisposeApi.store(this.dispose).then(response => {
        this.$emit('action-done', this.dispose, this.index)
        this.close()
      })
    },
    update() {
      productDisposeApi.update(this.dispose.id, this.dispose).then(response => {
        this.$emit('action-done', this.dispose, this.index)
        this.close()
      })
    },
    close() {
      this.visible = false
    }
  }
}
</script>
