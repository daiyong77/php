<?php

namespace Daiyong;

class Func
{
	//随机数
	//random(随机数个数,随机字符串)
	//return string
	public static function random($count = 5, $string = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	{
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
	//rsa转换
	//rsa(rsa公钥或者私钥,类型0公钥1私钥)
	//return string
	public static function rsa64($str, $type = 0)
	{
		if (!$type) {
			$type = 'PUBLIC';
		} else {
			$type = 'PRIVATE';
		}
		return '-----BEGIN RSA ' . $type . ' KEY-----' . PHP_EOL . wordwrap($str, 64, PHP_EOL, true) . PHP_EOL . '-----END RSA ' . $type . ' KEY-----' . PHP_EOL;
	}
}
