#!/usr/bin/php
<?php
/*
 * Solution to the here presented task: https://www.profihost-karriere.de/hilf-tamara/
 * Done in PHP just for the heck of it. It's probably simpler to use bash
 *
 * Dependencies needed on your linux system:
 *    - ls (so basically unix like systems)
 *    - openssl for opening the files
 */

//input folder for a bit more flexibility
do {
  $folder = readline('Enter folder path: ');

  if(!is_dir($folder)) {
    echo "\nNot an existing folder\n";
  }
} while(!is_dir($folder));

//Add trailing slash if neccessary
if($folder[(strlen($folder) - 1)] != '/') {
  $folder .= '/';
}

//Correct md5 hashes
$correctDatMd5 = '68f264d9a908f93e8ffea4fb8e77e799';
$correctKeyMd5 = '994a97b3e1e85878aee2702b48549a37';

//Get all files in folder to a string
$files = shell_exec('ls ' . $folder);

//Put them into Array
$filesArray = [];
$filesArray = explode("\n", $files); //MD5 hashes are by default 32 chars long. We remove all whitespace and then split the string

//Loop and compare hashes
$foundDat = false;
$foundKey = false;

foreach($filesArray as $file) {
  if (md5_file($folder . $file) === $correctDatMd5) {
    echo "Correct DAT file found as: " . $file . "\n";
    $foundDat = $file;
  } else if(md5_file($folder . $file) === $correctKeyMd5) {
    echo "Correct KEY file found as: " . $file . "\n";
    $foundKey = $file;
  }
}

if($foundKey && $foundDat) {

  //Now open the file to retrieve the password
  $opensslCmd = 'openssl rsautl -decrypt -in ' . $folder . $foundDat . ' -out pass.txt -inkey ' . $folder . $foundKey;
  $cleanCmd = escapeshellcmd ($opensslCmd);

  $pass = shell_exec($cleanCmd);

  echo "Password is saved to pass.txt\n";

} else {
  echo 'Not all files were found. Ending';
}
