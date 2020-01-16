let header = {
  'Authorization': '' // Authorization
}
// 封装微信请求
export function request (url, method, data) {
  return new Promise((resolve, reject) => {
    wx.request({
      data: data,
      method: method,
      url: url,
      header: header,
      success: function (res) {
        if (res.data.token) {
          header.Authorization = res.data.token
          wx.setStorageSync('token', res.data.token)
        }
        if (res.data.code === 1) {
          resolve(res.data)
        } else {
          wx.showToast({
            title: res.data.message,
            icon: 'none',
            duration: 2000
          })
          reject(res.data)
        }
      },
      fail: function (err) {
        reject(err)
      }
    })
  })
}
