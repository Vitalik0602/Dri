<?php
require_once __DIR__ . '/db.php';

// === БЕЗОПАСНОСТЬ ===
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): void {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die('Недопустимый запрос.');
    }
}

// === АВТОРИЗАЦИЯ ===
function is_admin(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_admin(): void {
    if (!is_admin()) {
        header('Location: /admin/login.php');
        exit;
    }
}

// === АВТО: РАБОТА С БД ===
function get_cars(array $filters = [], int $limit = 20, int $offset = 0): array {
    $where = ['c.status = "active"'];
    $params = [];

    if (!empty($filters['brand'])) {
        $where[] = 'c.brand = ?';
        $params[] = $filters['brand'];
    }
    if (!empty($filters['price_min'])) {
        $where[] = 'c.price >= ?';
        $params[] = (int)$filters['price_min'];
    }
    if (!empty($filters['price_max'])) {
        $where[] = 'c.price <= ?';
        $params[] = (int)$filters['price_max'];
    }
    if (!empty($filters['year_from'])) {
        $where[] = 'c.year >= ?';
        $params[] = (int)$filters['year_from'];
    }
    if (!empty($filters['year_to'])) {
        $where[] = 'c.year <= ?';
        $params[] = (int)$filters['year_to'];
    }
    if (!empty($filters['body_type'])) {
        $where[] = 'c.body_type = ?';
        $params[] = $filters['body_type'];
    }
    if (!empty($filters['transmission'])) {
        $where[] = 'c.transmission = ?';
        $params[] = $filters['transmission'];
    }

    $whereStr = implode(' AND ', $where);
    $orderBy = match($filters['sort'] ?? '') {
        'price_asc'  => 'c.price ASC',
        'price_desc' => 'c.price DESC',
        'year_desc'  => 'c.year DESC',
        'mileage'    => 'c.mileage ASC',
        default      => 'c.is_featured DESC, c.created_at DESC',
    };

    $sql = "SELECT c.*, 
                   (SELECT image_path FROM car_images WHERE car_id = c.id AND is_main = 1 LIMIT 1) as main_image
            FROM cars c
            WHERE $whereStr
            ORDER BY $orderBy
            LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    return db()->fetchAll($sql, $params);
}

function count_cars(array $filters = []): int {
    $where = ['status = "active"'];
    $params = [];
    if (!empty($filters['brand'])) { $where[] = 'brand = ?'; $params[] = $filters['brand']; }
    if (!empty($filters['price_min'])) { $where[] = 'price >= ?'; $params[] = (int)$filters['price_min']; }
    if (!empty($filters['price_max'])) { $where[] = 'price <= ?'; $params[] = (int)$filters['price_max']; }
    $whereStr = implode(' AND ', $where);
    $row = db()->fetchOne("SELECT COUNT(*) as cnt FROM cars WHERE $whereStr", $params);
    return (int)($row['cnt'] ?? 0);
}

function get_car_by_slug(string $slug): array|false {
    return db()->fetchOne(
        "SELECT * FROM cars WHERE slug = ? AND status = 'active' LIMIT 1",
        [$slug]
    );
}

function get_car_images(int $car_id): array {
    return db()->fetchAll(
        "SELECT * FROM car_images WHERE car_id = ? ORDER BY is_main DESC, sort_order ASC",
        [$car_id]
    );
}

function get_car_main_image(int $car_id): string {
    $row = db()->fetchOne(
        "SELECT image_path FROM car_images WHERE car_id = ? AND is_main = 1 LIMIT 1",
        [$car_id]
    );
    return $row ? $row['image_path'] : '/assets/images/no-car.jpg';
}

function get_featured_cars(int $limit = 6): array {
    return db()->fetchAll(
        "SELECT c.*, (SELECT image_path FROM car_images WHERE car_id = c.id AND is_main = 1 LIMIT 1) as main_image
         FROM cars c WHERE c.status = 'active'
         ORDER BY c.is_featured DESC, c.created_at DESC LIMIT ?",
        [$limit]
    );
}

function get_brands(): array {
    return db()->fetchAll(
        "SELECT brand, COUNT(*) as cnt FROM cars WHERE status = 'active' GROUP BY brand ORDER BY brand"
    );
}

// === ФОРМАТИРОВАНИЕ ===
function format_price(int $price): string {
    return number_format($price, 0, ',', ' ') . ' ₽';
}

function format_mileage(int $km): string {
    return number_format($km, 0, ',', ' ') . ' км';
}

function make_slug(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $translit = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
        'ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m',
        'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u',
        'ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch',
        'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        ' '=>'-', '_'=>'-',
    ];
    $text = strtr($text, $translit);
    $text = preg_replace('/[^a-z0-9\-]/', '', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

// === ЗАГРУЗКА ИЗОБРАЖЕНИЙ ===
function upload_image(array $file, string $prefix = 'car'): string|false {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > MAX_FILE_SIZE) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) return false;

    $name = $prefix . '_' . uniqid() . '_' . time() . '.' . $ext;
    $dest = UPLOAD_DIR . $name;

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

    return $name;
}

// === REDIRECT ===
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

// === FLASH MESSAGES ===
function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): array|null {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// === ЗАЯВКИ ===
function save_request(array $data): bool {
    try {
        db()->insert('requests', [
            'car_id'     => $data['car_id'] ?? null,
            'name'       => $data['name'],
            'phone'      => $data['phone'],
            'message'    => $data['message'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'is_read'    => 0,
        ]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}
