<template>
    <div class="create-poster">
      <canvas class="canvas-poster" id="canvasPoster" :style="{width:width+'px;',height: height+'px'}" canvas-id="canvasPoster"></canvas>
      <img :src="poster" class="poster-img" @longtap="savePoster" alt="">
      <div class="btn-text tc">长按保存图片</div>
    </div>
</template>

<script>
  export default {
    name: 'CreatePoster',
    data () {
      return {
        imgWidth: 0,
        imgHeight: 0,
        width: 0,
        height: 0,
        systemInfo: {},
        poster: '',
        shareInfo: {
          bgImg: '',
          qrcode: ''
        }
      }
    },
    onLoad: function (options) {
      const that = this
      wx.showLoading({
        title: '海报生成中...'
      })
      that.shareInfo.bgImg = options.bgImg
      that.shareInfo.qrcode = options.qrcode
      that.imgWidth = options.width
      that.imgHeight = options.height
      // = options.qrcode
      wx.getSystemInfo({
        success (res) {
          // 通过像素比计算出画布的实际大小（330x490）是展示的出来的大小
          that.width = 296 * res.pixelRatio
          that.height = 504 * res.pixelRatio
          that.systemInfo = res
          const query = wx.createSelectorQuery()
          query.select('#canvasPoster').boundingClientRect((res1) => {
            // 返回值包括画布的实际宽高
            that.drawImage(res1)
          }).exec()
        }
      })
    },
    methods: {
      drawImage (canvasAttrs) {
        // 创建一个canvas对象
        let ctx = wx.createCanvasContext('canvasPoster', this)
        let canvasW = this.width // 画布的真实宽度
        let canvasH = this.height // 画布的真实高度
        // 头像和二维码大小都需要在规定大小的基础上放大像素比的比例后面都会 *this.systemInfo.pixelRatio
        let qrcodeW = 80 * this.systemInfo.pixelRatio
        let qrcodeX = (canvasW - qrcodeW) / 2
        let qrcodeY = 360 * this.systemInfo.pixelRatio
        // 填充背景
        ctx.fillStyle = '#ffffff'
        ctx.fillRect(0, 0, canvasW, canvasH)
        ctx.drawImage(this.shareInfo.bgImg, 0, 0, this.imgWidth, this.imgHeight, 0, 0, canvasW, canvasW)
        ctx.save()
        // 画线
        ctx.lineWidth = 2
        ctx.strokeStyle = '#2DCCA4'
        ctx.moveTo(0, 316 * this.systemInfo.pixelRatio)
        ctx.lineTo(80 * this.systemInfo.pixelRatio, 316 * this.systemInfo.pixelRatio)
        ctx.stroke()
        ctx.moveTo(canvasW, 316 * this.systemInfo.pixelRatio)
        ctx.lineTo(canvasW - 80 * this.systemInfo.pixelRatio, 316 * this.systemInfo.pixelRatio)
        ctx.stroke()
        // 绘制文字
        ctx.setFontSize(14 * this.systemInfo.pixelRatio)
        ctx.setFillStyle('#2DCCA4')
        ctx.setTextAlign('left')
        ctx.fillText('与您相约  美好时光', (canvasW - ctx.measureText('与您相约  美好时光').width) / 2, 320 * this.systemInfo.pixelRatio)
        // 绘制二维码
        ctx.drawImage(this.shareInfo.qrcode, qrcodeX, qrcodeY, qrcodeW, qrcodeW)
        ctx.save()
        // 绘制文字
        ctx.setFontSize(10 * this.systemInfo.pixelRatio)
        ctx.setFillStyle('#A2A19F')
        ctx.setTextAlign('left')
        ctx.fillText('长按二维码，进入活动！', (canvasW - ctx.measureText('长按二维码，进入活动！').width) / 2, 460 * this.systemInfo.pixelRatio)
        ctx.draw()
        setTimeout(() => {
          // 下面的13以及减26推测是因为在写样式的时候写了固定的zoom: 50%而没有用像素比缩放导致的黑边，所以在生成时进行了适当的缩小生成，这个大家可以自行尝试
          wx.canvasToTempFilePath({
            canvasId: 'canvasPoster',
            x: 13,
            y: 13,
            width: canvasW - 26,
            height: canvasH - 26,
            destWidth: canvasW - 26,
            destHeight: canvasH - 26,
            success: (res) => {
              this.poster = res.tempFilePath
              wx.hideLoading()
            }
          })
        }, 200)
      },
      savePoster () {
        if (this.poster) {
          wx.saveImageToPhotosAlbum({
            filePath: this.poster,
            success: (result) => {
              wx.showToast({
                title: '海报已保存，快去分享给好友吧。',
                icon: 'none'
              })
            }
          })
        }
      }
    }
  }
</script>

<style scoped lang="scss">
  .create-poster{
    background:#f0f0f0;
    padding:23px 39px;
    .canvas-poster{
      zoom: 50%; // 将画布缩小到50%（最好通过像素比进行缩小，像素比是2的话就是50%，如果不全是以像素比为标准，在生成图片的时候可能会出现四周黑边）
      position: absolute;
      left: -10000px; // 将画布隐藏在可视区域外
      background: #206949;
    }
    .poster-img{
      width:296px;
      height:504px;
      margin: 0 auto;
    }
    .btn-text{
      margin-top:27px;
      font-size:15px;
      color:#676767;
    }
  }
</style>
