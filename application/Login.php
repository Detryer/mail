<?php

namespace Application;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$_SESSION['base'] = 'line';
$_SESSION['logged_user'] = true;
$_SESSION['user_id'] = 1;
Route::start();