<template>
  <div class="container my-activity">
    <ul class="find-result-activity">
      <p class="no-data tc" v-if="resultList.length==0">暂无数据</p>
      <li v-for="(item, index) in resultList" :key="item.id" class="activity-item clearfix" @click="toPage(item.id)">
        <div class="main clearfix">
          <img class="main-img fl" :src="item.active.cover_url" mode="aspectFill"/>
          <div class="main-content fr">
            <p class="main-title">
              <img src="../../../../static/images/circle-icon.png" mode="aspectFill"/>
              <span class="text-ellipsis">{{ item.active.title }}</span>
            </p>
            <div class="main-con">
              <span class="main-time text-ellipsis">{{ item.active.active_time?item.active.active_time:'暂未设置' }}</span>
              <span class="join-btn join">正在参与</span>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    name: 'MyActivity',
    data: function () {
      return {
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
    onPullDownRefresh: function () {
      this.getData()
    },
    onShow () {
      this.getData()
    },
    methods: {
      // 获取我的报名列表
      getData () {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/second/active/apply', 'GET', {}).then((res) => {
          that.resultList = res.data
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      },
      // 跳转到话题
      toPage (id) {
        // const that = this
        // source-0是用户发起,其他数值为系统设置,其他人呢
        wx.navigateTo({url: '/pages/find/activityInner/main?id=' + id})
      }
    }
  }
</script>

<style scoped lang="scss">
  .my-activity{
    .find-result-activity{
      height: 100%;
      overflow:auto;
      padding: 14px;
      .no-data{
        margin-top:20px;
        font-size:12px;
        color: #666666;
      }
      .activity-item{
        position:relative;
        .main{
          padding: 21px 0;
          margin-bottom:13px;
          .main-img{
            width: 136px;
            height:75px;
            border-radius: 6px;
            background-color:#eee;
          }
          .main-content{
            width: calc(100% - 143px);
            .main-title{
              font-size: 14px;
              color: #666666;
              padding: 9px 0;
              img{
                display: inline-block;
                width:19px;
                height:19px;
                margin-right:6px;
              }
              span{
                display: inline-block;
                width:calc(100% - 25px);
              }
            }
            .main-con{
              margin-top:8px;
              .main-time{
                display:inline-block;
                width:calc(100% - 75px);
                font-size: 12px;
                line-height:26px;
                color: #9d9c9c;
                vertical-align: middle;
              }
              .join-btn{
                display:inline-block;
                margin-left:7px;
                width: 64px;
                height: 26px;
                line-height:24px;
                border-radius: 13px;
                border: solid 1px #ffbd33;
                font-size: 12px;
                color: #ffbd33;
                text-align:center;
                vertical-align: middle;
                &.joined{
                  border: solid 1px #9d9c9c;
                  color: #9d9c9c;
                }
              }
            }
          }
        }
      }
    }
  }
</style>
