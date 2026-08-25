<template>
  <view class="page">
    <view class="hero">
      <view>
        <text class="eyebrow">JAPAN → GLOBAL</text>
        <text class="title">动漫周边商城</text>
        <text class="subtitle">正版 IP · 日本采购 · 全球配送</text>
      </view>
      <view class="cart-button" @click="goCart">
        <text>购物车</text>
        <text v-if="cartNumber" class="badge">{{ cartNumber }}</text>
      </view>
    </view>

    <view class="api-state" :class="apiOnline ? 'online' : 'offline'">
      <view class="state-dot"></view>
      <text>{{ apiOnline ? 'PHP API 已连接' : '演示数据模式：请启动 PHP API' }}</text>
    </view>

    <view class="search-box">
      <text class="search-icon">⌕</text>
      <input v-model="keyword" placeholder="搜索商品、品牌或 IP" confirm-type="search" @confirm="loadProducts" />
      <text class="search-action" @click="loadProducts">搜索</text>
    </view>

    <scroll-view scroll-x class="category-scroll" :show-scrollbar="false">
      <view class="categories">
        <view v-for="item in categories" :key="item" class="category" :class="selectedCategory === item ? 'active' : ''" @click="selectCategory(item)">
          {{ item }}
        </view>
      </view>
    </scroll-view>

    <view class="section-heading">
      <view>
        <text class="section-title">推荐商品</text>
        <text class="section-note">精选日本动漫 IP 周边</text>
      </view>
      <text class="count">{{ products.length }} 件</text>
    </view>

    <view v-if="loading" class="empty">正在加载商品…</view>
    <view v-else-if="products.length === 0" class="empty">没有找到匹配商品</view>
    <view v-else class="product-grid">
      <view v-for="product in products" :key="product.id" class="product-card" @click="openDetail(product)">
        <image class="product-image" :src="product.image" mode="aspectFill" />
        <view class="product-body">
          <text class="product-category">{{ product.category }} · {{ product.brand }}</text>
          <text class="product-name">{{ product.name }}</text>
          <view class="price-row">
            <text class="price">¥{{ product.price }}</text>
            <text class="market-price">¥{{ product.market_price }}</text>
          </view>
          <view class="card-footer">
            <text class="stock">库存 {{ product.stock }}</text>
            <view class="add-button" @click.stop="addProduct(product)">+</view>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup>
import { ref } from 'vue'
import { onLoad, onShow } from '@dcloudio/uni-app'
import { request, fallbackProducts } from '../../utils/api'
import { addToCart, cartCount } from '../../utils/cart'

const products = ref([])
const categories = ref(['全部'])
const selectedCategory = ref('全部')
const keyword = ref('')
const loading = ref(false)
const apiOnline = ref(false)
const cartNumber = ref(0)

async function loadProducts() {
  loading.value = true
  try {
    const query = `&keyword=${encodeURIComponent(keyword.value)}&category=${encodeURIComponent(selectedCategory.value === '全部' ? '' : selectedCategory.value)}`
    const result = await request(`products${query}`)
    products.value = result.data || []
    apiOnline.value = true
  } catch (error) {
    apiOnline.value = false
    const category = selectedCategory.value
    products.value = fallbackProducts.filter((product) => {
      const byCategory = category === '全部' || product.category === category
      const text = `${product.name}${product.brand}`.toLowerCase()
      return byCategory && text.includes(keyword.value.toLowerCase())
    })
  } finally {
    loading.value = false
  }
}

async function loadCategories() {
  try {
    const result = await request('categories')
    categories.value = ['全部', ...(result.data || [])]
  } catch (error) {
    categories.value = ['全部', ...new Set(fallbackProducts.map((product) => product.category))]
  }
}

function selectCategory(category) {
  selectedCategory.value = category
  loadProducts()
}

function addProduct(product) {
  addToCart(product)
  cartNumber.value = cartCount()
  uni.showToast({ title: '已加入购物车', icon: 'success' })
}

function openDetail(product) {
  uni.navigateTo({ url: `/pages/detail/detail?id=${product.id}` })
}

function goCart() {
  uni.switchTab({ url: '/pages/cart/cart' })
}

onLoad(() => {
  loadCategories()
  loadProducts()
})

onShow(() => {
  cartNumber.value = cartCount()
})
</script>

<style scoped>
.page { padding: 28rpx 28rpx 60rpx; }
.hero { display: flex; justify-content: space-between; align-items: center; padding: 38rpx 30rpx; background: linear-gradient(135deg, #102a56, #1f6feb); border-radius: 28rpx; color: #fff; box-shadow: 0 18rpx 40rpx rgba(31, 111, 235, .2); }
.eyebrow { display: block; font-size: 20rpx; letter-spacing: 3rpx; opacity: .7; }
.title { display: block; margin-top: 14rpx; font-size: 46rpx; font-weight: 700; }
.subtitle { display: block; margin-top: 10rpx; font-size: 24rpx; opacity: .85; }
.cart-button { position: relative; padding: 18rpx 20rpx; background: rgba(255,255,255,.16); border-radius: 16rpx; font-size: 24rpx; }
.badge { position: absolute; top: -14rpx; right: -14rpx; min-width: 34rpx; height: 34rpx; padding: 0 8rpx; border-radius: 20rpx; background: #ff5b62; color: #fff; font-size: 20rpx; line-height: 34rpx; text-align: center; }
.api-state { display: flex; align-items: center; gap: 12rpx; margin: 24rpx 4rpx 18rpx; font-size: 22rpx; color: #7a8494; }
.api-state.online { color: #149447; }
.state-dot { width: 14rpx; height: 14rpx; border-radius: 50%; background: #f2aa21; }
.online .state-dot { background: #20b56b; }
.search-box { display: flex; align-items: center; gap: 14rpx; padding: 20rpx 22rpx; background: #fff; border-radius: 18rpx; }
.search-icon { font-size: 38rpx; color: #8a93a2; }
.search-box input { flex: 1; font-size: 26rpx; }
.search-action { color: #1f6feb; font-size: 24rpx; }
.category-scroll { margin-top: 24rpx; white-space: nowrap; }
.categories { display: flex; gap: 16rpx; }
.category { padding: 14rpx 26rpx; border-radius: 30rpx; background: #e9edf4; color: #687386; font-size: 24rpx; }
.category.active { background: #1f6feb; color: #fff; }
.section-heading { display: flex; justify-content: space-between; align-items: end; margin: 36rpx 4rpx 22rpx; }
.section-title { display: block; font-size: 34rpx; font-weight: 700; }
.section-note { display: block; margin-top: 8rpx; color: #8b94a3; font-size: 22rpx; }
.count { color: #8b94a3; font-size: 22rpx; }
.product-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20rpx; }
.product-card { overflow: hidden; background: #fff; border-radius: 20rpx; box-shadow: 0 8rpx 24rpx rgba(30, 55, 90, .06); }
.product-image { width: 100%; height: 260rpx; background: #e7ebf2; }
.product-body { padding: 20rpx; }
.product-category { display: block; color: #1f6feb; font-size: 19rpx; }
.product-name { display: block; height: 72rpx; margin-top: 10rpx; font-size: 26rpx; font-weight: 600; line-height: 36rpx; }
.price-row { display: flex; align-items: baseline; gap: 12rpx; margin-top: 14rpx; }
.price { color: #ef4d56; font-size: 30rpx; font-weight: 700; }
.market-price { color: #a9b0bb; font-size: 20rpx; text-decoration: line-through; }
.card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 16rpx; }
.stock { color: #919aa8; font-size: 20rpx; }
.add-button { width: 48rpx; height: 48rpx; border-radius: 50%; background: #e8f0ff; color: #1f6feb; font-size: 36rpx; line-height: 43rpx; text-align: center; }
.empty { padding: 80rpx 0; color: #8b94a3; text-align: center; }
</style>
