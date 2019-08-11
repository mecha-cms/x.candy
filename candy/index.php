<?php

namespace _\lot\x\candy {
    // Replace pattern to its value
    function v($content, $vars = [], $prefix = '%{', $suffix = '}%') {
        if (!$content || !\is_string($content) || \strpos($content, $prefix) === false) {
            return $content;
        }
        foreach ((array) $vars as $k => $v) {
            if (\is_array($v) || \is_object($v)) {
                // `%{$.a.b.c}%`
                if (\strpos($content, $prefix . '.') !== false) {
                    $content = \preg_replace_callback('/' . \x($prefix . $k) . '(\.[a-z\d_]+)*' . \x($suffix) . '/i', function($m) use($v) {
                        if ($a = \explode('.', $m[1] ?? "")) {
                            while ($b = \array_shift($a)) {
                                if (\is_array($v)) {
                                    $v = $v[$b] ?? $m[0];
                                } else if (\is_object($v)) {
                                    $v = $v->{$b} ?? $m[0];
                                }
                            }
                            return $v;
                        }
                        return $m[0];
                    }, $content);
                }
                // `%{$}%`
                if (\is_object($v) && \method_exists($v, '__toString')) {
                    $content = \str_replace($prefix . $k . $suffix, \strval($v), $content);
                }
            // `%{a}%`
            } else if (\strpos($content, $kk = $prefix . $k . $suffix) !== false) {
                $v = \s($v);
                $content = \str_replace($kk, \is_string($v) ? $v : \json_encode($v), $content);
            }
        }
        return $content;
    }
}

namespace _\lot\x {
    function candy($content) {
        $state = \state('candy');
        $any = \array_replace($GLOBALS, $state['v'], $state['x']);
        return candy\v($content, $any);
    }
    \Hook::set([
        'page.content',
        'page.css',
        'page.description',
        'page.image',
        'page.js',
        'page.link'
    ], __NAMESPACE__ . "\\candy", 1); // Same with the `_\lot\x\block` stack! #TODO
}