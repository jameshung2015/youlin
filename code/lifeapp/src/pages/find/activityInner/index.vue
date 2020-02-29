<template>
  <div class="container inner-panel">
    <div class="inner-content">
      <p class="article-title">{{ activityData.title }}</p>
      <div class="article-content">{{ activityData.content }}</div>
      <div v-if="activityData.image.length" class="img-content" :style="showImgMask?'height:' + height + ';' :''" >
        <img v-for="item in activityData.image" :key="item" class="article-img" :src="item" mode="widthFix" @click="clickImg"/>
      </div>
    </div>
    <button v-if="showBtn" class="join-btn" @click="toJoin">参加</button>
    <div v-if="showDialog" class="authority-dialog">
      <p>请联系客服开通权限</p>
      <button @click="showDialog=false">确定</button>
    </div>
  </div>
</template>

<script>
  /* eslint-disable eqeqeq */

  export default {
    name: '',
    data () {
      return {
        height: 'auto',
        showDialog: false,
        showImgMask: true,
        showBtn: true,
        activityData: {
          image: [],
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
      this.showDialog = false
      if (options.type) {
        this.showBtn = false
      } else {
        this.showBtn = true
      }
      if (options.authority == 0) {
        this.showImgMask = true
      } else {
        this.showImgMask = false
      }
      this.$set(this.activityData, 'image', unescape(options.image).split(','))
      this.$set(this.activityData, 'title', options.title)
      this.$set(this.activityData, 'id', options.id)
      this.$set(this.activityData, 'content', options.content)
      wx.setNavigationBarTitle({
        title: options.title
      })
    },
    mounted () {
      this.calcWidth()
    },
    methods: {
      calcWidth () {
        const that = this
        const query = wx.createSelectorQuery()
        query.select('.img-content').boundingClientRect(function (rect) {
          that.height = rect.width * 1006 / 1125 + 'px'
        }).exec()
      },
      // 点击图片
      clickImg () {
        if (this.showImgMask) {
          this.showDialog = true
        } else {
          this.showDialog = false
        }
      },
      // 跳转到参与活动
      toJoin () {
        const item = this.activityData
        if (this.showImgMask) {
          this.showDialog = true
        } else {
          this.showDialog = false
          wx.navigateTo({url: '/pages/find/joinActivity/main?image=' + escape(item.image) + '&title=' + item.title + '&id=' + item.id + '&content=' + item.content})
        }
      }
    }
  }
</script>

<style scoped>
  .img-content{
    width:100%;
    height:auto;
    overflow:hidden;
  }
  .authority-dialog{
    position: fixed;
    top: 50%;
    left:50%;
    z-index:10000;
    width: 236px;
    height: 126px;
    margin-top: -68px;
    margin-left: -118px;
    opacity: 0.7;
    background-color: #000000;
    border-radius: 5px;
    padding:28px 0;
    box-sizing: border-box;
    animation:myShow 0.5s;
  }
  @keyframes myShow {
    from {opacity: 0;}
    to {opacity: 0.7;}
  }
  .authority-dialog p{
    line-height:25px;
    font-size: 15px;
    color: #ffffff;
    text-align:center;
  }
  .authority-dialog button{
    width: 100px;
    height: 30px;
    margin-top:16px;
    line-height:30px;
    background-color: #ffcc00;
    border-radius: 5px;
    font-size: 15px;
    color: #000000;
    text-align:center;
  }
  .article-img{
    display: block;
    width:100%;
    height:auto;
    margin-bottom:10px;
  }
  .article-img.show-mask{
    -webkit-filter:blur(10px);
    filter:blur(10px);
    overflow:hidden;
  }
  .inner-panel{
    width:100%;
    height:100%;
    overflow:hidden;
  }
  .inner-content{
    height: calc(100vh - 100px);
    margin:7px 7px 22px;
    border-radius: 25px;
    border: dashed 1px #ffcc00;
    padding: 33.5px;
    box-sizing: border-box;
    overflow: auto;
  }
  .inner-content .article-title{
    height: 30px;
    line-height:30px;
    font-size: 18px;
    color: #400181;
  }
  .inner-content .article-content{
    font-size: 11px;
    line-height:34px;
    color: #333333;
    text-indent:5px;
  }
  .join-btn{
    margin:0 37.5px 15px;
    height: 40px;
    background-color: #613390;
    border-radius: 20px;
    padding:0 132px;
    font-size: 18px;
    color: #ffffff;
    text-align:center;
  }
</style>
