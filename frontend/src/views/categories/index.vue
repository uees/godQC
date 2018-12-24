<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        style="width: 200px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"/>
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-refresh"
        @click="handleSearch">搜索
      </el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-edit"
        @click="handleCreate">添加
      </el-button>
      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-refresh"
        @click="fetchData">刷新
      </el-button>
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-document"
        @click="handleDownload">导出
      </el-button>
    </div>

    <el-table
      v-loading.body="listLoading"
      :data="tableData"
      style="width: 100%">

      <el-table-column :sortable="true" prop="name" label="名称"/>
      <el-table-column :sortable="true" prop="slug" label="型号"/>
      <el-table-column prop="memo" label="备注"/>
      <el-table-column label="创建时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at) }}
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="180" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button type="text" size="small" @click="showSelectWay(scope.row)">检测流程</el-button>
          <el-button
            type="text"
            size="small"
            @click="handleUpdate(scope.row)">编辑
          </el-button>
          <el-button
            type="text"
            size="small"
            @click="handleDelete(scope.row)">删除
          </el-button>
        </template>
      </el-table-column>

    </el-table>

    <form-dialog
      :action.sync="action"
      :prop-obj.sync="propObj"
      @createDone="createDone"
      @updateDone="updateDone"/>

    <select-way/>
  </div>
</template>

<script>
import list from '@/mixins/list'
import { categoryApi } from '@/api/basedata'
import FormDialog from './dialog'
import SelectWay from './SelectWay'
import Bus from '@/store/bus'
import echoTimeMethod from '@/mixins/echoTimeMethod'

export default {
  name: 'Categories',
  components: {
    FormDialog,
    SelectWay
  },
  mixins: [
    list,
    echoTimeMethod
  ],
  data() {
    return {
      api: categoryApi,
      propCategoryId: 0
    }
  },
  methods: {
    showSelectWay(category) {
      this.propCategoryId = category.id

      Bus.$emit('category-select-way', category.id)
    }
  }
}
</script>

<style scoped>

</style>
