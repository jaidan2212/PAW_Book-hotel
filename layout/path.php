<?php
$base_url = '/PAW_Book-hotel/';

function url($path = '') {
    global $base_url;
    return $base_url . ltrim($path, '/');
}
