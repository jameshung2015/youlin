<template>
  <div class="container publish-activity">
    <div class="form-panel">
      <textarea class="form-content" v-model="content" mode="wrapped" placeholder="请输入内容" maxlength="255"></textarea>
      <p class="warn-info">
        <span class="tl">说明：留言参与</span>
        <span class="tr">{{content.length}}/255</span>
      </p>
      <div class="form-item clearfix" @click="showDialog=true">
        <div class="left-box">
          <img src="../../../../static/images/title.png" mode="widthFix"/>
          <span>活动标签</span>
        </div>
        <div v-if="title.length" class="right-box text-ellipsis">#{{ title }}#</div>
        <div v-if="!title.length" class="right-box text-ellipsis">{{ title }}</div>
      </div>
      <div class="form-item clearfix" @click="selectMember">
        <div class="left-box text-ellipsis">
          <img src="../../../../static/images/person.png" mode="widthFix"/>
          <span>人员</span>
        </div>
        <div class="right-box text-ellipsis">{{ member }}</div>
      </div>
      <div class="form-item clearfix" @click="getLocation">
        <div class="left-box text-ellipsis">
          <img class="join-btn" src="../../../../static/images/address.png" mode="widthFix"/>
          <span>活动位置</span>
        </div>
        <div class="right-box text-ellipsis blue-style">{{ label?label:'' }}</div>
      </div>
      <picker class="form-item clearfix" mode="multiSelector" @change="bindMultiPickerChange" :value="multiIndex" :range="newMultiArray">
        <div class="left-box">
          <img class="join-btn" src="../../../../static/images/time.png" mode="widthFix"/>
          <span>活动时间</span>
        </div>
        <div class="right-box blue-style text-ellipsis">{{ active_time }}</div>
      </picker>
    </div>
    <button class="public-btn" @click="toJoin">发布</button>
    <div v-if="showDialog" class="change-dialog">
      <input class="change-input" v-model="newTitle" mode="wrapped" placeholder="请输入" />
      <div class="dialog-btn">
        <button @click="saveChange">确定</button>
        <button @click="showDialog=false;newTitle=''">取消</button>
      </div>
    </div>
    <div v-if="isCeng" class="ceng">
      <button open-type="openSetting" @opensetting="openLocation">请开启地理服务</button>
    </div>
  </div>
</template>

<script>
  import bus from '../../../utils/bus'
  export default {
    name: 'Activity',
    data () {
      return {
        isCeng: false,
        newTitle: '',
        showDialog: false,
        id: '',
        multiArray: [],
        multiIndex: [0, 0, 0, 0, 0],
        content: '',
        title: '',
        active_time: '',
        longitude: '',
        latitude: '',
        label: '',
        participant: [],
        member: ''
      }
    },
    computed: {
      newMultiArray: () => {
        let array = []
        const date = new Date()
        const years = []
        const months = []
        const days = []
        const hours = []
        const minutes = []
        for (let i = date.getFullYear(); i <= date.getFullYear() + 10; i++) {
          years.push('' + i)
        }
        array.push(years)
        for (let i = 1; i <= 12; i++) {
          if (i < 10) {
            i = '0' + i
          }
          months.push('' + i)
        }
        array.push(months)
        for (let i = 1; i <= 31; i++) {
          if (i < 10) {
            i = '0' + i
          }
          days.push('' + i)
        }
        array.push(days)
        for (let i = 0; i < 24; i++) {
          if (i < 10) {
            i = '0' + i
          }
          hours.push('' + i)
        }
        array.push(hours)
        for (let i = 0; i < 60; i++) {
          if (i < 10) {
            i = '0' + i
          }
          minutes.push('' + i)
        }
        array.push(minutes)
        return array
      }
    },
    onShareAppMessage: function () {
      return {
        title: '优临',
        path: '/pages/time/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
    },
    onLoad (options) {
      this.content = ''
      this.title = ''
      this.longitude = ''
      this.latitude = ''
      this.label = ''
      this.participant = []
      this.member = ''
      this.active_time = ''
      if (options.id) {
        this.getInfo(options.id)
        this.id = options.id
      }
    },
    onShow () {
      if (!this.id.length) {
        const date = new Date()
        const month = date.getMonth()
        const day = date.getDate()
        const hours = date.getHours()
        const minutes = date.getMinutes()
        this.multiIndex = [0, month, day - 1, hours, minutes]
      }
      bus.$on('changeMember1', res => {
        this.participant = res.map((item) => {
          return item.id
        })
        const arr = res.map((item) => {
          return item.nickname
        })
        this.member = arr.join('、')
      })
    },
    methods: {
      openLocation (e) {
        this.isCeng = false
        if (!e.target.authSetting['scope.userLocation']) {
          wx.showToast({
            title: '未获取位置权限',
            icon: 'none',
            duration: 2000
          })
        }
      },
      // 保存标题
      saveChange () {
        this.title = this.newTitle
        this.showDialog = false
      },
      // 获取详情
      getInfo (id) {
        const that = this
        that.$request(that.$baseUrl + '/api/time/show', 'GET', {
          id: id
        }).then((res) => {
          const year = new Date().getFullYear()
          that.content = res.data.time.content
          that.title = res.data.time.title
          that.longitude = res.data.time.longitude
          that.latitude = res.data.time.latitude
          that.label = res.data.time.label
          that.participant = res.data.userIds
          that.member = res.data.part.join('、')
          that.active_time = res.data.time.active_time
          let arr = res.data.time.active_time
          const arr1 = arr.split(/-|\s|:/)
          that.$set(that.multiIndex, 0, (parseInt(arr1) - year))
          that.$set(that.multiIndex, 1, (parseInt(arr1[1]) - 1))
          that.$set(that.multiIndex, 2, (parseInt(arr1[2]) - 1))
          that.$set(that.multiIndex, 3, parseInt(arr1[3]))
          that.$set(that.multiIndex, 4, parseInt(arr1[4]))
        })
      },
      // 获取时间日期
      bindMultiPickerChange (e) {
        this.multiIndex = e.target.value
        const index = this.multiIndex
        const year = this.newMultiArray[0][index[0]]
        const month = this.newMultiArray[1][index[1]]
        const day = this.newMultiArray[2][index[2]]
        const hour = this.newMultiArray[3][index[3]]
        const minute = this.newMultiArray[4][index[4]]
        this.active_time = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':00'
      },
      // 选择人员
      selectMember () {
        wx.navigateTo({url: '/pages/user/addressBook/selectMember/main?type=1'})
      },
      // 选择位置
      getLocation () {
        const that = this
        // 获取用户当前设置，判断用户是否已经授权位置获取
        wx.getSetting({
          success (res) {
            if (res.authSetting['scope.userLocation']) { // 已授权
              wx.chooseLocation({
                success: function (res) {
                  that.label = res.name
                  that.latitude = res.latitude
                  that.longitude = res.longitude
                },
                complete (r) {
                }
              })
            } else {
              that.isCeng = true
            }
          }
        })
      },
      // 发布后跳转到我的时光
      toJoin () {
        const that = this
        if (!that.title.length) {
          wx.showToast({
            title: '请输入标签',
            icon: 'none',
            duration: 2000
          })
        } else if (!that.active_time.length) {
          wx.showToast({
            title: '请选择时间',
            icon: 'none',
            duration: 2000
          })
        } else {
          let subscribe = 0
          const newsId = [
            '6xYb9N0N06_Bjc7o9_21rd4FYe3HrnR3ufc_qSeDP28'
          ]
          wx.requestSubscribeMessage({
            tmplIds: newsId,
            success (res) {
              subscribe = 1
            },
            fail (res) {
            },
            complete (res) {
              if (that.id.length) { // 编辑
                that.$request(that.$baseUrl + '/api/active/edit', 'POST', {
                  id: that.id,
                  subscribe: subscribe,
                  content: that.content,
                  title: that.title,
                  active_time: that.active_time,
                  longitude: that.longitude,
                  latitude: that.latitude,
                  label: that.label,
                  participant: that.participant
                }).then((res1) => {
                  that.Bus.$emit('change')
                  wx.switchTab({url: '/pages/time/main'})
                  that.content = ''
                  that.title = ''
                  that.longitude = ''
                  that.latitude = ''
                  that.label = ''
                  that.participant = []
                  that.member = ''
                  that.active_time = ''
                })
              } else { // 新建
                that.$request(that.$baseUrl + '/api/active/create', 'POST', {
                  subscribe: subscribe,
                  content: that.content,
                  title: that.title,
                  active_time: that.active_time,
                  longitude: that.longitude,
                  latitude: that.latitude,
                  label: that.label,
                  participant: that.participant
                }).then((res1) => {
                  wx.switchTab({url: '/pages/time/main'})
                  that.Bus.$emit('change')
                  that.content = ''
                  that.title = ''
                  that.longitude = ''
                  that.latitude = ''
                  that.label = ''
                  that.participant = []
                  that.member = ''
                  that.active_time = ''
                })
              }
            }
          })
        }
      }
    }
  }
</script>

<style scoped>
  .form-panel{
    width:100%;
    max-height:calc(100vh - 100px);
    overflow:auto;
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
    font-size: 12px;
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
  .warn-info{
    width:100%;
    height:19px;
    line-height:19px;
    font-size: 12px;
    color: #959595;
    border-bottom: 0.5px solid #c9c9c9;
  }
  .warn-info span{
    display: inline-block;
    width:50%;
    float:left;
  }
  .publish-activity{
    padding:14px 25px;
  }
  .publish-activity .form-content{
    width:100%;
    height:160px;
    padding: 0;
    font-size: 14px;
    color: #101010;
  }
  .form-item{
    height:36px;
    padding: 8px 6px;
    line-height:36px;
    border-bottom: 0.5px solid #c9c9c9;
    font-size: 14px;
    letter-spacing: 1px;
    color: #333333;
    vertical-align: middle;
  }
  .form-item .left-box{
    width:95px;
    float:left;
  }
  .left-box img{
    display: inline-block;
    position:relative;
    top:-2px;
    width: 19px;
    height: 23px;
    margin-right:12px;
    vertical-align: middle;
  }
  .form-item .right-box{
    width:calc(100% - 110px);
    float:right;
    font-size: 14px;
    letter-spacing: 1px;
    color: #ff9c00;
    text-align: right;
  }
  .form-item .right-box.blue-style{
    font-size:14px;
    color: #400181;
  }
  .public-btn {
    position:absolute;
    bottom:30px;
    left:50%;
    margin-left: -81.5px;
    width: 163px;
    height: 35px;
    line-height: 35px;
    margin-top:17px;
    background-color: #400181;
    border-radius: 5px;
    font-size: 18px;
    color: #ffffff;
    z-index:100;
  }
  .point-btn{
    position:absolute;
    bottom:15px;
    left:50%;
    margin-left: -25px;
    width: 50px;
    height: 8px;
    z-index:100;
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
