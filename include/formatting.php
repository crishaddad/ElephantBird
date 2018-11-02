<?php

function formatHandles($tweet_text) {
  if (REWRITE_HANDLES){
    switch (REWRITE_HANDLE_STYLE) {
      case "birdsite":
        $replace_text = "@$1@birdsite.com";
        break;
      case "brackets":
        $replace_text = "(@)$1";
        break;
      case "link":
        $replace_text = "http://www.twitter.com/$1";
        break;
      case "twitter":
        $replace_text =  "@$1@twitter.com";
        break;
      default:
        $replace_text = "@$1@twitter.com";
        break;
    }
    $tweet_text = preg_replace("/@([A-Za-z0-9_]+)/", $replace_text , $tweet_text);
  }
  return $tweet_text;
}

function expandUrls(){
  // FIXME : Unused / Unfinished
  // Thanks to Anu Bhava
  // https://stackoverflow.com/users/548225/anubhava
  $regex = "((https?|ftp)://)?"; // SCHEME
  $regex .= "([a-z0-9+!*(),;?&=$_.-]+(:[a-z0-9+!*(),;?&=$_.-]+)?@)?"; // User and Pass
  $regex .= "([a-z0-9\-\.]*)\.(([a-z]{2,4})|([0-9]{1,3}\.([0-9]{1,3})\.([0-9]{1,3})))"; // Host or IP
  $regex .= "(:[0-9]{2,5})?"; // Port
  $regex .= "(/([a-z0-9+$_%-]\.?)+)*/?"; // Path
  $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+/$_.-]*)?"; // GET Query
  $regex .= "(#[a-z_.-][a-z0-9+$%_.-]*)?"; // Anchor

  // TODO : WAT
}

function expandUrl($url, $hops = 0) {
  // FIXME : Unused / Unfinished
  // With thanks to Christian Joudrey
  // https://stackoverflow.com/users/504733/christian-joudrey
  if ($hops == MAX_URL_HOPS)
  {
    warnOut("Max URL hops reached while trying to expand URL; giving up");
    return rtrim($url);
  }
  $curl_request = curl_init($url);
  curl_setopt($curl_request, CURLOPT_HEADER, 1);
  curl_setopt($curl_request, CURLOPT_NOBODY, 1);
  curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
  $result = curl_exec($curl_request);

  if (preg_match('/Location: (?P<url>.*)/i', $r, $match))
  {
    return expandUrl($match['url'], $hops + 1);
  }
  else {
    return rtrim($url);
  }
}
?>
