<?php
/******************************************************************************
* Advanced Options 
******************************************************************************/

// Probably best not to mess with these unless you know what you're doing

// STRING
define(
  'TWITTER_API_URL',
  'https://api.twitter.com/1.1/statuses/user_timeline.json'
);

// STRING
define(
  'MASTODON_STATUS_API_PATH',
  '/api/v1/statuses'
);

// STRING
define(
  'MASTODON_ATTACHMENT_API_PATH',
  '/api/v1/media'
);

// INTEGER
// Not used 
define(
  'MAX_URL_HOPS',
  5
);

// BOOLEAN
define(
  'DISABLE_IDEMPOTENCY_KEY',
  FALSE
);

// BOOLEAN
define(
  'DRY_RUN',
  FALSE
);

// INTEGER
// How many tweets to fetch at a time
define(
  'TWEET_COUNT',
   20
);

// BOOLEAN
// Enable DEBUG output
define(
  'DEBUG',
  FALSE
);

// BOOLEAN
// Enable INFO output
define(
  'INFO',
  FALSE
);

// BOOLEAN
// Enable WARN output
define(
  'WARN',
  FALSE
);

// STRING
define(
  'MY_DB',
  'var/tweet.db'
);

// STRING
define(
  'MY_TMP',
  '/tmp/ElephantBird/'
);
?>
