<script>
export default {
  // 转发分享
  onShareAppMessage: function (options) {
    // const that = this
    // 设置菜单中的转发按钮触发转发事件时的转发内容
    let shareObj = {
      title: '优邻', // 默认是小程序的名称(可以写slogan等)
      path: '/pages/time/main', // 默认是当前页面，必须是以‘/’开头的完整路径
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
        // 转发结束之后的回调（转发成不成功都会执行）
      }
    }
    // 返回shareObj
    return shareObj
  }
}
</script>

<style>
  html, body{
    position:relative;
    width:100%;
    height:100%;
  }
  *{
    font-family:PingFang-SC-Regular;
    box-sizing: border-box;
  }
  .clearfix:after{
    content: '';
    display: block;
    clear:both;
    visibility: hidden;
    line-height:0;
    font-size:0;
  }
  .clearfix{
    zoom:1;
  }
  ::-webkit-scrollbar{
    display:none;
  }
  .text-ellipsis{
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
  .tl{
    text-align:left;
  }
  .tr{
    text-align:right;
  }
  .tc{
    text-align:center;
  }
  /* 隐藏滚动条 */
  ::-webkit-scrollbar{
    width: 0;
    height: 0;
    color: transparent;
  }
  /*定义滚动条轨道 内阴影+圆角*/
  ::-webkit-scrollbar-track {
    width: 0;
    height: 0;
    color: transparent;
  }

  /*定义滑块 内阴影+圆角*/
  ::-webkit-scrollbar-thumb {
    width: 0;
    height: 0;
    color: transparent;
  }
</style>
