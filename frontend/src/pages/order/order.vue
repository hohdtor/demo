<template>
  <view class="page">
    <view class="heading"><text class="title">确认订单</text><text class="muted">{{ items.length }} 种商品</text></view>
    <view class="card">
      <text class="card-title">收货信息</text>
      <input v-model="form.name" class="input" placeholder="姓名" />
      <input v-model="form.phone" class="input" placeholder="电话" type="number" />
      <textarea v-model="form.address" class="textarea" placeholder="完整收货地址" />
    </view>
    <view class="card">
      <text class="card-title">订单商品</text>
      <view v-for="item in items" :key="item.product.id" class="order-item">
        <text class="item-name">{{ item.product.name }} × {{ item.quantity }}</text>
        <text>¥{{ item.product.price * item.quantity }}</text>
      </view>
      <view class="total"><text>订单小计</text><text>¥{{ total }}</text></view>
    </view>
    <button class="primary-button submit" :loading="submitting" @click="submit">提交演示订单</button>
    <text class="hint">当前 Demo 不接入真实支付，订单状态为待付款。</text>
  </view>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { request } from '../../utils/api'
import { clearCart, getCart } from '../../utils/cart'

const items = ref([])
const submitting = ref(false)
const form = reactive({ name: '', phone: '', address: '' })
const total = computed(() => items.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0))

onLoad(() => {
  items.value = getCart()
})

async function submit() {
  if (!form.name || !form.phone || !form.address) {
    uni.showToast({ title: '请填写完整收货信息', icon: 'none' })
    return
  }
  if (items.value.length === 0) {
    uni.showToast({ title: '购物车为空', icon: 'none' })
    return
  }

  submitting.value = true
  const payload = {
    customer: { ...form },
    items: items.value.map((item) => ({ product_id: item.product.id, quantity: item.quantity }))
  }

  try {
    const result = await request('order', { method: 'POST', data: payload, header: { 'Content-Type': 'application/json' } })
    clearCart()
    uni.showModal({ title: '提交成功', content: `订单号：${result.data.order_no}`, showCancel: false, success: () => uni.switchTab({ url: '/pages/index/index' }) })
  } catch (error) {
    uni.showToast({ title: 'PHP API 未连接', icon: 'none' })
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.page { padding: 34rpx 28rpx 60rpx; }
.heading { display: flex; justify-content: space-between; align-items: end; margin-bottom: 28rpx; }
.title { font-size: 42rpx; font-weight: 700; }
.muted, .hint { color: #8b94a3; font-size: 22rpx; }
.card { margin-bottom: 20rpx; padding: 26rpx; background: #fff; border-radius: 20rpx; }
.card-title { display: block; margin-bottom: 20rpx; font-size: 28rpx; font-weight: 700; }
.input, .textarea { width: 100%; margin-top: 16rpx; padding: 20rpx; border-radius: 12rpx; background: #f4f6f9; font-size: 26rpx; }
.textarea { min-height: 140rpx; }
.order-item { display: flex; justify-content: space-between; gap: 16rpx; padding: 16rpx 0; color: #5f6a7a; font-size: 24rpx; }
.item-name { flex: 1; }
.total { display: flex; justify-content: space-between; margin-top: 14rpx; padding-top: 22rpx; border-top: 1rpx solid #edf0f4; color: #222; font-size: 32rpx; font-weight: 700; }
.submit { height: 88rpx; line-height: 88rpx; }
.hint { display: block; margin-top: 20rpx; text-align: center; }
</style>
