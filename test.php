<?php 

$url = "https://shazam.p.rapidapi.com/search?term=halliday&locale=fr-FR&offset=0&limit=5";
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: shazam.p.rapidapi.com",
        "X-RapidAPI-Key: 501264117fmsh6e9151960cef39fp17b375jsn8f757604d258"
    ],
]);

$response = curl_exec($curl);
$parsee=json_decode(curl_exec($curl), true);
echo "<pre>";
var_dump($parsee);
echo "</pre>";
$err = curl_error($curl);

curl_close($curl);


