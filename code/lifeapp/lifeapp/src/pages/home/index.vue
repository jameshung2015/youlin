<template>
  <div class="container home-panel">
    <div class="login-txt">欢迎回到优临公社！</div>
    <div v-if="isCeng" class="ceng" @touchmove.stop.prevent="touchmovehandle">
      <button v-if="!userName" @getuserinfo="getVxUserInfo" open-type="getUserInfo" class="btn">点击微信授权</button>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'TimePanel',
    data () {
      return {
        title: '',
        id: '',
        userName: '',
        user: {},
        userInfo: {},
        isCeng: false
      }
    },
    onLoad: function (options) {
      if (options.user_id) {
        this.id = options.user_id
      }
      if (options.title) {
        this.title = options.title
      }
    },
    onShow () {
      this.isLogin()
    },
    methods: {
      loginOk (res) { // 登录成功后的信息处理
        const that = this
        wx.setStorageSync('userInfo', res.userInfo)
        that.user = res
        that.userInfo = res.userInfo
        that.userName = res.nickName
        that.isCeng = false
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
                if (that.id.length) {
                  that.$request(that.$baseUrl + '/api/member/add', 'POST', {
                    user_id: that.id
                  }).then((res) => {
                  })
                }
                wx.hideLoading()
                wx.switchTab({url: '/pages/time/main?title=' + that.title})
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
              that.isCeng = true
            }
          }
        })
      },
      touchmovehandle () { // 解决vue蒙层滑动穿透问题
      },
      getVxUserInfo (e) {
        if (e.target.userInfo) {
          this.userName = e.target.userInfo.nickName
          this.isCeng = false
          this.isLogin()
        } else {
          this.userName = ''
          this.isCeng = true
        }
      }
    }
  }
</script>

<style scoped>
  .login-txt{
    font-size:20px;
    color:#ccc;
    line-height:400px;
    text-align:center;
  }
  .ceng {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    font-size:18px;
  }
</style>
