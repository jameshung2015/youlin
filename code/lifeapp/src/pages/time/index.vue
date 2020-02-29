<template>
  <div class="container time-panel">
    <span class="line"></span>
    <div class="search-panel">
      <i-panel class="search-panel">
        <img class="search-img" src="../../../static/images/search.png"/>
        <input class="search-input" v-model="searchVal" mode="wrapped" placeholder="在这里搜索内容" @focus="showMask=true;" @blur="doSearch"/>
      </i-panel>
    </div>
    <div class="main-panel">
      <ul class="search-result">
        <li v-for="(item, index) in timeList" :key="item.time_id" class="result-item">
          <span class="point"></span>
          <template v-if="item.type=='活动'">
            <!--活动格式-->
            <div class="card" @click="toPage(item)">
              <div class="card-title">
                #{{ item.title }}#
                <span v-if="item.master_type==1" class="author">自己</span>
                <span v-if="item.master_type==2" class="author">优临</span>
                <span v-if="item.master_type==3" class="author">{{item.master_name}}</span>
              </div>
              <div class="card-content">
                <div class="content-txt">
                  {{ item.data.content }}
                </div>
                <a class="more-content">全文</a>
              </div>
              <div class="card-footer">
                <p class="card-footer">时间:{{ item.active_time }}</p>
                <p v-if="item.data.label" class="card-footer">地点:{{ item.data.label }}</p>
              </div>
              <i-row v-if="item.data.images" i-class="card-img">
                <i-col v-for="(img, index1) in item.data.images" :key="img" span="8" i-class="col-class">
                  <img :src="img" mode="aspectFill"/>
                </i-col>
              </i-row>
            </div>
          </template>
          <template v-if="item.type=='话题'">
            <!--活动格式-->
            <div class="card" @click="toPage1(item)">
              <div class="card-title topic-title">
                <span class="title">#{{ item.title }}#</span>
                <span v-if="item.master_type==1" class="author">自己</span>
                <span v-if="item.master_type==2" class="author">优临</span>
                <span v-if="item.master_type==3" class="author">{{item.master_name}}</span>
              </div>
              <div class="card-content">
                <div class="content-txt">
                  {{ item.data.content }}
                </div>
                <a class="more-content">全文</a>
              </div>
              <div class="card-footer">
                <p class="card-footer">时间:{{ item.created_at }}</p>
                <p v-if="item.data.label" class="card-footer">地点:{{ item.data.label }}</p>
              </div>
              <i-row v-if="item.data.images" i-class="card-img">
                <i-col v-for="(img, index1) in item.data.images" :key="img" span="8" i-class="col-class">
                  <img :src="img" mode="aspectFill"/>
                </i-col>
              </i-row>
            </div>
          </template>
          <div class="item-bottom">
            <span>{{ item.created_at }}</span>
            <img class="more-btn" src="../../../static/images/more.png" mode="widthFix" @click="showCommit(index)"/>
            <button :data-name="item.title" open-type="share" plain="true">
              <img class="redirect-btn" src="../../../static/images/redirect.png" mode="widthFix"/>
            </button>
            <ul class="comment-list" :class="[showIndex[index]?'show-commit':'']">
              <li class="comment-item my-comment">
                <i-avatar i-class="avatar" :src="userInfo.avatarUrl" size="small"></i-avatar>
                <span class="name">{{ userInfo.nickName }}</span>
                <input class="comment-input" v-model="commentVal" mode="wrapped" placeholder="写下我想说的" @focus/>
                <span class="submit-btn" @click="doComment(item.time_id)">评论</span>
              </li>
              <li v-for="(com, index2) in item.comment" :key="com.id" class="comment-item">
                <i-avatar i-class="avatar" :src="com.headimgurl" size="small"></i-avatar>
                <span class="name">{{ com.nickname }}</span>
                <span class="comment-content">{{ com.content }}</span>
                <span class="comment-time text-ellipsis">{{ com.created_at }}</span>
                <span class="comment-line"></span>
              </li>
            </ul>
          </div>
        </li>
      </ul>
      <div class="system-init">
        <span class="point"></span>
        <!--活动格式-->
        <div class="content">
          开启我的家庭时光
          <span class="author">优临</span>
        </div>
        <p class="time">{{createTime}}</p>
      </div>
    </div>
    <div class="mask" :class="[showMask ? 'mask-show' : '']"></div>
    <div class="publish-panel">
      <div class="publish-box" :class="[showPublish?'show':'']">
        <p @click="toActivity">我的活动</p>
        <p @click="toTopic">我的话题</p>
      </div>
      <img class="publish-btn" src="../../../static/images/publish.png" @click="doPublish"/>
    </div>
    <!--<web-view v-if="url" :src="url"></web-view>-->
  </div>
</template>

<script>
  export default {
    name: 'TimePanel',
    data () {
      return {
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
    // 转发分享
    onShareAppMessage: function (options) {
      // const that = this
      // 设置菜单中的转发按钮触发转发事件时的转发内容
      let shareObj = {
        title: '优临', // 默认是小程序的名称(可以写slogan等)
        path: '/pages/time/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg', // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
        success: function (res) {
          // 转发成功之后的回调
          if (res.errMsg === 'shareAppMessage:ok') {
          }
        },
        fail: function (res) {
          // 转发失败之后的回调
          if (res.errMsg === 'shareAppMessage:fail cancel') {
            // 用户取消转发
          } else if (res.errMsg === 'shareAppMessage:fail') {
            // 转发失败，其中 detail message 为详细失败信息
          }
        },
        complete: function () {
          // 转发结束之后的回调（转发成不成功都会执行）
        }
      }
      // 来自页面内的按钮的转发
      if (options.from === 'button') {
        const eData = options.target.dataset
        // 此处可以修改 shareObj 中的内容
        shareObj.title = eData.name
        // shareObj.path = eData.path
        shareObj.path = '/pages/time/main?title=' + eData.name
      }
      // 返回shareObj
      return shareObj
    },
    onLoad (options) {
      this.Bus.$on('change', () => {
        this.getList()
      })
      this.Bus.$on('change1', () => {
        this.getList()
      })
      if (options.title) {
        this.searchVal = options.title
      } else {
        this.searchVal = ''
      }
      this.getList()
    },
    onShow () {
      if (!wx.getStorageSync('login')) { // 未登录
        wx.navigateTo({url: '/pages/home/main'})
      }
      this.showMask = false
      this.showPublish = false
      this.url = ''
      this.commentVal = ''
      this.getList()
      /* this.searchVal = ''
      this.showIndex = []
      this.timeList = []
      this.getList() */
    },
    onPullDownRefresh: function () {
      this.getList()
    },
    methods: {
      // 获取列表
      getList () {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/time/log', 'GET', {
          searchVal: that.searchVal
        }).then((res) => {
          res.data.data.forEach((item, index) => {
            that.showIndex[index] = false
          })
          that.createTime = res.data.created
          that.timeList = res.data.data
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      },
      // 活动跳转
      toActivity () {
        wx.navigateTo({url: '/pages/time/activity/main'})
      },
      // 活动跳转
      toTopic () {
        wx.navigateTo({url: '/pages/time/topic/main'})
      },
      // 跳转到活动
      toPage (item) {
        const that = this
        if (item.master_type !== 1) { // 系统或其他人
          that.$request(that.$baseUrl + '/api/system/active/click', 'GET', {
            id: item.source
          }).then((res) => {
            wx.navigateTo({url: '/pages/find/activityInner/main?image=' + escape(item.data.images ? item.data.images.join(',') : '') + '&title=' + item.title + '&id=' + item.source + '&content=' + item.data.content + '&type=1' + '&authority=' + item.authority})
          })
        } else if (item.master_type === 1) { // 自己
          wx.navigateTo({url: '/pages/time/activity/main?id=' + item.time_id})
        }
      },
      // 跳转到话题
      toPage1 (item) {
        const that = this
        if (item.master_type === 2) { // 系统
          that.$request(that.$baseUrl + '/api/system/subject/show', 'GET', {
            subjectId: item.source
          }).then((res) => {
            that.url = res.data.url
            wx.navigateTo({url: '/pages/time/empty/main?url=' + res.data.url})
          })
        } else if (item.master_type === 1) { // 自己
          wx.navigateTo({url: '/pages/time/topic/main?id=' + item.time_id})
        } else if (item.master_type === 3) { // 其他人
          wx.navigateTo({url: '/pages/find/activityInner/main?image=' + escape(item.data.images ? item.data.images.join(',') : '') + '&title=' + item.title + '&id=' + item.source + '&content=' + item.data.content + '&type=1' + '&authority=' + item.authority})
        }
      },
      // 搜索
      doSearch () {
        const that = this
        that.showMask = false
        that.getList()
      },
      // 下拉显示评论
      showCommit (index) {
        const that = this
        that.commentVal = ''
        this.showIndex.forEach((item, index1) => {
          if (index === index1) {
            that.$set(that.showIndex, index1, !item)
          } else {
            that.$set(that.showIndex, index1, false)
          }
        })
      },
      // 提交评论
      doComment (id) {
        const that = this
        that.$request(that.$baseUrl + '/api/time/comment', 'POST', {
          time_id: id,
          content: that.commentVal
        }).then((res) => {
          that.commentVal = ''
          that.getList()
        })
      },
      // 点击发布
      doPublish () {
        this.showPublish = !this.showPublish
      }
    }
  }
</script>

<style scoped>
  .main-panel{
    height: calc(100vh - 35px);
    overflow: auto;
  }
  button[plain]{
    display: inline-block;
    width:22px;
    float: right;
    margin-right:15px;
    border:0;
    padding:0;
    height:auto;
    line-height:0;
    border-radius:0;
  }
  .time-panel{
    padding:0 24px;
  }
  .line{
    display:inline-block;
    position:fixed;
    top:50px;
    bottom:0;
    left:48.8px;
    width:1px;
    border-left:0.5px solid #ccc;
  }
  .time-panel .search-panel{
    position:relative;
  }
  .time-panel .search-panel .search-img{
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
    top:30px;
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
  .search-result{
    position:relative;
    margin: 20px 13px 0 25px;
    border-left:0.5px solid #333;
  }
  .result-item{
    position: relative;
    margin-bottom:8px;
  }
  .point{
    position: absolute;
    width:6px;
    height:6px;
    border-radius:50%;
    background:#803ac9;
    left:-3.2px;
    top:0px;
  }
  .result-item .card{
    height:auto;
    margin-left:8px;
    box-shadow: 2px 2px 3px 0px rgba(3,0,0,0.05);
    border-radius:5px;
    padding:8px;
    box-sizing:content-box;
  }
  .card .card-title{
    position:relative;
    font-size:14px;
    letter-spacing: 1px;
    color:#ff9c00;
    padding: 8px 0;
    line-height:14px;
  }
  .card .topic-title .title{
    display: inline-block;
    vertical-align: top;
    line-height:14px;
    color:#400181;
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
  span.author{
    float:right;
    display: inline-block;
    line-height:14px;
    font-size:11px;
    color:#999;
  }
  .card .card-content{
    width:80%;
    font-size:14px;
    color:#333;
    padding: 0 0 8px;
  }
  .card-content .content-txt{
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
  }
  .card-content .more-content{
    font-size:13px;
    color:#6294c1;
    text-decoration:underline;
  }
  .card-footer{
    width:80%;
    font-size:12px;
    line-height:13px;
    letter-spacing: 1px;
    color:#6294c1;
  }
  .item-bottom{
    font-size:11px;
    color:#999;
    padding: 7px 7px 7px 14px;
    line-height:13px;
  }
  .item-bottom .more-btn{
    float:right;
    width:22px;
    height:auto;
  }
  .item-bottom .redirect-btn{
    width:22px;
    height:auto;
  }
  .publish-panel{
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
  }
  .publish-btn{
    display:block;
    position:absolute;
    width:40px;
    height:40px;
    left:30px;
    bottom:-21px;
    margin: 0 auto;
  }
  .system-init{
    position:relative;
    margin: 0 13px 0 25px;
    min-height:100px;
    font-size: 14px;
    color:#333;
    padding: 0 8px 0 18px;
  }
  .system-init .time{
    margin-top:11px;
    font-size: 11px;
    color: #999999;
  }
  .comment-list{
    margin: 10px 0;
    width:100%;
    max-height: 0;
    background-color: #f8f8f8;
    border-radius: 5px;
    opacity:0;
    transform-origin: center top 0px;
    transition: all 0.6s;
    overflow:hidden;
  }
  .comment-list.show-commit{
    max-height: 400px;
    opacity:1;
    overflow:auto;
  }
  .comment-list .comment-item{
    padding: 6px 10px 6px;
    overflow: auto;
  }
  .comment-item{
    position:relative;
  }
  .my-comment{
    position: relative;
    border-bottom:0.5px solid #e0e0e0;
  }
  .comment-item .name{
    position: absolute;
    left:41px;
    top:6px;
    font-size: 11px;
    color: #666666;
  }
  .comment-item .comment-content{
    display: inline-block;
    width:calc(100% - 34px);
    margin-left:10px;
    margin-top:18px;
    font-size: 12px;
    letter-spacing: 1px;
    color: #333333;
    overflow-wrap:break-word;
  }
  .comment-item .comment-time{
    display: block;
    margin-bottom:4px;
    width:100%;
    font-size: 10px;
    color: #999999;
    text-align:right;
  }
  .comment-item .comment-line{
    display: block;
    width: calc(100% - 30px);
    height:1px;
    border-top:0.5px solid #e0e0e0;
    margin: 0 0 0 30px;
  }
  .my-comment .comment-input{
    position:relative;
    top: 4px;
    display:inline-block;
    width:calc(100% - 90px);
    height:19px;
    min-height:19px;
    line-height:19px;
    margin-left:10px;
    margin-top:15px;
    padding-left:8px;
    border-bottom:1px solid #999999;
    font-size: 12px;
    color: #c5c5c5;
  }
  .my-comment .submit-btn{
    display: inline-block;
    width: 36px;
    height: 18px;
    line-height:18px;
    margin-left:9px;
    background-color: #ffffff;
    border-radius: 8px;
    border: solid 1px #e9e9e9;
    font-size: 11px;
    letter-spacing: 1px;
    color: #400181;
    text-align:center;
  }

</style>
<style>
  .time-panel .search-input{
    height:30px;
    line-height:30px;
    border-bottom:0.5px solid #ffcc00;
    font-size:12px;
    color:#999;
    padding-left:58px;
  }
  .card-img{
    width: 80%;
    height:auto;
    margin-top:10px;
  }
  .card-img .col-class{
    padding-right:5px;
  }
  .card-img img{
    max-width:100%;
    object-fit: cover;
    height:60px;
  }
  .comment-item .avatar{
    position:relative;
    top:-10px;
  }
</style>
