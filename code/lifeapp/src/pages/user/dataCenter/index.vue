<template>
  <div class="container data-center">
    <ul class="data-list">
      <!--<li class="data-item">
        <div class="data-title">
          <img class="title-img" src="../../../../static/images/remen.png" mode="widthFix"/>
          热门活动<span class="des-txt">（根据参与热度）</span>
        </div>
        <div class="data-content">
          <i-row>
            <i-col span="8" i-class="col-class">
              <view class='canvasBox'>
                <view class='bigCircle'></view>
                <view class='littleCircle tc'>{{centerData.hot_total}}</view>
                <canvas canvas-id="runCanvas1" id="runCanvas1" class='canvas'></canvas>
              </view>
              <p class="data-txt tc">参与热门活动</p>
              <p class="data-count tc">（{{centerData.hot_part}}次）</p>
            </i-col>
          </i-row>
        </div>
      </li>
      <li class="data-item">
        <div class="data-title">
          <img class="title-img" src="../../../../static/images/remen.png" mode="widthFix"/>
          我的活动热度
          <span class="des-txt">（报名人数）</span>
        </div>
        <div class="data-content">
          <i-row>
            <i-col span="8" i-class="col-class">
              <view class='canvasBox'>
                <view class='bigCircle'></view>
                <view class='littleCircle tc'>{{centerData.part_person}}</view>
                <canvas canvas-id="runCanvas2" id="runCanvas2" class='canvas'></canvas>
              </view>
              <p class="data-txt tc">报名总人数</p>
              <p class="data-count tc">（{{centerData.self_party}}个）</p>
            </i-col>
          </i-row>
        </div>
      </li>
      <li class="data-item">
        <div class="data-title">
          <img class="title-img" src="../../../../static/images/wz.png" mode="widthFix"/>
          好文分享排名前三名
          <span class="des-txt">（根据点击阅读）</span>
        </div>
        <div class="data-content">
          <i-row>
            <i-col v-for="(item, index) in centerData.click" :key="index" span="8" i-class="col-class" style="text-align: center;" @click="toDetail(item.id)">
              <i-avatar i-class="user-avatar" :src="item.head" size="large" shape="circle"></i-avatar>
              <p class="data-txt tc">{{item.nickname}}</p>
              <p class="data-count tc">（分享次数：{{item.amount}}次）</p>
            </i-col>
          </i-row>
        </div>
      </li>
      <li class="data-item">
        <div class="data-title">
          <img class="title-img" src="../../../../static/images/wz.png" mode="widthFix"/>
          用心生活排名
          <span class="des-txt">（根据发布次数）</span>
        </div>
        <div class="data-content">
          <i-row>
            <i-col v-for="(item, index) in centerData.live" :key="index" span="8" i-class="col-class" style="text-align: center;" @click="toDetail(item.id)">
              <i-avatar i-class="user-avatar" :src="item.head" size="large" shape="circle"></i-avatar>
              <p class="data-txt tc">{{item.nickname}}</p>
              <p class="data-count tc">（发布次数：{{item.amount}}次）</p>
            </i-col>
          </i-row>
        </div>
      </li>-->
      <li class="data-item">
        <div class="data-title">
          <img class="title-img" src="../../../../static/images/wz.png" mode="widthFix"/>
          好文分享
        </div>
        <div class="data-content">
          <div class="data-card" v-for="(item, index) in centerData.good" :key="index" @click="toDetail(item.id)">
            <img class="main-img" :src="item.active.cover_url" mode="aspectFill"/>
            <p class="main-title">
              <img src="../../../../static/images/circle-icon.png" mode="aspectFill"/>
              <span class="text-ellipsis">{{ item.active.title }}</span>
            </p>
          </div>
        </div>
      </li>
      <li class="data-item">
        <div class="data-title">
          <img class="title-img" src="../../../../static/images/remen.png" mode="widthFix"/>
          热门活动<span class="des-txt">（根据点击数量）</span>
        </div>
        <div class="data-content">
          <div class="data-card" v-for="(item, index) in centerData.hot_active" :key="index" @click="toDetail(item.id)">
            <img class="main-img" :src="item.active.cover_url" mode="aspectFill"/>
            <div class="content-txt">
              <span class="title text-ellipsis">#{{ item.active.title }}</span>
              <span>{{ limitNumber[index] }}</span>
            </div>
            <div class="user-panel clearfix">
              <p class="user-info tl">
                <i-avatar i-class="user-avatar" :src="item.user.header" size="small" shape="circle"></i-avatar>
                <span class="text-ellipsis nickname">{{item.user.nickname}}</span>
              </p>
              <p class="user-info tr">
                <i-icon type="praise" size="14" color="#999"/><span class="text-ellipsis click">{{item.click}}</span>
              </p>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    name: 'DataCenter',
    components: {},
    data () {
      return {
        centerData: {},
        ctx1: null,
        ctx2: null
      }
    },
    onShareAppMessage: function () {
      return {
        title: '优临',
        path: '/pages/home/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
    },
    onPullDownRefresh: function () {
      this.getChartData()
    },
    onShow () {
      this.getChartData()
    },
    computed: {
      // 超出20个字符显示省略号
      limitNumber () {
        const newContent = []
        this.centerData.hot_active.forEach((item) => {
          let newTxt = item.active.content
          if (item.active.content.length > 20) {
            newTxt = item.active.content.substring(0, 20) + '...'
          }
          newContent.push(newTxt)
        })
        return newContent
      }
    },
    created () {
      this.getChartData()
    },
    methods: {
      // 获取图表数据
      getChartData () {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/second/statistic', 'GET', {}).then((res) => {
          that.centerData = res.data
          /* this.draw(this.ctx1, 'runCanvas1', Math.round(that.centerData.hot_part / that.centerData.hot_total * 10000) / 100.00, 0.8)
          this.draw(this.ctx2, 'runCanvas2', Math.round(that.centerData.self_party / that.centerData.part_person * 10000) / 100.00, 0.8) */
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      },
      draw (ctx, id, percent, animTime) {
        const that = this
        const ctx2 = wx.createCanvasContext(id)
        ctx = ctx2
        const time = animTime / percent
        wx.createSelectorQuery().select('#' + id).boundingClientRect(function (rect) { // 监听canvas的宽高
          const w = parseInt(rect.width / 2) // 获取canvas宽的的一半
          const h = parseInt(rect.height / 2) // 获取canvas高的一半，
          that.canvasTap(ctx, 0, percent, time, w, h)
        }).exec()
      },
      canvasTap (ctx, start, end, time, w, h) { // 传入开始百分比和结束百分比的值，动画执行的时间，还有圆心横纵坐标
        const that = this
        start++
        if (start > end) {
          return false
        }
        that.run(ctx, start, w, h) // 调用run方法
        setTimeout(function () {
          that.canvasTap(ctx, start, end, time, w, h)
        }, time)
      },
      run (ctx2, c, w, h) { // c是圆环进度百分比, w，h是圆心的坐标
        let num = (2 * Math.PI / 100 * c) - 0.5 * Math.PI
        // 圆环的绘制
        ctx2.arc(w, h, w - 3, -0.5 * Math.PI, num) // 绘制的动作
        ctx2.setStrokeStyle('#2DCCA4') // 圆环线条的颜色
        ctx2.setLineWidth('6') // 圆环的粗细
        ctx2.setLineCap('butt') // 圆环结束断点的样式  butt为平直边缘 round为圆形线帽  square为正方形线帽
        ctx2.stroke()
        // 开始绘制百分比数字
        ctx2.beginPath()
        ctx2.draw()
      },
      // 跳转详情
      toDetail (id) {
        wx.navigateTo({url: '/pages/find/activityInner/main?id=' + id})
      }
    }
  }
</script>

<style scoped lang="scss">
  .data-center{
    width:100%;
    padding:11px 0;
    background:#f4f4f4;
    .data-list{
      .data-item{
        padding:9px 12px;
        margin-bottom:27.5px;
        background:#fff;
        .data-title{
          font-size:15px;
          font-family:PingFang SC;
          font-weight:400;
          color:#333;
          vertical-align: baseline;
          .title-img{
            position: relative;
            top: -1px;
            width:15px;
            height:auto;
            margin-right:8px;
            vertical-align: middle;
          }
          .des-txt{
            display: inline-block;
            font-size:10px;
            color:#2dcca4;
            margin-left:2px;
            vertical-align: baseline;
          }
        }
        .data-content{
          border-radius:6px;
          overflow:hidden;
          .canvasBox{
            height:80px;
            margin: 10px auto 0;
            position: relative;
            background-color: #fff;
          }
          .bigCircle{
            width: 70px;
            height: 70px;
            border-radius: 50%;
            position: absolute;
            top:1.5px;
            bottom: 1.5px;
            left: 1.5px;
            right: 1.5px;
            margin: auto auto;
            background-color: #FFBD33;
          }
          .littleCircle{
            width: 66px;
            height: 66px;
            line-height:66px;
            border-radius: 50%;
            position: absolute;
            top:1.5px;
            bottom: 1.5px;
            left: 1.5px;
            right: 1.5px;
            margin: auto auto;
            background-color: #fff;
            font-size:14px;
            color:#333;
          }
          .canvas{
            width: 73px;
            height: 73px;
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            right: 0;
            margin: auto auto;
            z-index: 99;
          }
          .data-txt{
            font-size:14px;
            color:#333;
          }
          .data-count{
            font-size:10px;
            color:#2DCCA4;
          }
          .data-card{
            width:calc(50% - 15px);
            box-shadow:1px 1px 5px 2px #ccc;
            border-radius:6px;
            margin: 10px 10px 2px 5px;
            float:left;
            overflow:hidden;
            &:nth-child(2n){
              margin-right:5px;
              margin-left:10px;
            }
          }
          .main-img{
            width: 100%;
            height:100px;
            background-color:#eee;
          }
          .main-title{
            font-size: 14px;
            color: #666666;
            padding: 9px 4px;
            vertical-align: middle;
            img{
              display: inline-block;
              width:19px;
              height:19px;
              margin-right:6px;
              vertical-align: middle;
            }
            span{
              display: inline-block;
              width:calc(100% - 25px);
              vertical-align: middle;
            }
          }
          .content-txt{
            height:50px;
            line-height:14px;
            font-size: 14px;
            color: #676767;
            padding: 0 5px;
            box-sizing: border-box;
            .title{
              display: block;
              margin-right:4px;
              float:left;
              max-width:80%;
              font-size: 14px;
              height:14px;
              line-height: 14px;
              color: #2dcca4;
              vertical-align: middle;
            }
          }
          .user-panel{
            padding: 5px;
            .user-info{
              float:left;
              width:50%;
              height:25px;
              line-height:20px;
            }
            .nickname{
              font-size:12px;
            }
            .click{
              font-size:12px;
              color:#999;
            }
          }
        }
      }
    }
  }
</style>

<style lang="scss">
  .data-center{
    .user-avatar{
      width:20px;
      height:20px;
      border-radius:50%;
      margin-right:4px;
    }
  }
</style>
