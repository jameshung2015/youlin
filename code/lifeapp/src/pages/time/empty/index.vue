<template>
  <div class="container">
    <web-view v-if="url" :src="url"></web-view>
  </div>
</template>

<script>
  export default {
    name: 'Empty',
    data () {
      return {
        url: ''
      }
    },
    onShareAppMessage: function () {
      return {
        title: '优临',
        path: '/pages/time/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
    },
    onLoad (options) {
      this.url = ''
      if (options.url) {
        this.url = options.url
      }
    }
  }
</script>

