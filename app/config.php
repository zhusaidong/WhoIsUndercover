<?php
/**
* 配置
* @author Zsdroid [635925926@qq.com]
*/
/*
存储方式
	FileDB：	文件存储(默认)
	RedisDB：	Redis存储
*/
$storageEngine = 'FileDB';
$config = [
	'FileDB'=>[
		'dataPath'	=>'../data/',			//存储目录
		'keyPrefix'	=>'who_is_undercover_',	//键前缀
	],
	'RedisDB'=>[
		'host'		=>'127.0.0.1',			//Redis的host
		'port'		=>6379,					//Redis的端口
		'password'	=>'',					//Redis的密码
		'db'		=>0,					//Redis存储的db库
		'keyPrefix'	=>'who_is_undercover_',	//键前缀
	],
];
