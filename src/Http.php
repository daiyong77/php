<?php
/*
 * @Author: daiyong 1031850847@qq.com
 * @Date: 2023-01-30 15:30:09
 * @LastEditors: daiyong
 * @LastEditTime: 2023-01-31 10:11:16
 * @Description: curl抓取远程连接内容
 */

namespace Daiyong;

use Daiyong\File;

class Http {
	public static $logPath = ''; //项目路径下的日志存放地址 例:cache/logs/curl.log
	public static $getcookie = ''; //获取cookie的路径
	public static $timeout = 3; //超时设置
	public static $repeat = 3; //请求失败后重复请求次数
	public static $usleep = 200; //每次请求暂停都少毫秒
	public static $agent = array(); //代理array(ip,port,username,password)
	public static $header = array(); //头部信息

	/**
	 * @description: curl抓取
	 * curl('请求地址',array(
	 *	  '302'=>'是否302跳转',
	 *	  'timeout'=>'超时时间',
	 *	  'repeat'=>'请求失败后的重复请求次数',
	 *	  'savecookie'=>'请求页面后的cookie保存地址,可为相对地址',
	 *	  'getcookie'=>'cookie地址,可为相对地址',
	 * 	  'header'=>array('类似于chrome的一条一条的头信息','类似于chrome的一条一条的头信息'),
	 *	  'showheader'=>'是否返回头信息'
	 *	  'post'=>'post参数最好是url形式'
	 * ));
	 * @return {string}
	 */
	public static function curl($url, $data = array()) {
		//定义
		if (!isset($data['timeout'])) {
			$data['timeout'] = self::$timeout; //超时设置
		}
		if (!isset($data['repeat'])) {
			$data['repeat'] = self::$repeat; //请求失败后重复请求次数
		}
		if (!isset($data['getcookie']) && self::$getcookie) {
			$data['getcookie'] = self::$getcookie;
		}
		//设置http头
		$header = array(
			'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'
		);
		if (isset($data['header'])) {
			if (self::headerHas($data['header'], 'User-Agent')) {
				$header = $data['header'];
			} else {
				$header = array_merge($header, $data['header']);
			}
		}
		if (count($header) == 1 && self::$header) {
			$header = array_merge($header, self::$header);
		}
		if (isset($data['savecookie'])) {
			File::put($data['savecookie'], '');
		}
		//日志记录
		$path_log = '';
		if (self::$logPath != '') {
			$path_log = File::path(self::$logPath);
		}
		if ($path_log != '' && !file_exists($path_log)) {
			File::put($path_log, '#dy curl log');
		}
		//执行抓取
		do {
			usleep(self::$usleep); //暂停毫秒
			if ($path_log != '') {
				$message = PHP_EOL . date('Y-m-d H:i:s') . '|' . $url;
				file_put_contents($path_log, $message, FILE_APPEND);
			}
			$time_begin = microtime(true);
			//执行抓取
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if (isset($data['showheader'])) {
				curl_setopt($ch, CURLOPT_HEADER, true); //不返回header部分
			} else {
				curl_setopt($ch, CURLOPT_HEADER, false); //不返回header部分
			}
			if (self::$agent) {
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
				curl_setopt($ch, CURLOPT_PROXY, self::$agent['ip'] . ':' . self::$agent['port']);  //"0.0.0.0:8080"
				if (self::$agent['username'] && self::$agent['password']) {
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, self::$agent['username'] . ':' . self::$agent['password']);  //"username:pwd"
				}
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //不自动输出内容
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, isset($data['302']) && $data['302'] ? true : false); // 网页有跳转时使用自动跳转 
			curl_setopt($ch, CURLOPT_ENCODING, ''); //不使用gzip等功能直接获取字符串
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			//设置超时
			curl_setopt($ch, CURLOPT_TIMEOUT, $data['timeout']);
			//https
			if (strpos($url, 'https://') === 0) {
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https(对认证证书来源的检查)
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // https(从证书中检查SSL加密算法是否存在)
			}
			//post
			if (isset($data['post'])) {
				curl_setopt($ch, CURLOPT_POST, true); //post提交
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']); //post提交
			}
			//其他方式请求
			if (isset($data['request'])) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $data['request']['type']); //post提交
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data['request']['data']); //post提交
			}
			//cookie
			if (isset($data['getcookie'])) {
				curl_setopt($ch, CURLOPT_COOKIEFILE, File::path($data['getcookie'])); //读取cookie
			}
			if (isset($data['savecookie'])) {
				curl_setopt($ch,  CURLOPT_COOKIEJAR, File::path($data['savecookie'])); //保存cookie
			}
			//返回
			$content = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			$data['repeat']--; //计算循环次数
			//保存日志
			$time_limit = sprintf('%.4f', (microtime(true) - $time_begin));
			if ($path_log != '') {
				file_put_contents($path_log, '|' . $time_limit . 's', FILE_APPEND);
			}
		} while ($data['repeat'] > 0 && $httpcode === 0);
		return $content;
	}

	/**
	 * @description: header中是否含有对应的头信息
	 * @param {header集合} $data
	 * @param {含有的头信息} $string
	 * @return {boolean}
	 */
	private static function headerHas($data, $string) {
		$has = false;
		foreach ($data as $v) {
			if (strpos($v, $string) === 0) {
				$has = true;
				break;
			}
		}
		return $has;
	}
}
