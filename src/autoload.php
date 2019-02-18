<?php

function recursive_loader($directory, $exclude_dir = [], $exclude_files = []) {
	$dir = new DirectoryIterator($directory);
	foreach ($dir as $elem) {
		if($elem->isDot()) continue;
		if($elem->isDir()) {
			if(!in_array($elem->getBasename(), $exclude_dir)) {
				if(is_file($elem->getPath().'/'.$elem->getBasename().'/autoload.php')) {
					require_once $elem->getPath().'/'.$elem->getBasename().'/autoload.php';
				}
				else {
					recursive_loader($elem->getPath().'/'.$elem->getBasename());
				}
			}
		}
		elseif ($elem->isFile()) {
			if(!in_array($elem->getBasename(), $exclude_files)) {
				require_once $elem->getPath().'/'.$elem->getBasename();
			}
		}
	}
}

function alphabetic_order_loader($directory) {
	$dir = new DirectoryIterator($directory);
	$files = [];
	foreach ($dir as $file) {
		if($file->isDot()) continue;
		if($file->isFile() && $file->getBasename() !== 'autoload.php') {
			$files[] = $file->getPath().'/'.$file->getBasename();
		}
	}
	asort($files);
	foreach ($files as $file) {
		require_once $file;
	}
}

function not_alphabetic_order_loader($directory) {
	$dir = new DirectoryIterator($directory);
	$files = [];
	foreach ($dir as $file) {
		if($file->isDot()) continue;
		if($file->isFile() && $file->getBasename() !== 'autoload.php') {
			$files[] = $file->getPath().'/'.$file->getBasename();
		}
	}
	arsort($files);
	foreach ($files as $file) {
		require_once $file;
	}
}

recursive_loader(__DIR__.'/interfaces');
recursive_loader(__DIR__.'/traits');
recursive_loader(__DIR__.'/cryptography/abstract');
recursive_loader(__DIR__.'/cryptography/algorithms/Rsa/abstract');
recursive_loader(__DIR__.'/cryptography/algorithms/Hmac/abstract');
recursive_loader(__DIR__.'/exceptions/abstract');
recursive_loader(__DIR__.'/validator/Rules');
recursive_loader(__DIR__, ['interfaces', 'traits', 'abstract'], ['autoload.php']);