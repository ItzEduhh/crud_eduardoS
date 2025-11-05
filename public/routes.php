<?php

use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\AutorController;
use App\Controllers\Admin\MusicController;
use App\Controllers\Admin\ProducerController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\UserController;
use App\Controllers\AuthController;
use App\Controllers\SiteController;
use App\Middleware\AuthMiddleware;
use Symfony\Component\HttpFoundation\Request;

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $routeCollector) {
    // Index Site
    $routeCollector->addGroup('/', function (FastRoute\RouteCollector $site) {
        $site->addRoute('GET', '', [SiteController::class, 'index']);
    });

    // Autenticação
    $routeCollector->addGroup('/auth', function (FastRoute\RouteCollector $auth) {
        $auth->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
        $auth->addRoute('GET', '/create', [AuthController::class, 'create']);
        $auth->addRoute('POST', '/login', [AuthController::class, 'login']);
        $auth->addRoute('POST', '/logout', [AuthController::class, 'logout']);
    });

    $routeCollector->addGroup('/admin', function (FastRoute\RouteCollector $group) {
        // Home Admin
        $group->addGroup('', function (FastRoute\RouteCollector $admin) {
            $admin->addRoute('GET', '', [AdminController::class, 'index']);
        });

        // Autores
        $group->addGroup('/autors', function (FastRoute\RouteCollector $autors) {
            $autors->addRoute('GET', '', [AutorController::class, 'index']);
            $autors->addRoute('GET', '/create', [AutorController::class, 'create']);
            $autors->addRoute('POST', '/store', [AutorController::class, 'store']);
            $autors->addRoute('GET', '/show', [AutorController::class, 'show']);
            $autors->addRoute('GET', '/edit', [AutorController::class, 'edit']);
            $autors->addRoute('POST', '/update', [AutorController::class, 'update']);
            $autors->addRoute('POST', '/delete', [AutorController::class, 'delete']);
        });

        // Músicas
        $group->addGroup('/musics', function (FastRoute\RouteCollector $musics) {
            $musics->addRoute('GET', '', [MusicController::class, 'index']);
            $musics->addRoute('GET', '/create', [MusicController::class, 'create']);
            $musics->addRoute('POST', '/store', [MusicController::class, 'store']);
            $musics->addRoute('GET', '/show', [MusicController::class, 'show']);
            $musics->addRoute('GET', '/edit', [MusicController::class, 'edit']);
            $musics->addRoute('POST', '/update', [MusicController::class, 'update']);
            $musics->addRoute('POST', '/delete', [MusicController::class, 'delete']);
        });

        // Produtoras
        $group->addGroup('/producers', function (FastRoute\RouteCollector $producers) {
            $producers->addRoute('GET', '', [ProducerController::class, 'index']);
            $producers->addRoute('GET', '/create', [ProducerController::class, 'create']);
            $producers->addRoute('POST', '/store', [ProducerController::class, 'store']);
            $producers->addRoute('GET', '/show', [ProducerController::class, 'show']);
            $producers->addRoute('GET', '/edit', [ProducerController::class, 'edit']);
            $producers->addRoute('POST', '/update', [ProducerController::class, 'update']);
            $producers->addRoute('POST', '/delete', [ProducerController::class, 'delete']);
        });

        // Produtos
        $group->addGroup('/products', function (FastRoute\RouteCollector $products) {
            $products->addRoute('GET', '', [ProductController::class, 'index']);
            $products->addRoute('GET', '/create', [ProductController::class, 'create']);
            $products->addRoute('POST', '/store', [ProductController::class, 'store']);
            $products->addRoute('GET', '/show', [ProductController::class, 'show']);
            $products->addRoute('GET', '/edit', [ProductController::class, 'edit']);
            $products->addRoute('POST', '/update', [ProductController::class, 'update']);
            $products->addRoute('POST', '/delete', [ProductController::class, 'delete']);
        });

        // Categorias
        $group->addGroup('/categories', function (FastRoute\RouteCollector $categories) {
            $categories->addRoute('GET', '', [CategoryController::class, 'index']);
            $categories->addRoute('GET', '/create', [CategoryController::class, 'create']);
            $categories->addRoute('POST', '/store', [CategoryController::class, 'store']);
            $categories->addRoute('GET', '/show', [CategoryController::class, 'show']);
            $categories->addRoute('GET', '/edit', [CategoryController::class, 'edit']);
            $categories->addRoute('POST', '/update', [CategoryController::class, 'update']);
            $categories->addRoute('POST', '/delete', [CategoryController::class, 'delete']);
        });

        // Usuários
        $group->addGroup('/users', function (FastRoute\RouteCollector $users) {
            $users->addRoute('GET', '', [UserController::class, 'index']);
            $users->addRoute('GET', '/create', [UserController::class, 'create']);
            $users->addRoute('POST', '/store', [UserController::class, 'store']);
            $users->addRoute('GET', '/show', [UserController::class, 'show']);
//            $users->addRoute('GET', '/edit', [UserController::class, 'edit']);
//            $users->addRoute('POST', '/update', [UserController::class, 'update']);
            $users->addRoute('POST', '/delete', [UserController::class, 'delete']);
        });

    });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) $uri = substr($uri, 0, $pos);
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$request = Request::createFromGlobals();

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405';
        break;
    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $controller = new $class();

        // Módulos protegidos
        $protectedRoutes = [
            '/admin',
        ];

        // Se a rota começar com alguma dessas, exige login
        foreach ($protectedRoutes as $prefix) {
            if (str_starts_with($uri, $prefix)) {
                $redirect = AuthMiddleware::requireLogin();
                if ($redirect) { $redirect->send(); exit; }
                break;
            }
        }

        $response = $controller->$method($request);
        $response->send();
        break;
}