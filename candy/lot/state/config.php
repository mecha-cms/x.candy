<?php

return [
    'v' => [
        // Create your custom candy syntax here…
        'mecha' => '<a href="http://mecha-cms.com">Mecha CMS</a>',
        // Or, override the default candy syntax here…
        // 'language' => To::JSON($language->get())
    ],
    'x' => [
        // Disable `%{url.user}%` and `%{url.pass}%` syntax
        'url.user' => false,
        'url.pass' => false
    ]
];