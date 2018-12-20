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
        icon="el-icon-search"
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

      <el-table-column prop="category.name" label="类别"/>
      <el-table-column prop="internal_name" label="名称"/>
      <el-table-column prop="color" label="颜色"/>
      <el-table-column prop="part_a" label="A组分"/>
      <el-table-column prop="part_b" label="B组分"/>
      <el-table-column prop="ab_ratio" label="比例(A:B)"/>

      <el-table-column label="检测流程">
        <template slot-scope="scope">
          <span v-if="scope.row.testWays.length > 0">{{ scope.row.testWays[0].name }}</span>
        </template>
      </el-table-column>

      <el-table-column label="创建时间">
        <template slot-scope="scope">
          {{ echoTime(scope.row.created_at) }}
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="180" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button type="text" size="small" @click="showSelectWay(scope.row)">检测流程</el-button>
          <el-button type="text" size="small" @click="handleUpdate(scope.row)">编辑</el-button>
          <el-button type="text" size="small" @click="handleDelete(scope.row)">删除</el-button>
        </template>
      </el-table-column>

    </el-table>

    <div v-show="!listLoading" class="pagination-container">
      <el-pagination
        :total="total"
        :current-page.sync="queryParams.page"
        :page-sizes="pageSizes"
        :page-size="queryParams.per_page"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"/>
    </div>

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
import pagination from '@/mixins/pagination'
import { productApi } from '@/api/basedata'
import FormDialog from './dialog'
import SelectWay from './SelectWay'
import Bus from '@/store/bus'
import echoTimeMethod from '@/mixins/echoTimeMethod'

export default {
  name: 'Index',
  components: {
    FormDialog,
    SelectWay
  },
  mixins: [
    list,
    pagination,
    echoTimeMethod
  ],
  data() {
    return {
      api: productApi,
      propProductId: 0,
      queryParams: {
        with: 'category,testWays'
      }
    }
  },
  methods: {
    showSelectWay(product) {
      this.propProductId = product.id

      Bus.$emit('product-select-way', product.id)
    }
  }
}
</script>

<style scoped>

</style>
