<template>
  <div class="container address-book">
    <div class="address-content">
      <div class="address-item address-group">
        <p class="title">群组</p>
        <ul class="group-list">
          <li class="group-item" v-for="(item, index) in addressBook.family" :key="item.family_id">
            <div class="collapse-title clearfix">
              <span class="title-txt text-ellipsis" @click="delFamily(item.family_id)"> {{ item.family_name }}</span>
              <i-icon class="collapse-btn" :class="activeList[index]?'active':''" type="enter" size="28" color="#80848f" @click="changeIndex(index)"/>
              <span v-if="!showEdit" class="change-txt" @click="changeVal('family', index, -1, item.family_id)">更改名称</span>
              <span v-if="showEdit" class="family-add" @click="addMember(index)">+</span>
            </div>
            <ul v-if="item.family_member.length" class="family-list" :class="activeList[index]?'active':''">
              <li v-for="(val, ind) in item.family_member" :key="val.user_id" class="clearfix">
                <span class="family-name text-ellipsis">{{ val.nickname }}</span>
                <span class="family-remark">{{ val.remark_name }}</span>
                <span v-if="!showEdit" class="change-remark" @click="changeVal('family', index, ind, val.user_id)">备注</span>
                <span v-if="showEdit" class="family-del" @click="delMember(index, val)">-</span>
              </li>
            </ul>
          </li>
        </ul>
        <div v-if="showEdit" class="add-group">
          <input class="add-input" v-model="newFamily" mode="wrapped" placeholder="群组名称" />
          <button class="add-btn" @click="addGroup">建立群组</button>
        </div>
      </div>
      <div class="address-item address-friends">
        <p class="title">好友</p>
        <ul class="friend-list">
          <li v-for="(item, index) in addressBook.memberShip" :key="item.user_id">
            <div class="list-title">
              <span class="title-txt text-ellipsis"> {{ item.nickname }}</span>
              <span class="remark-txt text-ellipsis"> {{ item.remark_name }}</span>
              <span v-if="!showEdit" class="change-remark" @click="changeVal('memberShip', index, -1, item.user_id)">备注</span>
              <span v-if="showEdit && !item.checked" class="check-radio" @click="changeCheck('memberShip', index)"></span>
              <i-icon v-if="showEdit && item.checked" class="checked-radio" type="success_fill" size="26" color="#07c160" @click="changeCheck('memberShip', index)"></i-icon>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="footer-btn clearfix">
      <button class="btn-item" :class="showEdit ? 'active': ''" data-name="优临" open-type="share" plain="true" @click="showEdit=false">
        <img src="../../../../static/images/add.png" mode="widthFix"/>
        <p style="line-height:0;">添加朋友</p>
      </button>
      <div class="btn-item" :class="showEdit ? 'active': ''" @click="updateAddressBook">
        <img src="../../../../static/images/update.png" mode="widthFix"/>
        <p>更新列表</p>
      </div>
      <div class="btn-item" @click="groupManagement">
        <img src="../../../../static/images/group.png" mode="widthFix"/>
        <p>群组管理</p>
      </div>
    </div>
    <div v-if="showDialog" class="change-dialog">
      <input class="change-input" v-model="newValue" mode="wrapped" placeholder="请输入" />
      <div class="dialog-btn">
        <button @click="saveChange">确定</button>
        <button @click="showDialog=false;newValue=''">取消</button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'AddressBook',
    data: function () {
      return {
        showEdit: false,
        changeArr: [],
        showDialog: false,
        newValue: '',
        activeList: [],
        newFamily: '',
        addressBook: {}
      }
    },
    onPullDownRefresh: function () {
      this.getAddressBook()
    },
    // 转发分享
    onShareAppMessage: function (options) {
      // 设置菜单中的转发按钮触发转发事件时的转发内容
      let shareObj = {
        title: '优临', // 默认是小程序的名称(可以写slogan等)
        path: '/pages/home/main?user_id=' + wx.getStorageSync('user_id'), // 默认是当前页面，必须是以‘/’开头的完整路径
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
          // 转发结束之后的回调（转发成不成功都会执行
          this.showEdit = false
          wx.startPullDownRefresh()
          this.getAddressBook()
        }
      }
      // 来自页面内的按钮的转发
      if (options.from === 'button') {
      }
      return shareObj
    },
    onLoad () {
      this.showEdit = false
      this.changeArr = []
      this.showDialog = false
      this.newValue = ''
      this.activeList = []
      this.newFamily = ''
      this.addressBook = {}
      this.getAddressBook()
    },
    methods: {
      // 删除群组
      delFamily (id) {
        const that = this
        if (this.showEdit) {
          wx.showModal({
            content: '确定删除此群组？',
            success (res) {
              if (res.confirm) {
                that.$request(that.$baseUrl + '/api/family/del', 'GET', {
                  family_id: id
                }).then((res) => {
                  that.getAddressBook()
                })
              } else if (res.cancel) {
              }
            }
          })
        }
      },
      // 更新列表
      updateAddressBook () {
        this.showEdit = false
        wx.startPullDownRefresh()
        this.getAddressBook()
      },
      // 点击修改
      changeVal (type, index1, index2, id) {
        this.showDialog = true
        this.changeArr = [type, index1, index2, id]
      },
      // 保存修改
      saveChange () {
        const that = this
        const type = that.changeArr[0]
        const index1 = that.changeArr[1]
        const index2 = that.changeArr[2]
        const id = that.changeArr[3]
        if (type === 'memberShip') { // 好友管理
          that.addressBook[type][index1].remark_name = that.newValue
          that.addressBook = Object.assign({}, that.addressBook, {a: new Date().getTime()})
          that.$request(that.$baseUrl + '/api/member/remark', 'POST', {
            user_id: id,
            remark: that.newValue
          }).then((res) => {
          })
        } else { // 家庭管理
          if (index2 === -1) { // 选择父项
            that.addressBook[type][index1].family_name = that.newValue
            that.$request(that.$baseUrl + '/api/family/rename', 'POST', {
              family_id: id,
              family_name: that.newValue
            }).then((res) => {
            })
          } else { // 选择子项
            that.addressBook[type][index1].family_member[index2].remark_name = that.newValue
            that.$request(that.$baseUrl + '/api/member/remark', 'POST', {
              user_id: id,
              remark: that.newValue
            }).then((res) => {
            })
          }
          that.addressBook = Object.assign({}, that.addressBook, {a: new Date().getTime()})
        }
        that.showDialog = false
        that.newValue = ''
      },
      // 添加群组
      addGroup () {
        const that = this
        that.$request(that.$baseUrl + '/api/family/add', 'POST', {
          'family_name': that.newFamily,
          'member': []
        }).then((res) => {
          that.getAddressBook()
          that.newFamily = ''
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
      changeCheck (type, index1) {
        const that = this
        that.addressBook[type][index1].checked = !that.addressBook[type][index1].checked
        that.addressBook = Object.assign({}, that.addressBook, {a: new Date().getTime()})
      },
      // 家庭点击添加
      addMember (index) {
        const that = this
        const member = []
        that.addressBook.memberShip.forEach((item) => {
          if (item.checked) {
            const flag = that.addressBook.family[index].family_member.findIndex((val) => {
              return val.user_id === item.user_id
            })
            if (flag === -1) {
              // that.addressBook.family[index].family_member.push(item)
              member.push(item.user_id)
            }
          }
        })
        if (member.length) {
          that.$request(that.$baseUrl + '/api/family/add/member', 'POST', {
            family_id: that.addressBook.family[index].family_id,
            member: member
          }).then((res) => {
            that.getAddressBook()
          })
        } else {
          wx.showToast({
            title: '请选择要新添加的成员',
            icon: 'none',
            duration: 2000
          })
        }
      },
      // 删除成员
      delMember (index, item) {
        const that = this
        that.$request(that.$baseUrl + '/api/family/sub', 'POST', {
          family_id: that.addressBook.family[index].family_id,
          member: item
        }).then((res) => {
          that.getAddressBook()
        })
      },
      // 群组管理
      groupManagement () {
        this.showEdit = true
      },
      // 获取通讯录列表信息
      getAddressBook () {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/family', 'GET').then((res) => {
          const obj = res.data
          obj.family.forEach((item, index) => {
            this.activeList[index] = false
          })
          that.$set(this.activeList, 0, true)
          that.addressBook = Object.assign(obj, {})
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      }
    }
  }
</script>

<style scoped>
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
    display: inline-block;
    float:right;
    margin-right:14px;
    width: 17px;
    height: 17px;
  }
  .change-dialog{
    position: fixed;
    top: 50%;
    left:50%;
    z-index:10000;
    width: 236px;
    height: 126px;
    margin-top: -68px;
    margin-left: -118px;
    background-color: rgba(0,0,0,0.7);
    border-radius: 5px;
    padding:28px 20px;
    box-sizing: border-box;
    animation:myShow 0.5s;
    border: 0.5px solid #ccc;
  }
  @keyframes myShow {
    from {opacity: 0;}
    to {opacity: 1;}
  }
  .change-input{
    height: 30px;
    line-height:30px;
    background-color: #ffffff;
    border-radius: 5px;
    border: solid 0.5px #ccc;
    font-size:12px;
    color: #999999;
    padding-left:10px;
  }
  .dialog-btn{
    width:100%;
    text-align:right;
  }
  .change-dialog button{
    display:inline-block;
    height: 30px;
    margin-top:16px;
    line-height:30px;
    background-color: #ffcc00;
    border-radius: 5px;
    font-size: 15px;
    color: #000000;
    text-align:center;
    margin-left:10px;
  }
  .add-group{
    margin:0 30px;
    padding: 13px 30px;
    border-bottom:0.5px solid #ccc;
  }
  .add-input{
    display: inline-block;
    width: calc(100% - 102px);
    height: 20px;
    line-height:20px;
    margin-right:16px;
    background-color: #ffffff;
    border-radius: 11px;
    border: solid 0.5px #400181;
    font-size: 11px;
    color: #999999;
    padding-left:10px;
  }
  .add-btn{
    display:inline-block;
    width: 75px;
    height: 20px;
    line-height:20px;
    background-color: #ffcc00;
    border-radius: 10px;
    font-size: 11px;
    color: #400181;
  }
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
    max-width:100px;
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
  .group-list .group-item .change-txt{
    display: inline-block;
    float:right;
    margin-right:10px;
    padding:0 9px;
    height:18px;
    line-height:18px;
    border-radius: 10px;
    border: solid 1px #400181;
    font-size: 11px;
    color: #400181;
    text-align:center;
  }
  .family-add{
    display: inline-block;
    float:right;
    margin-right:10px;
    width: 20px;
    height: 20px;
    line-height:18px;
    background-color: #22ac38;
    border-radius:50%;
    color:#fff;
    font-size:20px;
    text-align:center;
  }
  .family-del{
    display: inline-block;
    float:right;
    margin-right:10px;
    width: 20px;
    height: 20px;
    line-height:18px;
    background-color: #e60012;
    border-radius:50%;
    color:#fff;
    font-size:20px;
    text-align:center;
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
    vertical-align: middle;
  }
  .family-list li .family-name{
    display: inline-block;
    width:60px;
    height: 19px;
    line-height:19px;
    margin-right:10px;
    vertical-align: middle;
  }
  .family-list li .family-remark{
    display: inline-block;
    width:40px;
    height: 19px;
    line-height:19px;
    font-size: 11px;
    color: #999999;
    vertical-align: middle;
  }
  .family-list li .change-remark{
    display: inline-block;
    float:right;
    padding:0 9px;
    height:18px;
    line-height:18px;
    border-radius: 10px;
    border: solid 1px #400181;
    font-size: 11px;
    color: #400181;
    text-align:center;
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
  .friend-list li .change-remark{
    display: inline-block;
    float:right;
    padding:0 9px;
    height: 19px;
    line-height:19px;
    border-radius: 10px;
    border: solid 1px #400181;
    font-size: 11px;
    color: #400181;
    text-align:center;
  }
  .footer-btn {
    position: fixed;
    left: 50%;
    bottom: 19px;
    transform: translateX(-50%);
    width: 255px;
    height: 72px;
  }

  .btn-item {
    float: left;
    margin-right: 45px;
    width: 55px;
    height: 100%;
    text-align: center;
    font-size: 9px;
    color: #666666;
    border:none;
    padding:0;
  }
  .btn-item.active{
    opacity:0.3;
  }

  .btn-item:last-child {
    margin-right: 0;
  }

  .btn-item img {
    width: 100%;
    height: 55px;
  }

  .btn-item p {
    width: 100%;
    margin-top: 4px;
  }
</style>
