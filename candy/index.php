<?php

namespace _\lot\x\candy {
    // Replace pattern to its value
    function v($content, $vars = [], $prefix = '%{', $suffix = '}%') {
        if (!$content || !\is_string($content) || false === \strpos($content, $prefix)) {
            return $content;
        }
        foreach ((array) $vars as $k => $v) {
            if (\is_array($v) || \is_object($v)) {
                // `%{$.a.b.c}%`
                if (false !== \strpos($content, $prefix . $k . '.')) {
                    $content = \preg_replace_callback('/' . \x($prefix . $k) . '(\.\w+)*' . \x($suffix) . '/i', function($m) use($v) {
                        if ($a = \explode('.', \trim($m[1] ?? "", '.'))) {
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
            } else if (false !== \strpos($content, $kk = $prefix . $k . $suffix)) {
                $v = \s($v);
                $content = \str_replace($kk, \is_string($v) ? $v : \json_encode($v), $content);
            }
        }
        return $content;
    }
}

namespace _\lot\x {
    function candy($content) {
        $state = \State::get('x.candy', true) ?? [];
        $any = \array_replace($GLOBALS, $state['v'] ?? [], $state['x'] ?? []);
        return \_\lot\x\candy\v($content, $any);
    }
    \Hook::set([
        'page.content',
        'page.css', // `.\lot\x\art`
        'page.description',
        'page.js', // `.\lot\x\art`
        'page.link'
    ], __NAMESPACE__ . "\\candy", 1); // Same with the `_\lot\x\block` stack!
}
