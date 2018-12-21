<template>
  <div>
    <div class="main-context">
      <div class="author"><span>处理人：</span> {{ dispose.author }}</div>
      <div class="time"><span>创建时间：</span> {{ echoTime(dispose.created_at) }}</div>
      <div class="batch">
        <span>品名：</span> {{ dispose.batch.product_name }} {{ dispose.batch.product_name_suffix }},
        <span>批号：</span> {{ dispose.batch.batch_number }},
      </div>
      <div class="method">
        <span>处理办法：</span> {{ dispose.method }}
      </div>

      <div v-if="dispose.to_record_id" class="link">
        <router-link :to="{name: 'records.show', params: { id: dispose.to_record_id }}">
          经处理后的检测结果
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import echoTimeMethod from '@/mixins/echoTimeMethod'
import { productDisposeApi } from '@/api/qc'

export default {
  name: 'Dispose',
  mixins: [
    echoTimeMethod
  ],
  props: {
    id: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      dispose: this.newDispose()
    }
  },
  mounted() {
    productDisposeApi.detail(this.id, {params: {with: 'batch'}}).then(response => {
      const { data } = response.data
      this.dispose = data

      this.$route.meta.title = `${this.dispose.batch.batch_number} 处理方法`
    })
  },
  methods: {
    newDispose() {
      return {
        id: 0,
        created_at: {
          date: '',
          timezone_type: '',
          timezone: ''
        },
        updated_at: {
          date: '',
          timezone_type: '',
          timezone: ''
        },
        product_batch_id: 0,
        from_record_id: 0,
        to_record_id: 0,
        method: '',
        author: '',
        memo: '',
        batch: {
          id: 0,
          product_name: '',
          product_name_suffix: '',
          batch_number: '',
          type: '',
          memo: ''
        }
      }
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
