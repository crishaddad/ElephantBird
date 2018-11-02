<?php 
function buildRequest($url, $method, $parameters) {
  $request = array();
  ksort($parameters);
  foreach($parameters as $key=>$value){
    $request[] = "$key=" . rawurlencode($value);
  }
  $return_base = $method . "&" . rawurlencode($url) . '&' . rawurlencode(implode('&', $request));
  debugOut("Twitter Base: $return_base");
  return $return_base;
}

function buildSignatureHeader($oauth_data) {
  $signature_header = 'Authorization: OAuth ';
  $values = array();
  foreach($oauth_data as $key=>$value)
  $values[] = "$key=\"" . rawurlencode($value) . "\"";
  $signature_header =  $signature_header . implode(', ', $values);
  debugOut("Twitter Oauth Signature: $signature_header");
  return $signature_header;
}

function getTweets($handle,$count){
  $oauth_data = array(
    'screen_name' => $handle,
    'count' => $count,
    'tweet_mode' => 'extended',
    'oauth_consumer_key' => TWITTER_CONSUMER_KEY,
    'oauth_nonce' => time(),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_token' => TWITTER_ACCESS_TOKEN,
    'oauth_timestamp' => time(),
    'oauth_version' => '1.0'
  );
  
  $base_info = buildRequest(TWITTER_API_URL, 'GET', $oauth_data);
  
  $encoded_key =
    rawurlencode(TWITTER_CONSUMER_SECRET) .
    '&' .
    rawurlencode(TWITTER_ACCESS_TOKEN_SECRET);
 
  $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $encoded_key, true));
  
  $oauth_data['oauth_signature'] = $oauth_signature;
  
  // Make Requests
  $header = array(buildSignatureHeader($oauth_data), 'Expect:');
  $curl_options = array(
    CURLOPT_SSL_VERIFYPEER => TRUE,
    CURLOPT_HTTPHEADER => $header,
    CURLOPT_HEADER => FALSE,
    CURLOPT_URL => 
      TWITTER_API_URL . 
      '?screen_name=' . $handle .
      '&count=' . $count .
      '&tweet_mode=extended',
    CURLOPT_RETURNTRANSFER => TRUE
  );
  
  $curl_request = curl_init();
  curl_setopt_array($curl_request, $curl_options);
  $json = curl_exec($curl_request);
  $status = curl_getinfo($curl_request,CURLINFO_HTTP_CODE);
  curl_close($curl_request);
  
  $twitter_data = json_decode($json);
  if ( $status == 200 ){
    infoOut("Tweet status OK");
    return $twitter_data;
  }
  else {
    var_dump($twitter_data);
    errorOut("Twitter request failed status $status");
  }
}
?>
