<?php
// config/config.php

/**
 * Fungsi untuk melakukan GET request menggunakan cURL
 */
function http_request_get($url) {
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (PHP REST Client)');
    $output = curl_exec($ch); 
    curl_close($ch); 
    return $output; 
}
