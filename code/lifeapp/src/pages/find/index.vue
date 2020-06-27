<template>
  <div class="container find-panel">
    <div class="search-panel">
      <i-panel class="search-panel">
        <i-icon type="search" size="14" class="search-img" color="#cac8c8"/>
        <input class="search-input" v-model="searchVal" mode="wrapped" placeholder="搜索精彩活动" @focus="showMask=true;" @blur="doSearch"/>
      </i-panel>
    </div>
    <div class="find-content">
      <ul v-if="activeItem=='topic'" class="result-list find-result-topic">
        <li v-for="item in resultList" :key="item.type_id" class="topic-item" @click="jumpTopic(item)">
          <img :src="item.image" mode="aspectFill"/>
          <div>
            <p class="title text-ellipsis">{{ item.title }}</p>
            <p class="content text-ellipsis">{{ item.desc }}</p>
          </div>
        </li>
      </ul>
    </div>
    <div class="mask" :class="[showMask ? 'mask-show' : '']"></div>
  </div>
</template>

<script>
  export default {
    name: '',
    data () {
      return {
        activeItem: 'topic', // activity
        searchVal: '',
        showMask: false,
        height: wx.getSystemInfoSync().windowHeight - 175,
        resultList: []
      }
    },
    onShareAppMessage: function () {
      return {
        title: '优临',
        path: '/pages/home/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
    },
    onLoad () {
      this.Bus.$on('change1', () => {
        this.getData()
      })
      this.getData()
    },
    onShow () {
      if (!wx.getStorageSync('login')) { // 未登录
        wx.navigateTo({url: '/pages/home/main'})
      }
      this.showMask = false
    },
    onPullDownRefresh: function () {
      this.getData()
    },
    methods: {
      // 搜索
      doSearch () {
        this.showMask = false
        // 跳转页面
        this.getData()
      },
      // 获取数据
      getData () {
        const that = this
        wx.showNavigationBarLoading()
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        that.$request(that.$baseUrl + '/api/second/active/type', 'GET', {
          searchVal: that.searchVal
        }).then((res) => {
          that.resultList = res.data
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      },
      // 话题跳转
      jumpTopic (item) {
        wx.navigateTo({url: '/pages/find/topic/main?title=' + item.title + '&id=' + item.type_id})
      }
    }
  }
</script>

<style scoped lang="scss">
  .find-panel{
    position:relative;
    width:100%;
    height:100%;
    box-sizing: border-box;
    overflow:hidden;
    .search-panel{
      position:relative;
      z-index:100;
      width:calc(100% - 25px);
      margin:8px auto 20px;
      height: 30px;
      line-height: 30px;
      .search-img{
        position:absolute;
        left:39px;
        top:0;
        line-height:28px;
        z-index:1000;
      }
      .search-input{
        height: 30px;
        line-height: 30px;
        background-color: #f7f7f7;
        border-radius: 14px;
        padding-left:58px;
        font-size: 12px;
        color: #cac8c8;
        border:none;
      }
    }
  }
  .find-panel .find-content{
    width:calc(100% - 25px);
    height:calc(100% - 65px);
    margin: 0 auto;
  }
  .result-list{
    height: calc(100% - 10px);
    overflow:auto;
  }
  .activity-item{
    position:relative;
    margin-bottom:10px;
    width:100%;
    height: 75px;
    background-color: #f7f5e7;
    box-shadow: 1px 2px 3px 0px rgba(3, 0, 0, 0.05);
    border-radius: 5px;
    overflow:hidden;
    color:#803ac9;
  }
  .activity-item img{
    width:100%;
    height:100%;
  }
  .activity-item div{
    position:absolute;
    top:50%;
    left:50%;
    transform: translate(-50%, -50%);
    width: calc(100% - 36px);
    height: 55px;
    line-height:55px;
    background-color: #ffffff;
    border-radius: 28px;
    opacity: 0.8;
    font-size: 16px;
    text-align:center;
  }
  .topic-item{
    position:relative;
    margin-bottom:10px;
    width:100%;
    height: 60px;
    border-bottom:0.5px solid #eaeaea;
    padding: 10px 8px;
    box-sizing: border-box;
  }
  .topic-item img{
    float:left;
    width:50px;
    height:40px;
  }
  .topic-item div{
    width:calc(100% - 80px);
    float:left;
    padding: 0 15px;
  }
  .topic-item div .title{
    width:100%;
    font-size: 15px;
    color: #ff9c00;
    line-height:25px;
  }
  .topic-item div .content{
    width:100%;
    font-size: 11px;
    color: #666666;
  }
  .mask{
    width:100%;
    position:fixed;
    top:0;
    bottom:0;
    background: rgba(0,0,0,0.5);
    z-index:-1;
    opacity: 0;
    transition: opacity 0.1s;
  }
  .mask.mask-show{
    z-index:10;
    opacity: 1;
  }
</style>

