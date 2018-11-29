<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.search"
        style="width: 200px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"/>
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleSearch">搜索</el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-edit"
        @click="handleCreate">添加</el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-my-shuaxin"
        @click="fetchData">刷新</el-button>
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-document"
        @click="handleDownload">导出</el-button>
    </div>

    <el-table
      v-loading.body="listLoading"
      :data="tableData"
      element-loading-text="给我一点时间"
      element-loading-spinner="el-icon-loading"
      element-loading-background="rgba(0, 0, 0, 0.8)"
      style="width: 100%">

      <el-table-column :sortable="true" prop="name" label="名称" />
      <el-table-column :sortable="true" prop="slug" label="型号" />
      <el-table-column prop="memo" label="备注" />
      <el-table-column prop="created_at" label="创建时间" />

      <el-table-column align="center" label="操作" width="100" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button
            type="text"
            size="small"
            @click="handleUpdate(scope.row)">编辑</el-button>
          <el-button
            type="text"
            size="small"
            @click="handleDelete(scope.row)">删除</el-button>
        </template>
      </el-table-column>

    </el-table>
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
      tableData: [],
      updateIndex: -1,
      queryParams: {
        search: ''
      },
      obj: newCategory(),
      objRules: {
        slug: { required: true, message: '请输入型号', trigger: 'blur' },
        name: { required: true, message: '请输入名称', trigger: 'blur' }
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
          this.tableData = this.categories
          this.listLoading = false
        })
      } else {
        this.tableData = this.categories
      }
    },
    handleSearch() {
      //
    },
    handleDownload() {
      //
    },
    handleCreate() {
      //
    },
    handleUpdate() {
      //
    },
    handleDelete() {
      //
    }
  }
}
</script>

<style scoped>

</style>
