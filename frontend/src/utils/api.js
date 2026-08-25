export const API_BASE = 'http://127.0.0.1:8000'

export function request(action, options = {}) {
  return new Promise((resolve, reject) => {
    uni.request({
      url: `${API_BASE}/api.php?action=${action}`,
      timeout: 8000,
      ...options,
      success: (response) => {
        const body = response.data || {}
        if (body.ok === false) {
          reject(new Error(body.message || 'API 请求失败'))
          return
        }
        resolve(body)
      },
      fail: reject
    })
  })
}

export const fallbackProducts = [
  {
    id: 1,
    name: '宝可梦皮卡丘收藏手办',
    brand: 'Pokémon',
    category: '宝可梦',
    price: 6800,
    market_price: 8200,
    stock: 18,
    description: '日本采购正版收藏手办，适合收藏和展示。',
    image: 'https://images.unsplash.com/photo-1607453998774-d533f65dac99?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 2,
    name: '海贼王路飞限定模型',
    brand: 'ONE PIECE',
    category: '海贼王',
    price: 12800,
    market_price: 15000,
    stock: 8,
    description: '海贼王系列限定模型，数量有限。',
    image: 'https://images.unsplash.com/photo-1612036782180-6f0b6cd846fe?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 3,
    name: '高达 RX-78-2 拼装模型',
    brand: 'Bandai',
    category: '高达',
    price: 5600,
    market_price: 6400,
    stock: 25,
    description: '经典高达拼装模型，支持多种展示姿态。',
    image: 'https://images.unsplash.com/photo-1560942485-b2a11cc13456?auto=format&fit=crop&w=800&q=80'
  },
  {
    id: 4,
    name: '七龙珠孙悟空收藏卡',
    brand: 'Dragon Ball',
    category: '七龙珠',
    price: 3200,
    market_price: 3900,
    stock: 40,
    description: '七龙珠主题收藏卡，适合入门收藏。',
    image: 'https://images.unsplash.com/photo-1613771404721-1f92d799e49f?auto=format&fit=crop&w=800&q=80'
  }
]
