<?php
/*
 * @Author: daiyong 1031850847@qq.com
 * @Date: 2023-01-30 17:24:29
 * @LastEditors: daiyong
 * @LastEditTime: 2023-01-31 17:07:57
 * @Description: 文件操作
 */

namespace Daiyong;

class File {
	public static $path = __DIR__ . '/../../../../'; //当前项目路径

	/**
	 * @description: 获取当前项目的绝对地址,没有文件夹则创建
	 * @param {文件相对项目地址或者绝对地址} $file
	 * @return {string}
	 */
	public static function path($file = '') {
		$root = __DIR__ . '/' . self::$path;
		if (!$file) {
			return $root;
		} else {
			if (!(strpos($file, '/') === 0 || preg_match('/^[A-Z]:/', $file))) {
				return $root . $file;
			} else {
				return $file;
			}
		}
	}

	/**
	 * @description: 写入文件
	 * @param {文件路径} $file
	 * @param {文件内容} $content
	 * @param {是否追加} $append
	 * @return {boolean}
	 */
	public static function put($file = '', $content = '', $append = '') {
		$file = self::path($file);
		if (!is_dir(dirname($file))) {
			mkdir(dirname($file), 0777, true);
		}
		if (!is_file($file)) {
			touch($file);
			chmod($file, 0777);
		}
		if ($append) {
			$return = file_put_contents($file, $content . PHP_EOL, FILE_APPEND);
		} else {
			$return = file_put_contents($file, $content);
		}
		if (!$return && $return !== 0) {
			return false;
		}
		return true;
	}

	/**
	 * @description: 获取文件信息
	 * @param {文件路径,可以为相对路径} $file
	 * @return {string}
	 */
	public static function get($file) {
		return @file_get_contents(self::path($file));
	}

	/**
	 * @description: 删除文件
	 * @param {文件路径,可以为相对路径} $file
	 * @return {boolean}
	 */
	public static function delete($file) {
		return unlink(self::path($file));
	}
}
