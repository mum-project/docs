<?php

return [
    'baseUrl' => '',
    'production' => false,
    'collections' => [],
    'navigation' => require_once('navigation.php'),
    'active' => function ($page, $path) {
        $pages = collect(array_wrap($page));
        return $pages->contains(function ($page) use ($path) {
            return substr_compare($page->getPath(), $path, -strlen($path), strlen($path)) === 0;
        });
    },
    'version' => 'v0.1.0'
];
