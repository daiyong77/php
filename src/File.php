<?php

namespace Daiyong;

//文件操作(请注意最好不要用中文)
class File
{
	public static $path = '../'; //当前项目路径
	//获取当前项目的绝对地址
	//path('文件相对项目地址或者绝对地址','没有文件夹则创建')
	//return 绝对地址
	public static function path($file = '')
	{
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
	//写入文件
	//put('文件路径','文件内容','是否追加')
	//return 是否成功
	public static function put($file = '', $content = '', $append = '')
	{
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
	//获取文件信息
	public static function get($file)
	{
		return @file_get_contents(self::path($file));
	}
	//删除文件
	public static function delete($file)
	{
		return unlink(self::path($file));
	}

	// //导出excel
	// public static function exportXlsx($file = 'download.xlsx', $data, $page = 1)
	// {
	// 	$file = str_replace('.xlsx', '', $file);
	// 	if ($page == 1) {
	// 		$data = array(
	// 			$file => $data
	// 		);
	// 	}
	// 	$excel = new PHPExcel();
	// 	//使用缓存不至于让内存泄漏
	// 	PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp, array('memoryCacheSize' => '8MB'));
	// 	$page = 0;
	// 	foreach ($data as $k => $v) {
	// 		//设置第一页标题
	// 		if ($page != 0) {
	// 			$excel->createSheet();
	// 		}
	// 		$excel->setactivesheetindex($page);
	// 		$page++;
	// 		$excel->getActiveSheet()->setTitle($k);
	// 		$l = 1;
	// 		foreach ($v as $k2 => $v2) {
	// 			$d = 0;
	// 			for ($i = 'A'; $i <= 'Z'; $i++) {
	// 				if ($d > count($v2) || $i == 'GA') break;
	// 				$excel->getActiveSheet()->setCellValue($i . ($l), $v2[$d]);
	// 				$d++;
	// 			}
	// 			$l++;
	// 		}
	// 	}
	// 	//选择第一页并且保存输出
	// 	$excel->setactivesheetindex(0);
	// 	$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	// 	//设置header
	// 	header("Content-Type:application/vnd.ms-excel");
	// 	header("Content-Disposition:attachment;filename=" . $file . ".xlsx");
	// 	header("Pragma:no-cache");
	// 	header("Expires:0");
	// 	$write->save('php://output'); //输出
	// }

	// //excel
	// //getExcel(路径,返回类型A1,A2类型与一行一行的类型)
	// public static function getExcel($path, $returntype = 'excel')
	// {
	// 	$path = self::path($path);
	// 	// 判断文件是什么格式
	// 	$type = pathinfo($path);
	// 	$type = strtolower($type["extension"]);
	// 	if ($type == 'xlsx') {
	// 		$type = 'Excel2007';
	// 	} elseif ($type == 'xls') {
	// 		$type = 'Excel5';
	// 	}
	// 	$objReader = PHPExcel_IOFactory::createReader($type); //判断使用哪种格式
	// 	$objReader->setReadDataOnly(true); //只读取数据,会智能忽略所有空白行,这点很重要！！！
	// 	$objPHPExcel = $objReader->load($path); //加载Excel文件
	// 	$sheetCount = $objPHPExcel->getSheetCount(); //获取sheet工作表总个数
	// 	$rowData = array();
	// 	$RowNum = 0;
	// 	/*读取表格数据*/
	// 	for ($i = 0; $i <= $sheetCount - 1; $i++) { //循环sheet工作表的总个数
	// 		$sheet = $objPHPExcel->getSheet($i);
	// 		$highestRow = $sheet->getHighestRow();
	// 		$RowNum += $highestRow - 1; //计算所有sheet的总行数
	// 		$highestColumn = $sheet->getHighestColumn();
	// 		//从第$i个sheet的第0行开始获取数据
	// 		for ($row = 0; $row <= $highestRow; $row++) {
	// 			//把每个sheet作为一个新的数组元素 键名以sheet的索引命名 利于后期数组的提取
	// 			try {
	// 				$range = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
	// 			} catch (Exception $e) {
	// 				$range = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, false, FALSE);
	// 			}
	// 			$rowData[$i][] = self::arrToOne($range); //如果数组太长可能返回2个数组则需要拼接
	// 		}
	// 	}
	// 	/*删除每行表头数据*/
	// 	foreach ($rowData as $k => $v) {
	// 		array_shift($rowData[$k]);
	// 	}
	// 	if ($returntype == 'excel') { //excel格式输出
	// 		$az = self::excelAZ();
	// 		$rowData_new = array();
	// 		foreach ($rowData as $k => $v) {
	// 			foreach ($v as $k2 => $v2) {
	// 				foreach ($v2 as $k3 => $v3) {
	// 					$rowData_new[$k][$az[$k3]][$k2 + 1] = $v3;
	// 				}
	// 			}
	// 		}
	// 		return $rowData_new;
	// 	} else {
	// 		return $rowData;
	// 	}
	// }
	private static function excelAZ()
	{
		$array = array();
		for ($i = 0; $i <= 701; $i++) {
			$y = ($i / 26);
			if ($y >= 1) {
				$y = intval($y);
				$array[$i] = chr($y + 64) . chr($i - $y * 26 + 65);
			} else {
				$array[$i] = chr($i + 65);
			}
		}
		return $array;
	}
	private static function arrToOne($Array)
	{
		$arr = array();
		foreach ($Array as $key => $val) {
			if (is_array($val)) {
				$arr = array_merge($arr, self::arrToOne($val));
			} else {
				$arr[] = $val;
			}
		}
		return $arr;
	}
}
