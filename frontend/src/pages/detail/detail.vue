<template>
  <view class="page" v-if="product">
    <image class="cover" :src="product.image" mode="aspectFill" />
    <view class="panel">
      <text class="category">{{ product.category }} · {{ product.brand }}</text>
      <text class="name">{{ product.name }}</text>
      <view class="price-row"><text class="price">¥{{ product.price }}</text><text class="market">¥{{ product.market_price }}</text></view>
      <view class="line"><text>库存</text><text>{{ product.stock }} 件</text></view>
      <view class="description">{{ product.description }}</view>
      <view class="bottom-actions">
        <button class="cart" @click="add">加入购物车</button>
        <button class="buy" @click="buyNow">立即购买</button>
      </view>
    </view>
  </view>
  <view v-else class="empty">正在加载商品…</view>
</template>

<script setup>
import { ref } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { request, fallbackProducts } from '../../utils/api'
import { addToCart } from '../../utils/cart'

const product = ref(null)

onLoad(async (options) => {
  const id = Number(options.id)
  try {
    const result = await request(`product&id=${id}`)
    product.value = result.data
  } catch (error) {
    product.value = fallbackProducts.find((item) => item.id === id) || null
  }
})

function add() {
  addToCart(product.value)
  uni.showToast({ title: '已加入购物车', icon: 'success' })
}

function buyNow() {
  addToCart(product.value)
  uni.switchTab({ url: '/pages/cart/cart' })
}
</script>

<style scoped>
.page { padding-bottom: 40rpx; }
.cover { width: 100%; height: 600rpx; background: #e9edf4; }
.panel { margin: -28rpx 24rpx 0; position: relative; padding: 34rpx 28rpx; background: #fff; border-radius: 26rpx; }
.category { display: block; color: #1f6feb; font-size: 22rpx; }
.name { display: block; margin-top: 16rpx; font-size: 40rpx; font-weight: 700; line-height: 54rpx; }
.price-row { display: flex; align-items: baseline; gap: 16rpx; margin-top: 20rpx; }
.price { color: #ef4d56; font-size: 42rpx; font-weight: 700; }
.market { color: #a9b0bb; font-size: 24rpx; text-decoration: line-through; }
.line { display: flex; justify-content: space-between; margin-top: 28rpx; padding: 22rpx 0; border-top: 1rpx solid #edf0f4; border-bottom: 1rpx solid #edf0f4; color: #667184; font-size: 24rpx; }
.description { margin-top: 26rpx; color: #626d7d; font-size: 26rpx; line-height: 42rpx; }
.bottom-actions { display: flex; gap: 18rpx; margin-top: 44rpx; }
.bottom-actions button { flex: 1; height: 86rpx; line-height: 86rpx; border-radius: 14rpx; font-size: 26rpx; }
.cart { background: #edf3ff; color: #1f6feb; }
.buy { background: #1f6feb; color: #fff; }
.empty { padding: 100rpx 0; text-align: center; color: #8b94a3; }
</style>
