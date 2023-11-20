<?php namespace x\candy;

function e(string $content) {
    if (!$content || (false === \strpos($content, '%{') && false === \strpos($content, '{{'))) {
        return $content;
    }
    return \preg_replace_callback('/[%{][{]\s*(&?[^{}]+?)\s*[}][%}]/', static function ($m) {
        if (0 === \strpos($m[0], '%{') && '}%' !== \substr($m[0], -2)) {
            return $m[0];
        }
        if (0 === \strpos($m[0], '{{') && '}}' !== \substr($m[0], -2)) {
            return $m[0];
        }
        if ($raw = 0 === \strpos($m[1], '&')) {
            $m[1] = \trim(\substr($m[1], 1));
        }
        $test = $GLOBALS[\strstr($m[1], '.', true) ?: $m[1]] ?? [];
        if (null === $test) {
            return "";
        }
        if (\is_object($test) || \is_scalar($test)) {
            $v = get($m[1]);
            return $raw ? $v : \eat($v);
        }
        return $m[0];
    }, $content);
}

function get(string $key): string {
    $from = $GLOBALS ?? [];
    if (false === \strpos($key = \strtr($key, ["\\." => \P]), '.')) {
        return $from[\strtr($key, [\P => '.'])] ?? "";
    }
    $keys = \explode('.', $key);
    foreach ($keys as $k) {
        $k = \strtr($k, [\P => '.']);
        $to = "";
        if (\is_array($from)) {
            $from = $from[$k] ?? $from[\f2p($k)] ?? "";
            continue;
        }
        if (\is_object($from)) {
            if (\property_exists($from, $f2p = \f2p($k))) {
                $to = $from->{$f2p};
            } else if (\method_exists($from, $f2p)) {
                $to = $from->{$f2p}();
            } else if (\method_exists($from, '__get')) {
                $to = $from->__get($f2p);
            } else if (\method_exists($from, 'offsetGet')) {
                $to = $from->offsetGet($k);
            } else if (\method_exists($from, '__call')) {
                $to = $from->__call($f2p);
            } else if (\method_exists($from, '__invoke')) {
                $to = $from->__invoke();
            } else if (\method_exists($from, '__toString')) {
                $to = $from->__toString();
            }
            $from = $to;
            continue;
        }
    }
    return \s($from);
}

function page__content(?string $content) {
    return \is_string($content) ? e($content) : $content;
}

function page__description(?string $description) {
    return page__content($description);
}

function page__link(?string $link) {
    return page__content($link);
}

function page__script(?string $script) {
    return page__content($script);
}

function page__style(?string $style) {
    return page__content($style);
}

function page__title(?string $title) {
    return page__content($title);
}

\Hook::set('page.content', __NAMESPACE__ . "\\page__content", -1);
\Hook::set('page.description', __NAMESPACE__ . "\\page__description", -1);
\Hook::set('page.link', __NAMESPACE__ . "\\page__link", -1);
\Hook::set('page.script', __NAMESPACE__ . "\\page__script", -1);
\Hook::set('page.style', __NAMESPACE__ . "\\page__style", -1);
\Hook::set('page.title', __NAMESPACE__ . "\\page__title", -1);