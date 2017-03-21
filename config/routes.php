<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $routes->get('/recipi', function() {
    HelloWorldController::recipe_list();
  });
  
  $routes->get('/recipi/1', function() {
    HelloWorldController::recipe_show();
  });
  
  $routes->get('/login', function() {
    HelloWorldController::sign_in();
  });
  
  $routes->get('/register', function() {
    HelloWorldController::sign_up();
  });
  
  $routes->get('/settings', function() {
    HelloWorldController::change_settings();
  });
  
  $routes->get('/suggest', function() {
    HelloWorldController::recipe_suggest();
  });
  
  $routes->get('/suggestion', function() {
    HelloWorldController::suggest_list();
  });
  
  $routes->get('/user', function() {
    HelloWorldController::user_list();
  });
  
  $routes->get('/recipe/1/edit', function() {
    HelloWorldController::recipi_edit();
  });
