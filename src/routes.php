<?php
use core\Router;
use src\controllers\LoginController;

$router = new Router();

$router->get('/', 'HomeController@index');

$router->get('/login', 'LoginController@signin');
$router->get('/sair', 'LoginController@logout');
$router->post('/login', 'LoginController@signinAction');

$router->get('/cadastro', 'LoginController@signup');
$router->post('/cadastro', 'LoginController@signupAction');

$router->post('/post/new', 'PostController@new');

$router->get('/perfil/{id}', 'ProfileController@index');
$router->get('/perfil', 'ProfileController@index');


// $router->get('/pesquisa');
// $router->get('/amigos');
// $router->get('/fotos');
// $router->get('/config');