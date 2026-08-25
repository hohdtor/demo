<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$dataDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'data';
$productsFile = $dataDirectory . DIRECTORY_SEPARATOR . 'products.json';
$ordersFile = $dataDirectory . DIRECTORY_SEPARATOR . 'orders.json';

function respond(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function readJsonFile(string $filename, array $fallback = []): array
{
    if (!is_file($filename)) {
        return $fallback;
    }

    $contents = file_get_contents($filename);
    $decoded = json_decode($contents ?: '', true);
    return is_array($decoded) ? $decoded : $fallback;
}

function writeJsonFile(string $filename, array $data): void
{
    file_put_contents(
        $filename,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        LOCK_EX
    );
}

$products = readJsonFile($productsFile);
$action = $_GET['action'] ?? 'products';

if ($action === 'health') {
    respond(['ok' => true, 'service' => 'anime-shop-demo']);
}

if ($action === 'categories') {
    $categories = array_values(array_unique(array_map(
        static fn (array $product): string => (string) $product['category'],
        $products
    )));

    respond(['ok' => true, 'data' => $categories]);
}

if ($action === 'product') {
    $id = (int) ($_GET['id'] ?? 0);
    foreach ($products as $product) {
        if ((int) $product['id'] === $id) {
            respond(['ok' => true, 'data' => $product]);
        }
    }

    respond(['ok' => false, 'message' => '商品不存在'], 404);
}

if ($action === 'products') {
    $keyword = trim((string) ($_GET['keyword'] ?? ''));
    $category = trim((string) ($_GET['category'] ?? ''));

    $filtered = array_values(array_filter($products, static function (array $product) use ($keyword, $category): bool {
        $matchesKeyword = $keyword === ''
            || str_contains(mb_strtolower($product['name']), mb_strtolower($keyword))
            || str_contains(mb_strtolower($product['brand']), mb_strtolower($keyword));

        $matchesCategory = $category === '' || $product['category'] === $category;
        return $matchesKeyword && $matchesCategory;
    }));

    respond(['ok' => true, 'data' => $filtered]);
}

if ($action === 'order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input') ?: '', true);
    if (!is_array($body)) {
        respond(['ok' => false, 'message' => '请求数据格式错误'], 422);
    }

    $customer = $body['customer'] ?? [];
    $items = $body['items'] ?? [];

    if (trim((string) ($customer['name'] ?? '')) === '' || count($items) === 0) {
        respond(['ok' => false, 'message' => '请填写姓名并至少选择一件商品'], 422);
    }

    $productMap = [];
    foreach ($products as $product) {
        $productMap[(int) $product['id']] = $product;
    }

    $orderItems = [];
    $total = 0.0;

    foreach ($items as $item) {
        $productId = (int) ($item['product_id'] ?? 0);
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        if (!isset($productMap[$productId])) {
            respond(['ok' => false, 'message' => '订单中包含不存在的商品'], 422);
        }

        $product = $productMap[$productId];
        $lineTotal = round((float) $product['price'] * $quantity, 2);
        $total += $lineTotal;
        $orderItems[] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'quantity' => $quantity,
            'unit_price' => $product['price'],
            'line_total' => $lineTotal,
        ];
    }

    $orders = readJsonFile($ordersFile);
    $order = [
        'order_no' => 'DEMO-' . date('YmdHis') . '-' . random_int(100, 999),
        'status' => 'PENDING_PAYMENT',
        'currency' => 'JPY',
        'customer' => [
            'name' => trim((string) ($customer['name'] ?? '')),
            'phone' => trim((string) ($customer['phone'] ?? '')),
            'address' => trim((string) ($customer['address'] ?? '')),
        ],
        'items' => $orderItems,
        'total' => round($total, 2),
        'created_at' => date(DATE_ATOM),
    ];

    $orders[] = $order;
    writeJsonFile($ordersFile, $orders);

    respond(['ok' => true, 'message' => '演示订单创建成功', 'data' => $order], 201);
}

respond(['ok' => false, 'message' => '未知操作'], 404);

