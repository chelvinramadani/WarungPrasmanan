<?php
$base_url = "http://localhost/katalog_produk/";

function base_url($path = '') {
    global $base_url;
    return rtrim($base_url, '/') . '/' . ltrim($path, '/');
}
