<template>
  <div class="container join-panel">
    <div class="join-content">
      <img class="join-img" :src="activityData.image" mode="widthFix"/>
      <div class="text-ellipsis join-title">#{{ activityData.title }}#</div>
      <div>
        <span class="join-input" placeholder="活动人员"> {{ member }} </span>
        <img class="join-btn" src="../../../../static/images/person.png" mode="widthFix" @click="selectMember"/>
      </div>
      <div>
        <span class="join-input" placeholder="活动地点">{{ activityObj.label }}</span>
        <img class="join-btn" src="../../../../static/images/address.png" mode="widthFix" @click="getLocation"/>
      </div>
      <div>
        <span class="join-input" placeholder="活动时间">{{ activityObj.active_time }}</span>
        <picker mode="multiSelector" @change="bindMultiPickerChange" :value="multiIndex" :range="newMultiArray" style="display: inline-block;">
          <div class="index_picker">
            <img class="join-btn" src="../../../../static/images/time.png" mode="widthFix"/>
          </div>
        </picker>
      </div>
      <div>
        <textarea class="join-content-textarea" v-model="activityObj.comment" mode="wrapped" placeholder="活动心得"></textarea>
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
        multiArray: [],
        multiIndex: [0, 0, 0, 0, 0],
        activityObj: {
          active: '',
          participant: [],
          longitude: '',
          latitude: '',
          label: '',
          active_time: '',
          comment: ''
        },
        member: '',
        activityData: {
          image: '',
          title: ''
        }
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
    onLoad: function (options) {
      this.$set(this.activityData, 'image', unescape(options.image))
      this.$set(this.activityData, 'title', options.title)
      this.$set(this.activityObj, 'active', options.id)
      wx.setNavigationBarTitle({
        title: '参与活动'
      })
    },
    onShow () {
      const date = new Date()
      const month = date.getMonth()
      const day = date.getDate()
      const hours = date.getHours()
      const minutes = date.getMinutes()
      this.multiIndex = [0, month, day - 1, hours, minutes]
      bus.$on('changeMember3', res => {
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
      // 获取时间日期
      bindMultiPickerChange (e) {
        this.multiIndex = e.target.value
        const index = this.multiIndex
        const year = this.newMultiArray[0][index[0]]
        const month = this.newMultiArray[1][index[1]]
        const day = this.newMultiArray[2][index[2]]
        const hour = this.newMultiArray[3][index[3]]
        const minute = this.newMultiArray[4][index[4]]
        const time = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':00'
        const date1 = new Date(time)
        const date2 = new Date()
        if (date1 < date2) {
          wx.showToast({
            title: '不能选择当前时间之前的时间',
            icon: 'none',
            duration: 2000
          })
        } else {
          this.activityObj.active_time = time
        }
      },
      // 选择人员
      selectMember () {
        wx.navigateTo({url: '/pages/user/addressBook/selectMember/main?type=3'})
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
        } else if (!that.activityObj.active_time.length) {
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
              that.$set(that.activityObj, 'subscribe', subscribe)
              that.$request(that.$baseUrl + '/api/system/active/party', 'POST', that.activityObj).then((res) => {
                wx.switchTab({url: '/pages/time/main'})
                that.Bus.$emit('change1')
                that.activityObj.comment = ''
                that.activityObj.active = ''
                that.activityObj.longitude = ''
                that.activityObj.latitude = ''
                that.activityObj.label = ''
                that.activityObj.participant = []
                that.activityObj.active_time = ''
                that.member = ''
              })
            }
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

  .join-content {
    height: calc(100vh - 100px);
    padding: 8px;
    line-height:12px;
    background-color: #ffffff;
    box-shadow: 1px 2px 3px 0px rgba(3, 0, 0, 0.05);
    border-radius: 5px;
    border: solid 1px #ffcc00;
    overflow: auto;
  }
  .join-content-textarea{
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

  .join-img {
    width:100%;
    height:auto;
  }

  .join-title {
    width: 100%;
    font-size: 15px;
    line-height: 44px;
    color: #400181;
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
    border: solid 1px #999999;
    font-size: 10px;
    color: #ffcc00;
    text-align: left;
    vertical-align: middle;
  }

  .join-btn {
    width: 19px;
    height: 23px;
  }

  .join-content {
    box-sizing: border-box;
    width: 100%;
    min-height: 60px;
    padding: 7px 10px;
    background-color: #fefefe;
    border-radius: 3px;
    border: solid 1px #999999;
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
