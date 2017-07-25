<?php
/**
* Redis
*/
class RedisDB
{
	
	private $_redis = null;
	private $keyPrefix = '';
	private $expire = 86400;
	/**
	* 构造函数
	*/
	public function __construct($config)
	{
		$this->connect($config['host'],$config['port'],$config['password']);
		$this->setDbNumber($config['db']);
		$this->setKeyPrefix($config['keyPrefix']);
	}
	public function connect($host = '127.0.0.1',$port = 6379,$password = '')
	{
		$this->_redis = new \Redis;
		$this->_redis->connect($host,$port);
		if(!empty($password))
		{
			$auth = $this->_redis->auth($password);
			if(!$auth)
			{
				exit('password error');
			}
		}
	}
	public function setDbNumber($dbID = 0)
	{
		$this->_redis->select($dbID);
	}
	public function setKeyPrefix($keyPrefix = '')
	{
		$this->keyPrefix = $keyPrefix;
	}
	public function setExpire($expire)
	{
		$this->expire = $expire;
	}
	public function set($key,$value,$expire = null)
	{
		$this->_redis->setex($this->keyPrefix.$key,$expire == NULL ? $this->expire : $expire,$value);
	}
	public function exists($key)
	{
		return $this->_redis->exists($this->keyPrefix.$key);
	}
	public function get($key)
	{
		return $this->_redis->get($this->keyPrefix.$key);
	}
	public function keys($keys)
	{
		return $this->_redis->keys('*');
	}
	public function setList($key,$value,$isPutRight = TRUE)
	{
		!is_array($value) and $value = [$value];
		foreach($value as $vvalue)
		{
			$fun = $isPutRight ? 'rpush' : 'lpush' ;
   			$this->_redis->$fun($this->keyPrefix.$key,$vvalue);
		}
	}
	public function getList($key,$limit = 0,$offset = -1)
	{
		return $this->_redis->lrange($this->keyPrefix.$key,$limit,$offset);
	}
	public function info()
	{
		return $this->_redis->info();
	}
	public function close()
	{
		$this->_redis->close();
	}
	public function clear()
	{
		$this->_redis->flushDB();
	}
	public function delete($key)
	{
		$this->_redis->delete($this->keyPrefix.$key);
	}
}
