<?php
$framework_routes = new \Framework\Route;
$framework_routes->ajax('path_to_index_method_of_indexController');
$framework_routes->ajax('path_to_login_of_indexController', 'index/login');