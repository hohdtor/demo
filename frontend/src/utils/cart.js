const CART_KEY = 'anime_shop_demo_cart'

export function getCart() {
  return uni.getStorageSync(CART_KEY) || []
}

export function saveCart(cart) {
  uni.setStorageSync(CART_KEY, cart)
}

export function addToCart(product, quantity = 1) {
  const cart = getCart()
  const existing = cart.find((item) => item.product.id === product.id)

  if (existing) {
    existing.quantity += quantity
  } else {
    cart.push({ product, quantity })
  }

  saveCart(cart)
  return cart
}

export function cartCount() {
  return getCart().reduce((sum, item) => sum + item.quantity, 0)
}

export function cartTotal() {
  return getCart().reduce((sum, item) => sum + item.product.price * item.quantity, 0)
}

export function clearCart() {
  saveCart([])
}
