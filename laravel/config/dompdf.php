<?php

return [
    'show_warnings'           => false,
    'orientation'             => 'portrait',
    'defines'                 => [
        'DOMPDF_UNICODE_ENABLED'   => true,
        'DOMPDF_ENABLE_REMOTE'     => false,
        'DOMPDF_DEFAULT_FONT'      => 'dejavu sans',
        'DOMPDF_ENABLE_HTML5PARSER'=> true,
        'DOMPDF_DPI'               => 150,
    ],
    'pdf_options' => [
        'isPhpEnabled'    => false,
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => false,
    ],
];
