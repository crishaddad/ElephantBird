<?php
function createAttachment(
  $file,
  $alt_text,
  $token
  )
{
  global $DRY_RUN;
  $upload_url = MASTODON_INSTANCE_URL .  '/' . MASTODON_ATTACHMENT_API_PATH;

  $curl_headers = array(
    'Authorization: Bearer ' . $token,
  );

  debugOut("file is $file");
  $file_data = array (
    'file' => new CURLFile($file),
    'description' => $alt_text
  );
 
  // debugOut("Dumping \$file_data : ");
  // var_dump($file_data); 

  $curl_options = array(
    CURLOPT_HTTPHEADER => $curl_headers,
    CURLOPT_URL => $upload_url,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $file_data,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_SSL_VERIFYPEER => TRUE
  );

  if ( ! DRY_RUN && ! $DRY_RUN ) {
    $curl_request = curl_init();
    curl_setopt_array($curl_request, $curl_options);
    $response = curl_exec($curl_request);
    $status = curl_getinfo($curl_request,CURLINFO_HTTP_CODE);
    curl_close($curl_request);
  
    infoOut("Toot Status: $status");
    debugOut("Toot Response: $response");
    if ( $status == 200 ){
      $response_decoded = json_decode($response, true);
      debugOut("Toot Status: $status");
      $attachment_id = $response_decoded['id'];
      debugOut("Attachment ID: $attachment_id");
      return $attachment_id;
    }
    else{
      warnOut("Unable to upload file $file");
    }
  }
  else {
    infoOut('DRY_RUN! - Not posting file...');
 }
}

function postToot(
  $handle,
  $token,
  $toot_text,
  $visibility = null,
  $requires_cw = null,
  $cw_text = null,
  $media_attachments = [],
  $idempotency_key = null
  )
{
  global $DRY_RUN;
  $toot_url = MASTODON_INSTANCE_URL .  '/' . MASTODON_STATUS_API_PATH;

  $curl_headers = array(
    'Authorization: Bearer ' . $token,
  );

  if ( ! DISABLE_IDEMPOTENCY_KEY ){
    $curl_headers[] = 'Idempotency-Key: ' . $idempotency_key;
  }
  
  $toot = array(
    'status' => $toot_text,
    'visibility' => $visibility
  );

  if ( $requires_cw ) {
    $toot['sensitive'] = 'true';
    $toot['spoiler_text'] = $cw_text;
    infoOut("Toot CW Enabled with text : $cw_text");
  }

  $http_query = http_build_query($toot);
 
  $media_string = "";
  if ( isset($media_attachments ) ) {
    debugOut("Attempting to attach media...");
    //build media string 
    $i = 0;
    foreach ($media_attachments as $attachment) {
      $media_string .= '&media_ids%5B%5D=' . $attachment;
      $i++; 
    }
    debugOut("\$media_string is: $media_string");
    $http_query .= $media_string;
  }
  
  infoOut("Toot data as http query : $http_query");

  $curl_options = array(
    CURLOPT_HTTPHEADER => $curl_headers,
    CURLOPT_URL => $toot_url,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $http_query,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_SSL_VERIFYPEER => TRUE
  );

  if ( ! DRY_RUN ){
    $curl_request = curl_init();
    curl_setopt_array($curl_request, $curl_options);
    $response = curl_exec($curl_request);
    $status = curl_getinfo($curl_request,CURLINFO_HTTP_CODE);
    curl_close($curl_request);
  
    infoOut("Toot Status: $status");
    debugOut("Toot Response: $response");
   
    if ( $status == 200 ){
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  else {
    infoOut('DRY_RUN! - Not posting toot...');
    return TRUE;
  }
}
?>
