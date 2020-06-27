<template>
  <div class="container set-member">
    <i-cell-group>
      <i-cell i-class="set-cell" title="公开" label="所有人可见" @click="activeVal=0">
        <i-icon type="right" slot="icon" size="16" :color="activeVal==0?'#2DCCA4':'#fff'"/>
      </i-cell>
      <i-cell i-class="set-cell" title="私密" label="仅自己可见" @click="activeVal=1">
        <i-icon type="right" slot="icon" size="16" :color="activeVal==1?'#2DCCA4':'#fff'"/>
      </i-cell>
    </i-cell-group>
    <button class="save-btn" @click="saveSet">保存</button>
  </div>
</template>

<script>
  import bus from '../../../utils/bus'
  export default {
    name: 'SetMember',
    data () {
      return {
        activeVal: 1
      }
    },
    onLoad (options) {
      this.activeVal = options.flag
    },
    methods: {
      saveSet () {
        bus.$emit('changeSet', this.activeVal)
        wx.navigateBack({
          delta: 1
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .set-member{
    position:relative;
    height:100%;
    .set-cell{
      font-size:15px;
      color:#050505;
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

