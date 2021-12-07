<?php
//controla manualmente o cache da aplicação
//tem somente um output para otimizar recursos
ob_start();

require __DIR__ . '/vendor/autoload.php';

/**
 * BOOTSTRAP
 */
use Source\Core\Session;
use CoffeeCode\Router\Router;

$session = new Session();
$route = new Router(url(), ":");

/**
 * WEB ROUTES
 */
$route->namespace("Source\App");
$route->get("/", "Web:home");
$route->get("/sobre", "Web:about");


//Blog
$route->group("/blog");
$route->get("/", "Web:blog");
$route->get("/p/{page}", "Web:blog");
$route->get("/{uri}", "Web:blogPost");
$route->post("/buscar", "Web:blogSearch");
$route->get("/buscar/{terms}/{page}", "Web:blogSearch");

//Auth
$route->group(null);
$route->get("/entrar", "Web:login");
$route->post("/entrar", "Web:login");
$route->get("/cadastrar", "Web:register");
$route->post("/cadastrar", "Web:register");
$route->get("/recuperar", "Web:forget");
$route->post("/recuperar", "Web:forget");
$route->get("/recuperar/{code}", "Web:forgetReset");
$route->post("/recuperar/resetar", "Web:forgetReset");

//OptIn
$route->get("/confirma", "Web:confirm");
$route->get("/obrigado/{email}", "Web:success");

//Services
$route->get("/termos", "Web:terms");

/**
 * APP
 */
$route->group("/app");
$route->get("/", "App:home");
$route->get("/sair", "App:logout");

/**
 * ERROR ROUTES
 */
$route->namespace("Source\App")->group("/ops");
$route->get("/{errcode}", "Web:error");

/**
 * ROUTE
 */
$route->dispatch();

/**
 * ERROR REDIRECT
 */
if ($route->error()) {
  $route->redirect("/ops/{$route->error()}");
}

//termina o output - conclui/desarmazena o cache
ob_end_flush();
