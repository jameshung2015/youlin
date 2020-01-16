<template>
  <div class="container join-panel">
    <div class="join-topic-content">
      <div class="text-ellipsis content-panel">
        <div class="title">#{{ activityData.title }}#</div>
        <div class="content">{{ activityData.content }}</div>
        <img class="img" :src="activityData.image" mode="widthFix"/>
      </div>
      <div>
        <span class="join-input" placeholder="参与人员"> {{ member }} </span>
        <img class="join-btn" src="../../../../static/images/person.png" mode="widthFix" @click="selectMember"/>
      </div>
      <div>
        <span class="join-input" placeholder="我的位置">{{ activityObj.label }}</span>
        <img class="join-btn" src="../../../../static/images/address.png" mode="widthFix" @click="getLocation"/>
      </div>
      <div>
        <textarea class="join-content" v-model="activityObj.content" mode="wrapped" placeholder="我的看法"></textarea>
      </div>
    </div>
    <button class="public-btn" @click="toJoin">发布</button>
  </div>
</template>

<script>
  import bus from '../../../utils/bus'
  export default {
    name: '',
    data () {
      return {
        member: '',
        activityObj: {
          participant: [],
          label: '',
          latitude: '',
          longitude: '',
          content: '',
          subject: '',
          images: []
        },
        activityData: {
          content: '',
          image: '',
          title: ''
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
    onLoad: function (options) {
      this.$set(this.activityData, 'image', unescape(options.image))
      this.$set(this.activityData, 'title', options.title)
      this.$set(this.activityData, 'content', options.content)
      this.$set(this.activityObj, 'subject', options.id)
      wx.setNavigationBarTitle({
        title: '参与话题'
      })
    },
    onShow () {
      bus.$on('changeMember4', res => {
        this.activityObj.participant = res.map((item) => {
          return item.id
        })
        const arr = res.map((item) => {
          return item.nickname
        })
        this.member = arr.join('、')
      })
    },
    methods: {
      // 选择人员
      selectMember () {
        wx.navigateTo({url: '/pages/user/addressBook/selectMember/main?type=4'})
      },
      // 选择位置
      getLocation () {
        const that = this
        wx.chooseLocation({
          success: function (res) {
            /* const name = res.name
            const address = res.address
            const latitude = res.latitude
            const longitude = res.longitude */
            // that.activityObj = res
            that.$set(that.activityObj, 'latitude', res.latitude)
            that.$set(that.activityObj, 'longitude', res.longitude)
            that.$set(that.activityObj, 'label', res.name)
          },
          complete (r) {
          }
        })
      },
      // 发布后跳转到我的时光
      toJoin () {
        const that = this
        if (!that.activityObj.participant.length) {
          wx.showToast({
            title: '请选择人员',
            icon: 'none',
            duration: 2000
          })
        } else if (!that.activityObj.label.length) {
          wx.showToast({
            title: '请选择位置',
            icon: 'none',
            duration: 2000
          })
        } else {
          that.$request(that.$baseUrl + '/api/system/subject/party', 'POST', that.activityObj).then((res) => {
            wx.switchTab({url: '/pages/time/main'})
            that.Bus.$emit('change1')
            that.activityObj.comment = ''
            that.activityObj.subject = ''
            that.activityObj.longitude = ''
            that.activityObj.latitude = ''
            that.activityObj.label = ''
            that.activityObj.participant = []
            that.activityObj.images = []
            that.member = ''
          })
        }
      }
    }
  }
</script>

<style scoped>
  .join-panel {
    box-sizing: content-box;
    padding: 0 25px;
  }
  .join-topic-content{
    height: calc(100vh - 100px);
    padding: 8px;
    line-height:12px;
    background-color: #ffffff;
    box-shadow: 1px 2px 3px 0px rgba(3, 0, 0, 0.05);
    border-radius: 5px;
    border: solid 1px #400181;
    overflow: auto;
  }
  .join-topic-content .content-panel{
    margin-bottom:12px;
    padding: 11px 8px;
    background-color: #ffffff;
    border-radius: 5px;
    border: solid 1px #400181;
  }

  .content-panel .img {
    width:calc((100% - 10px) / 2);
    height: auto;
    border-radius: 5px;
  }
  .content-panel .img:first-child{
    margin-right:10px;
  }

  .content-panel .title {
    width: 100%;
    font-size: 15px;
    color: #ff9c00;
  }
  .content-panel .content{
    width:100%;
    overflow : hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    margin: 12px auto;
    font-size: 9px;
    color: #999999;
  }

  .join-input {
    display: inline-block;
    padding: 0 10px;
    margin-bottom: 5px;
    margin-right:5px;
    width: calc(100% - 50px);
    height: 30px;
    line-height: 30px;
    background-color: #fefefe;
    border-radius: 3px;
    border: solid 1px #ffcc00;
    font-size: 10px;
    color: #333333;
    text-align: left;
    vertical-align: middle;
  }

  .join-btn {
    width: 19px;
    height: 23px;
  }

  .join-content {
    height:100px;
    box-sizing: border-box;
    width: 100%;
    overflow:auto;
    padding: 7px 10px;
    background-color: #fefefe;
    border-radius: 3px;
    border: solid 1px #ffcc00;
    font-size: 10px;
    color: #333333;
  }

  .public-btn {
    width: 163px;
    height: 35px;
    line-height: 35px;
    margin-top:17px;
    background-color: #400181;
    border-radius: 5px;
    font-size: 18px;
    color: #ffffff;
  }
</style>
