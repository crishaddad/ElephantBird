<?php

class TweetDB extends SQLite3 {
  function __construct() {
    $this->open(MY_DB);
  }
}

function initDB($db_file, $table = 'default') {
  global $DRY_RUN;
  $table = trim($table,"@");
  if ( ! file_exists($db_file) ) { 
    warnOut( $db_file . " - file not found; one will be created." );
  }

  if ( ! DRY_RUN && ! $DRY_RUN ) {
    try { 
      $db_connection = new TweetDB();
      if ( ! $db_connection ) {
        errorOut('Could not connect to DB ' . $db_file);
      }
    }
    catch (PDOException $e) {
      print "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
      die();
    }
  }
  else {
    infoOut("DRY RUN - DB not Opened/Created");
  }

  $sql_create = <<<EOF
    CREATE TABLE IF NOT EXISTS
    TOOTED_TWEETS_$table
    ( 
      ID INTEGER PRIMARY KEY AUTOINCREMENT,
      TWEET_ID TEXT
    );
EOF;
    
  if ( ! DRY_RUN && ! $DRY_RUN ) {
    if ( ! $db_connection->exec($sql_create) ) {
      errorOut($db_connection->lastErrorMsg());
    }
    else {
      infoOut("DB Init OK");
    }
    $db_connection->close();
  }
  else { 
    infoOut("DRY RUN - DB not Initialised");
  }
}

function recordTweet($tweet_id, $table = 'default'){
  $table = trim($table,"@");
  global $DRY_RUN;
  debugOut("Attempting to add $tweet_id to DB");
  if ( ! checkTweet($tweet_id, $table) ) {
    try {
      $db_connection = new TweetDB();
      if ( ! $db_connection ) {
        errorOut('Could not connect to DB ' . $db_file );
      }
    }
    catch (PDOException $e) {
      print "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
      die();
    }
   
    $sql_add_tweet = <<<EOF
      INSERT INTO TOOTED_TWEETS_$table(TWEET_ID)
      VALUES ('$tweet_id')
EOF;
    if ( ! DRY_RUN && ! $DRY_RUN ) {
      if ( ! $db_connection->exec($sql_add_tweet) ) {
        errorOut($db_connection->lastErrorMsg());
      }
      else {
        infoOut("Tweet $tweet_id recorded in DB OK");
      }
    }
    else {
      infoOut("DRY RUN - not recording in db");
    }
  }
  else {
    infoOut("Tweet $tweet_id already exists in DB; not adding it again...");
  }
}

function checkTweet($tweet_id, $table = 'default'){
  $table = trim($table,"@");
  try {
    $db_connection = new TweetDB();
    if ( ! $db_connection ) {
      errorOut('Could not connect to DB ' . $db_file . '\r\n');
    }
  }
  catch (PDOException $e) {
    errorOut(htmlspecialchars($e->getMessage()));
  }

  $sql_check_tweet = <<<EOF
    SELECT EXISTS(
      SELECT 1 FROM TOOTED_TWEETS_$table
      WHERE TWEET_ID = "$tweet_id"
   )
EOF;
  
  debugOut("Checking if DB contains tweet $tweet_id");

  if ( $db_connection->querySingle($sql_check_tweet) ) {
    infoOut("Tweet $tweet_id found in DB");
    return TRUE;
  }
  else{
    infoOut("Tweet $tweet_id not found in DB");
    return FALSE;
  }
}
?>
