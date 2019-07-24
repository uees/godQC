<template>
  <div>
    <div class="main-context">
      <div class="author"><span>处理人：</span> {{ dispose.author }}</div>

      <div class="time"><span>创建时间：</span> {{ dispose.created_at | parseTime }}</div>

      <div class="batch">
        <span>品名：</span> {{ dispose.batch.product_name }} {{ dispose.batch.product_name_suffix }},
        <span>批号：</span> {{ dispose.batch.batch_number }},
      </div>

      <div class="method">
        <span>处理办法：</span> {{ dispose.method }}
      </div>

      <div
        v-if="dispose.memo"
        class="method"
      >
        {{ dispose.memo }}
      </div>

      <div
        v-if="dispose.to_record_id"
        class="link"
      >
        <router-link :to="{name: 'records.show', params: { id: dispose.to_record_id }}">
          经处理后的检测结果
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { parseTime } from '@/filters/erp'
import { productDisposeApi } from '@/api/qc'
import { ProductBatch, ProductDispose } from '@/defines/models'

export default {
  name: 'Dispose',
  filters: { parseTime },
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      dispose: this.newDispose()
    }
  },
  created() {
    this.fetchData()
  },
  activated() {
    // 添加keep-alive后会增加两个生命周期mounted>activated、离开时执行deactivated
  },
  methods: {
    newDispose() {
      const dispose = ProductDispose()
      dispose.batch = ProductBatch()
      return dispose
    },
    async fetchData() {
      const response = await productDisposeApi.show(this.id, { params: { with: 'batch' }})
      const { data } = response.data
      this.dispose = data
    }
  }
}
</script>

<style scoped>
.main-context {
  padding: 20px 15px;
}

.author {
  font-size: 18px;
  color: #666;
  padding: 10px;
}

.time {
  font-size: 12px;
  color: #999;
  padding: 10px;
}

.batch {
  color: #666;
  padding: 10px;
}

.method {
  font-size: 20px;
  margin-top: 10px;
  color: #b3450e;
  width: 100%;
  background-color: #f7f7f7;
  padding: 20px 10px;
}

.link {
  text-decoration: underline;
  color: blue;
}
</style>
