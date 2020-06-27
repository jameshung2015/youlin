<template>
  <div class="container user-panel">
    <div class="top-panel">
      <img class='background-img' src="../../../static/images/user-bg.png" mode="aspectFit"/>
      <div class="user-info">
        <i-avatar i-class="user-avatar" :src="userInfo.avatarUrl" size="large" shape="circle"></i-avatar>
        <p class="user-name">{{  userInfo.nickName }}</p>
      </div>
    </div>
    <div class="box-panel"></div>
    <div class="main-panel">
      <i-cell-group>
        <i-cell i-class="link-cell" title="我的报名" is-link url="/pages/user/myActivity/main">
          <img class="cell-icon-img" src="../../../static/images/baoming.png" mode="widthFix" slot="icon"/>
        </i-cell>
        <i-cell i-class="link-cell" title="数据中心" is-link url="/pages/user/dataCenter/main">
          <img class="cell-icon-img" src="../../../static/images/shuju.png" mode="widthFix" slot="icon"/>
        </i-cell>
        <button class="concat-btn" open-type="contact" bindcontact="handleContact">
          <i-cell i-class="link-cell" title="联系客服" is-link>
            <img class="cell-icon-img" src="../../../static/images/kefu.png" mode="widthFix" slot="icon"/>
          </i-cell>
        </button>
        <i-cell i-class="link-cell" title="使用指南" is-link @click="toPage">
          <i-icon class="cell-icon-icon" type="feedback_fill" size="17" slot="icon" color="#2DCCA4"/>
        </i-cell>
      </i-cell-group>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'UserPanel',
    data () {
      return {
        url: '',
        userInfo: {
          headimgurl: '',
          nickName: '',
          mobile: ''
        }
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
      this.userInfo = wx.getStorageSync('userInfo')
    },
    onShow () {
      const that = this
      if (!wx.getStorageSync('login')) { // 未登录
        wx.navigateTo({url: '/pages/home/main'})
      }
      that.$request(that.$baseUrl + '/api/instructions', 'GET').then((res) => {
        that.url = res.data
      })
    },
    methods: {
      // 跳转到详情
      toPage () {
        wx.navigateTo({url: '/pages/user/empty/main?url=' + this.url})
      }
    }
  }
</script>

<style scoped lang="scss">
  .user-panel{
    position:relative;
    .top-panel{
      position:relative;
      width:100%;
      height:178px;
      .background-img{
        position:absolute;
        width:100%;
        height:100%;
        left:0;
        top:0;
      }
      .user-info{
        position:absolute;
        width:100%;
        text-align: center;
        bottom:0;
        .user-name{
          font-size:18px;
          font-family:PingFang SC;
          font-weight:400;
          color:#333333;
          line-height:38px;
        }
      }
    }
    .box-panel{
      width:100%;
      height:12px;
      background:#F0F0F0;
    }
    .main-panel{
      .cell-icon-img{
        margin-left:14px;
        margin-right:10px;
        width:15.5px;
        height:auto;
      }
      .cell-icon-icon{
        margin-left:12px;
        margin-right:6px;
        height:auto;
      }
      .concat-btn{
        border:none;
        padding:0;
        text-align: left;
        line-height:0;
        background:transparent;
      }
      .concat-btn::after{
        border:none;
      }
    }
  }
</style>
<style lang="scss">
  .user-panel{
    .user-avatar{
      width:75px;
      height:75px;
      border-radius:50%;
      border:3px solid #fff;
    }
    .link-cell{
      border-top:0;
      padding:13.5px 11.5px 13.5px 0;
      line-height:14.5px;
      vertical-align:middle;
      font-size:14px;
      color:#333333;
      .i-cell-ft::after{
        color:#2A2A2A;
      }
    }
  }
</style>
