<?php

declare(strict_types=1);

error_reporting(E_ERROR);
ini_set('display_errors', '1');

require_once __DIR__ . '/core/db.php';
require_once __DIR__ . '/core/smarty.php';
require_once __DIR__ . '/Repository/CategoryRepository.php';
require_once __DIR__ . '/Repository/PostRepository.php';

$pdo          = get_pdo();
$smarty       = get_smarty();
$categoryRepo = new CategoryRepository($pdo);
$postRepo     = new PostRepository($pdo);

// роутинг
$route = $_GET['route'] ?? 'home';
switch ($route) {
    case 'home':
        $categories = $categoryRepo->getCategoriesWithRecentPosts(3);
        $smarty->assign('title', 'Блог — главная');
        $smarty->assign('categories', $categories);
        $smarty->display('home.tpl');
        break;

    case 'category':
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($categoryId <= 0) {
            http_response_code(404);
            echo 'Category not found';
            break;
        }

        $category = $categoryRepo->getCategoryById($categoryId);
        if ($category === null) {
            http_response_code(404);
            echo 'Category not found';
            break;
        }

        $sort = ($_GET['sort'] ?? 'date') === 'views' ? 'views' : 'date';
        $page = max(1, (int)($_GET['page'] ?? 1));

        $total      = $postRepo->countPostsByCategory($categoryId);
        $totalPages = max(1, (int)ceil($total / $categoryRepo::POSTS_PER_PAGE));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $categoryRepo::POSTS_PER_PAGE;

        $posts = $postRepo->getPostsByCategory($categoryId, $sort, $categoryRepo::POSTS_PER_PAGE, $offset);

        $smarty->assign('title', 'Блог — ' . $category['name']);
        $smarty->assign('category', $category);
        $smarty->assign('posts', $posts);
        $smarty->assign('sort', $sort);
        $smarty->assign('currentPage', $page);
        $smarty->assign('totalPages', $totalPages);
        $smarty->display('category.tpl');
        break;

    case 'post':
        $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($postId <= 0) {
            http_response_code(404);
            echo 'Post not found';
            break;
        }

        $post = $postRepo->getPostById($postId);
        if ($post === null) {
            http_response_code(404);
            echo 'Post not found';
            break;
        }

        $postRepo->incrementViews($postId);
        $similarPosts = $postRepo->getSimilarPosts($postId, 3);
        $postCategories = $postRepo->getCategoriesByPostId($postId);
        $smarty->assign('title', 'Блог — '.$post['title']);
        $smarty->assign('post', $post);
        $smarty->assign('postCategories', $postCategories);
        $smarty->assign('similarPosts', $similarPosts);
        $smarty->display('post.tpl');
        break;

    case 'seed':
        require __DIR__ . '/seed.php';
        break;

    default:
        http_response_code(404);
        echo 'Page not found';
}