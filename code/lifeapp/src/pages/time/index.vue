<template>
  <div class="container time-panel">
    <!--<div class="line"></div>-->
    <div class="top-panel clearfix">
      <div class="location-box fl" @click="selectCity">
        <i-icon type="coordinates_fill" size="16" color="#000"/>
        <span class="location-txt text-ellipsis">{{ userInfo.city }}</span>
      </div>
      <div class="search-panel fr">
        <i-panel class="search-panel">
          <!--<img class="search-img" src="../../../static/images/search.png"/>-->
          <i-icon type="search" size="14" class="search-img" color="#cac8c8"/>
          <input class="search-input" v-model="searchVal" mode="wrapped" placeholder="搜索精彩活动" @focus="showMask=true;" @blur="doSearch"/>
        </i-panel>
      </div>
    </div>
    <div class="main-panel">
      <ul class="search-result">
        <li v-for="(item, index) in timeList" :key="item.id" class="result-item">
          <div class="create-time">
            <span class="time-month">{{ getDateFunc[index].month }}</span>
            <span class="time-year">{{ getDateFunc[index].year }}</span>
            <span class="time-day">{{ getDateFunc[index].day }}</span>
          </div>
          <span class="point"></span>
          <div class="card" @click="toPage1(item)">
            <div class="card-title">
              <i-avatar :src="item.user.header" shape="circle"></i-avatar>
              <div class="topic-right">
                <div class="author text-ellipsis">{{item.user.nickname}}</div>
                <div class="time">{{ item.user.create }}</div>
              </div>
              <!--<span class="title">#{{ item.title }}#</span>
              <span v-if="item.master_type==1" class="author">自己</span>
              <span v-if="item.master_type==2" class="author">优临</span>
              <span v-if="item.master_type==3" class="author">{{item.master_name}}</span>-->
            </div>
            <div class="card-content">
              <div class="content-txt">
                <span class="title">#{{ item.active.title }}</span>
                {{ limitNumber[index] }}
                <span v-if="item.active.content.length > 20" class="more-content">全文</span>
              </div>
            </div>
            <i-row v-if="item.images.length" i-class="card-img">
              <i-col v-for="(img, index1) in item.images" :key="index+'-'+index1" span="12" i-class="col-class">
                <img :src="img.filepath" mode="aspectFill"/>
              </i-col>
            </i-row>
            <div class="card-footer-panel">
              <p class="card-footer">
                <i-icon type="time_fill" size="14" color="#a2a19f"/>
                <span>开始时间：{{ item.active.active_time?item.active.active_time:'暂未设置' }}</span>
              </p>
              <p v-if="item.active.label" class="card-footer">
                <i-icon type="coordinates_fill" size="16" color="#a2a19f"/>
                <span>{{ item.active.label?item.active.label:'暂未设置' }}</span>
              </p>
            </div>
            <div class="item-bottom" @click.stop>
              <div class="btn-box tl"  @click="toPage1(item)"><img class="comment-btn" src="../../../static/images/comment-num.png" mode="widthFix"/><span>{{item.apply.length}}</span></div>
              <div class="btn-box tc" @click.stop="showCommit(item,index)"><img class="more-btn" src="../../../static/images/more.png" mode="widthFix"/><span>{{item.comment}}</span></div>
              <div class="btn-box tr">
                <button :data-name="item.active.title" :data-id="item.id" open-type="share" plain="true">
                  <img class="redirect-btn" src="../../../static/images/redirect.png" mode="widthFix"/>
                </button>
                <span>{{item.transmit}}</span>
              </div>
            </div>
          </div>
        </li>
      </ul>
      <div class="system-init">
        <div class="create-time">
          <span class="time-month">{{ getDateFunc1.month }}</span>
          <span class="time-year">{{ getDateFunc1.year }}</span>
          <span class="time-day">{{ getDateFunc1.day }}</span>
        </div>
        <span class="point"></span>
        <div class="content">
          开启我的家庭时光
        </div>
      </div>
    </div>
    <!--<div class="mask" :class="[showMask ? 'mask-show' : '']"></div>-->
    <img class="publish-btn" src="../../../static/images/publish.png" @click="toTopic"/>
    <!--评论弹框------------------开始-->
    <div class="i-as-mask i-class-mask" :class="showCommitFlag ? 'i-as-mask-show' : ''" @click="showCommitFlag=false" @touchmove.prevent></div>
    <div class="i-class i-as comment-panel" :class="showCommitFlag ? 'i-as-show' : ''">
      <div v-show="showCommitFlag"class="comment-box">
        <p class="comment-count tc">{{ commentNum }}条评论
          <i-icon class="close-comment fr" type="close" size="18" color="#454545" @click="showCommitFlag=false"/>
        </p>
        <ul class="comment-list">
          <li v-for="(com, index2) in commentData" :key="com.comment_id" class="comment-item">
            <div class="comment-title" @click="handleComment(com)">
              <img class="avatar-img" :src="com.header" mode="widthFix"/>
              <div class="comment-user tl">
                <p class="comment-name text-ellipsis">{{ com.nickname }}</p>
                <p class="comment-time text-ellipsis">{{ com.created_at }}</p>
              </div>
              <span class="comment-like tr" @click="handleAssent(com)" :style="com.assented!=='没赞过'?'color:#FF6C2E':'color:#999'">
                <i-icon class="assent-btn" type="praise" size="20" :color="com.assented!=='没赞过'?'#FF6C2E':'#999'"/>
                <!--<img class="assent-btn" src="../../../static/images/assent.png" mode="aspectFill"/>-->
                {{ com.assent }}</span>
            </div>
            <p class="comment-content" @click="handleComment(com)">{{ com.content }}</p>
            <div class="comment-content-box">
              <ul v-if="com.revert && com.revert.length" class="comment-list">
                <li v-for="(com1, index3) in com.revert" :key="com1.comment_id" class="comment-item" style="border:none;">
                  <div class="comment-title">
                    <img class="avatar-img" :src="com1.header" mode="widthFix"/>
                    <div class="comment-user comment-user1 tl">
                      <p class="comment-name text-ellipsis">{{ com1.nickname }}</p>
                      <p class="comment-time text-ellipsis">{{ com1.created_at }}</p>
                    </div>
                    <span class="comment-like tr" @click="handleAssent(com1)" :style="com1.assented!=='没赞过'?'color:#FF6C2E':'color:#999'">
                      <i-icon class="assent-btn" type="praise" size="20" :color="com1.assented!=='没赞过'?'#FF6C2E':'#999'"/>
                      {{ com1.assent }}
                    </span>
                  </div>
                  <p class="comment-content">{{ com1.content }}</p>
                </li>
              </ul>
            </div>
          </li>
        </ul>
        <div class="my-comment">
          <input class="comment-input" v-model="commentVal" mode="wrapped" :placeholder="commentPlaceHolder" @focus/>
          <span class="submit-btn tc" @click="doComment()">发送</span>
        </div>
      </div>
    </div>
     <!--评论弹框------------------结束-->
  </div>
</template>

<script>
  export default {
    name: 'TimePanel',
    data () {
      return {
        activeItem: -1,
        scrollTo: 0,
        commentPlaceHolder: '发表你自己的看法吧~',
        page: 1,
        hasMoreData: true, // 上拉时是否继续请求数据，即是否还有更多数据
        commentId: 0, // 回复的评论id
        commentTimeId: '', // 当前评论的活动id
        commentNum: 0, // 总评论条数
        commentData: [
          {
            comment_id: '',
            nickname: '',
            header: '',
            content: '',
            assent: 0,
            created_at: '',
            revert: []
          }
        ], // 评论信息
        showCommitFlag: false, // 是否显示评论弹框
        url: '',
        createTime: '',
        userInfo: wx.getStorageSync('userInfo'),
        showIndex: [],
        timeList: [],
        searchVal: '',
        commentVal: '',
        showMask: false,
        showPublish: false
      }
    },
    computed: {
      // 超出20个字符显示省略号
      limitNumber () {
        const newContent = []
        this.timeList.forEach((item) => {
          let newTxt = item.active.content
          if (item.active.content.length > 20) {
            newTxt = item.active.content.substring(0, 20) + '...'
          }
          newContent.push(newTxt)
        })
        return newContent
      },
      // 日期转为年月日，月为英文缩写
      getDateFunc: function () {
        const dateArr = []
        const m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Spt', 'Oct', 'Nov', 'Dec']
        this.timeList.forEach((item) => {
          if (item.user.create) {
            dateArr.push({
              year: item.user.create.split(' ')[0].split('-')[0],
              month: m[parseInt(item.user.create.split(' ')[0].split('-')[1]) - 1],
              day: item.user.create.split(' ')[0].split('-')[2]
            })
          }
        })
        return dateArr
      },
      // 日期转为年月日，月为英文缩写
      getDateFunc1: function () {
        const m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Spt', 'Oct', 'Nov', 'Dec']
        const dateObj = {
          year: this.createTime.split(' ')[0].split('-')[0],
          month: m[parseInt(this.createTime.split(' ')[0].split('-')[1]) - 1],
          day: this.createTime.split(' ')[0].split('-')[2]
        }
        return dateObj
      }
    },
    watch: {
      // 如果 `showCommitFlag` 发生改变，这个函数就会运行
      showCommitFlag: function (newValue, oldValue) {
        if (newValue) {
          wx.hideTabBar()
        } else {
          wx.showTabBar()
        }
      }
    },
    // 转发分享
    onShareAppMessage: function (options) {
      const that = this
      let shareObj = {
        title: '优临', // 默认是小程序的名称(可以写slogan等)
        path: '/pages/home/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
      // 来自页面内的按钮的转发
      if (options.from === 'button') {
        const eData = options.target.dataset
        shareObj.title = eData.name
        // shareObj.path = '/pages/home/main?title=' + eData.name
        shareObj.path = '/pages/find/activityInner/main?id=' + eData.id
        that.$request(that.$baseUrl + '/api/second/transmit', 'GET', {
          id: eData.id
        }).then((res) => {
        })
      }
      return shareObj
    },
    onLoad (options) {
      if (wx.getStorageSync('title')) {
        this.searchVal = wx.getStorageSync('title')
      } else {
        this.searchVal = ''
      }
      this.userInfo = wx.getStorageSync('userInfo')
      if (!this.userInfo.city) {
        this.userInfo.city = '暂无定位'
      }
      this.Bus.$on('add', () => {
        wx.pageScrollTo({
          scrollTop: 0
        })
      })
    },
    onShow () {
      if (!wx.getStorageSync('login')) { // 未登录
        wx.navigateTo({url: '/pages/home/main'})
      }
      this.Bus.$on('changeCity', (val) => {
        this.userInfo.city = val
      })
      this.showMask = false
      this.showPublish = false
      this.url = ''
      this.commentVal = ''
      this.page = 1
      this.getList()
    },
    onPageScroll: function (e) {
      this.scrollTo = e.scrollTop
    },
    onPullDownRefresh: function () {
      this.page = 1
      if (this.showCommitFlag) {
        wx.stopPullDownRefresh()
      } else {
        this.getList()
      }
    },
    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom: function () {
      if (this.hasMoreData) {
        wx.showToast({
          title: '加载更多数据',
          icon: 'none'
        })
        this.getList()
      } else {
        wx.showToast({
          title: '没有更多数据',
          icon: 'none'
        })
      }
    },
    methods: {
      // 选择城市
      selectCity () {
        wx.navigateTo({url: '/pages/time/setCity/main'})
      },
      // 获取列表
      getList () {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/second/time', 'GET', {
          page: that.page,
          searchVal: that.searchVal,
          address: that.userInfo.city === '全国' ? '' : that.userInfo.city
        }).then((res) => {
          that.createTime = res.data.created
          if (that.page === 1) {
            that.timeList = []
          }
          if (res.data.times) {
            let contentlist = res.data.times
            if (contentlist.length < 20) {
              that.timeList = that.timeList.concat(res.data.times)
              that.hasMoreData = false
            } else {
              that.timeList = that.timeList.concat(res.data.times)
              that.hasMoreData = true
              that.page = that.page + 1
            }
          }
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          setTimeout(() => {
            wx.hideToast()
          }, 1000)
        })
      },
      // 活动跳转
      toTopic () {
        wx.navigateTo({url: '/pages/time/topic/main'})
      },
      // 跳转到话题
      toPage1 (item) {
        // const that = this
        // source-0是用户发起,其他数值为系统设置,其他人呢
        wx.navigateTo({url: '/pages/find/activityInner/main?id=' + item.id})
      },
      // 搜索
      doSearch () {
        const that = this
        that.showMask = false
        that.page = 1
        that.getList()
      },
      // 下拉显示评论
      showCommit (item, index) {
        const that = this
        that.activeItem = index
        that.$request(that.$baseUrl + '/api/second/comment', 'GET', {
          id: item.id
        }).then((res) => {
          that.commentPlaceHolder = '发表你自己的看法吧~'
          that.commentTimeId = item.id
          that.commentId = 0
          that.showCommitFlag = true
          that.commentVal = ''
          that.commentNum = item.comment
          that.commentData = res.data
        })
      },
      // 点赞操作
      handleAssent (item) {
        const that = this
        if (item.assented !== '没赞过') { // 已经点赞则取消点赞
          that.$request(that.$baseUrl + '/api/second/comment/assent/cancel', 'GET', {
            id: item.comment_id
          }).then((res) => {
            that.$request(that.$baseUrl + '/api/second/comment', 'GET', {
              id: that.commentTimeId
            }).then((res) => {
              that.showCommitFlag = true
              that.commentVal = ''
              this.commentData = res.data
            })
          })
        } else { // 未点赞则执行点赞
          that.$request(that.$baseUrl + '/api/second/comment/assent', 'GET', {
            id: item.comment_id,
            time_id: that.commentTimeId
          }).then((res) => {
            that.$request(that.$baseUrl + '/api/second/comment', 'GET', {
              id: that.commentTimeId
            }).then((res) => {
              that.showCommitFlag = true
              that.commentVal = ''
              this.commentData = res.data
            })
          })
        }
      },
      // 点击某条评论
      handleComment (item) {
        this.commentPlaceHolder = '回复' + item.nickname + '的评论'
        this.commentId = item.comment_id
      },
      // 提交评论
      doComment () {
        const that = this
        that.$request(that.$baseUrl + '/api/second/comment/store', 'POST', {
          id: that.commentTimeId,
          comment_id: that.commentId,
          content: that.commentVal
        }).then((res) => {
          that.$set(that.timeList[that.activeItem], 'comment', that.timeList[that.activeItem].comment + 1)
          that.$request(that.$baseUrl + '/api/second/comment', 'GET', {
            id: that.commentTimeId
          }).then((res) => {
            that.commentPlaceHolder = '发表你自己的看法吧~'
            that.showCommitFlag = true
            that.commentVal = ''
            that.commentData = res.data
          })
        })
      }
    }
  }
</script>

<style scoped lang="scss">
  .time-panel{
    padding:0 12px;
    /*height: 100%;*/
    /*overflow: hidden;*/
    overflow:auto;
    /*.line{
      position: fixed;
      top: 65px;
      bottom: 0;
      left: 59px;
      width: 1px;
      border-left: 1px solid #d3d3d3;
    }*/
    .comment-panel {
      height: 437px;
      background-color: #ffffff;
      color: #454545;
      border-radius: 11px 11px 0 0;
      z-index: 5000;
      overflow: hidden;
      .comment-box{
        width:100%;
        height:100%;
      }
      .comment-count{
        height:15px;
        font-size: 14px;
        margin-top:20px;
        margin-bottom:10px;
        .close-comment{
          margin-right: 10px;
        }
      }
      .comment-list{
        width:100%;
        height: calc(100% - 95px);
        overflow-y:auto;
        .comment-item{
          padding: 5px 15px;
          border-bottom: solid 1px #f0f0f0;
          .comment-title{
            height:32px;
            vertical-align: top;
            .avatar-img{
              position:relative;
              display: inline-block;
              top:5px;
              width:32px;
              margin-right:10px;
            }
            .comment-user{
              display: inline-block;
              width: calc(100% - 120px);
              .comment-name{
                width:60%;
                font-size: 11px;
                color: #666666;
                line-height:13px;
              }
              .comment-time{
                font-size: 10px;
                color: #999999;
                line-height:13px;
              }
            }
            .comment-user1{
              width:calc(100% - 105px);
            }
            .comment-like{
              display: inline-block;
              width: 60px;
              font-size: 11px;
              color: #666;
              vertical-align: top;
              .assent-btn{
                margin-right:2px;
              }
            }
          }
          .comment-content{
            margin-top:10px;
            margin-bottom:5px;
            font-size: 13px;
            color: #666666;
            padding-left:43px;
            line-height:20px;
            text-overflow:ellipsis;
            word-wrap:break-word;
          }
          .comment-content-box{
            padding-left:15px;
          }
        }
      }
      .my-comment{
        position:relative;
        height: 50px;
        background-color: #ffffff;
        box-shadow: 0 -5px 8px #f0f0f0;
        .comment-input{
          height:20px;
          background-color: #ffffff;
          padding: 15px 80px 15px 30px;
          font-size: 13px;
          color: #bbbbbb;
        }
        .submit-btn{
          position:absolute;
          top:11.5px;
          right:15px;
          display: inline-block;
          width:60px;
          height:30px;
          line-height:30px;
          color: #333;
          border:1px solid #bbb;
          border-radius:5px;
          font-size:12px;
        }
      }
     }
    .main-panel{
      /*height: calc(100vh - 35px);*/
      overflow: auto;
    }
    button[plain]{
      display: inline-block;
      border:0;
      padding:0;
      height:auto;
      line-height:0;
      border-radius:0;
    }
    .top-panel{
      position:relative;
      background:#fff;
      margin: 7px auto;
      z-index:100;
      .location-box{
        width:62px;
        height: 33px;
        vertical-align: middle;
        line-height: 33px;
      }
      .location-txt{
        display: inline-block;
        width:35px;
        vertical-align: middle;
        margin-left:6px;
        font-size: 15px;
        font-weight: bold;
        font-stretch: normal;
        color: #000000;
      }
      .search-panel{
        position:relative;
        width: calc(100% - 64px);
        height: 33px;
        line-height: 33px;
        .search-img{
          position:absolute;
          left:17px;
          top:0;
          line-height:31px;
          z-index:1000;
        }
        .search-input{
          height: 33px;
          line-height: 33px;
          background-color: #f4f4f4;
          border-radius: 16px;
          padding-left:38px;
          font-size: 12px;
          color: #cac8c8;
          border:none;
        }
      }
    }
    .mask{
      width:100%;
      position:fixed;
      top:0;
      bottom:0;
      left:0;
      right:0;
      background: rgba(0,0,0,0.5);
      z-index:-1;
      opacity: 0;
      transition: opacity 0.1s;
    }
    .mask.mask-show{
      z-index:1000;
      opacity: 1;
    }
    .search-result{
      position:relative;
      margin: 20px 0 0 49px;
      border-left:1px solid #d3d3d3;
      background-color: #f4f4f4;
    }
    .result-item{
      position: relative;
      margin-bottom:10px;
      .card{
        position:relative;
        top:-20px;
        height:auto;
        background: #fff;
        padding:10px 8px 0 15px;
        box-sizing:content-box;
      }
    }
    .create-time{
      position: absolute;
      left:-48px;
      top:-1px;
      font-size:9px;
      span{
        display:inline-block;
        color: #7e7e7e;
      }
      .time-month{
        color: #7e7e7e;
        margin-right:5px;
      }
      .time-day{
        display: block;
        font-size:18px;
        text-align:center;
        font-weight: bold;
        color: #393939;
        margin-top:12px;
      }
    }
    .point{
      position: absolute;
      width:6px;
      height:6px;
      border-radius:50%;
      background-color: #ffbd33;
      border: solid 1px #4d4d4d;
      left:-4.7px;
      top:0px;
      z-index:10;
      box-sizing: content-box;
    }
    .card .card-title{
      vertical-align: middle;
      height:37px;
      i-avatar{
        position:relative;
        top:-5px;
      }
      .topic-right{
        display: inline-block;
        width:calc(100% - 110px);
        margin-left:10px;
        padding-top:5px;
        .author{
          width:100%;
          font-size: 12px;
          line-height: 16px;
          color: #333333;
        }
        .time{
          font-size: 9px;
          line-height: 14px;
          color: #a2a19f;
        }
      }
    }
    .time-address{
      position:absolute;
      left:95px;
      bottom:7px;
    }
    .card-title img{
      display: inline-block;
      vertical-align: top;
      width:80px;
      height: auto;
      max-height:60px;
      margin-right:15px;
    }
    .card .card-content{
      width:100%;
      font-size:14px;
      line-height:24px;
      color:#333;
      margin:5px auto;
      .content-txt{
        font-size: 12px;
        color: #676767;
      }
      .title{
        font-size: 14px;
        line-height: 14px;
        color: #2dcca4;
      }
    }
    .card-content .more-content{
      font-size: 14px;
      color: #325397;
    }
    .card-footer{
      width:100%;
      font-size: 10px;
      vertical-align: middle;
      color: #a2a19f;
      i-icon{
        display: inline-block;
        width:20px;
        text-align:center;
        margin-right:5px;
      }
      span{
        display: inline-block;
        vertical-align: middle;
      }
    }
    .item-bottom{
      height:28px;
      line-height:28px;
      padding: 0 18px;
      border-top: solid 1px #ebebeb;
      margin-top:10px;
      .btn-box{
        display: inline-block;
        width:33.33%;
        height:28px;
        line-height:28px;
      }
      span{
        display: inline-block;
        font-size: 9px;
        color: #323333;
        margin-left:6px;
        line-height: 20px;
        height: 100%;
        vertical-align: top;
        margin-top:2px;
      }
      .comment-btn{
        display: inline-block;
        width:15px;
        height:auto;
      }
      .more-btn{
        display: inline-block;
        width:12px;
        height:auto;
      }
      .redirect-btn{
        width:11px;
        height:auto;
      }
    }
    /*.publish-panel{
      position:fixed;
      bottom:40px;
      right:7px;
      width:100px;
      height:auto;
      font-size:14px;
      z-index:0;
    }
    .publish-box{
      position: absolute;
      bottom:0;
      width:100%;
      height:0;
      border-radius:3px;
      transform-origin: center bottom 0px;
      transition: height 0.4s, opacity 0.2s;
      overflow:hidden;
      border:0.5px dashed #ffcc00;
      opacity:0;
      background:#fff;
    }
    .publish-box.show{
      bottom:0;
      height:100px;
      opacity:1;
    }
    .publish-box p{
      text-align:center;
      line-height:38px;
      margin: 0 10px;
    }
    .publish-box p:first-child{
      border-bottom:0.5px solid #f2f2f2;
    }*/
    .publish-btn{
      display:block;
      position:fixed;
      width:50px;
      height:50px;
      right:15px;
      bottom:20px;
      z-index: 10;
    }
    .system-init{
      position:relative;
      margin: 0 13px 0 49px;
      padding: 80px 8px 120px 18px;
      font-size: 12px;
      color: #666666;
      border-left: 1px solid #d3d3d3;
      top: -20rpx;
      .create-time{
        top: 83px;
      }
      .point{
        top:85px;
      }
      .time{
        margin-top:11px;
        font-size: 11px;
        color: #999999;
      }
    }
  }
  .i-as{
    position:fixed;
    width:100%;
    box-sizing:border-box;
    left:0;
    right:0;
    bottom:0;
    background:#f7f7f7;
    transform:translate3d(0,100%,0);
    transform-origin:center;
    transition:all .4s ease-in-out;
    z-index:900;visibility:hidden
  }
  .i-as-show{
    transform:translate3d(0,0,0);
    visibility:visible
  }
  .i-as-mask{
    position:fixed;
    top:0;
    left:0;
    right:0;
    bottom:0;
    background:rgba(0,0,0,.7);
    z-index:900;
    transition:all .4s ease-in-out;
    opacity:0;
    visibility:hidden
  }
  .i-as-mask-show{
    opacity:1;
    visibility:visible
  }
  @keyframes btn-spin{
    0%{transform:rotate(0)}
    100%{transform:rotate(360deg)}
  }
</style>
<style lang="scss">
  .time-panel{
    .card-img{
    width: 100%;
    height:auto;
    margin:10px auto 5px;
  }
    .card-img .col-class{
    padding-right:5px;
  }
    .card-img img{
    max-width:100%;
    object-fit: cover;
    height:93px;
    border-radius:9px;
  }
  }
</style>
