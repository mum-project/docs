<?php

return [
    'baseUrl' => '',
    'production' => false,
    'collections' => [],
    'navigation' => require_once('navigation.php'),
    'active' => function ($page, $path) {
        $pages = collect(array_wrap($page));
        return $pages->contains(function ($page) use ($path) {
            return str_contains($page->getPath(), $path);
        });
    },
    'version' => 'v0.1.0'
];
