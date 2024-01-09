<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group("api", function ($routes) {
  $routes->post('register', 'Auth::register');
  $routes->post('login', 'Auth::login');
  $routes->get('users/me', 'Auth::me');

  $routes->resource('mahasiswa', ['except' => 'new,edit', 'filter' => 'auth']);
  $routes->resource('photos', ['only' => ['show'], 'filter' => 'auth']);
});
