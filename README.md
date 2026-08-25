# 跨境动漫 IP 周边电商 - 最小可运行 Demo

这个 Demo 用于先验证客户需求中最核心的链路：

```text
uni-app 用户端 → PHP API → 商品数据
uni-app 用户端 → PHP API → 创建订单
```

已经包含：

- 首页商品列表
- 分类筛选和关键词搜索
- 商品详情
- 购物车
- 简单结算
- PHP 创建订单接口
- PHP 健康检查接口
- PHP 文件存储演示订单，不需要先安装 MySQL

## 目录

```text
demo-anime-shop/
├── backend/
│   ├── api.php
│   └── data/
│       ├── orders.json
│       └── products.json
└── frontend/
    ├── App.vue
    ├── main.js
    ├── manifest.json
    ├── pages.json
    ├── package.json
    ├── vite.config.js
    ├── pages/
    │   ├── index/index.vue
    │   ├── detail/detail.vue
    │   ├── cart/cart.vue
    │   └── order/order.vue
    └── utils/
        ├── api.js
        └── cart.js
```

## 运行方式一：HBuilderX

### 1. 启动 PHP API

在 VS Code 的终端中进入 Demo 根目录：

```powershell
cd D:\你的路径\demo-anime-shop
php -S 127.0.0.1:8000 -t backend
```

看到类似以下内容表示 PHP 服务已经启动：

```text
PHP 8.x Development Server started
Listening on http://127.0.0.1:8000
```

先在浏览器测试：

```text
http://127.0.0.1:8000/api.php?action=health
```

应该返回：

```json
{"ok":true,"service":"anime-shop-demo"}
```

### 2. 在 HBuilderX 中运行前端

1. 打开 HBuilderX。
2. 选择“文件 → 打开目录”。
3. 选择：

```text
demo-anime-shop/frontend
```

4. 选择“运行 → 运行到浏览器”。

页面打开后，可以浏览商品、加入购物车并提交演示订单。

## 运行方式二：VS Code + uni-app CLI

进入前端目录：

```powershell
cd D:\你的路径\demo-anime-shop\frontend
npm install
npm run dev:h5
```

如果之前已经执行过 `npm install`，修改依赖后请先清理旧的 Vue 2 依赖：

```powershell
cd D:\你的路径\demo-anime-shop\frontend
Remove-Item -Recurse -Force node_modules
Remove-Item -Force package-lock.json -ErrorAction SilentlyContinue
npm install
npm run dev:h5
```

本项目使用 Vue 3，并且 `@dcloudio/uni-app`、`@dcloudio/vite-plugin-uni` 和 `@dcloudio/uni-cli-shared` 必须使用同一套版本。不要把它们改成 `latest`，也不要执行 `npm audit fix --force`，否则可能破坏 uni-app 的版本匹配。

浏览器打开终端显示的地址，通常是：

```text
http://localhost:5173
```

PHP API 仍然需要单独运行：

```powershell
cd D:\你的路径\demo-anime-shop
php -S 127.0.0.1:8000 -t backend
```

## 真机测试

手机不能访问电脑的 `127.0.0.1`。需要在 `frontend/utils/api.js` 中把：

```javascript
export const API_BASE = 'http://127.0.0.1:8000'
```

改成电脑局域网 IP，例如：

```javascript
export const API_BASE = 'http://192.168.1.100:8000'
```

同时允许 Windows 防火墙开放 8000 端口：

```powershell
New-NetFirewallRule -DisplayName "PHP Demo 8000" -Direction Inbound -Protocol TCP -LocalPort 8000 -Action Allow
```

手机和电脑必须连接同一个 Wi-Fi。

## 后续升级方向

当前订单保存到 `backend/data/orders.json`，只是为了让 Demo 不依赖 MySQL。正式开发时应替换为：

- MySQL 用户、商品、SKU、库存和订单表
- Redis 购物车、库存锁和过期任务
- Laravel API
- 对象存储保存商品图片、付款凭证和 Invoice
- 登录、权限、支付和物流接口
