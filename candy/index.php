<?php namespace fn\candy;

function x($content, array $lot = []) {
    // Escape syntax is not available!
    return $content;
}

function v($content, array $lot = []) {
    if (\strpos($content, '%{') === false) {
        return $content;
    }
    $a = \Plugin::state('candy');
    $b = \Lot::get();
    if (!empty($a['x'])) {
        $a['v'] = \extend($a['v'], $a['x']);
    }
    $b = \extend($b, $a['v']);
    $b['$'] = $b['page'] ?? [];
    return \candy($content, $b);
}

\Hook::set([
    '*.content',
    '*.css',
    '*.description',
    '*.image',
    '*.js',
    '*.link'
], __NAMESPACE__ . "\\x", 0); // Same with the `fn\block\x` stack!

\Hook::set([
    '*.content',
    '*.css',
    '*.description',
    '*.image',
    '*.js',
    '*.link'
], __NAMESPACE__ . "\\v", 1); // Same with the `fn\block\v` stack!