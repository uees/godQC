<template>
  <div class="dashboard-editor-container">
    <div class=" clearfix">
      <pan-thumb
        :image="user.avatar"
        style="float: left"
      > 你的角色:
        <span
          v-for="item in roles"
          :key="item.name"
          class="pan-info-roles"
        >{{ item.display_name }}({{ item.name }})</span>
      </pan-thumb>
      <!-- <github-corner style="position: absolute; top: 0; border: 0; right: 0;" /> -->
      <div class="info-container">
        <span class="display_name">{{ user.name }}</span>
        <span style="font-size:20px;padding-top:20px;display:inline-block;">你好！</span>
      </div>
    </div>
    <div>
      <img
        :src="welcomePng"
        class="welcomePng"
      >
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import PanThumb from '@/components/PanThumb'
// import GithubCorner from '@/components/GithubCorner'

export default {
  name: 'EditorDashboard',
  components: { PanThumb },
  data() {
    return {
      welcomePng: '/static/img/welcome.png'
    }
  },
  computed: {
    ...mapGetters([
      'user',
      'roles'
    ])
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.welcomePng {
  display: block;
  width: 45%;
  margin: 0 auto;
}

.dashboard-editor-container {
  background-color: #e3e3e3;
  min-height: 100vh;
  padding: 50px 60px 0;

  .pan-info-roles {
    font-size: 12px;
    font-weight: 700;
    color: #333;
    display: block;
  }

  .info-container {
    position: relative;
    margin-left: 190px;
    height: 150px;
    line-height: 200px;

    .display_name {
      font-size: 48px;
      line-height: 48px;
      color: #212121;
      position: absolute;
      top: 25px;
    }
  }
}
</style>
