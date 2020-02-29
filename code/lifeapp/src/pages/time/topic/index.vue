<template>
  <div class="container publish-topic">
    <div class="form-panel">
      <textarea class="form-content" v-model="content" mode="wrapped" placeholder="请输入内容" maxlength="255"></textarea>
      <p class="warn-info">
        <span class="tl">说明：留言参与</span>
        <span class="tr">{{content.length}}/255</span>
      </p>
      <div class="form-item clearfix" @click="showDialog=true">
        <div class="left-box">
          <img class="label-img" src="../../../../static/images/title.png" mode="widthFix"/>
          <span>话题标签</span>
        </div>
        <div v-if="title.length" class="right-box text-ellipsis">#{{ title }}#</div>
        <div v-if="!title.length" class="right-box text-ellipsis">{{ title }}</div>
      </div>
      <div class="form-item clearfix" @click="selectMember">
        <div class="left-box text-ellipsis">
          <img class="label-img" src="../../../../static/images/person.png" mode="widthFix"/>
          <span>人员</span>
        </div>
        <div class="right-box text-ellipsis">{{ member }}</div>
      </div>
      <div class="form-item clearfix" @click="getLocation">
        <div class="left-box text-ellipsis">
          <img class="label-img" src="../../../../static/images/address.png" mode="widthFix"/>
          <span>我的位置</span>
        </div>
        <div class="right-box text-ellipsis blue-style">{{ label?label:'' }}</div>
      </div>
      <div class="form-item clearfix img-panel">
        <img class="label-img" src="../../../../static/images/img.png" mode="widthFix"/>
        <div class="right-img">
          <div v-for="(item, index) in images" :key="item">
            <img :src="item" mode="aspectFill"/>
            <a @click="delImg(index)">—</a>
          </div>
          <span v-if="images.length<3" class="img-btn" @click="selImg">+</span>
          <span class="right-txt">{{ images.length }}/3</span>
        </div>
      </div>
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
    <canvas :style="'width:'+cw+'px;height:'+ch+'px;'" style="position: absolute; z-index: -1; left: -10000px; top: -10000px;" canvas-id="firstCanvas"></canvas>
    <!--<img class="point-btn" src="../../../../static/images/point.png" mode="widthFix"/>-->
  </div>
</template>

<script>
  import bus from '../../../utils/bus'
  export default {
    name: 'Activity',
    data () {
      return {
        cw: 0,
        ch: 0,
        isCeng: false,
        showDialog: false,
        newTitle: '',
        id: '',
        content: '',
        title: '',
        longitude: '',
        latitude: '',
        label: '',
        participant: [],
        member: '',
        images: []
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
      this.id = ''
      this.content = ''
      this.title = ''
      this.longitude = ''
      this.latitude = ''
      this.label = ''
      this.participant = []
      this.member = ''
      this.images = []
      if (options.id) {
        this.getInfo(options.id)
        this.id = options.id
      }
    },
    onShow () {
      bus.$on('changeMember2', res => {
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
      // 删除图片
      delImg (index) {
        this.images.splice(index, 1)
      },
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
          that.content = res.data.time.content
          that.title = res.data.time.title
          that.longitude = res.data.time.longitude
          that.latitude = res.data.time.latitude
          that.label = res.data.time.label
          that.participant = res.data.userIds
          that.member = res.data.part.join('、')
          that.images = res.data.time.images
        })
      },
      // 选择人员
      selectMember () {
        wx.navigateTo({url: '/pages/user/addressBook/selectMember/main?type=2'})
      },
      // 选择图片
      selImg () {
        const that = this
        wx.chooseImage({
          count: parseInt(3 - that.images.length), // 默认9
          sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
          sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
          success: function (res) {
            // 返回选定照片的本地文件路径列表，tempFilePath可以作为img标签的src属性显示图片
            res.tempFilePaths.forEach((item, index) => {
              wx.uploadFile({
                url: that.$baseUrl + '/api/upload/images',
                filePath: item,
                name: 'images',
                header: {
                  'Content-Type': 'multipart/form-data',
                  'Authorization': wx.getStorageSync('token')
                },
                success: function (res1) {
                  that.$set(that.images, that.images.length, JSON.parse(res1.data).data.path)
                }
              })
            })
          }
        })
      },
      // 获取位置
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
        } else if (!that.images.length) {
          wx.showToast({
            title: '请选择图片',
            icon: 'none',
            duration: 2000
          })
        } else {
          if (that.id.length) { // 编辑
            that.$request(that.$baseUrl + '/api/subject/edit', 'POST', {
              id: that.id,
              content: that.content,
              title: that.title,
              longitude: that.longitude,
              latitude: that.latitude,
              label: that.label,
              participant: that.participant,
              images: that.images
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
              that.images = []
            })
          } else {
            that.$request(that.$baseUrl + '/api/subject/create', 'POST', {
              content: that.content,
              title: that.title,
              longitude: that.longitude,
              latitude: that.latitude,
              label: that.label,
              participant: that.participant,
              images: that.images
            }).then((res) => {
              wx.switchTab({url: '/pages/time/main'})
              that.Bus.$emit('change')
              that.content = ''
              that.title = ''
              that.longitude = ''
              that.latitude = ''
              that.label = ''
              that.participant = []
              that.member = ''
              that.images = []
            })
          }
        }
      }
    }
  }
</script>

<style scoped>
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
  .publish-topic{
    padding:14px 25px;
  }
  .form-panel{
    width:100%;
    max-height:calc(100vh - 100px);
    overflow:auto;
  }
  .publish-topic .form-content{
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
  img.label-img{
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
  .form-item.img-panel{
    position:relative;
    min-height:65px;
    border-bottom:0;
  }
  .img-panel .label-img{
    float:left;
  }
  .right-img{
    position:relative;
    float:left;
    width: calc(100% - 32px);
  }
  .right-img div{
    position:relative;
    display:inline-block;
    float:left;
    width: 70px;
    height: 70px;
    margin-right:5px;
    margin-bottom:5px;
    background-color: #ebebeb;
    border-radius: 3px;
  }
  .right-img a{
    position:absolute;
    right:2px;
    top:2px;
    display: inline-block;
    width:20px;
    height:20px;
    line-height:18px;
    border-radius:50%;
    text-align:center;
    color:#fff;
    background:#ed5a65;
    font-size:13px;
  }
  .right-img img{
    object-fit:cover;
    display:inline-block;
    width: 70px;
    height: 70px;
    background-color: #ebebeb;
    border-radius: 3px;
  }
  .right-img .img-btn{
    display: inline-block;
    float:left;
    width: 70px;
    height: 70px;
    margin-bottom:5px;
    font-size:28px;
    text-align:center;
    line-height:70px;
    border:0.5px solid #ccc;
    color:#ccc;
  }
  .right-txt{
    position:absolute;
    bottom:0;
    right:0;
    font-size: 10px;
    color: #999999;
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
