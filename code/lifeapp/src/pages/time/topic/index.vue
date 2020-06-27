<template>
  <div class="container publish-topic">
    <div class="form-panel">
      <textarea class="form-content" v-model="content" mode="wrapped" placeholder="这一刻，想说点什么..." maxlength="1000"></textarea>
      <div class="form-item clearfix img-panel">
        <div class="right-img">
          <div class="img-box" v-for="(item, index) in images" :key="item">
            <img :src="item" mode="aspectFill" @click="coverIndex=index"/>
            <a @click="delImg(index)">—</a>
            <div v-if="coverIndex==index" class="mask tc">选封面</div>
          </div>
          <span v-if="images && images.length<4" class="img-btn" @click="selImg">
            <i-icon type="add" size="56" color="#EAEAEA"/>
          </span>
          <!--<span class="right-txt">{{ images.length }}/4</span>-->
        </div>
      </div>
      <div class="form-item clearfix">
        <p v-if="disabled" class="default-title"># {{title}} #</p>
        <template v-if="!disabled">
          <div class="left-box">
            <img class="label-img img-icon" src="../../../../static/images/circle-icon.png" mode="widthFix"/>
            <span>活动标签</span>
          </div>
          <div class="right-box">
            <input class="item-content tl" v-model="title" mode="wrapped" placeholder="请输入活动标签"/>
            <i-icon v-if="title && title.length" class="right-icon" type="delete_fill" size="18" color="#BFBFBF" @click="title=''"/>
          </div>
        </template>
      </div>
      <div class="form-item clearfix" @click="getLocation">
        <div class="left-box text-ellipsis">
          <i-icon class="label-img" type="coordinates_fill" size="20" color="#2DCCA4"/>
          <span>活动地点</span>
        </div>
        <div class="right-box">
          <span class="item-content tl text-ellipsis">{{ label ? label : '请选择活动地点' }}</span>
          <i-icon type="enter" size="20" color="#BFBFBF"/>
        </div>
      </div>
      <picker class="form-item clearfix" mode="multiSelector" @change="bindMultiPickerChange" :value="multiIndex" :range="newMultiArray">
        <div class="left-box">
          <i-icon class="label-img" type="time_fill" size="20" color="#2DCCA4"/>
          <span>活动时间</span>
        </div>
        <div class="right-box blue-style text-ellipsis">
          <span class="item-content tl text-ellipsis">{{ active_time ? active_time : '请选择活动时间' }}</span>
          <i-icon type="enter" size="20" color="#BFBFBF"/>
        </div>
      </picker>
      <div class="form-item clearfix" @click="selectMember">
        <div class="left-box text-ellipsis">
          <i-icon class="label-img" type="browse_fill" size="20" color="#2DCCA4"/>
          <span>谁可以看</span>
        </div>
        <div class="right-box text-ellipsis">
          <span class="item-content text-ellipsis tr">{{ open_type==0 ? '公开' : '私密' }}</span>
          <i-icon type="enter" size="20" color="#BFBFBF"/>
        </div>
      </div>
      <div class="form-item clearfix" @click="setDetail">
        <div class="left-box text-ellipsis">
          <i-icon class="label-img" type="document_fill" size="20" color="#2DCCA4"/>
          <span>活动详情</span>
        </div>
        <div class="right-box text-ellipsis">
          <span v-if="introduction.length || detail.length" class="item-content text-ellipsis tr detail-txt">已完善</span>
          <span v-else="introduction.length==0 && detail.length==0" class="item-content text-ellipsis tr">未填写</span>
          <i-icon type="enter" size="20" color="#BFBFBF"/>
        </div>
      </div>
    </div>
    <button class="public-btn" @click="toJoin">发布</button>
    <div v-if="isCeng" class="ceng">
      <button open-type="openSetting" @opensetting="openLocation">请开启地理服务</button>
    </div>
    <canvas :style="'width:'+cw+'px;height:'+ch+'px;'" style="position: absolute; z-index: -1; left: -10000px; top: -10000px;" canvas-id="firstCanvas"></canvas>
  </div>
</template>

<script>
  import bus from '../../../utils/bus'
  export default {
    name: 'Activity',
    data () {
      return {
        flag: true,
        disabled: false,
        multiArray: [],
        multiIndex: [0, 0, 0],
        cw: 0,
        ch: 0,
        isCeng: false,
        id: '',
        source: 0, // 如果是发现 家庭活动的，则填写活动id，否则写0
        content: '', // 内容
        title: '', // 标签
        longitude: '', // 经度
        latitude: '', // 纬度
        address: '',
        active_time: '', // 时间
        label: '', // 活动地点
        open_type: 0, // 是否公开
        coverIndex: 0, // 封面index
        images: [], // 图片
        introduction: '', // 活动介绍
        detail: '' // 活动内容
      }
    },
    computed: {
      newMultiArray: () => {
        let array = []
        const date = new Date()
        const years = []
        const months = []
        const days = []
        /* const hours = []
        const minutes = [] */
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
        /* for (let i = 0; i < 24; i++) {
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
        array.push(minutes) */
        return array
      }
    },
    onLoad (options) {
      if (options.title) { // 参与系统活动的
        this.disabled = true
        this.id = ''
        this.source = options.source
        this.content = ''
        this.title = options.title
        this.longitude = ''
        this.latitude = ''
        this.address = ''
        this.label = ''
        this.active_time = ''
        this.open_type = 0
        this.images = []
        this.coverIndex = 0
        this.introduction = '' // 活动介绍
        this.detail = '' // 活动内容
      } else if (options.id) { // 编辑活动
        this.disabled = false
        this.getInfo(options.id)
        this.id = options.id
      } else { // 发布活动
        this.disabled = false
        this.id = ''
        this.source = 0
        this.content = ''
        this.title = ''
        this.longitude = ''
        this.latitude = ''
        this.address = ''
        this.label = ''
        this.active_time = ''
        this.open_type = 0
        this.images = []
        this.coverIndex = 0
        this.introduction = '' // 活动介绍
        this.detail = '' // 活动内容
      }
    },
    onShow () {
      // 初始获取定位权限
      wx.authorize({
        scope: 'scope.userLocation',
        success: (res) => {
        }
      })
      // 谁可以看选择后触发
      bus.$on('changeSet', res => {
        this.open_type = res
      })
      // 活动详情填写后触发
      bus.$on('changeDetailSet', res => {
        this.introduction = res.introduction
        this.detail = res.detail
      })
    },
    methods: {
      // 获取时间日期
      bindMultiPickerChange (e) {
        this.multiIndex = e.target.value
        const index = this.multiIndex
        const year = this.newMultiArray[0][index[0]]
        const month = this.newMultiArray[1][index[1]]
        const day = this.newMultiArray[2][index[2]]
        /* const hour = this.newMultiArray[3][index[3]]
        const minute = this.newMultiArray[4][index[4]]
        const time = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':00' */
        const time = year + '-' + month + '-' + day
        const date1 = new Date(time)
        const date2 = new Date()
        if (date1 < date2) {
          wx.showToast({
            title: '不能选择当前时间之前的时间',
            icon: 'none',
            duration: 2000
          })
        } else {
          this.active_time = time
        }
      },
      // 删除图片
      delImg (index) {
        if (this.coverIndex === index) {
          this.coverIndex = 0
        }
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
      // 获取详情
      getInfo (id) {
        const that = this
        that.$request(that.$baseUrl + '/api/second/time/show', 'GET', {
          id: id
        }).then((res) => {
          that.id = id
          that.source = res.data[0].source
          that.content = res.data[0].active.content
          that.title = res.data[0].active.title
          that.longitude = res.data[0].active.longitude
          that.latitude = res.data[0].active.latitude
          that.address = res.data[0].address
          that.label = res.data[0].active.label
          that.active_time = res.data[0].active.active_time
          that.open_type = res.data[0].open_type
          that.introduction = res.data[0].active.introduction // 活动介绍
          that.detail = res.data[0].active.detail // 活动内容
          that.coverIndex = res.data[0].active.coverIndex
          that.images = []
          res.data[0].images.forEach((item) => {
            that.images.push(item.filepath)
          })
        })
      },
      // 选择谁可以看
      selectMember () {
        wx.navigateTo({url: '/pages/time/setMember/main?flag=' + this.open_type})
      },
      // 活动详情设置
      setDetail () {
        wx.navigateTo({url: '/pages/time/setDetail/main?introduction=' + this.introduction + '&detail=' + this.detail})
      },
      // 选择图片
      selImg () {
        const that = this
        wx.chooseImage({
          count: parseInt(4 - that.images.length), // 默认9
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
                  that.address = res.address
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
        /* else if (!that.images.length) {
          wx.showToast({
            title: '请选择图片',
            icon: 'none',
            duration: 2000
          })
        } */
        const that = this
        if (!that.title.length) {
          wx.showToast({
            title: '请输入标签',
            icon: 'none',
            duration: 2000
          })
        } else {
          if (that.flag) {
            that.flag = false
            setTimeout(() => {
              that.flag = true
            }, 10000)
            if (that.id.length) { // 编辑
              that.$request(that.$baseUrl + '/api/second/active/update', 'POST', {
                id: that.id,
                source: that.source,
                content: that.content,
                title: that.title,
                longitude: that.longitude,
                latitude: that.latitude,
                address: that.address,
                label: that.label,
                active_time: that.active_time,
                open_type: that.open_type,
                introduction: that.introduction,
                detail: that.detail,
                images: that.images,
                coverIndex: that.coverIndex
              }).then((res1) => {
                wx.switchTab({url: '/pages/time/main'})
                // that.Bus.$emit('change')
                that.source = 0
                that.content = ''
                that.title = ''
                that.longitude = ''
                that.latitude = ''
                that.address = ''
                that.label = ''
                that.active_time = ''
                that.open_type = 0
                that.introduction = '' // 活动介绍
                that.detail = '' // 活动内容
                that.images = []
                that.coverIndex = 0
              })
            } else {
              that.$request(that.$baseUrl + '/api/second/active/create', 'POST', {
                content: that.content,
                title: that.title,
                source: that.source,
                longitude: that.longitude,
                latitude: that.latitude,
                address: that.address,
                label: that.label,
                active_time: that.active_time,
                open_type: that.open_type,
                introduction: that.introduction,
                detail: that.detail,
                images: that.images,
                coverIndex: that.coverIndex
              }).then((res) => {
                wx.switchTab({url: '/pages/time/main'})
                that.Bus.$emit('add')
                that.content = ''
                that.source = 0
                that.title = ''
                that.longitude = ''
                that.latitude = ''
                that.address = ''
                that.label = ''
                that.active_time = ''
                that.open_type = 0
                that.introduction = '' // 活动介绍
                that.detail = '' // 活动内容
                that.images = []
                that.coverIndex = 0
              })
            }
          }
        }
      }
    }
  }
</script>

<style scoped lang="scss">
  .publish-topic{
    padding:16px;
    .form-panel{
      height:calc(100vh - 100px);
      overflow-y:auto;
      .form-content{
        width:100%;
        height:80px;
        padding: 0;
        font-size: 15px;
        color: #999;
      }
      .form-item{
        padding: 12px 0;
        font-size: 15px;
        letter-spacing: 1px;
        color: #676767;
        vertical-align: middle;
        .default-title{
          font-size:17px;
          color:#EF9200;
        }
        .left-box{
          width:95px;
          float:left;
          .label-img{
            display: inline-block;
            position:relative;
            top:-2px;
            width: 17px;
            height: 22px;
            margin-right:5px;
            vertical-align: middle;
            &.img-icon{
              width:16px;
              left:2px;
            }
          }
        }
        .right-box{
          width:calc(100% - 168px);
          float:right;
          .item-content{
            display: inline-block;
            width:calc(100% - 30px);
            font-size:12px;
            color:#7D7D7D;
            line-height:1;
            vertical-align: middle;
            &.detail-txt{
              color:#2DCCA4;
            }
          }
          .right-icon{
            display: inline-block;
          }
        }
        &.img-panel{
          position:relative;
          min-height:65px;
          padding-bottom:34px;
          border-bottom:1px solid #EAEAEA;
          .right-img{
            position:relative;
            float:left;
            width: 100%;
            .img-box{
              position:relative;
              display:inline-block;
              float:left;
              width:30%;
              background:#fff;
              border-radius:9px;
              margin-right:3%;
              margin-bottom:10px;
              overflow:hidden;
              a{
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
              img{
                object-fit:cover;
                display:inline-block;
                width:100%;
                height:103px;
                background:#fff;
                border-radius:9px;
              }
              .mask{
                position:absolute;
                bottom:0;
                left:0;
                width:100%;
                height:17px;
                line-height:17px;
                font-size:10px;
                background:rgba(0,0,0,0.64);
                color:#fff;
              }
            }
            .img-btn{
              display: inline-block;
              float:left;
              margin-bottom:5px;
              width:103px;
              height:103px;
              line-height:103px;
              background:#fff;
              border:1px dashed #EAEAEA;
              border-radius:9px;
              font-size:28px;
              text-align:center;
              color:#EAEAEA;
            }
          }
        }
      }
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

    .right-txt{
      position:absolute;
      bottom:0;
      right:0;
      font-size: 10px;
      color: #999999;
    }
    .public-btn {
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
  }
</style>
