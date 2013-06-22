<?php
/**
 * File: Utils.php
 * Created at: 2:56 AM, 6/22/13
 * Author: JackYYF<root@jackyyf.com>
 * Project is published under GPLv2 (http://www.gnu.org/licenses/gpl-2.0.txt).
 */

namespace EasyPHP;

class Utils {
	private function __construct() { // Do not try to create an instance. All method are static.
	}

	public static function joinPath() {

		/**
		 * function joinPath
		 * @param: path parts to join together.
		 * @return: joined path.
		 *
		 * Smart join, all trailing directory separator will be removed.
		 * @example: joinPath('path1', 'path2', 'path3/', 'finalDirectory/') -> path1/path2/path3/finalDirectory (UNIX)
		 */

		$wrongDS = DS == '/' ? '\\' : '/'; // Wrong Directory Separator.
		$tokens = func_get_args();
		if (empty($tokens)) return '';
		$ret = str_replace($wrongDS, DS, $tokens[0]);
		if($ret[strlen($ret) - 1] == DS) { // Ended with /
			$ret = substr($ret, 0, -1);
		}
		$size = func_num_args();
		for($index = 1; $index < $size; ++ $index) {
			$nextToken = str_replace($wrongDS, DS, $tokens[$index]);
			if($nextToken[strlen($nextToken) - 1] == DS) {
				$nextToken = substr($nextToken, 0, -1);
			}
			$ret .= DS . $nextToken;
		}
		return $ret;
	}
}