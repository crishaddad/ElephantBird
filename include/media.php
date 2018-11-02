<?php
function generateRandomString($length = 10) {
  //https://stackoverflow.com/a/4356295
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

function hasMedia($this_tweet){
  if (
    isset($this_tweet->entities->media)
    && is_array($this_tweet->entities->media)
  ){
    infoOut("Media Found");
    return true;
  }
  else{
    infoOut("No Media Found");
    return false;
  }
}

function getMediaLinks($this_tweet){
  $found_links = array();
  $found_count = 0;

  foreach ($this_tweet->extended_entities->media as $item ){
    $media_type = $item->type;
    debugOut("Media type is $media_type");

    switch ($media_type) {
      case "photo":
        $found_links[$found_count] = $item->media_url_https;
        debugOut("Image download link is " . $found_links[$found_count]);
        break;
      case "video":
      case "animated_gif":
        if (TOOT_VIDEO_THUMB_ONLY) {
          $found_links[$found_count] = $item->media_url_https;
          debugOut("Thumbnail download link is " . $found_links[$found_count]);
        }
        else{
          foreach ( $item->video_info->variants as $variant ){ 
            if ( $variant->content_type ==  'application/x-mpegURL' ) {
              debugOut("Invalid content; skipping");
              continue;
            }
            else {
              $found_links[$found_count] = $variant->url;
              debugOut("Video download link is " . $found_links[$found_count]);
              break;
            }
          }
        }
      break;
      default :
        $found_links[$found_count] = $item->media_url_https;
        break;
    }
    $found_count++;
  }
  return $found_links;
}

function initMediaTmp($dir){
  global $DRY_RUN;

  if ( ! is_dir($dir) ) {
    if ( ! DRY_RUN && ! $DRY_RUN ) {
      if ( ! mkdir($dir,0755,true)) {
        errorOut("Could not create TMP dir $dir");
      }
      else {
        infoOut("DRY RUN - not creating tmp dir");
      }
    }
  }
  elseif ( ! is_writable($dir)){
    errorOut("TMP dir $dir is not writable");
  }
  infoOut("TMP dir init OK");  
}

function getMediaFiles($media_links){
  global $DRY_RUN;
  $media_files = array();

  foreach ( $media_links as $media_link ) {
    $my_tmpfile = MY_TMP . '/' . generateRandomString(20);
    infoOut("Caching file from $media_link to $my_tmpfile");

    $curl_request = curl_init();
    $curl_options = array(
      CURLOPT_URL => $media_link,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_SSL_VERIFYPEER => TRUE
    );

    curl_setopt_array($curl_request, $curl_options);
    if ( ! DRY_RUN && ! $DRY_RUN ) {
      $saved_file = fopen($my_tmpfile, "w+");
      fputs($saved_file, curl_exec($curl_request));
      fclose($saved_file);
    }
    else {
      infoOut("DRY RUN - not saving file to disk");
    }

    $status = curl_getinfo($curl_request,CURLINFO_HTTP_CODE);

    curl_close($curl_request);

    $media_files[] = $my_tmpfile;
  }

  return $media_files;
}

function deleteTempMedia($media_files){
  global $DRY_RUN;
  infoOut("Cleaning up...");
  foreach ( $media_files as $file ){
    debugOut("Cleaning up temp file $file");
    if ( ! DRY_RUN && ! $DRY_RUN ) {
    unlink($file);
    }
    else {
      infoOut("DRY RUN - not deleting any files");
    }
  }
}
?>
