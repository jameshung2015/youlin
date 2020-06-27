<template>
    <div class="set-detail-panel">
      <p class="detail-title">活动介绍</p>
      <textarea class="content-panel introduce" v-model="introduction" mode="wrapped" maxlength="200" placeholder="请注明#活动名额 #报名截止日期"></textarea>
      <p class="detail-title">活动内容</p>
      <textarea class="content-panel" v-model="detail" mode="wrapped" maxlength="200"></textarea>
      <button class="save-btn" @click="saveSet">保存</button>
    </div>
</template>

<script>
  import bus from '../../../utils/bus'
  export default {
    name: 'SetDetail',
    data () {
      return {
        introduction: '', // 活动介绍
        detail: '' // 活动内容
      }
    },
    onLoad (options) {
      wx.setNavigationBarTitle({
        title: '您的活动内容'
      })
      wx.setNavigationBarColor({
        frontColor: '#000000',
        backgroundColor: '#ffbd33'
      })
      this.introduction = options.introduction
      this.detail = options.detail
    },
    methods: {
      saveSet () {
        bus.$emit('changeDetailSet', {
          introduction: this.introduction, // 活动介绍
          detail: this.detail // 活动内容
        })
        wx.navigateBack({
          delta: 1
        })
      }
    }
  }
</script>

<style scoped lang="scss">
  .set-detail-panel{
    position:relative;
    width:100%;
    height:100%;
    padding: 14px 16px;
    box-sizing: border-box;
    .detail-title{
      font-size:18px;
      color:#101010;
    }
    .content-panel{
      width:100%;
      height:275px;
      padding:8px;
      margin: 13px auto 20px;
      font-size:15px;
      color:#999999;
      background:#FFFFFF;
      border-radius:7px;
      box-shadow:0 1px 6px rgba(153, 153, 153,.117647), 0 1px 4px rgba(153, 153, 153,.117647);
      box-sizing: border-box;
      &.introduce{
        height:108px;
      }
    }
    .save-btn {
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
  }
</style>
