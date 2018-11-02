#!/usr/bin/php
<?php
/******************************************************************************
*
* ElephantBird.php
*
* By Cris Haddad
*
* requires php, php-curl, php-sqlite3
*
* Created 2018/09/27
*
******************************************************************************/

$shortopts = "c:t:vhd";
$longopts = array("config:", "verbose", "help", "dry-run","number-of-tweets");

$options = getopt($shortopts, $longopts);

// Output help text
if ( isset($options['h']) || isset($options['help']) ){
  echo "Usage:\r\n";
  echo "\r\n";
  echo "  $argv[0] [-v] [-t NUM] [-c DIR] [-d]\r\n\r\n";
  echo "Options:\r\n";
  echo "\r\n";
  echo "  [-c|--config] DIR   Override config dir (default: ./config)\r\n";
  echo "  [-t|--tweets] NUM   Number of Tweets to pull" ;
  echo "  [-d|--dry-run]      No toot; no changes to local filesystem\r\n";
  echo "  [-v|--verbose]      Increase verbosity (works mutiple times)\r\n";
  echo "  [-h|--help]         Display this help text\r\n";
  die();
}

// Override configured DRY_RUN at runtime
global $DRY_RUN;
if (isset($options['d']) || (isset($options['dry-run']))){
  $DRY_RUN = true;
}
else {
  $DRY_RUN = false;
}

// Increase verbosity at run time
$verbosity=0;
if (isset($options['v'])){
  $verbosity += count($options['v']);
}
if (isset($options['verbose'])){
  $verbosity += count($options['verbose']);
}

global $WARN;
global $INFO;
global $DEBUG;

switch (true) {
  case ( $verbosity == 0) :
    $WARN = false;
    $INFO = false;
    $DEBUG = false;
    break;
  case ( $verbosity == 1) :
    $WARN = true;
    $INFO = false;
    $DEBUG = false;
    break;
  case ( $verbosity == 2) :
    $WARN = true;
    $INFO = true;
    $DEBUG = false;
    break;
  case ( $verbosity >= 3 ) :
    $WARN = true;
    $INFO = true;
    $DEBUG = true;
    break;
  default:
    $WARN = false;
    $INFO = false;
    $DEBUG = false;
    break;
}

// Override default config dir at run time
if (isset($options['c'])){
  $config_dir = $options['c'];
}
elseif (isset($options['config'])){
  $config_dir = $options['config'];
}
else {
  $config_dir = './config';
}


// Verify config exists and load it in
foreach (
  array(
    'advanced.php',
    'twitter.php',
    'mastodon.php',
    'options.php',
  )
  as $config_file
  ){
    if ( file_exists($config_dir . '/' . $config_file) ){
    require_once $config_dir . '/' . $config_file;
  }
  else {
    die(
        "ERROR: Could not find $config_dir/$config_file\r\n" .
        "       Have you created a config profile?\r\n"
    );
  }
}

// Override configured tweet count at run time
if (isset($options['t']) ){
  $number_of_tweets = $options['t'];
} elseif ( isset($options['tweets']) ){
  $number_of_tweets = $options['tweets'];
}
else {
  $number_of_tweets = TWEET_COUNT;
}

include 'include/database.php';
include 'include/formatting.php';
include 'include/mastodon.php';
include 'include/media.php';
include 'include/misc.php';
include 'include/twitter.php';

infoOut("Using config at $config_dir");

initDB(MY_DB,SOURCE_TWITTER_HANDLE);
initMediaTmp(MY_TMP);

$tweets = getTweets(SOURCE_TWITTER_HANDLE,$number_of_tweets);

// var_dump($tweets);

foreach($tweets AS $this_tweet){
  $tweet_id = $this_tweet->id_str;
  $tweet_text = $this_tweet->full_text;

  infoOut("Processing tweet " . $tweet_id);

  // check if this tweet has been tooted already
  if (checkTweet($tweet_id,SOURCE_TWITTER_HANDLE) ){
    infoOut($tweet_id . ' has already been tooted; skipped.');
    continue;
  }

  // check if this tweet is a reply
  if (
    isset($this_tweet->in_reply_to_status_id_str)
    || isset($this_tweet->in_reply_to_user_id_str)
    || isset($this_tweet->in_reply_to_screen_name)
  ){
    debugOut($tweet_id . " is a reply") ;
    if ( ! TOOT_REPLIES ) {
      infoOut($tweet_id . " Ignored because TOOT_REPLIES is disabled") ;
      continue;
    }
  }

  // check if this tweet is a retweet
  if (
    isset($this_tweet->retweeted_status)
    && is_object($this_tweet->retweeted_status)
  ){
    debugOut($tweet_id . " is a retweet") ;
    if ( ! TOOT_RETWEETS) {
      infoOut($tweet_id . " Ignored because TOOT_RETWEETS is disabled") ;
      continue;
    }
  }

  $attachment_ids = array();
  if (hasMedia($this_tweet)){
    $media_links = getMediaLinks($this_tweet);
    $media_files = getMediaFiles($media_links);
    foreach ( $media_files as $this_file ){
      $attachment_ids[] = createAttachment(
        $this_file,
        ACCESSIBILITY_TEXT,
        MASTODON_ACCESS_TOKEN
      );
    }
  }
  else {
    $attachment_ids = null;
  }

  // Reformat the tweet text to toot text
  $toot_text = formatHandles($tweet_text);
  $toot_text = $toot_text . "\n\n" . APPEND_TO_TOOT;

  // Finally, toot it out!
  debugOut($tweet_id . " Processing toot") ;
  if (
    postToot(
      MASTODON_LOGIN,
      MASTODON_ACCESS_TOKEN,
      $toot_text,
      TOOT_VISIBILITY,
      CONTENT_WARNING,
      CONTENT_WARNING_TEXT,
      $attachment_ids,
      $tweet_id
      )
    ){
    if (hasMedia($this_tweet)){
      deleteTempMedia($media_files);
    }
    recordTweet($tweet_id,SOURCE_TWITTER_HANDLE);
  }
  else {
    if (hasMedia($this_tweet)){
      deleteTempMedia($media_files);
    }
    warnOut("Failed to toot $tweet_id");
  }
}
?>
