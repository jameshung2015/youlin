<template>
  <div class="container home-panel">
    <div class="login-txt">
      欢迎回到优临公社！
      <p class="register-txt">
        功能说明：<br>
        用户可在留言版上对活动留言互动。 <br>
        用户可以发送邀请微信友人参与自己建立的活动。 <br>
        用户在参与活动上有问题，有客服可以提供解答。<br>
        为正常使用优临需要您的授权登录，如同意请点击如下按钮
      </p>
      <button open-type="getUserInfo" @getuserinfo="getVxUserInfo" @click="getUserInfo1" class="btn">立即登录</button>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'TimePanel',
    data () {
      return {
        activityId: '',
        id: '',
        userName: '',
        user: {},
        userInfo: {}
      }
    },
    onLoad: function (options) {
      if (options.user_id) {
        this.id = options.user_id
      }
      if (options.id) {
        this.activityId = options.id
        wx.setStorageSync('activityId', options.id)
      } else {
        wx.setStorageSync('activityId', '')
      }
    },
    methods: {
      loginOk (res) { // 登录成功后的信息处理
        const that = this
        wx.setStorageSync('userInfo', res.userInfo)
        wx.setStorageSync('login', true)
        that.user = res
        that.userInfo = res.userInfo
        that.userName = res.nickName
        wx.login({
          success (res1) {
            if (res1.code) {
              wx.setStorageSync('code', res1.code)
              that.$request(that.$baseUrl + '/small/login', 'GET', {
                code: wx.getStorageSync('code')
              }).then((res2) => {
                wx.setStorageSync('user_id', res2.data.user_id)
                that.$request(that.$baseUrl + '/api/user/info', 'POST', {
                  encryptedData: res.encryptedData,
                  iv: res.iv,
                  rawData: res.rawData
                }).then((res3) => {
                })
                wx.hideLoading()
                if (wx.getStorageSync('activityId') !== '') {
                  wx.navigateTo({url: '/pages/find/activityInner/main?id=' + wx.getStorageSync('activityId')})
                } else {
                  wx.switchTab({url: '/pages/time/main'})
                }
              })
            } else {
            }
          }
        })
      },
      isLogin () {
        const that = this
        // 获取用户当前设置，判断用户是否已经授权
        wx.getSetting({
          success (res) {
            if (res.authSetting['scope.userInfo']) { // 已授权
              wx.getUserInfo({ // 获取用户信息
                lang: 'zh_CN',
                success (response) {
                  wx.showLoading({
                    title: '登录中...',
                    mask: true,
                    icon: 'none'
                  })
                  that.loginOk(response)
                }
              })
            } else {
            }
          }
        })
      },
      getUserInfo1 () {
        // 判断小程序的API，回调，参数，组件等是否在当前版本可用。  为false 提醒用户升级微信版本
        if (wx.canIUse('button.open-type.getUserInfo')) {
          // 用户版本可用
        } else {
          console.log('请升级微信版本')
        }
      },
      getVxUserInfo (e) {
        if (e.target.userInfo) {
          this.userName = e.target.userInfo.nickName
          this.isLogin()
        } else {
          this.userName = ''
        }
      }
    }
  }
</script>

<style scoped lang="scss">
  .login-txt{
    font-size:20px;
    color:#ccc;
    padding-top:100px;
    text-align:center;
  }
  .register-txt{
    font-size:12px;
    margin:50px 50px 0;
    text-align:left;
    line-height:24px;
  }
  .btn{
    margin-top:40px;
    width:120px;
    background:#ffbd33;
    color:#fff;
    border:1px solid #fff;
    border-radius:2px;
    font-size:14px;
    &::after{
      border-radius:2px;
      border:none;
    }
  }
</style>
