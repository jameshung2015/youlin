<template>
  <div class="container find-panel">
    <div class="search-panel">
      <i-panel class="search-panel">
        <img class="search-img" src="../../../../static/images/search.png" mode="widthFix"/>
        <input class="search-input" v-model="searchVal" mode="wrapped" placeholder="在这里搜索内容" @focus="showMask=true;" @blur="doSearch"/>
      </i-panel>
    </div>
    <div class="find-content">
      <ul class="result-list find-result-activity">
        <li v-for="item in resultList" :key="item.active_id" class="activity-item clearfix" @click="toPage(item)">
          <div class="time">
            <span class="day">{{ item.day }}</span>
            <span class="month">{{ item.month }}月</span>
            <span class="point" :style="item.parted?'':'background:#803ac9;'"></span>
          </div>
          <img :src="item.thumb_image" mode="aspectFill"/>
          <div class="main">
            <p class="text-ellipsis">#{{ item.title }}#</p>
            <p class="price">{{ (item.price=='0' || item.price=='0.00') ? '免费' : item.price+'元' }}</p>
            <span v-if="item.parted" class="joined">已参与</span>
            <span v-if="!item.parted" class="join">参与</span>
          </div>

        </li>
      </ul>
    </div>
    <div class="mask" :class="[showMask ? 'mask-show' : '']"></div>
    <img class="point-btn" src="../../../../static/images/point.png" mode="widthFix"/>
    <div v-if="showDialog" class="authority-dialog">
      <p>请联系客服开通权限</p>
      <button @click="showDialog=false">确定</button>
    </div>
    <button class="concat-btn" open-type="contact" bindcontact="handleContact"></button>
  </div>
</template>

<script>
  export default {
    name: '',
    data () {
      return {
        showDialog: false,
        type: '',
        id: '',
        searchVal: '',
        showMask: false,
        resultList: []
      }
    },
    onShareAppMessage: function () {
      return {
        title: '优临',
        path: '/pages/time/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
    },
    onLoad: function (options) {
      this.showDialog = false
      this.type = '活动'
      this.id = ''
      this.searchVal = ''
      this.showMask = false
      this.resultList = []
      if (options.type) {
        this.type = options.type
      }
      if (options.search) {
        this.type = '搜索内容'
        this.searchVal = options.search
      }
      if (options.id) {
        this.id = options.id
      }
      wx.setNavigationBarTitle({
        title: this.type
      })
      this.getData()
    },
    onPullDownRefresh: function () {
      this.getData()
    },
    methods: {
      // 获取日期
      getDay (time) {
        // 2019-12-08 14:30:00
        const timearr = time.replace(' ', '-').replace(':', '-').split('-')
        return timearr[2]
      },
      // 获取月
      getMonth (time) {
        // 2019-12-08 14:30:00
        const timearr = time.replace(' ', '-').replace(':', '-').split('-')
        return timearr[1]
      },
      // 搜索
      doSearch () {
        this.showMask = false
        this.getData()
      },
      // 获取数据
      getData () {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/system/active/all', 'GET', {
          searchVal: that.searchVal,
          type: that.id
        }).then((res) => {
          res.data.forEach((item, index) => {
            item.day = that.getDay(item.created_at)
            item.month = that.getMonth(item.created_at)
          })
          that.resultList = res.data
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      },
      // 跳转到已参与活动
      toPage (item) {
        // 判断是否有权限
        if (!item.authority) {
          this.showDialog = true
        } else {
          this.$request(this.$baseUrl + '/api/system/active/click', 'GET', {
            id: item.active_id
          }).then((res) => {
          })
          wx.navigateTo({url: '/pages/find/activityInner/main?image=' + escape(item.image ? item.image : '') + '&title=' + item.title + '&id=' + item.active_id + '&content=' + item.content + '&authority=' + item.authority})
        }
      }
    }
  }
</script>

<style scoped>
  .concat-btn{
    position:fixed;
    right:0;
    top:40%;
    width:54px;
    height:54px;
    border-radius: 50%;
    background:url('../../../../static/images/contact-btn.png') no-repeat center center;
    background-size:100% 100%;
    box-shadow: 0px 2px 2px 0px rgba(83, 83, 83, 0.15);
    z-index:1000;
    border:none;
  }
  .concat-btn::after{
    border:none;
  }
  .authority-dialog{
    position: fixed;
    top: 50%;
    left:50%;
    z-index:10000;
    width: 236px;
    height: 126px;
    margin-top: -68px;
    margin-left: -118px;
    opacity: 0.7;
    background-color: #000000;
    border-radius: 5px;
    padding:28px 0;
    box-sizing: border-box;
    animation:myShow 0.5s;
  }
  @keyframes myShow {
    from {opacity: 0;}
    to {opacity: 0.7;}
  }
  .authority-dialog p{
    line-height:25px;
    font-size: 15px;
    color: #ffffff;
    text-align:center;
  }
  .authority-dialog button{
    width: 100px;
    height: 30px;
    margin-top:16px;
    line-height:30px;
    background-color: #ffcc00;
    border-radius: 5px;
    font-size: 15px;
    color: #000000;
    text-align:center;
  }
  .point-btn{
    position:absolute;
    bottom:15px;
    left:50%;
    margin-left: -25px;
    width: 50px;
    height: 8px;
    z-index:100;
  }
  .find-panel{
    position:relative;
    box-sizing: content-box;
    height:100%;
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
    height: calc(100vh - 70px);
    margin: 0 auto;
    overflow:auto;
  }
  .activity-item{
    position:relative;
    margin-top:22px;
    width:100%;
    height: 100px;
    box-sizing: border-box;
  }
  .activity-item .time{
    float:left;
    width:21px;
    margin-right:10px;
    text-align:center;
  }
  .activity-item .time span{
    display:block;
  }
  .activity-item .time .day{
    font-size: 15px;
    color: #ffcc00;
  }
  .activity-item .time .month{
    font-size: 9px;
    color: #999999;
  }
  .time .point{
    width:8px;
    height:8px;
    background:#ffcc00;
    border-radius:50%;
    margin:0 auto;
  }
  .activity-item img{
    float:left;
    display: block;
    width:115px;
    height:100px;
    margin-right:24px;
    background:#eee;
  }
  .activity-item .main{
    position:relative;
    float:left;
    width:calc(100% - 172px);
    height:100%;
  }
  .activity-item .main p{
    width:100%;
    line-height:30px;
    font-size: 12px;
    color: #400181;
  }
  .activity-item .main span{
    position:absolute;
    bottom:6px;
    left:0;
  }
  .activity-item .joined{
    display: inline-block;
    padding: 5px 8px;
    font-size: 10px;
    border-radius: 12px;
    border: solid 0.5px #ff3447;
    color: #ff3447;
  }
  .activity-item .join{
    display: inline-block;
    padding: 4px 8px;
    font-size: 10px;
    border-radius: 12px;
    border: solid 0.5px #400181;
    color: #400181;
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
    border: solid 1px #ffcc00;
    font-size:10px;
    color:#999;
    padding-left:58px;
    background-color:#fff;
    box-sizing:border-box;
  }
</style>
