# WhoIsUndercover
谁是卧底

## 游戏规则
1：每轮每个玩家只允许说一句话描述自己的身份词，既不能让卧底发现，也要给同伴暗示。

2：玩家发言完毕后开始投票，获票最多的玩家出局。

3：活着的平民小于等于2时，卧底胜利。

## 游戏经验
每个人拿到自己的词语之后，需要从别人的描述中，快速的辨别自己是否是卧底。

如果确认自己是卧底，需要快速确定平民是什么词语，描述时与平民类似，从而混淆视听。

如果确认自己是平民，描述时必须慎重，不能让卧底察觉，同时也要给队友们暗示

## 说明
1. 该程序只负责生成房间，随机卧底

其它操作:描述，投票等在线下自由进行

2. 可修改app/config.php配置文件
<pre>
<code>
/*
存储方式
	FileDB：	文件存储(默认)
	RedisDB：Redis存储
*/
$storageEngine = 'FileDB';
$config = [
	'FileDB'=>[
		'dataPath'	=>'../data/',		//存储目录
		'keyPrefix'	=>'who_is_undercover_',	//键前缀
	],
	'RedisDB'=>[
		'host'		=>'127.0.0.1',		//Redis的host
		'port'		=>6379,			//Redis的端口
		'password'	=>'',			//Redis的密码
		'db'		=>0,			//Redis存储的db库
		'keyPrefix'	=>'who_is_undercover_',	//键前缀
	],
];
</code>
</pre>
