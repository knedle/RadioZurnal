<?php

$cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp'. DIRECTORY_SEPARATOR . 'cache' ;

$dirContent = scandir ($cacheDir);

// var_dump($dirContent);

foreach($dirContent as $fileDir) {
	if(preg_match("/.*Database.*/i", $fileDir, $match)) {
		$dbCacheDir = $match[0];
		break;
	}
}

if (!empty($dbCacheDir)) {

$dir = $cacheDir . DIRECTORY_SEPARATOR . $dbCacheDir;

//var_dump($dir);
//exit;


$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($it,
	RecursiveIteratorIterator::CHILD_FIRST);
foreach($files as $file) {
	if ($file->isDir()){
		rmdir($file->getRealPath());
	} else {
		unlink($file->getRealPath());
	}
}
rmdir($dir);



// $dirContent = scandir ($cacheDir);
// var_dump($dirContent);

die('db cache adresar byl prave smazan');

} else {
	die('db cache adresar je uz smazan');
}