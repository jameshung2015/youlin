<template>
  <div class="container address-book">
    <div class="address-content">
      <div class="address-item address-group">
        <p class="title">群组</p>
        <ul class="group-list">
          <li class="group-item" v-for="(item, index) in addressBook.family" :key="item.family_id+index+item.family_name">
            <div class="collapse-title clearfix">
              <span class="title-txt text-ellipsis"> {{ item.family_name }}</span>
              <i-icon class="collapse-btn" :class="activeList[index]?'active':''" type="enter" size="28" color="#80848f" @click="changeIndex(index)"/>
              <span v-if="!item.checked" class="check-radio" @click="changeCheck('family', index, -1)"></span>
              <i-icon v-if="item.checked" class="checked-radio" type="success_fill" size="26" color="#07c160" @click="changeCheck('family', index, -1)"></i-icon>
            </div>
            <ul v-if="item.family_member.length" class="family-list" :class="activeList[index]?'active':''">
              <li v-for="(val, ind) in item.family_member" :key="val.id + ind + val.user_id" class="clearfix">
                <span class="family-name">{{ val.nickname }}</span>
                <span class="family-remark">{{ val.remark_name }}</span>
                <span v-if="!val.checked" class="check-radio" @click="changeCheck('family', index, ind)"></span>
                <i-icon v-if="val.checked" class="checked-radio" type="success_fill" size="26" color="#07c160" @click="changeCheck('family', index, ind)"></i-icon>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="address-item address-friends">
        <p class="title">好友</p>
        <ul class="friend-list">
          <li v-for="(item, index) in addressBook.memberShip" :key="item.user_id+index+item.nickname">
            <div class="list-title">
              <span class="title-txt text-ellipsis"> {{ item.nickname }}</span>
              <span class="remark-txt text-ellipsis"> {{ item.remark_name }}</span>
              <span v-if="!item.checked" class="check-radio" @click="changeCheck('memberShip', index)"></span>
              <i-icon v-if="item.checked" class="checked-radio" type="success_fill" size="26" color="#07c160" @click="changeCheck('memberShip', index)"></i-icon>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="footer-btn clearfix">
      <button class="btn-item" @click="selectMember">选择</button>
    </div>
  </div>
</template>

<script>
  import bus from '../../../../utils/bus'
  export default {
    name: 'AddressBook',
    data: function () {
      return {
        type: 1,
        activeList: [],
        addressBook: {}
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
      this.type = options.type
      this.activeList = []
      this.getAddressBook()
    },
    methods: {
      // 选择
      selectMember () {
        let memberList = []
        this.addressBook.memberShip.forEach((item) => {
          if (item.checked) {
            memberList.push({
              id: item.user_id,
              nickname: item.nickname
            })
          }
        })
        this.addressBook.family.forEach((item) => {
          item.family_member.forEach((item) => {
            if (item.checked) {
              memberList.push({
                id: item.user_id,
                nickname: item.nickname
              })
            }
          })
        })
        if (this.type === '1') {
          bus.$emit('changeMember1', memberList)
        } else if (this.type === '2') {
          bus.$emit('changeMember2', memberList)
        } else if (this.type === '3') {
          bus.$emit('changeMember3', memberList)
        } else if (this.type === '4') {
          bus.$emit('changeMember4', memberList)
        }
        wx.navigateBack({
          delta: 1
        })
      },
      // changeIndex
      changeIndex (index) {
        this.activeList.forEach((item, ind) => {
          if (ind === index) {
            this.$set(this.activeList, ind, !this.activeList[ind])
          }
        })
      },
      // 点击
      changeCheck (type, index1, index2) {
        const that = this
        if (type === 'memberShip') { // 好友管理
          that.addressBook[type][index1].checked = !that.addressBook[type][index1].checked
          that.addressBook = Object.assign({}, that.addressBook, {a: new Date().getTime()})
        } else { // 家庭管理
          if (index2 === -1) { // 选择父项
            that.addressBook[type][index1].checked = !that.addressBook[type][index1].checked
            that.addressBook[type][index1].family_member.forEach((item, ind) => {
              item.checked = that.addressBook[type][index1].checked
            })
          } else { // 选择子项
            that.addressBook[type][index1].family_member[index2].checked = !that.addressBook[type][index1].family_member[index2].checked
            let num = 0
            that.addressBook[type][index1].family_member.forEach((item, ind) => {
              if (item.checked) {
                num++
              }
            })
            if (num === that.addressBook[type][index1].family_member.length) {
              that.addressBook[type][index1].checked = true
            } else {
              that.addressBook[type][index1].checked = false
            }
          }
          that.addressBook = Object.assign({}, that.addressBook, {a: new Date().getTime()})
        }
      },
      // 获取通讯录列表信息
      getAddressBook () {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        that.$request(that.$baseUrl + '/api/family', 'GET').then((res) => {
          const obj = res.data
          obj.memberShip.forEach((item, index) => {
            item.checked = false
          })
          obj.family.forEach((item, index) => {
            this.activeList[index] = false
            item.checked = false
            if (item.family_member) {
              item.family_member.forEach((item1, index1) => {
                item1.checked = false
              })
            }
          })
          that.$set(this.activeList, 0, true)
          that.addressBook = Object.assign(obj, {})
          wx.hideToast()
        })
      }
    }
  }
</script>

<style scoped>
  .address-book {
    position: relative;
    width: 100%;
    height: 100%;
  }

  .address-content {
    width: 100%;
    height: calc(100% - 110px);
    overflow:auto;
  }

  .address-item .title {
    width: 100%;
    height: 44px;
    line-height: 44px;
    font-size: 16px;
    color: #666666;
    text-align: center;
    border-bottom: 0.5px solid #ccc;
  }
  .group-list{
    padding:0 30px;
  }
  .group-list .collapse-title{
    padding: 15px 0;
    height:18px;
    line-height:18px;
  }
  .group-list .group-item{
    border-bottom:0.5px solid #ccc;
  }
  .group-list .group-item .title-txt{
    display: inline-block;
    width:100px;
    font-size: 14px;
    color: #010101;
  }
  .group-list .group-item .collapse-btn{
    position:relative;
    top:-3px;
    display: inline-block;
    padding: 0 4px;
    font-size:20px;
    float:right;
    transition: all 0.1s;
    height:18px;
    line-height:18px;
  }
  .group-list .group-item .collapse-btn.active{
    transform:rotate(90deg);
    top: 0;
  }
  .family-list{
    padding:0 10px;
    height:0;
    overflow:hidden;
    transition: all 0.5s;
  }
  .family-list.active{
    height:auto;
  }
  .family-list li{
    position:relative;
    padding: 7px 5px;
    height:20px;
    line-height:20px;
    font-size: 12px;
    color: #333;
    border-top:0.5px solid #ccc;
  }
  .family-list li .family-name{
    display: inline-block;
    width:60px;
    height: 19px;
    line-height:19px;
    margin-right:10px;
  }
  .family-list li .family-remark{
    display: inline-block;
    width:40px;
    height: 19px;
    line-height:19px;
    font-size: 11px;
    color: #999999;
  }
  .check-radio{
    display: inline-block;
    float:right;
    margin-right:10px;
    margin-top:2px;
    width: 17px;
    height: 17px;
    border: solid 0.5px #cccccc;
    border-radius:50%;
  }
  .checked-radio{
    position:relative;
    display: inline-block;
    top:-1px;
    float:right;
    margin-right:14px;
    width: 17px;
    height: 17px;
  }
  .family-list .check-radio{
    margin-top:0;
  }
  .family-list .checked-radio{
    top: -2px;
  }


  .friend-list{
    padding:0 30px;
  }
  .friend-list li{
    position:relative;
    padding: 13px 11px;
    font-size: 13px;
    color: #010101;
    border-bottom:0.5px solid #ccc;
  }
  .friend-list li .title-txt{
    display: inline-block;
    width:60px;
    height: 19px;
    line-height:19px;
    margin-right:10px;
  }
  .friend-list li .remark-txt{
    display: inline-block;
    width:50px;
    height: 19px;
    line-height:19px;
    font-size: 12px;
    color: #999999;
  }

  .footer-btn {
    position: fixed;
    bottom: 25px;
    width:100%;
    height: 35px;
  }

  .btn-item {
    width: 163px;
    height: 35px;
    line-height:35px;
    background-color: #400181;
    border-radius: 5px;
    font-size: 18px;
    letter-spacing: 0px;
    color: #ffffff;
    text-align:center;
    margin:0 auto;
  }
</style>
