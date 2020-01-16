<template>
  <div class="container topic-panel">
    <div class="topic-content">
      <div class="title">{{ title }}</div>
      <ul class="result-list find-result-activity">
        <li v-for="(item, index) in resultList" :key="item.subject_id" class="activity-item clearfix" @click="toPage(item.subject_id)">
          <div class="time">
              <span class="day">{{ item.day }}</span>
              <span class="month">{{ item.month }}月</span>
              <span class="point" :style="item.parted?'':'background:#803ac9;'"></span>
            </div>
          <div class="main">
              <span class="content">{{ index+1 }}.{{ item.title }}</span>
              <span v-if="item.parted" class="joined">已参与</span>
              <span v-if="!item.parted" class="join" @click.stop="toJoin(item)">参与</span>
            </div>
        </li>
      </ul>
      <!--<web-view v-if="url" :src="url"></web-view>-->
    </div>
    <img class="point-btn" src="../../../../static/images/point.png" mode="widthFix"/>
  </div>
</template>

<script>
  export default {
    name: '',
    data () {
      return {
        url: '',
        title: '',
        id: '',
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
      this.title = options.title
      this.id = options.id
      this.url = ''
      this.getData()
    },
    onShow () {
      this.url = ''
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
      // 获取数据
      getData () {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/system/subject/index', 'GET', {
          typeId: that.id
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
      // 跳转到详情
      toPage (id) {
        const that = this
        that.$request(that.$baseUrl + '/api/system/subject/show', 'GET', {
          subjectId: id
        }).then((res) => {
          that.url = res.data.url
          wx.navigateTo({url: '/pages/time/empty/main?url=' + res.data.url})
        })
      },
      // 跳转到参与话题
      toJoin (item) {
        wx.navigateTo({url: '/pages/find/joinTopic/main?image=' + escape(item.image) + '&title=' + item.title + '&id=' + item.subject_id + '&content=' + item.content})
      }
    }
  }
</script>

<style scoped>
  .point-btn{
    position:absolute;
    bottom:15px;
    left:50%;
    margin-left: -25px;
    width: 50px;
    height: 8px;
    z-index:100;
  }
  .topic-panel{
    position:relative;
    height:100%;
    box-sizing: content-box;
    overflow:hidden;
  }
  .topic-panel .topic-content{
    width:calc(100% - 25px);
    height: calc(100vh - 50px);
    margin: 0 auto;
  }
  .topic-panel .find-result-activity{
    width:100%;
    height: calc(100% - 51px);
    overflow:auto;
  }
  .topic-content .title{
    height:51px;
    line-height:51px;
    text-align:center;
    font-size: 15px;
    color: #400181;
  }
  .activity-item{
    position:relative;
  }
  .activity-item .time{
    float:left;
    width:19px;
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
  .activity-item .main{
    position:relative;
    width: calc(100% - 29px);
    float:left;
    padding:20px;
    background-color: #ffffff;
    border-radius: 10px;
    border: solid 1px #ffcc00;
    margin-bottom:10px;
    box-sizing: border-box;
  }
  .activity-item .content{
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    width:calc(100% - 60px);
    float:left;
    font-size: 12px;
    line-height:20px;
    color: #333;
  }
  .activity-item .joined{
    float:right;
    display: inline-block;
    padding: 5px 8px;
    font-size: 10px;
    border-radius: 12px;
    border: solid 0.5px #ff3447;
    color: #ff3447;
  }
  .activity-item .join{
    float:right;
    display: inline-block;
    padding: 4px 8px;
    font-size: 10px;
    border-radius: 12px;
    border: solid 0.5px #400181;
    color: #400181;
  }
</style>
