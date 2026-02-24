<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/metrics', function () {
    $registry = new \Prometheus\CollectorRegistry(
        new \Prometheus\Storage\InMemory()
    );

    $counter = $registry->getOrRegisterCounter(
        'app',
        'http_requests_total',
        'Total HTTP Requests',
        ['method']
    );

    $counter->inc([request()->method()]);

    $renderer = new \Prometheus\RenderTextFormat();
    return response(
        $renderer->render($registry->getMetricFamilySamples()),
        200
    )->header('Content-Type', \Prometheus\RenderTextFormat::MIME_TYPE);
});