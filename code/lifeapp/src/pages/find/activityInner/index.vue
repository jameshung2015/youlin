<template>
  <div class="container inner-panel">
    <div class="inner-content">
      <i-cell v-if="activityData.own_id == userId" i-class="link-cell" title="编辑" is-link @click="toEdit">
        <i-icon class="cell-icon-img" type="editor" slot="icon" size="20" color="#2A2A2A"/>
      </i-cell>
      <i-cell v-if="activityData.own_id == userId" i-class="link-cell" title="删除" @click="toDel">
        <i-icon class="cell-icon-img" type="trash" slot="icon" size="20" color="#2A2A2A"/>
      </i-cell>
      <div class="card">
        <div class="card-title">
          <i-avatar :src="activityData.user.header" shape="circle"></i-avatar>
          <div class="topic-right">
            <div class="author text-ellipsis">{{activityData.user.nickname}}</div>
            <div class="time">{{ activityData.user.create }}</div>
          </div>
        </div>
        <div class="card-content">
          <div class="content-txt">
            <span class="more-content"><span class="title">#{{ activityData.active.title }}</span>{{ activityData.active.content }}</span>
          </div>
        </div>
        <div class="img-panel" v-for="(img, index) in activityData.images" :key="index">
          <img :src="img.filepath" mode="widthFix"/>
        </div>
        <p class="party-num">{{ activityData.apply.length }}人已参与</p>
        <div v-if="activityData.apply.length" class="party-panel clearfix">
          <div class="left-panel fl" :style="showAvatar?'overflow:auto;height:auto;':''">
            <i-avatar v-for="(people, index) in activityData.apply" :key="index" :src="people.header" shape="circle"></i-avatar>
          </div>
          <div class="right-panel fr tc" @click="showAvatar=!showAvatar">...</div>
        </div>
        <div class="activity-detail">
          <div class="collapse-title">
            活动详情
            <span class="collapse-span fr" @click="showDetail=!showDetail">
              <i-icon class="collapse-icon" :class="showDetail?'collapse-open-icon':''" type="enterinto_fill" size="18" color="#999999"/>
              {{showDetail?'收起':'展开'}}
            </span>
          </div>
          <div class="collapse-content" :class="showDetail?'collapse-open':''">
            <p class="content-title">【活动介绍】</p>
            <div class="content-con">
              {{activityData.active.introduction.length ? activityData.active.introduction: '暂无' }}
            </div>
            <p class="content-title">【活动内容】</p>
            <div class="content-con">
              {{activityData.active.detail.length ? activityData.active.detail: '暂无'}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="btn-panel">
      <img src="../../../../static/images/home.png" mode="widthFix" @click="toHome"/>
      <img src="../../../../static/images/share.png" mode="widthFix" @click="handleOpen1"/>
      <button class="join-btn tc" @click="doSign">立即报名</button>
    </div>
    <div class="i-as-mask i-class-mask" :class="visible1 ? 'i-as-mask-show' : ''" @click="visible1=false"></div>
    <div class="i-class i-as" :class="visible1 ? 'i-as-show' : ''">
      <div class="i-as-actions">
        <div class="i-as-action-item">
          <i-button
            i-class="share-btn"
            :data-name="activityData.active.title"
            :data-id="activityData.id"
            open-type="share"
            type="ghost"
            size="large"
            long
            class="tl"
          >
            <img src="../../../../static/images/weixin.png" mode="widthFix"/>
            <span class="i-as-btn-text">转发好友或群聊</span>
            <i-icon class="right-icon fr" type="enter" size="16" color="#A2A19F"/>
          </i-button>
        </div>
        <div class="i-as-action-item">
          <i-button
            i-class="share-btn"
            @click="handleClickItem1"
            type="ghost"
            size="large"
            class="tl"
            long
          >
            <img src="../../../../static/images/shengcheng.png" mode="widthFix"/>
            <span class="i-as-btn-text">生成海报</span>
            <i-icon class="right-icon fr" type="enter" size="16" color="#A2A19F"/>
          </i-button>
        </div>
      </div>
      <div class="i-as-cancel">
        <i-button i-class="i-as-cancel-btn" type="ghost" size="large" long="true" @click="handleCancel1">取消</i-button>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: '',
    data () {
      return {
        id: '',
        visible1: false,
        userId: wx.getStorageSync('user_id'),
        showDetail: true,
        showAvatar: false,
        name: 'name1',
        activityData: {}
      }
    },
    // 转发分享
    onShareAppMessage: function (options) {
      const that = this
      let shareObj = {
        title: '优临', // 默认是小程序的名称(可以写slogan等)
        path: '/pages/home/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
      // 来自页面内的按钮的转发
      if (options.from === 'button') {
        const eData = options.target.dataset
        shareObj.title = eData.name
        // shareObj.path = '/pages/home/main?title=' + eData.name
        shareObj.path = '/pages/find/activityInner/main?id=' + eData.id
        that.$request(that.$baseUrl + '/api/second/transmit', 'GET', {
          id: eData.id
        }).then((res) => {
        })
      }
      return shareObj
    },
    onLoad: function (options) {
      this.id = options.id
    },
    onShow: function () {
      this.getData(this.id)
      this.userId = wx.getStorageSync('user_id')
    },
    created () {
      this.getData(this.id)
    },
    methods: {
      // 编辑
      toEdit () {
        wx.navigateTo({url: '/pages/time/topic/main?id=' + this.activityData.id})
      },
      // 删除
      toDel () {
        const that = this
        that.$request(that.$baseUrl + '/api/second/time/delete', 'GET', {
          id: that.activityData.id
        }).then((res) => {
          wx.showToast({
            title: '删除成功',
            icon: 'none',
            duration: 2000
          })
          wx.switchTab({url: '/pages/time/main'})
        })
      },
      // 报名
      doSign () {
        wx.navigateTo({url: '/pages/time/signForm/main?id=' + this.activityData.id})
      },
      handleOpen1 () {
        this.visible1 = true
      },
      handleCancel1 () {
        this.visible1 = false
      },
      handleClickItem1 () {
        const that = this
        let image
        if (this.activityData.active.cover_url) {
          image = this.activityData.active.cover_url
        } else if (this.activityData.images) {
          image = this.activityData.images[0].filepath
        } else {
          image = 'https://www.youlings.cn/images/logo.jpg'
        }
        let qrcode
        let imgWidth
        let imgHeight
        that.$request(that.$baseUrl + '/api/second/qr/code/two', 'GET', {
          id: that.activityData.id
        }).then((res) => {
          qrcode = this.$baseUrl + '/' + res.data.image
          wx.getImageInfo({
            src: image,
            success (res0) {
              imgWidth = res0.width
              imgHeight = res0.height
              wx.downloadFile({
                url: res0.path, // 图片
                success: (res1) => {
                  image = res1.tempFilePath
                  wx.getImageInfo({
                    src: qrcode,
                    success (res2) {
                      wx.downloadFile({
                        url: res2.path, // 图片
                        success: (res3) => {
                          qrcode = res3.tempFilePath
                          wx.navigateTo({
                            url: '/pages/find/createPoster/main?bgImg=' + image + '&qrcode=' + qrcode + '&width=' + imgWidth + '&height=' + imgHeight
                          })
                        },
                        fail: function (e) {
                          wx.showToast({
                            title: '下载图片失败',
                            icon: 'none',
                            duration: 2000
                          })
                        }
                      })
                    },
                    fail (res) {
                      wx.showToast({
                        title: '获取图片失败',
                        icon: 'none',
                        duration: 2000
                      })
                    }
                  })
                },
                fail: function (e) {
                  wx.showToast({
                    title: '下载图片失败',
                    icon: 'none',
                    duration: 2000
                  })
                }
              })
            },
            fail (res) {
              wx.showToast({
                title: '获取图片失败',
                icon: 'none',
                duration: 2000
              })
            }
          })
        })
      },
      toHome () {
        wx.switchTab({
          url: '/pages/time/main'
        })
      },
      getData (id) {
        const that = this
        wx.showToast({
          title: '加载中...',
          icon: 'none',
          mask: true
        })
        wx.showNavigationBarLoading()
        that.$request(that.$baseUrl + '/api/second/time/show', 'GET', {
          id: id
        }).then((res) => {
          that.activityData = Object.assign(res.data[0], {})
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      }
    }
  }
</script>

<style scoped lang="scss">
  .inner-panel{
    position:relative;
    width:100%;
    height:100%;
    .inner-content{
      height:calc(100% - 50px);
      overflow:auto;
      .card{
        position:relative;
        height:auto;
        background: #fff;
        padding:10px 17px;
        box-sizing:content-box;
        .card-title{
          vertical-align: middle;
          height:37px;
          i-avatar{
            position:relative;
            top:-10px;
          }
          .topic-right{
            display: inline-block;
            width:calc(100% - 110px);
            margin-left:10px;
            padding-top:5px;
            .author{
              width:100%;
              font-size: 12px;
              line-height: 16px;
              color: #333333;
            }
            .time{
              font-size: 9px;
              line-height: 14px;
              color: #a2a19f;
            }
          }
        }
        .card-content{
          width:100%;
          font-size:14px;
          line-height:24px;
          color:#333;
          margin:5px auto;
          .content-txt{
            line-height:15px;
            margin-bottom:18px;
            .more-content{
              display: inline-block;
              font-size: 14px;
              color: #666666;
              word-break: break-all;
              .title{
                display: inline-block;
                font-size: 17px;
                margin-right:10px;
                color: #2DCCA4;
              }
            }
          }
        }
        .img-panel{
          width:100%;
          height:auto;
          margin-bottom:10px;
          img{
            width:100%;
            height:auto;
            border-radius:9px;
            overflow:hidden;
          }
        }
        .party-num{
          font-size:14px;
          color:#666666;
          line-height:30px;
          border-top:1px solid #F0F0F0;
        }
        .party-panel{
          padding: 7px 0;
          .left-panel{
            width:calc(100% - 40px);
            height:40px;
            overflow:hidden;
            i-avatar{
              display: inline-block;
              margin-right:9.5px;
              margin-bottom:5px;
            }
          }
          .right-panel{
            width:36px;
            height:36px;
            line-height:27px;
            border-radius: 50%;
            background:#eee;
            color:#999;
            font-weight:bold;
          }
        }
      }
    }
    .btn-panel{
      position:fixed;
      width:100%;
      padding:12px;
      bottom:0;
      left:0;
      background:#fff;
      box-shadow: 0 -2px 5px 0 #eee;
      vertical-align: middle;
      img{
        display: inline-block;
        width:22px;
        height:auto;
        margin-right:25px;
        vertical-align: middle;
      }
      .join-btn{
        display: inline-block;
        width: calc(100% - 120px);
        height:34px;
        line-height:34px;
        font-size:16px;
        color:#FFFFFF;
        background:#FFBD33;
        border-radius:17px;
        border:none;
        vertical-align: middle;
      }
    }
    .activity-detail{
      margin-top:10px;
      .collapse-title{
        font-size:15px;
        color:#666666;
        .collapse-span{
          display: inline-block;
          font-size:13px;
          .collapse-icon{
            display: inline-block;
            transform: rotate(90deg);
            transition: transform 0.5s;
          }
          .collapse-open-icon{
            transform: rotate(-90deg);
          }
        }
      }
      .collapse-content{
        padding:0 18px;
        height:0;
        transition: height 0.5s;
        overflow:hidden;
        .content-title{
          font-size:16px;
          color:#FF6C2E;
          font-weight:500;
          line-height:40px;
        }
        .content-con{
          padding-left:8px;
          font-size:12px;
          color:#666666;
          font-weight:500;
          line-height:30px;
        }
      }
      .collapse-open{
        height:auto;
      }
    }
  }
  .i-as{
    position:fixed;
    width:100%;
    box-sizing:border-box;
    left:0;
    right:0;
    bottom:0;
    background:#f7f7f7;
    border-radius:22px 22px 0px 0px;
    overflow:hidden;
    transform:translate3d(0,100%,0);
    transform-origin:center;
    transition:all .2s ease-in-out;
    z-index:900;visibility:hidden
  }
  .i-as-show{
    transform:translate3d(0,0,0);
    visibility:visible
  }
  .i-as-mask{
    position:fixed;
    top:0;
    left:0;
    right:0;
    bottom:0;
    background:rgba(0,0,0,.7);
    z-index:900;
    transition:all .2s ease-in-out;
    opacity:0;
    visibility:hidden
  }
  .i-as-mask-show{
    opacity:1;
    visibility:visible
  }
  .i-as-action-item{
    position:relative;
    vertical-align: middle;
    font-size:14px;
    color:#666666;
    text-align:left;
    img{
      display: inline-block;
      margin-right:12px;
      width:38px;
      height:auto;
      vertical-align: middle;
    }
  }
  .i-as-action-item::after{
    content:'';
    position:absolute;
    top:0;
    left:0;
    width:200%;
    height:200%;
    transform:scale(.5);
    transform-origin:0 0;
    pointer-events:none;
    box-sizing:border-box;
    border:0 solid #e9eaec;
    border-bottom-width:1px
  }
  .i-as-header{
    background:#fff;
    text-align:center;
    position:relative;
    font-size:12px;
    color:#80848f
  }
  .i-as-header::after{
    content:'';
    position:absolute;
    top:0;
    left:0;
    width:200%;
    height:200%;
    transform:scale(.5);
    transform-origin:0 0;
    pointer-events:none;
    box-sizing:border-box;
    border:0 solid #e9eaec;
    border-bottom-width:1px
  }
  .i-as-cancel{
    margin-top:6px
  }
  .i-as-btn-loading{
    display:inline-block;
    vertical-align:middle;
    margin-right:10px;
    width:12px;
    height:12px;
    background:0 0;
    border-radius:50%;
    border:2px solid #e5e5e5;
    border-color:#666 #e5e5e5 #e5e5e5 #e5e5e5;
    animation:btn-spin .6s linear;
    animation-iteration-count:infinite
  }
  .i-as-btn-text{
    display:inline-block;
    vertical-align:middle
  }
  .i-as-btn-icon{
    font-size:14px!important;
    margin-right:4px
  }
  @keyframes btn-spin{
    0%{transform:rotate(0)}
    100%{transform:rotate(360deg)}
  }
</style>

<style lang="scss">
  .inner-panel{
    .link-cell{
      border-bottom:1px solid #F0F0F0;
    }
    .share-btn{
      height:62.5px;
      line-height:62.5px;
      text-align: left!important;
    }
  }
</style>
