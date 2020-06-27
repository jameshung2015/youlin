<template>
  <div class="container topic-panel">
    <div class="search-panel">
      <i-panel class="search-panel">
        <i-icon type="search" size="14" class="search-img" color="#cac8c8"/>
        <input class="search-input" v-model="searchVal" mode="wrapped" placeholder="搜索精彩活动" @focus="showMask=true;" @blur="doSearch"/>
      </i-panel>
    </div>
    <ul class="result-list find-result-activity">
      <li v-for="(item, index) in resultList" :key="item.active_id" class="activity-item clearfix" @click="toPage(item)">
        <div class="main clearfix">
          <img class="main-img fl" :src="item.cover_url" mode="aspectFill"/>
          <div class="main-content fr">
            <p class="main-title">
              <img src="../../../../static/images/circle-icon.png" mode="aspectFill"/>
              <span class="text-ellipsis">{{ item.title }}</span>
            </p>
            <div class="main-con">
              <span class="main-time">{{ item.created_at }}</span>
              <span v-if="item.parted" class="join-btn joined" @click.stop>已参与</span>
              <span v-if="!item.parted" class="join-btn join" @click.stop="toJoin(item)">马上参与</span>
            </div>
          </div>
        </div>
      </li>
    </ul>
    <div class="mask" :class="[showMask ? 'mask-show' : '']"></div>
  </div>
</template>

<script>
  export default {
    name: '',
    data () {
      return {
        searchVal: '',
        showMask: false,
        url: '',
        title: '',
        id: '',
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
    onLoad: function (options) {
      this.title = options.title
      this.id = options.id
      this.url = ''
      wx.setNavigationBarTitle({
        title: this.title
      })
      this.getData()
    },
    onShow () {
      this.url = ''
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
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/second/active', 'GET', {
          searchVal: that.searchVal,
          typeId: that.id
        }).then((res) => {
          that.resultList = res.data
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      },
      // 跳转到详情
      toPage (item) {
        wx.navigateTo({url: '/pages/time/empty/main?url=' + item.url})
      },
      // 跳转到参与话题
      toJoin (item) {
        wx.navigateTo({url: '/pages/time/topic/main?title=' + item.title + '&source=' + item.active_id})
      }
    }
  }
</script>

<style scoped lang="scss">
  /*.point-btn{
    position:absolute;
    bottom:15px;
    left:50%;
    margin-left: -25px;
    width: 50px;
    height: 8px;
    z-index:100;
  }*/
  .topic-panel{
    position:relative;
    height:100%;
    box-sizing: content-box;
    overflow:hidden;
    padding: 0 14px;
    .mask{
      position:fixed;
      left: 0;
      right:0;
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
    .search-panel{
      position:relative;
      z-index:100;
      width:100%;
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
    /*.topic-content{
      width:calc(100% - 25px);
      height: calc(100vh - 50px);
      margin: 0 auto;
      .title{
        height:51px;
        line-height:51px;
        text-align:center;
        font-size: 15px;
        color: #400181;
      }
    }*/
    .find-result-activity{
      width:100%;
      height: calc(100% - 58px);
      overflow:auto;
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
        }
        .main-content{
          width: calc(100% - 143px);
          .main-title{
            font-size: 14px;
            color: #666666;
            padding: 9px 0;
            vertical-align: middle;
            img{
              display: inline-block;
              width:19px;
              height:19px;
              margin-right:6px;
              vertical-align: middle;
            }
            span{
              display: inline-block;
              width:calc(100% - 25px);
              vertical-align: middle;
            }
          }
          .main-con{
            margin-top:8px;
            .main-time{
              display:inline-block;
              width:calc(100% - 135px);
              font-size: 12px;
              line-height:26px;
              color: #9d9c9c;
              vertical-align: middle;
              overflow: hidden;
              white-space: nowrap;
            }
            .join-btn{
              display:inline-block;
              margin-left:54px;
              width: 79px;
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
    /*.activity-item .time{
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
    }*/
  }
</style>
