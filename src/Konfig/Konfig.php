<?php

class Konfig {
	
	static private $data_ = array();
	static private $lookupFolders_ = array();
	
	static public function addLookupFolder($folder) {
		if (!is_dir($folder)) throw new Exception("invalid or inaccessible folder: \"$folder\"");
		self::$lookupFolders_[] = $folder;
	}
	
	static public function load($groupName) {
		if (isset(self::$data_[$groupName])) return true;

		self::$data_[$groupName] = array();
		$somethingLoaded = false;

		foreach (self::$lookupFolders_ as $folder) {
			$filePath = $folder . DIRECTORY_SEPARATOR . $groupName . '.php';
			if (!file_exists($filePath)) continue;
			$somethingLoaded = true;
			self::$data_[$groupName] = self::mergeConfigArrays(self::$data_[$groupName], include $filePath);
		}
		
		return $somethingLoaded;
	}
	
	static private function mergeConfigArrays($destination, $source) {
		foreach ($source as $k => $v) {
			if (array_key_exists($k, $destination)) {
				if (is_array($destination[$k]) && is_array($source[$k])) {
					$destination[$k] = self::mergeConfigArrays($destination[$k], $v);
				} else {
					$destination[$k] = $v;
				}
			} else {
				$destination[$k] = $v;
			}
		}
		return $destination;
	}

	static public function get($groupName, $path = null, $defaultValue = null) {
		if ($path === null) {
			$path = $groupName;
			$groupName = 'default';
		}
		$pieces = explode('.', $path);
		$output = self::getGroup($groupName);
		foreach ($pieces as $p) {
			if (!array_key_exists($p, $output)) return $defaultValue;
			$output = $output[$p];
		}
		return $output;
	}
	
	static public function getGroup($groupName) {
		if (!isset(self::$data_[$groupName])) {
			$ok = self::load($groupName);
			if (!$ok) throw new Exception("invalid group name: \"$groupName\"");
		}
		return self::$data_[$groupName];		
	}

}
