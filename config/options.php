<?php
/******************************************************************************
* Application Options 
******************************************************************************/

// BOOLEAN
// Set to TRUE to toot tweets that are @RTs
// Set to FALSE to ignore tweets that are @RTs
define(
  'TOOT_RETWEETS',
  FALSE
);

// BOOLEAN
// Set to TRUE to toot tweets that are @Replies
// Set to FALSE to ignore tweets that are @Replies
define(
  'TOOT_REPLIES',
   FALSE
);

// STRING
// Text (e.g. some hashtags) to be appended to all Toots
define(
  'APPEND_TO_TOOT',
  '#bot #elephantbird'
);

// STRING
// Text to be used as accessibility text for all images
define(
  'ACCESSIBILITY_TEXT',
  'Sorry; this image was uploaded by a bot that cannot describe its content.'
);

// BOOLEAN
// Set to TRUE to enable CW for all toots
// Set to FALSE to disable CW for all toots
define(
  'CONTENT_WARNING',
  TRUE
);

// STRING
// Set a global CW text for all toots
define(
  'CONTENT_WARNING_TEXT',
  '[Bot] Possible spoilers'
);

// BOOLEAN
// Rewrite '@usernames' to a more approriate style
// Set to TRUE or FALSE
define(
  'REWRITE_HANDLES',
  TRUE
);

// STRING
// style to rewrite @ user names 
// "twitter" -> '@username@twitter.com'
// "birdsite" -> '@username@birdsite.com'
// "brackets" -> '(@)username'
// "link" ->  'https://twitter.com/username'
// default: twitter
define(
  'REWRITE_HANDLE_STYLE',
  'twitter'
);

// STRING
# Visibility of toots 
# public || unlisted || private || direct
define(
  'TOOT_VISIBILITY',
  'unlisted'
);

// BOOLEAN
// Set to TRUE to toot a thumbnail of video content
// Set to FALSE to toot full video content
define(
  'TOOT_VIDEO_THUMB_ONLY',
  FALSE
);
?>
