<template>
  <div>
    <el-dialog
      :visible="disposeForm.visable"
      title="处理方法"
      @close="close"
    >
      <el-form
        ref="obj_form"
        :model="disposeForm.formData"
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
            v-model="disposeForm.formData.method"
            :fetch-suggestions="queryMethods"
            placeholder="请输入内容"
          />
        </el-form-item>

        <el-form-item
          label="处理人"
          prop="author"
        >
          <el-autocomplete
            v-model="disposeForm.formData.author"
            :fetch-suggestions="queryAuthors"
            value-key="name"
          />
        </el-form-item>

        <el-form-item label="备注">
          <el-input
            v-model="disposeForm.formData.memo"
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
          @click="disposeForm.action==='create' ? create : update"
        >确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import Bus from '@/store/bus'
import { productDisposeApi } from '@/api/qc'

export default {
  name: 'DisposeForm',
  props: {
    recordScope: {
      type: Object,
      required: true
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
      }
    }
  },
  computed: {
    ...mapState('basedata', { // namespaced module
      suggests: state => state.suggests
    }),
    ...mapState('qc', {
      disposeForm: state => state.fqcDisposeForm
    })
  },
  async created() {
    if (this.suggests.length === 0) {
      await this.$store.dispatch('basedata/FetchSuggest')
    }
    this.initAuthors()
    this.initMethods()
  },
  methods: {
    newDispose() {
      return {
        id: null,
        product_batch_id: null,
        from_record_id: null,
        to_record_id: null,
        method: '',
        author: '',
        memo: '',
        is_done: false
      }
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
        Bus.$emit('dispose-created', this.dispose)
        this.close()
      })
    },
    update() {
      productDisposeApi.update(this.dispose.id, this.dispose).then(response => {
        Bus.$emit('dispose-updated', this.dispose)
        this.close()
      })
    },
    close() {
      this.dialogFormVisible = false
      this.dispose = this.newDispose()
    }
  }
}
</script>
