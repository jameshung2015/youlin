<template>
  <div class="container user-panel">
    <div class="user-content">
      <div class="user-info">
        <i-row i-class="user-info">
          <i-col span="8">
            <img class="user-avatar" :src="userInfo.avatarUrl" mode="widthFix"/>
          </i-col>
          <i-col span="16">
            <p class="user-name">{{  userInfo.nickName }}</p>
            <p class="user-phone">{{ userInfo.mobile }}</p>
          </i-col>
        </i-row>
      </div>
      <ul class="content-panel">
        <li>
          <navigator url="./addressBook/main" hover-class="none">
            <img class="item-img" src="../../../static/images/connect.png" mode="widthFix"/>
            <span>通讯录</span>
          </navigator>
        </li>
        <li>
          <navigator url="./echarts/main" hover-class="none">
            <img class="item-img" src="../../../static/images/echart.png" mode="widthFix"/>
            <span>统计表</span>
          </navigator>
        </li>
        <li>
          <button class="concat-btn" open-type="contact" bindcontact="handleContact">
            <img class="item-img" src="../../../static/images/concat.png" mode="widthFix"/>
            <span>客服</span>
          </button>
          <!--<span>客服</span>-->
        </li>

      </ul>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'UserPanel',
    data () {
      return {
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
        path: '/pages/time/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
    },
    onLoad () {
      this.userInfo = wx.getStorageSync('userInfo')
    }
  }
</script>

<style scoped>
  .user-panel{
    position:relative;
  }
  .user-content{
    position:fixed;
    top:0;
    bottom:0;
    left:76px;
    border-left: dotted 4px #ffcc00;
  }
  .user-info{
    position:absolute;
    width:250px;
    height:72px;
    top:110px;
    left:-37px;
  }
  .user-avatar{
    width:72px;
    height:72px;
    border-radius:36px;
    border:1px solid #ffcc00;
  }
  .user-name{
    padding-left:10px;
    margin-top:15px;
    line-height:20px;
    font-size: 20px;
    color: #400181;
  }
  .user-phone{
    padding-left:10px;
    font-size: 12px;
    line-height:28px;
    color: #999999;
  }
  .content-panel{
    position: absolute;
    min-width:100px;
    top:50%;
    left:47px;
    transform:translateY(-50%);
  }
  .content-panel .item-img{
    display: inline-block;
    width:36px;
    height:auto;
    margin-right:10px;
    vertical-align: middle;
  }
  .content-panel .concat-btn{
    border:none;
    height:40px;
    padding:0;
    text-align: left;
    line-height:0;
    font-size:15px;
    background:transparent;
  }
  .content-panel .concat-btn::after{
    border:none;
  }
  .content-panel li{
    margin-bottom:27px;
  }
  .content-panel li span{
    display: inline-block;
    width:45px;
    font-size: 15px;
    color: #666666;
    vertical-align: middle;
  }
</style>
