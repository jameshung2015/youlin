<template>
  <div class="longinPage">
    <div class="right_Letter">
      <div class="Letter_list" v-for="(item,index) in LetterCity" :key="index" @click="addClassName(index,item.letter)" :class="{active:index == thatnum}">{{item.letter}}</div>
    </div>

    <scroll-view class="longinPage_scroll"  :scroll-y="true" @scrolltolower="scrolltolower" @scroll="scroll" :scroll-into-view="toView" :scroll-with-animation="true">
      <div class="head_gps">
        <div class="title">定位城市</div>
        <div class="city_list" @click="toHome(userInfo.city)">{{userInfo.city}}</div>
      </div>

      <div class="head_gps">
        <div class="title">热门城市</div>
        <div class="hot_city">
          <div class="city_list" v-for="(item,index) in hotCity" :key="index" @click="toHome(item.name,item.linkageid)">{{item.name}}</div>
        </div>
      </div>

      <div class="Letter_city" v-for="(item,index) in LetterCity" :key="index" :id="item.letter">
        <div class="Letter_title">{{item.letter}}</div>
        <div v-for="(item2,index2) in item.childArr" :key="index2">
          <div class="Letter_province">{{item2.name}}</div>
          <div class="hot_city">
            <div class="city_list" v-for="(item3,index3) in item2.childArr" :key="index3" @click="toHome(item3.name,item3.linkageid)">{{item3.name}}</div>
            <div class="placeholder"></div>
          </div>
        </div>
      </div>


      <div class="box"></div>

    </scroll-view>
  </div>

</template>

<script>
  import LetterCity from './city.json'

  export default {
    data () {
      return {
        userInfo: wx.getStorageSync('userInfo'),
        LetterCity: '',
        hotCity: [],
        thatnum: 0,
        scrollTop: 0,
        toView: ''
      }
    },
    onLoad: function (options) {
      this.userInfo = wx.getStorageSync('userInfo')
      if (!this.userInfo.city) {
        this.userInfo.city = '暂无定位'
      }
      wx.pageScrollTo({
        scrollTop: 0
      })
      // 标题
      wx.setNavigationBarTitle({
        title: '选择城市'
      })
      this.LetterCity = LetterCity
      this.thatnum = 0
      // 获取开通城市
      this.hotCity = [
        {linkageid: 0, letter: 'Q', name: '全国'},
        {linkageid: 110100, letter: 'B', name: '北京'},
        {linkageid: 310100, letter: 'S', name: '上海'},
        {linkageid: 440100, letter: 'G', name: '广州'},
        {linkageid: 440300, letter: 'S', name: '深圳'},
        {linkageid: 330100, letter: 'H', name: '杭州'},
        {linkageid: 420100, letter: 'W', name: '武汉'},
        {linkageid: 510100, letter: 'C', name: '成都'},
        {linkageid: 320100, letter: 'N', name: '南京'}
      ]
    },
    methods: {
      scrolltolower () {
      },
      scroll (e) {
      },
      // to首页
      toHome (name, numID) {
        this.Bus.$emit('changeCity', name)
        wx.switchTab({url: '/pages/time/main'})
      },
      // 选择字母
      addClassName (index, id) {
        this.Tips(id)
        this.thatnum = index
        const that = this
        for (let i = 0; i < that.LetterCity.length; ++i) {
          if (that.LetterCity[i].letter === id) {
            that.toView = that.LetterCity[i].letter
            break
          }
        }
      },
      // 提示
      Tips (text) {
        wx.showToast({
          title: text,
          icon: 'none',
          duration: 500
        })
      }
    }
  }
</script>

<style scoped lang="scss">
  .longinPage{
    position: fixed;
    top: 0;
    left: 0;
    z-index: 50;
    width: 100%;
    height: 100%;
    background: #F7F7F7;
    padding: 0 9% 0 3%;
    box-sizing: border-box;
    overflow: hidden;
  }
  .longinPage_scroll{
    width: 100%;
    height: 100%;
    padding-top: 10px;
  }
  .bottom_text{
    font-size: 20px;
    color: #B5B5B5;
    text-align: center;
  }
  .right_Letter{
    position: fixed;
    right: 10px;
    top: 80px;
    z-index: 100;
    width: 25px;
    overflow: hidden;
  }
  .Letter_list{
    width: 25px;
    height: 25px;
    font-size: 14px;
    color: #ffbd33;
    text-align: center;
    line-height: 25px;
    margin-bottom: 5px;
  }
  .head_gps{
    margin-top: 10px;
  }
  .title{
    font-size: 14px;
    color: #333333;
    margin-bottom: 10px;
  }
  .city_list{
    width: 96px;
    height: 27px;
    padding: 0 5px;
    border: 1px solid #CCCBCB;
    border-radius: 2px;
    line-height: 27px;
    text-align: center;
    margin-bottom: 10px;
    font-size: 14px;
    color: #6D6D6D;
    overflow: hidden;
    text-overflow:ellipsis;
    white-space: nowrap;
  }
  .hot_city{
    display: flex;
    justify-content:space-between;
    flex-wrap: wrap;
  }
  .Letter_title{
    width: 22px;
    height: 22px;
    background: #ffbd33;
    border-radius: 50%;
    font-size: 14px;
    color: #FFFFFF;
    text-align: center;
    line-height: 22px;
    margin: 15px 0;
  }
  .Letter_province{
    font-size: 18px;
    margin-bottom:10px;
  }
  .placeholder {
    width: 30%;
    height: 0px;
  }
  .active{
    background: #ffbd33;
    border-radius: 5px;
    color: #fff;
    font-size: 16px;
  }
  .box{
    width: 100%;
    height: 30px;
  }

  /*隐藏滚动条*/
  .longinPage_scroll ::-webkit-scrollbar {
    width: 0;
    height: 0;
    color: transparent;
  }
</style>
