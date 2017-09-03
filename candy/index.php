<?php

function fn_candy_x($content, $lot = []) {
    // Escape syntax is not available!
    return $content;
}

function fn_candy($content, $lot = []) {
    if (strpos($content, '%{') === false) {
        return $content;
    }
    $c = Plugin::state(__DIR__);
    $cc = Lot::get(null, []);
    if (!empty($c['x'])) {
        $c['v'] = array_replace_recursive($c['v'], $c['x']);
    }
    $cc = array_replace_recursive($cc, $c['v']);
    $cc['$'] = isset($cc['page']) ? $cc['page'] : [];
    return __replace__($content, $cc);
}

Hook::set([
    '*.content',
    '*.css',
    '*.description',
    '*.js'
], 'fn_candy_x', 0); // Same with the `fn_block_x` stack!

Hook::set([
    '*.content',
    '*.css',
    '*.description',
    '*.js'
], 'fn_candy', 1); // Same with the `fn_block` stack!