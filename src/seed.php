<?php
/**
 * внешние API могут подтрмаживать
 */

declare(strict_types=1);

require_once __DIR__ . '/core/db.php';
$pdo = get_pdo();

// т.к. админки в проекте нет, захардкодим путь для img src здесь
const IMG_DIR = __DIR__ . '/assets/images/posts/';

function download_random_image(string $baseUrl, string $targetDir, int $index): string
{
    // мы конечно умеем хранить блобы в базе, но не будем этого делать
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $targetDir));
        }
    }

    $filename = sprintf('post-%d-%s.jpg', $index, bin2hex(random_bytes(4)));
    $targetPath = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    $imageData = file_get_contents($baseUrl);
    if ($imageData === false) {
        throw new RuntimeException('Failed to download image from ' . $baseUrl);
    }

    if (file_put_contents($targetPath, $imageData) === false) {
        throw new RuntimeException('Failed to write image file to ' . $targetPath);
    }

    return IMG_DIR . $filename;
}


// очищаем таблицы
$pdo->exec('DELETE FROM post_category');
$pdo->exec('DELETE FROM posts');
$pdo->exec('DELETE FROM categories');

// старые картинки тоже убьем
if (is_dir(IMG_DIR)) {
    $files = glob(IMG_DIR . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

$categories = [
    ['name' => 'PHP', 'description' => 'Статьи о PHP и бекенде'],
    ['name' => 'MySQL', 'description' => 'Работа с базами данных MySQL'],
    ['name' => 'Веб-разработка', 'description' => 'Общие темы фронтенда и бэкенда'],
];

$insertCategory = $pdo->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');

foreach ($categories as $cat) {
    $insertCategory->execute($cat);
}

$categoryIds = $pdo->query('SELECT id FROM categories')->fetchAll(PDO::FETCH_COLUMN);

$insertPost = $pdo->prepare('INSERT INTO posts (title, slug, image, short_description, content, views, published_at) VALUES (:title, :slug, :image, :short_description, :content, :views, :published_at)');
$insertPostCategory = $pdo->prepare('INSERT INTO post_category (post_id, category_id) VALUES (:post_id, :category_id)');

for ($i = 1; $i <= 20; $i++) {
    $title = file_get_contents("https://fish-text.ru/get?type=title&format=html");
    $imagePath = download_random_image('https://picsum.photos/400/300', __DIR__.DIRECTORY_SEPARATOR.IMG_DIR, $i);
    $content = file_get_contents("https://fish-text.ru/get?type=paragraph&number=10&format=html");

    $insertPost->execute([
        'title'             => sanitize($title),
        'slug'              => 'article-' . $i,
        'image'             => sanitize(basename($imagePath)),
        'short_description' => 'Краткое описание статьи №' . $i,
        'content'           => sanitize($content),
        'views'             => rand(0, 100),
        'published_at'      => date('Y-m-d H:i:s', strtotime("-{$i} days")),
    ]);

    $postId = (int)$pdo->lastInsertId();
    $catIndex = ($i - 1) % count($categoryIds);
    $catId    = (int)$categoryIds[$catIndex];

    $insertPostCategory->execute([
        'post_id'     => $postId,
        'category_id' => $catId,
    ]);
}

header('Location: /');
//echo "Seeding completed\n";
