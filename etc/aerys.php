<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 20/07/2016
 * Time: 16:58
 */
use Aerys\{Request, function router};
use Amp\Mysql\Pool;
use Auryn\Injector;

$mysqlConfig = sprintf(
    "host=%s;user=%s;pass=%s;db=%s",
    config("database.host"),
    config("database.user"),
    config("database.pass"),
    config("database.name")
);

$redisUri = config("redis.protocol") . "://" . config("redis.host") . ":" . config("redis.port");

// Defining Dependency Injections
$injector = new Injector();
$injector->share(new Pool($mysqlConfig));
$injector->share(new \Amp\Redis\Client($redisUri));
// Ok, this is a test and a bad practise. Try to remove it.
// For the moment, it is used in Bot.php for initializing Commands.
$injector->share($injector);

$injector->alias(\Slamp\Web\Storage\UserStorage::class, \Slamp\Web\Storage\MysqlUserStorage::class);

$dispatcher = $injector->make(\Slamp\Web\Dispatcher::class);

$router = router();
$routes = json_decode(file_get_contents(__DIR__ . "/../res/routes.json"));
foreach ($routes as $route) {
    $router->route($route->method, $route->uri, function (Request $request) use ($route) {
        $request->setLocalVar("slamp.bot.endpoint", $route->endpoint);
    }, [$dispatcher, "handle"]);
}
$api = (new Aerys\Host)
    ->expose("*", config("app.port"))
    ->name(config("app.host"))
    ->use([$dispatcher, "authorize"])
    ->use($router)
    ->use([$dispatcher, "fallback"]);