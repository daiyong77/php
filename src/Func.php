<?php
/*
 * @Author: daiyong 1031850847@qq.com
 * @Date: 2023-01-30 15:30:09
 * @LastEditors: daiyong
 * @LastEditTime: 2023-02-03 08:58:38
 * @Description: 常用方法
 */

namespace Daiyong;

class Func {

	/**
	 * @description: 随机数
	 * @param {随机数个数} $count
	 * @param {随机字符串} $string
	 * @return {string}
	 */
	public static function random($count = 5, $string = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
		$random = '';
		for ($i = 0; $i < $count; $i++) {
			if (function_exists('mb_strlen')) {
				$scount = mb_strlen($string, 'utf-8');
			} else {
				$scount = strlen($string);
			}
			$rand = mt_rand(0, $scount - 1);
			if (function_exists('mb_strlen')) {
				$random .= mb_substr($string, $rand, 1);
			} else {
				$random .= substr($string, $rand, 1);
			}
		}
		return $random;
	}

	/**
	 * @description: 一长串的rsa转换为换行模式的rsa
	 * @param {rsa公钥或者私钥} $str
	 * @param {类型0公钥1私钥} $type
	 * @return {换行模式的rsa}
	 */
	public static function rsa64($str, $type = 0) {
		if (!$type) {
			$type = 'PUBLIC';
		} else {
			$type = 'PRIVATE';
		}
		return '-----BEGIN RSA ' . $type . ' KEY-----' . PHP_EOL . wordwrap($str, 64, PHP_EOL, true) . PHP_EOL . '-----END RSA ' . $type . ' KEY-----' . PHP_EOL;
	}
}
