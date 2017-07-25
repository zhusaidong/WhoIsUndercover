<?php
/**
* 谁是卧底主程序
* @author Zsdroid [635925926@qq.com]
*/
class WhoIsUndercover
{
	public function __construct($kv = null,$keyPrefix = '',$get = [])
	{
		$this->keyPrefix = $keyPrefix;
		$this->kv = $kv;
		$this->get = $get;
	}
	private function getCookie($key)
	{
		$key = $this->keyPrefix.$key;
		return (!isset($_COOKIE[$key]) or empty(isset($_COOKIE[$key]))) ? '' : $_COOKIE[$key];
	}
	private function setCookie($key,$value = '',$del = FALSE)
	{
		setcookie($this->keyPrefix.$key,$value, $del ? time() - 86400 : time() + 86400);
	}
	private function outPut($data = [])
	{
		exit(is_array($data) ? json_encode($data,JSON_UNESCAPED_UNICODE) : (string)$data);
	}
	/**
	* 创建房间
	*/
	public function createRoom()
	{
		$roomId = $this->getCookie('roomId');
		if(empty($roomId))
		{
			$roomId = rand(10000,99999);
			$this->setCookie('roomId',$roomId);
			$this->kv->set($roomId,json_encode([],JSON_UNESCAPED_UNICODE));
		}

		$roomInfo = json_decode($this->kv->get($roomId),TRUE);
		$this->outPut([
				'roomId'	=>$roomId,
				'roomInfo'	=>$roomInfo
			]);
	}
	/**
	* 刷新用户
	*/
	public function reflushRoomUser()
	{
		$roomId = $this->getCookie('roomId');
		if(empty($roomId))
		{
			$this->outPut(0);
		}

		$roomInfo = json_decode($this->kv->get($roomId),true);
		$this->outPut($roomInfo);
	}
	/**
	* 分配身份词
	*/
	public function reflushIdentityWord()
	{
		$roomId = $this->getCookie('roomId');
		if(empty($roomId))
		{
			$this->outPut(0);
		}

		$lists = json_decode($this->kv->get($roomId),true);

		//卧底数量与参与人数有关
		$wdNumbers = [
			1=>1,
			2=>1,
			3=>1,
			4=>1,
			5=>1,
			6=>2,
			7=>2,
			8=>2,
			9=>2,
			10=>3,
			11=>3,
			12=>3,
			13=>3,
			14=>4,
			15=>4,
			16=>5,
		];
		$allKeys   = json_decode($this->kv->get('IdentityWord'),true);
		$oneKeys = $allKeys[rand(0,count($allKeys) - 1)];

		for($i = 0; $i < count($lists); $i++)
		{
			$lists[$i]['IdentityWord'] = $oneKeys['k1'];
			$lists[$i]['IsUndercover'] = 0;
		}
		$count = count($lists);
		if($count == 0)
		{
			$this->outPut( - 1);
		}
		$count > 16 and $count    = 16;

		$wdNumber = $wdNumbers[$count];

		$randWD   = array_rand($lists,$wdNumber);
		for($i = 0; $i < count($randWD); $i++)
		{
			if($wdNumber > 2)
			{
				//当卧底有2人以上时，卧底的词有20 % 的几率抽到 "空白" 。
				$randArray = [1,1,1,1,1,1,1,1,2,2];
				shuffle($randArray);
				$lists[$i]['IdentityWord'] = $randArray[rand(0,9)] == 2 ? 'empty' : $oneKeys['k2'];
			}
			else
			{
				$lists[$i]['IdentityWord'] = $oneKeys['k2'];
			}
			$lists[$i]['IsUndercover'] = 1;
		}

		$this->kv->set($roomId,json_encode($lists,JSON_UNESCAPED_UNICODE));
		$this->outPut($lists);
	}
	/**
	* 移除用户游戏
	*/
	public function createrRemoveUser()
	{
		$roomId = $this->getCookie('roomId');
		$delName= isset($this->get['name'])?$this->get['name']:'';
		$roomInfo = json_decode($this->kv->get($roomId),true);
		foreach($roomInfo as $key => $value)
		{
			if($value['name'] == $delName)
			{
				unset($roomInfo[$key]);
			}
		}
		$roomInfo = array_values($roomInfo);
		$this->kv->set($roomId,json_encode($roomInfo,JSON_UNESCAPED_UNICODE));
		$this->reflushRoomUser();
	}
	/**
	* 销毁房间
	*/
	public function createrRoomOut()
	{
		$roomId = $this->getCookie('roomId');
		$this->kv->delete($roomId);
		$this->setCookie('roomId',$roomId,TRUE);
		$this->outPut(1);
	}
	/**
	* 身份词数量
	*/
	public function IdentityWordNumber()
	{
		$IdentityWord = $this->kv->get('IdentityWord');
		$this->outPut(count(json_decode($IdentityWord,true)));
	}
	/**
	* 身份词提交
	*/
	public function uploadIdentityWord()
	{
		$IdentityWord = json_decode($this->kv->get('IdentityWord'),true);
		$IdentityWord[] = [
			'k1'=>$this->get['k1'],
			'k2'=>$this->get['k2'],
		];
		$this->kv->set('IdentityWord',json_encode($IdentityWord,JSON_UNESCAPED_UNICODE));
		$this->outPut(1);
	}
	/**
	* 加入房间检查
	*/
	public function joinRoomCheck()
	{
		$roomIdAndName = $this->getCookie('roomIdAndName');
		if(empty($roomIdAndName))
		{
			$this->outPut(0);
		}
		else
		{
			$roomIdAndNames = explode(',',$roomIdAndName);
			$roomId         = $roomIdAndNames[0];
			$name           = $roomIdAndNames[1];
			
			$roomInfo = $this->kv->get($roomId);
			if(empty($roomInfo))
			{
				$this->setCookie('roomIdAndName','',TRUE);
				$this->outPut(-2);
			}
			$lists          = json_decode($roomInfo,TRUE);
			
			$inRoom = 0;
			$myword = '';
			$list   = [];
			for($i = 0; $i < count($lists); $i++)
			{
				if($lists[$i]['name'] == $name)
				{
					$inRoom++;
					$myword = $lists[$i]['IdentityWord'];
				}
				$list[] = $lists[$i]['name'];
			}
			if($inRoom == 0)
			{
				$this->setCookie('roomIdAndName','',TRUE);
				$this->outPut(-1);
			}
			$this->outPut([
					'roomkey'=>$roomId,
					'myword' =>$myword,
					'list'   =>$list
				]);
		}
	}
	/**
	* 加入房间
	*/
	public function joinRoomIn()
	{
		$roomId = !empty($this->get['id'])?$this->get['id']:'';
		$name = !empty($this->get['name'])?$this->get['name']:'';
		$roomInfo = $this->kv->get($roomId);
		if(empty($roomInfo))
		{
			$this->outPut(0);
		}
		else
		{
			$this->setCookie('roomIdAndName',$roomId.','.$name);
			$array = json_decode($this->kv->get($roomId),true);
			$array[] = [
				'name'			=>$name,
				'IdentityWord'	=>'',
				'IsUndercover'	=>0,
			];
			$this->kv->set($roomId,json_encode($array,JSON_UNESCAPED_UNICODE));
			$this->outPut(1);
		}
	}
	/**
	* 退出房间
	*/
	public function joinRoomOut()
	{
		$roomIdAndName = $this->getCookie('roomIdAndName');
		$roomIdAndNames= explode(',',$roomIdAndName);
		$roomId        = $roomIdAndNames[0];
		$name          = $roomIdAndNames[1];
		$array = json_decode($this->kv->get($roomId),true);
		$list = [];
		foreach($array as $key => $value)
		{
			if($name == $value['name'])
			{
				unset($array[$key]);
			}
		}
		$array = array_values($array);
		$this->kv->set($roomId,json_encode($array,JSON_UNESCAPED_UNICODE));
		$this->setCookie('roomIdAndName','',TRUE);
		$this->outPut(1);
	}
}
