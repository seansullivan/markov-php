<?PHP

// run: php -f example.php

require(dirname(__FILE__).'/markov.php');

$file_path = dirname(__FILE__).'/tom_sawyer.txt';
echo Markov::instance($file_path)->generate(20);
echo "\n"

?>