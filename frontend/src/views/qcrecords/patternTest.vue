<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="queryParams.q"
        style="width: 250px;"
        class="filter-item"
        placeholder="搜索"
        @keyup.enter.native="handleSearch"/>

      <el-button
        class="filter-item"
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-search"
        @click="handleSearch">搜索
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
        style="margin-left: 10px;"
        type="primary"
        icon="el-icon-edit"
        @click="handleCreate">添加
      </el-button>

      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-document"
        @click="handleDownload">导出
      </el-button>
    </div>

    <el-table v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="创建时间">
        <template slot-scope="scope">
          <span v-if="scope.row.created_at">{{ echoTime(scope.row.created_at) }}</span>
        </template>
      </el-table-column>

      <el-table-column width="150" align="center" label="品名">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.product_name"
            :fetch-suggestions="querySearchProducts"
            value-key="internal_name"
            select-when-unmatched
            placeholder="品名"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="批号">
        <template slot-scope="scope">
          <el-input v-model="scope.row.batch_number" placeholder="批号" @blur="save(scope)"/>
        </template>
      </el-table-column>

      <el-table-column align="center" label="12小时显影">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.h12_xian_ying"
            :fetch-suggestions="querySearchXianYing"
            select-when-unmatched
            placeholder="放置12小时显影"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="24小时显影">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.h24_xian_ying"
            :fetch-suggestions="querySearchXianYing"
            select-when-unmatched
            placeholder="放置24小时显影"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="隔夜显影">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.ge_ye_xian_ying"
            :fetch-suggestions="querySearchXianYing"
            select-when-unmatched
            placeholder="隔夜显影"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="隔夜曝光">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.ge_ye_bao_guang"
            :fetch-suggestions="querySearchLevel"
            select-when-unmatched
            placeholder="隔夜曝光"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="老化">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.lao_hua"
            :fetch-suggestions="querySearchPassNG"
            select-when-unmatched
            placeholder="老化"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="耐焊性">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.nai_han_xing"
            :fetch-suggestions="querySearchPassNG"
            select-when-unmatched
            placeholder="耐焊性"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="耐溶剂">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.nai_rong_ji"
            :fetch-suggestions="querySearchPassNG"
            select-when-unmatched
            placeholder="耐溶剂"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="耐酸碱">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.nai_suan_jian"
            :fetch-suggestions="querySearchPassNG"
            select-when-unmatched
            placeholder="耐酸碱"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="检测员">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.tester"
            :fetch-suggestions="querySearchTesters"
            select-when-unmatched
            value-key="name"
            placeholder="检测员"
            @select="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="60" class-name="small-padding fixed-width">
        <template slot-scope="scope">
          <el-button type="text" size="small" @click="handleDelete(scope)">删除</el-button>
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
  </div>
</template>

<script>
import { deepClone } from '@/utils'
import { productApi } from '@/api/basedata'
import { patternTestApi } from '@/api/qc'
import testersSuggestions from '@/mixins/testersSuggestions'
import pagination from '@/mixins/pagination'
import echoTimeMethod from '@/mixins/echoTimeMethod'

export default {
  name: 'PatternTest',
  mixins: [
    testersSuggestions,
    pagination,
    echoTimeMethod
  ],
  data() {
    return {
      list: [],
      cachedData: [],
      listLoading: true,
      queryParams: {
        q: ''
      },
      products: [],
      testers: []
    }
  },
  mounted() {
    this.$nextTick(function () {
      this.fetchData()
    })
  },
  methods: {
    fetchData() {
      this.listLoading = true
      patternTestApi.list({ params: this.queryParams }).then(response => {
        const { data } = response.data
        this.list = data
        this.updateCache()
        this.pagination(response)
        this.listLoading = false
      })
    },
    handleSearch() {
      this.fetchData()
    },
    updateCache() {
      this.cachedData = deepClone(this.list)
    },
    querySearchProducts(queryString, cb) {
      productApi.list({ params: { q: queryString, sort_by: 'internal_name', order: 'asc' } }).then(response => {
        const { data } = response.data
        this.products = data
        cb(this.products)
      })
    },
    querySearchXianYing(queryString, cb) {
      const suggests = ['干净', '微量', '少量', '少量多', '有残', '严残', '烤死一半', '完全烤死'].map(item => {
        return { value: item, label: item }
      })

      const results = queryString
        ? suggests.filter(suggest => suggest.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : suggests

      cb(results)
    },
    querySearchPassNG(queryString, cb) {
      const suggests = ['PASS', 'NG'].map(item => {
        return { value: item, label: item }
      })

      const results = queryString
        ? suggests.filter(suggest => suggest.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : suggests

      cb(results)
    },
    querySearchLevel(queryString, cb) {
      const suggests = [...Array(48).keys()].map(item => {
        const value = item * 0.5
        return { value: value.toString(), label: value }
      })

      const results = queryString
        ? suggests.filter(suggest => suggest.value.toLowerCase().indexOf(queryString.toLowerCase()) >= 0)
        : suggests

      cb(results)
    },
    newTest() {
      return {
        id: null,
        product_name: '',
        batch_number: '',
        nai_han_xing: '',
        nai_rong_ji: '',
        nai_suan_jian: '',
        h12_xian_ying: '',
        h24_xian_ying: '',
        ge_ye_xian_ying: '',
        ge_ye_bao_guang: '',
        die_ban: '',
        lao_hua: '',
        tester: ''
      }
    },
    handleCreate() {
      this.list.unshift(this.newTest())
      this.updateCache()
    },
    handleDelete(scope) {
      this.$confirm('此操作将永久删除该条目, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        patternTestApi.delete(scope.row.id).then(response => {
          this.list.splice(scope.$index, 1)
          this.updateCache()
          this.$message({
            type: 'success',
            message: '删除成功!'
          })
        })
      })
    },
    handleDownload() {
      this.$message({
        showClose: true,
        message: '还未实现此功能'
      })
    },
    save(scope) {
      const index = scope.$index
      const test = scope.row

      const changed = Object.keys(test).some(key => {
        return test[key] != this.cachedData[index][key]
      })

      if (changed) {
        let request

        if (test.id) {
          request = patternTestApi.update(test.id, test)
        } else {
          request = patternTestApi.add(test)
        }

        request.then(response => {
          const { data } = response.data

          this.list.splice(index, 1, data) // 更新视图

          this.updateCache()
        })
      }
    }
  }
}
</script>

<style scoped>
  .edit-input {
    padding-right: 100px;
  }

  .cancel-btn {
    position: absolute;
    right: 15px;
    top: 10px;
  }
</style>
