<?php

return [
    'v' => [
        // Create your custom candy syntax here…
        'asset' => To::URL(constant('ASSET')),
        'mecha' => '<a href="//mecha-cms.com">Mecha CMS</a>',
        // Or, override the default candy syntax here…
        // 'language' => $language
    ],
    'x' => [
        '_COOKIE' => false,
        '_FILES' => false,
        '_GET' => false,
        '_POST' => false,
        '_REQUEST' => false,
        '_SESSION' => false
    ]
];