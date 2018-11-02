<?php
// Some misc functions
function debugOut($debug_output){
  global $DEBUG;
  if ( DEBUG || $DEBUG ){
    echo('DEBUG: ' . $debug_output . "\r\n");
  }
}
function infoOut($debug_output){
  global $INFO;
  if ( INFO || $INFO ){
    echo('INFO: ' . $debug_output . "\r\n");
  }
}

function warnOut($debug_output){
  global $WARN;
  if ( WARN || $WARN ){
    echo('WARN: ' . $debug_output . "\r\n");
  }
}

function errorOut($error_output){
  die('ERROR: ' . $error_output . "\r\n");
}
?>
