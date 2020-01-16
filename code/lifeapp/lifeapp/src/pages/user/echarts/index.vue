<template>
  <div class="container echarts-panel">
    <div class="left-btn-panel">
      <button @click="changeIndex(0)" :class="[activeIndex==0?'active':'']"><span>成员参与热度</span></button>
      <button @click="changeIndex(1)" :class="[activeIndex==1?'active':'']"><span>建设重点</span></button>
      <button @click="changeIndex(2)" :class="[activeIndex==2?'active':'']"><span>发现阅览热度</span></button>
    </div>
    <div class="right-echart-panel">
      <div v-show="activeIndex==0" class="echarts-wrap">
        <template v-if="!showData1">
          <div class="no-data">暂无数据</div>
        </template>
        <template v-if="showData1">
          <mpvue-echarts id="pie" :echarts="echarts" :onInit="initPieChart" canvasId="pie-chart"/>
        </template>
      </div>
      <div v-show="activeIndex==1" class="echarts-wrap">
        <template v-if="!showData2">
          <div class="no-data">暂无数据</div>
        </template>
        <template v-if="showData2">
          <mpvue-echarts id="column" :echarts="echarts" :onInit="initColumnChart" canvasId="column-chart"/>
        </template>
      </div>
      <div v-show="activeIndex==2" class="echarts-wrap">
        <template v-if="!showData3">
          <div class="no-data">暂无数据</div>
        </template>
        <template v-if="showData3">
          <mpvue-echarts id="line" :echarts="echarts" :onInit="initLineChart" canvasId="line-chart"/>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
  import * as echarts from 'echarts/dist/echarts.min'
  import mpvueEcharts from 'mpvue-echarts'

  let chart1 = null
  let chart2 = null
  let chart3 = null

  export default {
    name: 'EchartsPanel',
    components: {
      mpvueEcharts
    },
    data () {
      return {
        width: '',
        height: '',
        showData1: false,
        showData2: false,
        showData3: false,
        activeIndex: 0,
        echarts,
        option1: {},
        option2: {},
        option3: {}
      }
    },
    onShareAppMessage: function () {
      return {
        title: '优临',
        path: '/pages/time/main', // 默认是当前页面，必须是以‘/’开头的完整路径
        imageUrl: 'https://www.youlings.cn/images/logo.jpg' // 自定义图片路径，可以是本地文件路径、代码包文件路径或者网络图片路径，支持PNG及JPG，不传入 imageUrl 则使用默认截图。显示图片长宽比是 5:4
      }
    },
    onLoad () {
      this.width = ''
      this.height = ''
      this.showData1 = false
      this.showData2 = false
      this.showData3 = false
      this.activeIndex = 0
      this.option1 = {}
      this.option2 = {}
      this.option3 = {}
      this.getChartData()
    },
    onPullDownRefresh: function () {
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
        let cellSize = [80, 80]
        this.$request(this.$baseUrl + '/api/user/week', 'GET', {}).then((res) => {
          if (res.data.leaving_message.length) {
            that.option1 = {
              color: ['#ffcc00', '#400181', '#ff3447'],
              title: {
                text: '成员参与热度',
                x: 'center'
              },
              tooltip: {
                trigger: 'item',
                formatter: '{b} : {c} ({d}%)'
              },
              legend: {
                orient: 'vertical',
                left: 'center',
                bottom: 40,
                data: res.data.leaving_message
              },
              series: [
                {
                  name: '热度',
                  type: 'pie',
                  radius: '55%',
                  center: ['50%', '50%'],
                  data: res.data.leaving_message,
                  itemStyle: {
                    emphasis: {
                      shadowBlur: 10,
                      shadowOffsetX: 0,
                      shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                  },
                  label: {
                    normal: {
                      show: true,
                      formatter: function (params) {
                        return params.value
                      },
                      offset: [-cellSize[0] / 2 + 10, -cellSize[1] / 2 + 10],
                      textStyle: {
                        color: '#000'
                      },
                      rich: {}
                    }
                  }
                }
              ]
            } // ECharts 配置项
            setTimeout(() => {
              that.showData1 = true
            }, 500)
          } else {
            that.showData1 = false
          }
          if (res.data.active_type.length) {
            const name = res.data.active_type.map((item) => {
              return item.name
            })
            const value = res.data.active_type.map((item) => {
              return item.value
            })
            that.option2 = {
              color: ['#ffcc00', '#400181', '#ff3447'],
              title: {
                text: '建设重点',
                x: 'center'
              },
              tooltip: {
                trigger: 'axis',
                axisPointer: {
                  type: 'shadow'
                }
              },
              grid: {
                left: '3%',
                right: '10%',
                top: '20%',
                bottom: 40
              },
              xAxis: [{
                data: name,
                axisLine: {
                  lineStyle: {
                    color: 'rgba(255,255,255,0.12)'
                  }
                },
                axisLabel: {
                  margin: 10,
                  color: '#999'
                }
              }],
              yAxis: [{
                show: false
              }],
              series: [{
                type: 'bar',
                data: value,
                barWidth: '5px',
                itemStyle: {
                  normal: {
                    color: function (params) {
                      const colorList = ['#400181', '#ffcc00', '#ff3447', '#6294c1', '#613390', '#32b16c', '#ff9c00']
                      return colorList[params.dataIndex]
                    },
                    barBorderRadius: [30, 30, 30, 30]
                  }
                },
                label: {
                  normal: {
                    show: true,
                    lineHeight: 20,
                    width: 60,
                    height: 20,
                    backgroundColor: '#fff',
                    borderRadius: 100,
                    position: ['-5', '-30'],
                    distance: 1,
                    formatter: [
                      '    {d|●}',
                      ' {a|{c}}     \n'
                    ],
                    rich: {
                      d: {
                        color: '#333'
                      },
                      a: {
                        color: '#999',
                        align: 'center'
                      }
                    }
                  }
                }
              }]
            } // ECharts 配置项
            setTimeout(() => {
              that.showData2 = true
            }, 500)
          } else {
            that.showData2 = false
          }
          if (res.data.system_browse.length) {
            const name1 = res.data.system_browse.map((item) => {
              return item.name
            })
            const value1 = res.data.system_browse.map((item) => {
              return item.value
            })
            that.option3 = {
              color: ['#ffcc00', '#400181', '#ff3447'],
              title: {
                text: '发现阅览热度',
                x: 'center'
              },
              tooltip: {
                trigger: 'axis',
                axisPointer: {
                  type: 'shadow'
                }
              },
              grid: {
                left: '3%',
                right: '10%',
                top: '20%',
                bottom: 40
              },
              xAxis: [{
                data: name1,
                axisLine: {
                  lineStyle: {
                    color: 'rgba(255,255,255,0.12)'
                  }
                },
                axisLabel: {
                  margin: 10,
                  color: '#999'
                }
              }],
              yAxis: [{
                show: false
              }],
              series: [{
                type: 'bar',
                data: value1,
                barWidth: '5px',
                itemStyle: {
                  normal: {
                    color: function (params) {
                      const colorList = ['#400181', '#ffcc00', '#ff3447', '#6294c1', '#613390', '#32b16c', '#ff9c00']
                      return colorList[params.dataIndex]
                    },
                    barBorderRadius: [30, 30, 30, 30]
                  }
                },
                label: {
                  normal: {
                    show: true,
                    lineHeight: 20,
                    width: 60,
                    height: 20,
                    backgroundColor: '#fff',
                    borderRadius: 100,
                    position: ['-5', '-30'],
                    distance: 1,
                    formatter: [
                      '    {d|●}',
                      ' {a|{c}}     \n'
                    ],
                    rich: {
                      d: {
                        color: '#333'
                      },
                      a: {
                        color: '#999',
                        align: 'center'
                      }
                    }
                  }
                }
              }]
            } // ECharts 配置项
            setTimeout(() => {
              that.showData3 = true
            }, 500)
          } else {
            that.showData3 = false
          }
          wx.stopPullDownRefresh()
          wx.hideNavigationBarLoading()
          wx.hideToast()
        })
      },
      initPieChart (canvas, width, height) {
        this.width = width
        this.height = height
        chart1 = echarts.init(canvas, null, {
          width: width,
          height: height
        })
        canvas.setChart(chart1)
        chart1.setOption(this.option1)
        return chart1
      },
      initColumnChart (canvas, width, height) {
        chart2 = echarts.init(canvas, null, {
          width: this.width,
          height: this.height
        })
        canvas.setChart(chart2)
        chart2.setOption(this.option2)
        return chart2
      },
      initLineChart (canvas, width, height) {
        chart3 = echarts.init(canvas, null, {
          width: this.width,
          height: this.height
        })
        canvas.setChart(chart3)
        chart3.setOption(this.option3)
        return chart3
      },
      changeIndex (index) {
        this.activeIndex = index
        if (index === 0) {
          this.$nextTick(() => {
            if (chart1) {
              chart1.resize()
            }
          })
        }
        if (index === 1) {
          this.$nextTick(() => {
            if (chart2) {
              chart2.resize()
            }
          })
        }
        if (index === 2) {
          this.$nextTick(() => {
            if (chart3) {
              chart3.resize()
            }
          })
        }
      }
    }
  }
</script>

<style scoped>
  .no-data{
    width: 100%;
    height: 400px;
    line-height:400px;
    text-align:center;
    color:#ccc;
    font-size:20px;
  }
  .echarts-wrap {
    width: 100%;
    height: 400px;
  }

  .echarts-panel {
    padding-top: 38px;
  }

  .left-btn-panel {
    float: left;
    width: 65px;
    height: 100%;
  }

  .left-btn-panel button {
    width: 29px;
    background-color: #ffcc00;
    border-radius: 5px;
    text-align: right;
    font-size: 15px;
    color: #400181;
    transition: width 0.3s;
    padding: 10px 0;
    margin: 0 0 1px;
    vertical-align: middle;
  }

  .left-btn-panel button:nth-child(2) {
    background-color: #400181;
    color: #ffcc00;
  }

  .left-btn-panel button:nth-child(3) {
    background-color: #ff3447;
  }

  .left-btn-panel button.active {
    width: 56px;
  }

  .left-btn-panel button span {
    float: right;
    display: flex;
    -webkit-display: flex;
    flex-direction: column;
    align-content: right;
    justify-content: center;
    margin: 0 11px;
    width: 12px;
    height: 100%;
    line-height: 15px;
  }

  .right-echart-panel {
    position: relative;
    right: -2px;
    float: right;
    width: calc(100% - 65px);
    box-shadow: 0px 3px 3px 0px rgba(3, 0, 0, 0.1);
    border: 0.5px solid rgba(3, 0, 0, 0.1);
    border-radius: 5px;
    padding: 10px;
    box-sizing: border-box;
  }
</style>
