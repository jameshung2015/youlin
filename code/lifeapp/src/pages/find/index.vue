<template>
  <div class="container find-panel">
    <div class="search-panel">
      <i-panel class="search-panel">
        <img class="search-img" src="../../../static/images/search.png" mode="widthFix"/>
        <input class="search-input" v-model="searchVal" mode="wrapped" placeholder="在这里搜索内容" @focus="showMask=true;" @blur="doSearch"/>
      </i-panel>
    </div>
    <div class="find-content">
      <div class="tab-panel">
        <div class="tab-item" :class="[activeItem=='activity'?'active':'']" @click="changeType('activity')">
          <img v-if="activeItem=='activity'" src="../../../static/images/activity-active.png" mode="widthFix"/>
          <img v-if="activeItem!='activity'" src="../../../static/images/activity.png" mode="widthFix"/>
        </div>
        <div class="tab-item" :class="[activeItem=='topic'?'active':'']" @click="changeType('topic')">
          <img v-if="activeItem=='topic'" src="../../../static/images/topic-active.png" mode="widthFix"/>
          <img v-if="activeItem!='topic'" src="../../../static/images/topic.png" mode="widthFix"/>
        </div>
      </div>
      <ul v-if="activeItem=='activity'" class="result-list find-result-activity">
        <li v-for="item in resultList" :key="item.type_id" class="activity-item" @click="jumpActivity(item)">
          <img :src="item.background_url" mode="aspectFill"/>
          <div :style="'color:' + item.color + ';'">{{ item.title }}</div>
        </li>
      </ul>
      <ul v-if="activeItem=='topic'" class="result-list find-result-topic">
        <li v-for="item in resultList1" :key="item.type_id" class="topic-item" @click="jumpTopic(item)">
          <img :src="item.image" mode="aspectFill"/>
          <div>
            <p class="title text-ellipsis">{{ item.title }}</p>
            <p class="content text-ellipsis">{{ item.desc }}</p>
          </div>
        </li>
      </ul>
    </div>
    <div class="mask" :class="[showMask ? 'mask-show' : '']"></div>
    <!--<button class="concat-btn" open-type="contact" bindcontact="handleContact"></button>-->
  </div>
</template>

<script>
  export default {
    name: '',
    data () {
      return {
        activeItem: 'activity', // activity
        searchVal: '',
        showMask: false,
        height: wx.getSystemInfoSync().windowHeight - 175,
        resultList: [],
        resultList1: []
      }
    },
    onShareAppMessage: function () {
      return {
        title: '优临',
        path: '/pages/time/main', // 默认是当前页面，必须是以‘/’开头的完整路径
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
      // this.searchVal = ''
      this.showMask = false
      // this.getData()
    },
    onPullDownRefresh: function () {
      this.getData()
    },
    methods: {
      // tab切换
      changeType (type) {
        const that = this
        that.activeItem = type
        that.getData()
      },
      // 搜索
      doSearch () {
        this.showMask = false
        // 跳转页面
        if (this.activeItem === 'activity') {
          wx.navigateTo({url: '/pages/find/activity/main?search=' + this.searchVal})
        } else {
          this.getData()
        }
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
        if (that.activeItem === 'activity') { // 活动
          that.$request(that.$baseUrl + '/api/system/active/index', 'GET', {
            searchVal: that.searchVal
          }).then((res) => {
            that.resultList = res.data
            wx.stopPullDownRefresh()
            wx.hideNavigationBarLoading()
          })
        } else { // 话题
          that.$request(that.$baseUrl + '/api/system/subject/all', 'GET', {
            searchVal: that.searchVal
          }).then((res) => {
            that.resultList1 = res.data
            wx.stopPullDownRefresh()
            wx.hideNavigationBarLoading()
            wx.hideToast()
          })
        }
      },
      // 活动跳转
      jumpActivity (item) {
        wx.navigateTo({url: '/pages/find/activity/main?title=' + item.title + '&id=' + item.type_id})
      },
      // 话题跳转
      jumpTopic (item) {
        wx.navigateTo({url: '/pages/find/topic/main?title=' + item.title + '&id=' + item.type_id})
      }
    }
  }
</script>

<style scoped>
  .find-panel{
    position:relative;
    box-sizing: border-box;
    overflow:hidden;
  }
  .find-panel .search-panel{
    position:relative;
    z-index:100;
    width:calc(100% - 25px);
    margin:4px auto 0;
  }
  .find-panel .find-content{
    width:calc(100% - 25px);
    margin: 0 auto;
  }
  .tab-panel{
    position:relative;
    height:52px;
    border-bottom: 0.5px solid #c9c9c9;
    margin-bottom:22px;
  }
  .tab-panel .tab-item{
    width:50%;
    float:left;
    text-align:center;
    box-sizing: border-box;
  }
  .tab-panel .tab-item:first-child{
    padding-left:10%;
  }
  .tab-panel .tab-item:last-child{
    padding-right:10%;
  }
  .tab-panel .tab-item img{
    position: relative;
    top:9px;
    width:40px;
    height:auto;
  }
  .tab-panel .tab-item.active img {
    width: 44px;
    height: auto;
  }
  .result-list{
    height: calc(100vh - 115px);
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
  .find-panel .search-panel .search-img{
    display: block;
    width:18px;
    height:19px;
    position:absolute;
    left:19px;
    top:6px;
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

<style>
  .find-panel .search-input{
    width: 100%;
    height: 30px;
    line-height:30px;
    border-radius: 14px;
    border: solid 0.5px #ffcc00;
    font-size:12px;
    color:#999;
    padding-left:58px;
    background-color:#fff;
    box-sizing:border-box;
  }
</style>
