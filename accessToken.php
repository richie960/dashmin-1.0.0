<?php
//YOU MPESA API KEYS
$consumerKey = "BTBovK34kbAGtCwkCDzWQ27t4nTyRqp3E3D4U1rs3ASFJL2N"; //Fill with your app Consumer Key
$consumerSecret = "3COYovIfvhQ8kgg2xQmGAtVumxiSYr4e8Af1EWS4VGrky44EyKahJ3ird4NKBTyc"; //Fill with your app Consumer Secret
//ACCESS TOKEN URL
$access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$headers = ['Content-Type:application/json; charset=utf8'];
$curl = curl_init($access_token_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
 $access_token = $result->access_token;
 
// echo " $access_token";
  curl_close($curl);