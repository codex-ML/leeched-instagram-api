<?php

// Get the URL parameter from the request
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Check if the URL is provided
if (empty($url)) {
    echo 'Error: URL parameter is missing.';
    exit;
}

$apiUrl = 'https://v3.saveig.app/api/ajaxSearch';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => "q=$url", // Use the URL parameter
    CURLOPT_HTTPHEADER => array(
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:102.0) Gecko/20100101 Firefox/102.0',
        'Accept: */*',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate, br',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'Origin: https://saveig.app',
        'Connection: keep-alive',
        'Referer: https://saveig.app/',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-site'
    ),
));

$response = curl_exec($curl);

curl_close($curl);

// Decode JSON response
$jsonData = json_decode($response, true);

// Check if JSON decoding was successful
if ($jsonData === null) {
    echo 'Error decoding JSON response.';
    exit;
}

// Extract links from HTML data
$dom = new DOMDocument;
@$dom->loadHTML($jsonData['data']); // Suppress warnings

$links = array();

// Extract img and video URLs
$imgElement = $dom->getElementsByTagName('img')->item(0);
if ($imgElement) {
    $imgUrl = $imgElement->getAttribute('src');
    $links['img'] = $imgUrl;
}

$videoElement = $dom->getElementsByTagName('a')->item(0);
if ($videoElement) {
    $videoUrl = $videoElement->getAttribute('href');
    $links['video'] = $videoUrl;
}

// Echo links in JSON format
echo json_encode($links, JSON_PRETTY_PRINT);
