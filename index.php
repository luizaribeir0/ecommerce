<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;

$app = new Slim();

$app->config('debug', true);

require_once ("site.php");
require_once ("admin.php");
require_once ("admin-user.php");

$app->get('/admin/forgot', function () {
    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl("forgot");
});

$app->post('/admin/forgot', function () {
    $user = User::getForgot($_POST["email"]);
    header("Location: /admin/forgot/sent");
});

$app->get('/admin/forgot/sent', function () {
    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl("forgot-sent");
});

$app->get('/admin/forgot/reset', function () {
    $user = User::validForgotDecrypt($_GET["code"]);
    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl("forgot-reset", array(
        "name" => $user["desperson"],
        "code" => $_GET["code"]
    ));
});

$app->post('/admin/forgot/reset', function () {
    $forgot = User::validForgotDecrypt($_POST["code"]);
    User::setForgotUsed($forgot["idrecovery"]);
    $user = new User();
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT, [
        "cost" => 12
    ]);
    $user->get((int)$forgot["iduser"]);
    $user->setPassword($password);

    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);
    $page->setTpl("forgot-reset-success");
});

$app->get('/admin/categories', function () {
    User::verifyLogin();
    $categories = Category::listAll();
    $page = new PageAdmin();
    $page->setTpl("categories", [
        "categories" => $categories
    ]);
});

$app->get('/admin/categories/create', function () {
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("categories-create");
});

$app->post('/admin/categories/create', function () {
    User::verifyLogin();
    $category = new Category();
    $category->setData($_POST);
    $category->save();
    header("Location: /admin/categories");
    exit;
});

$app->get('/admin/categories/:idcategory/delete', function ($idcategory) {
    User::verifyLogin();
    $category = new Category();
    $category->get((int)$idcategory);
    $category->delete();
    header("Location: /admin/categories");
    exit;
});

$app->get('/admin/categories/:idcategory', function ($idcategory) {
    User::verifyLogin();
    $category = new Category();
    $category->get((int)$idcategory);
    $page = new PageAdmin();
    $page->setTpl("categories-update", [
        'category' => $category->getValues()
    ]);
});

$app->post('/admin/categories/:idcategory', function ($idcategory) {
    User::verifyLogin();
    $category = new Category();
    $category->get((int)$idcategory);
    $category->setData($_POST);
    $category->save();
    header("Location: /admin/categories");
    exit;
});

$app->get('/categories/:idcategory', function ($idcategory) {
   $category = new Category();
   $category->get((int)$idcategory);
    $page = new Page();
    $page->setTpl("category", [
        'category' => $category->getValues(),
        'products' => []
    ]);
});

$app->run();

?>