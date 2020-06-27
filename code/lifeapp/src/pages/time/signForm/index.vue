<template>
    <div class="container sign-form">
      <p class="sign-title">姓名<span>（必填）</span></p>
      <input class="sign-input" v-model="name" mode="wrapped" maxlength="100"/>
      <p class="sign-title">手机<span>（必填）</span></p>
      <input class="sign-input"  v-model="mobile" type="number" mode="wrapped" maxlength="100" @blur="exam('mobile')"/>
      <p class="sign-title">邮箱</p>
      <input class="sign-input"  v-model="email" type="email" mode="wrapped" maxlength="100" @blur="exam('email')"/>
      <p class="sign-title">微信</p>
      <input class="sign-input"  v-model="we_chat" mode="wrapped" maxlength="100"/>
      <button class="save-btn" @click="saveForm">提交</button>
      <i-modal :visible="visible2" :actions="actions" @click="handleClose">
        <div class="success-box tc">
          <i-icon class="icon-span" type="success_fill" size="45" color="#2DCCA4"/>
          报名成功，在<span class="href-span" @click="toMyActivity">我的报名</span>可查看订单噢～
        </div>
      </i-modal>
    </div>
</template>

<script>
  export default {
    name: 'SignForm',
    data () {
      return {
        actions: [
          {
            name: '确定',
            color: '#666'
          }
        ],
        visible2: false,
        id: '',
        name: '',
        mobile: '',
        email: '',
        we_chat: ''
      }
    },
    onLoad: function (options) {
      this.id = options.id
    },
    onShow () {
      this.name = ''
      this.mobile = ''
      this.email = ''
      this.we_chat = ''
    },
    methods: {
      toMyActivity () {
        wx.redirectTo({
          url: '/pages/user/myActivity/main'
        })
      },
      exam (type) {
        const reg = /^1\d{10}$/
        const reg1 = /^([A-Za-z0-9_+-.])+@([A-Za-z0-9\-.])+\.([A-Za-z]{2,22})$/
        if (type === 'mobile') {
          const flag = reg.test(this.mobile)
          if (!flag) {
            wx.showToast({
              title: '请输入正确手机号',
              icon: 'none',
              duration: 2000
            })
            this.mobile = ''
          }
        } else {
          const flag = reg1.test(this.email)
          if (!flag) {
            wx.showToast({
              title: '请输入正确邮箱',
              icon: 'none',
              duration: 2000
            })
            this.email = ''
          }
        }
      },
      handleClose (detail) {
        this.visible2 = false
        wx.redirectTo({
          url: '/pages/find/activityInner/main?id=' + this.id
        })
      },
      saveForm () {
        const that = this
        if (!that.name.length) {
          wx.showToast({
            title: '请输入姓名',
            icon: 'none',
            duration: 20000
          })
        } else if (!that.mobile.length) {
          wx.showToast({
            title: '请输入手机号',
            icon: 'none',
            duration: 20000
          })
        } else {
          that.$request(that.$baseUrl + '/api/second/active/party', 'POST', {
            id: that.id,
            name: that.name,
            mobile: that.mobile,
            email: that.email,
            we_chat: that.we_chat
          }).then((res) => {
            that.visible2 = true
          })
        }
      }
    }
  }
</script>

<style scoped lang="scss">
  .sign-form{
    height:100%;
    padding:12px;
    .sign-title{
      height:42px;
      line-height:42px;
      font-size:15px;
      color:#050505;
      span{
        font-size:10px;
        color:#E81600;
      }
    }
    .sign-input{
      margin:0;
      padding: 7px 15px;
      font-size: 14px;
      color: #495060;
      background:#F0F0F0;
      border-radius:6.5px;
    }
    .save-btn {
      position:absolute;
      bottom:25px;
      left:50%;
      margin-left: -62px;
      width: 124px;
      height: 38px;
      line-height: 38px;
      background-color: #FFBD33;
      border-radius: 20px;
      font-size: 18px;
      color: #ffffff;
      z-index:100;
    }
    .success-box{
      font-size:13px;
      color:#848484;
      .icon-span{
        display: block;
        margin: 0 auto 10px;
      }
      .href-span{
        color:#F2A833;
      }
    }
  }
</style>

<style lang="scss">
  .sign-form{
    .i-btn-ghost{
      background:#FFBD33!important;
    }
  }
</style>
