<template>
  <div>
    <el-dialog :visible.sync="dialogFormVisible" title="处理方法" @close="close">
      <el-form
        ref="obj_form"
        :model="dispose"
        :rules="rules"
        class="small-space"
        label-position="right"
        label-width="100px">

        <el-form-item label="处理方法" prop="method">
          <el-autocomplete
            v-model="dispose.method"
            :fetch-suggestions="queryMethods"
            placeholder="请输入内容"
          />
        </el-form-item>

        <el-form-item label="处理人" prop="author">
          <el-autocomplete
            v-model="dispose.author"
            :fetch-suggestions="queryAuthors"
            value-key="name"/>
        </el-form-item>

        <el-form-item label="备注">
          <el-input v-model="dispose.memo" :rows="2" type="textarea" placeholder="请输入内容"/>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="close">取 消</el-button>
        <el-button type="primary" @click="create">确 定</el-button>
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
  data() {
    return {
      dispose: this.newDispose(),
      loading: false,
      authors: [],
      methods: [],
      rules: {
        method: { required: true, message: '必填项', trigger: 'blur' },
        author: { required: true, message: '必填项', trigger: 'blur' }
      },
      dialogFormVisible: false
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
        this.initAuthors()
        this.initMethods()
      })
    } else {
      this.initAuthors()
      this.initMethods()
    }
  },
  mounted() {
    Bus.$on('show-dispose-form', (record) => {
      this.dispose.from_record_id = record.id
      this.dispose.product_batch_id = record.batch ? record.batch.id : null
      this.dialogFormVisible = true
    })
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
      const users = this.authors
      const results = queryString
        ? users.filter(user => user.name.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : users
      cb(results)
    },
    queryMethods(queryString, cb) {
      const methods = this.methods.map(method => {
        return {value: method, label: method}
      })
      const results = queryString
        ? methods.filter(method => method.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : methods
      cb(results)
    },
    create() {
      productDisposeApi.add(this.dispose).then(response => {
        Bus.$emit('dispose-created', this.dispose)
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
