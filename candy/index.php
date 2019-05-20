<?php namespace _\candy;

// Replace pattern to its value
function parse(string $s, $a = [], $x = "\n", $r = true) {
    if (!$s || \strpos($s, '%') === false) {
        return $s;
    }
    $a = (array) $a;
    foreach ($a as $k => $v) {
        if (\is_array($v) || \is_object($v)) {
            // `%{$.a.b.c}%`
            if (\strpos($s, '%{' . $k . '.') !== false) {
                $s = \preg_replace_callback('#\%\{' . x($k) . '(\.[a-z\d_]+)*\}\%#i', function($m) use($v) {
                    $a = \explode('.', $m[1] ?? "");
                    $b = \array_pop($a);
                    if (isset($m[2])) {
                        $fn = \substr($m[2], 1);
                        $fn = \is_callable($fn) ? $fn : false;
                    } else {
                        $fn = false;
                    }
                    if ($b) {
                        if (\is_object($v)) {
                            if (!\method_exists($v, '__get') && !isset($v->{$b})) {
                                return $m[0];
                            }
                            $v = $v->{$b};
                        } else if (\is_array($v)) {
                            if (!isset($v[$b])) {
                                return $m[0];
                            }
                            $v = $v[$b];
                        }
                        if ($a) {
                            if (!\is_array($v) && !\is_object($v)) {
                                return $v;
                            }
                            while ($b = \array_pop($a)) {
                                if (!\is_array($v) && !\is_object($v)) {
                                    return $v;
                                }
                                if (\is_object($v)) {
                                    if (!\method_exists($v, '__get') && !isset($v->{$b})) {
                                        return $m[0];
                                    }
                                    $v = $v->{$b};
                                } else if (\is_array($v)) {
                                    $v = $v[$b] ?? $m[0];
                                }
                            }
                            return $fn ? \call_user_func($fn, $v) : $v;
                        }
                    }
                    return $fn ? \call_user_func($fn, $v) : $v;
                }, $s);
            }
            // `%{$}%`
            if (\is_object($v) && \method_exists($v, '__toString')) {
                $s = \str_replace('%{' . $k . '}%', \strval($v), $s);
            }
        // `%{a}%`
        } else if (\strpos($s, '%{' . $k . '}%') !== false) {
            $s = \str_replace('%{' . $k . '}%', s($v), $s);
            continue;
        }
        // TODO: replace pattern(s) as in `format` function
    }
    return $s;
}

function x($content, array $lot = []) {
    // Escape syntax is not available!
    return $content;
}

function v($content, array $lot = []) {
    if (\strpos($content, '%{') === false) {
        return $content;
    }
    $a = \plugin('candy');
    $b = $GLOBALS;
    if (!empty($a['x'])) {
        $a['v'] = \alter($a['v'], $a['x']);
    }
    $b = \alter($b, $a['v']);
    $b['$'] = $b['page'] ?? [];
    return parse($content, $b);
}

\Hook::set([
    '*.content',
    '*.css',
    '*.description',
    '*.image',
    '*.js',
    '*.link'
], __NAMESPACE__ . "\\x", 0); // Same with the `_\block\x` stack!

\Hook::set([
    '*.content',
    '*.css',
    '*.description',
    '*.image',
    '*.js',
    '*.link'
], __NAMESPACE__ . "\\v", 1); // Same with the `_\block\v` stack!