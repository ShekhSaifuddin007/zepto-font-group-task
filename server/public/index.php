<?php // php -S localhost:8000 -t public

/**
 * Request class has a global function called request(),
 * you can call it anywhere in application
 */


use App\Controllers\FontGroupController;
use App\Controllers\TtfFileHandlerController;
use App\Core\Application;

require __DIR__.'/../vendor/autoload.php';

session_start();

const BASE_URL = 'http://localhost:8000';

$app = new Application;

$app->cors();

// just pass view name in second argument, from views folder it will render the view

$app->router->get('/ttf-files', [TtfFileHandlerController::class, 'index']);
$app->router->post('/ttf-files', [TtfFileHandlerController::class, 'store']);
$app->router->post('/delete/file', [TtfFileHandlerController::class, 'delete']);


$app->router->get('/font-groups', [FontGroupController::class, 'index']);
$app->router->post('/font-groups', [FontGroupController::class, 'store']);
$app->router->get('/get-font-groups', [FontGroupController::class, 'show']);
$app->router->get('/delete/font-group-title', [FontGroupController::class, 'delete']);
$app->router->post('/update-font-groups', [FontGroupController::class, 'update']);
$app->router->get('/delete/font-group', [FontGroupController::class, 'deleteRow']);

$app->run();