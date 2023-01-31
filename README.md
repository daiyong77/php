# 基本方法操作介绍  
  
# 数据库操作  
Db::connect()//数据库链接  
*::connect('mysql:host=127.0.0.1;dbname=test','root','123456')  
*return pdo  
Db::insert()//插入数据  
*::insert('table',array('key'=>'value','key2'=>'value2'))  
*return 主键id  
Db::delete() //删除数据  
*::delete('table',array('id'=>1,'username'=>'daiyong'))  
*//第二个参数给只有一个替代字符的语句传参可以为字符串  
*::delete('table','fid=? or id!=?',array(1,2))  
*::delete('table','fid=:fid or id!=:id',array(':id'=>1,':fid'=>2))  
*return 影响的行数  
Db::update()//修改数据  
*::update('table',array('name'=>'daiyong','age'=>'24'),array('id'=>1))  
*//第二个参数给只有一个替代字符的语句传参可以为字符串  
*::update('table',array('name'=>'daiyong'),'id=? or id=?',array(1,2))  
*::update('table',array('name'=>'daiyong'),'id=:id or id=:id2',array(':id'=>1,':id2'=>2))  
*return 影响的行数  
Db::findAll()//查询多条数据  
*::findAll('table|username,password',array('id'=>1),'order by id desc limit 0,10','id')  
*//第二个参数给只有一个替代字符的语句传参可以为字符串  
*::findAll('select * from table where sex=? or sex=? limit 0,10',array('男','未知'))  
*::findAll('select * from table where sex=:sex1 or sex=:sex2 limit 0,10',array('sex1'=>'男','sex2'=>'未知'))  
*//以id为数组key值  
*::findAll('select * from table where sex=:sex limit 0,10',array(':sex'=>'男'),'id')  
*return 数据列表  
Db::find('table|username,password',array('id'=>1))//查询一条数据  
*::find('table|username,password',array('id'=>1))  
*//第二个参数给只有一个替代字符的语句传参可以为字符串  
*::find('select * from table where id=? or is=?',array('男','1'))  
*::find('select * from table where id=:id and is=:is',array(':id'=>'男',':is'=>'24'))  
*rerturn 数组|如果只查询一个值则返回字符串  
Db::query()//执行sql语句  
*//第二个参数给只有一个替代字符的语句传参可以为字符串logPath  
*::query('sql语句可带?或:key','数组')  
*return sql结果  
# 文件操作  
File::path('文件相对项目地址或者绝对地址')//获取当前项目的绝对地址,没有文件夹则创建  
File::put('文件路径,可为相对路径','文件内容','是否追加')//写入文件  
File::get('文件路径,可为相对路径')//获取文件信息  
File::delete('文件路径,可为相对路径')//删除文件  
# 常用方法  
Func::random(随机数个数,随机字符串)//获取随机数  
Func::rsa(rsa公钥或者私钥,类型0公钥1私钥)//将rsa字符串转换成换行模式  
# 远程抓取  
Http::curl('请求地址',array(//数组中的参数如果不需要可以不用传入  
*'302'=>'是否302跳转',  
*'timeout'=>'超时时间',  
*'repeat'=>'请求失败后的重复请求次数',  
*'savecookie'=>'请求页面后的cookie保存地址,可为相对地址',  
*'getcookie'=>'cookie地址,可为相对地址',  
*'header'=>array('类似于chrome的一条一条的头信息','类似于chrome的一条一条的头信息'),  
*'showheader'=>'是否返回头信息'  
*'post'=>'post参数最好是url形式'  
));  
