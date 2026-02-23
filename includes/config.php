<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'lol_portal';
const DB_USER = 'lol_user';
const DB_PASS = 'ChangeMe_StrongPassword!';

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
session_name('LOLSESSID');
