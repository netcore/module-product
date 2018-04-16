<?php

if (!app()->routesAreCached()) {
    require __DIR__ . '/Http/routes.php';
}

require __DIR__ . '/Http/breadcrumbs.php';
require __DIR__ . '/helpers.php';