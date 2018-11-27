<template>
  <div class="app-container">
    Categories
  </div>
</template>

<script>
export function newCategory() {
  return {
    id: 0,
    slug: '',
    name: '',
    memo: '',
    created_at: '',
    updated_at: ''
  }
}

export default {
  name: 'Index',
  data() {
    return {
      listLoading: false,
      tabledata: [],
      updateIndex: -1,
      queryParams: {
        search: ''
      },
      obj: newCategory(),
      objRules: {
        slug: {required: true, message: '请输入型号', trigger: 'blur'},
        name: {required: true, message: '请输入名称', trigger: 'blur'}
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: '编辑',
        create: '创建'
      }
    }
  },
  methods: {
    fetchData() {
      if (this.categories.length === 0) {
        this.listLoading = true
        this.$store.dispatch('basedata/FetchCategory').then(() => {
          this.tabledata = this.categories
          this.listLoading = false
        })
      } else {
        this.tabledata = this.categories
      }
    }
  }
}
</script>

<style scoped>

</style>
