<?php
/**
* 
* @author Zsdroid [635925926@qq.com]
*/
class FileDB
{
	/**
	* 设置存key的前缀
	*/
	private $keyPrefix = '';
	private $dataPath = '';
	/**
	* 构造函数
	*
	* @param boolean $isUseCluster 是否采用 M/S 方案
	*/
	public function __construct($config)
	{
		$this->keyPrefix = $config['keyPrefix'];
		$this->dataPath = $config['dataPath'];
		!file_exists($this->dataPath) and mkdir($this->dataPath);
	}
	/**
	* 写缓存
	*
	* @param string $key 组存KEY
	* @param string $value 缓存值
	* @param int $expire 过期时间， 0:表示无过期时间
	*/
	public function set($key, $value)
	{
		file_put_contents($this->dataPath.$this->keyPrefix.$key,$value);
	}
	/**
	* 读缓存
	*
	* @param string $key 缓存KEY,支持一次取多个 $key = array('key1','key2')
	* @return string || boolean 失败返回 false, 成功返回字符串
	*/
	public function get($key)
	{
		if(!is_file($this->dataPath.$this->keyPrefix.$key))
		return '';
		return file_get_contents($this->dataPath.$this->keyPrefix.$key);
	}
	
	/**
	* 删除缓存
	*/
	public function delete($key)
	{
		unlink($this->dataPath.$this->keyPrefix.$key);
	}
	public function close()
	{
	}
}
