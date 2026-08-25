<template>
  <view class="page">
    <view class="heading"><text class="title">购物车</text><text class="muted">{{ items.length }} 种商品</text></view>
    <view v-if="items.length === 0" class="empty">
      <text class="empty-title">购物车还是空的</text>
      <button class="primary-button browse" @click="goHome">去逛逛</button>
    </view>
    <view v-else>
      <view v-for="item in items" :key="item.product.id" class="cart-item">
        <image class="thumb" :src="item.product.image" mode="aspectFill" />
        <view class="info">
          <text class="name">{{ item.product.name }}</text>
          <text class="category">{{ item.product.category }}</text>
          <text class="price">¥{{ item.product.price }}</text>
          <view class="quantity"><text @click="changeQuantity(item, -1)">−</text><text>{{ item.quantity }}</text><text @click="changeQuantity(item, 1)">+</text></view>
        </view>
      </view>
      <view class="summary">
        <view class="summary-line"><text>商品小计</text><text>¥{{ total }}</text></view>
        <view class="summary-line"><text>运费</text><text>待后台计算</text></view>
        <view class="summary-total"><text>合计</text><text>¥{{ total }}</text></view>
        <button class="primary-button checkout" @click="checkout">去结算</button>
      </view>
    </view>
  </view>
</template>

<script setup>
import { computed, ref } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import { getCart, saveCart } from '../../utils/cart'

const items = ref([])
const total = computed(() => items.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0))

function load() {
  items.value = getCart()
}

function changeQuantity(item, delta) {
  item.quantity += delta
  if (item.quantity <= 0) {
    items.value = items.value.filter((current) => current.product.id !== item.product.id)
  }
  saveCart(items.value)
}

function checkout() {
  uni.navigateTo({ url: '/pages/order/order' })
}

function goHome() {
  uni.switchTab({ url: '/pages/index/index' })
}

onShow(load)
</script>

<style scoped>
.page { padding: 34rpx 28rpx 60rpx; }
.heading { display: flex; justify-content: space-between; align-items: end; margin-bottom: 28rpx; }
.title { font-size: 42rpx; font-weight: 700; }
.muted { color: #8b94a3; font-size: 22rpx; }
.cart-item { display: flex; gap: 22rpx; margin-bottom: 18rpx; padding: 20rpx; background: #fff; border-radius: 20rpx; }
.thumb { width: 180rpx; height: 180rpx; border-radius: 14rpx; background: #e9edf4; }
.info { flex: 1; position: relative; min-width: 0; }
.name { display: block; font-size: 28rpx; font-weight: 600; line-height: 38rpx; }
.category { display: block; margin-top: 12rpx; color: #1f6feb; font-size: 21rpx; }
.price { display: block; margin-top: 16rpx; color: #ef4d56; font-size: 28rpx; font-weight: 700; }
.quantity { display: flex; align-items: center; justify-content: space-between; width: 170rpx; margin-top: 18rpx; padding: 8rpx 14rpx; border-radius: 12rpx; background: #f1f4f8; color: #4e5969; font-size: 27rpx; }
.quantity text:first-child, .quantity text:last-child { color: #1f6feb; font-size: 32rpx; }
.summary { margin-top: 32rpx; padding: 26rpx; background: #fff; border-radius: 20rpx; }
.summary-line, .summary-total { display: flex; justify-content: space-between; padding: 14rpx 0; color: #697486; font-size: 24rpx; }
.summary-total { margin-top: 12rpx; padding-top: 22rpx; border-top: 1rpx solid #edf0f4; color: #222; font-size: 32rpx; font-weight: 700; }
.checkout { height: 82rpx; line-height: 82rpx; margin-top: 24rpx; }
.empty { padding: 100rpx 0; text-align: center; }
.empty-title { display: block; color: #7e8898; font-size: 28rpx; }
.browse { width: 240rpx; height: 76rpx; line-height: 76rpx; margin-top: 32rpx; }
</style>
