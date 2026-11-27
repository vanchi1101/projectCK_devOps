<?php

return [
    'APP_URL' => getenv('APP_URL') ?: 'https://azcode.site',

    'GOOGLE_CLIENT_ID' => getenv('GOOGLE_CLIENT_ID') ?: '',
    'GOOGLE_CLIENT_SECRET' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
    'GOOGLE_REDIRECT_URI' => getenv('GOOGLE_REDIRECT_URI') ?: ''
];