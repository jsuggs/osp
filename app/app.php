<?php

require __DIR__ . '/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../src/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/app.db',
    ),
));

$app->before(function () use ($app) {
    $notifications = $app['db']->fetchAssoc('SELECT * FROM notifications');
    $app['twig']->addGlobal('notifications', $notifications);

    $categories = $app['db']->fetchAll('SELECT * FROM categories');
    $app['twig']->addGlobal('categories', $categories);
});

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig');
});

$app->get('/category/{slug}', function ($slug) use ($app) {
    $sql = "SELECT * FROM categories WHERE slug = ?";
    $category = $app['db']->fetchAssoc($sql, array($slug));

    return $app['twig']->render('category.twig', array(
        'category' => $category,
    ));
})->bind('category');

return $app;
