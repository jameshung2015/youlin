<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    'secret'     => env('JWT_SECRET', '8844556632197'),
    'content'    => env('JWT_CONTENT', '51118'),
    'passphrase' => env('JWT_PASsPHRASE', '35123f'),
    'exp_time'   => env('JWT_TIME', 7200),
    'issuer'     => env('JWT_ISSUER', 'http://ncbuwq.com'),
    'audience'   => env('JWT_AUD', 'buifbwueba'),
    'id'         => env('JWT_ID', '88fewifimo'),
];
