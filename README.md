# 基本方法操作
# 数据库操作,更多复杂写法可以查看src/Db.php
Daiyong\Db::connect('mysql:host=127.0.0.1;dbname=test','root','123456');//数据库链接
Daiyong\Db::insert('table',array('key'=>'value','key2'=>'value2'))//插入
Daiyong\Db::delete('table',array('id'=>1,'username'=>'daiyong'))//删除
Daiyong\Db::update('table',array('name'=>'daiyong','age'=>'24'),array('id'=>1))//修改
Daiyong\Db::findAll('table|username,password',array('id'=>1),'order by id desc limit 0,10','id')//查询多条数据
Daiyong\Db::find('table|username,password',array('id'=>1))//查询一条数据
Daiyong\Db::query('sql语句可带?或:key','数据')//执行sql语句
Daiyong\Db::getFather('table','fid')//获取树形结构 必须为id,fid关系
Daiyong\Db::getChildId('table','id')//获取子集id 必须为id,fid关系
# 文件操作
Daiyong\File::path('文件相对项目地址或者绝对地址')//获取当前项目的绝对地址,没有文件夹则创建
Daiyong\File::put('文件路径','文件内容','是否追加')//写入文件
Daiyong\File::get('文件路径')//获取文件信息
Daiyong\File::delete('文件路径')//删除文件
# 常用方法
Daiyong\Func::random(随机数个数,随机字符串)//获取随机数
Daiyong\Func::rsa(rsa公钥或者私钥,类型0公钥1私钥)//将rsa字符串转换成换行模式
# 远程抓取
Daiyong\Http:curl('链接',数组参数)//远程抓取

