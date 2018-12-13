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

      <el-button class="filter-item" type="primary" icon="el-icon-document" @click="handleDownload">导出</el-button>
    </div>

    <el-table v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column width="180px" align="center" label="创建时间">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at.date | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="品名">
        <template slot-scope="scope">
          <span>{{ scope.row.product_name }}</span>
          <el-autocomplete
            v-model="scope.row.product_name"
            :fetch-suggestions="querySearchProducts"
            placeholder="品名"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="批号">
        <template slot-scope="scope">
          <el-input v-model="scope.row.batch_number" placeholder="批号"/>
        </template>
      </el-table-column>

      <el-table-column align="center" label="耐焊性">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.nai_han_xing"
            :fetch-suggestions="querySearchPassNG"
            placeholder="耐焊性"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="放置12小时显影">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.h12_xian_ying"
            :fetch-suggestions="querySearchXianYing"
            placeholder="放置12小时显影"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="放置24小时显影">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.h24_xian_ying"
            :fetch-suggestions="querySearchXianYing"
            placeholder="放置24小时显影"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="隔夜显影">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.ge_ye_xian_ying"
            :fetch-suggestions="querySearchXianYing"
            placeholder="隔夜显影"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="隔夜曝光">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.ge_ye_bao_guang"
            :fetch-suggestions="querySearchLevel"
            placeholder="隔夜曝光"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="叠板">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.die_ban"
            :fetch-suggestions="querySearchPassNG"
            placeholder="叠板"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="老化">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.lao_hua"
            :fetch-suggestions="querySearchPassNG"
            placeholder="老化"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

      <el-table-column align="center" label="检测员">
        <template slot-scope="scope">
          <el-autocomplete
            v-model="scope.row.tester"
            :fetch-suggestions="querySearchTesters"
            value-key="name"
            placeholder="检测员"
            @select="save(scope)"
            @blur="save(scope)"
          />
        </template>
      </el-table-column>

    </el-table>
  </div>
</template>

<script>
import { deepClone } from '@/utils'
import { productApi } from '@/api/basedata'
import { patternTestApi } from '@/api/qc'
import testersSuggestions from '@/mixins/testersSuggestions'
import pagination from '@/mixins/pagination'

export default {
  name: 'PatternTest',
  mixins: [
    testersSuggestions,
    pagination
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
      patternTestApi.list({params: this.queryParams}).then(response => {
        const {data} = response.data
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

          scope.row = data

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
