var store = (function()
	{
		var api =
		{
		},
		win = window,
		doc = win.document,
		localStorageName = 'localStorage',
		globalStorageName = 'globalStorage',
		storage;

		api.set = function(key, value)
		{
		};
		api.get = function(key)
		{
		};
		api.remove = function(key)
		{
		};
		api.clear = function()
		{
		};

		if (localStorageName in win && win[localStorageName])
		{
			storage = win[localStorageName];
			api.set = function(key, val)
			{
				storage.setItem(key, val)
			};
			api.get = function(key)
			{
				return storage.getItem(key)
			};
			api.remove = function(key)
			{
				storage.removeItem(key)
			};
			api.clear = function()
			{
				storage.clear()
			};


		} else if (globalStorageName in win && win[globalStorageName])
		{
			storage = win[globalStorageName][win.location.hostname];
			api.set = function(key, val)
			{
				storage[key] = val
			};
			api.get = function(key)
			{
				return storage[key] && storage[key].value
			};
			api.remove = function(key)
			{
				delete storage[key]
			};
			api.clear = function()
			{
				for (var key in storage)
				{
					delete storage[key]
				}
			};


		} else if (doc.documentElement.addBehavior)
		{
			function getStorage()
			{
				if (storage)
				{
					return storage
				}
				storage = doc.body.appendChild(doc.createElement('div'));
				storage.style.display = 'none';
				// See http://msdn.microsoft.com/en-us/library/ms531081(v=VS.85).aspx
				// and http://msdn.microsoft.com/en-us/library/ms531424(v=VS.85).aspx
				storage.addBehavior('#default#userData');
				storage.load(localStorageName);
				return storage;

			}
			api.set = function(key, val)
			{
				var storage = getStorage();
				storage.setAttribute(key, val);
				storage.save(localStorageName);

			};
			api.get = function(key)
			{
				var storage = getStorage();
				return storage.getAttribute(key);

			};
			api.remove = function(key)
			{
				var storage = getStorage();
				storage.removeAttribute(key);
				storage.save(localStorageName);

			};
			api.clear = function()
			{
				var storage = getStorage();
				var attributes = storage.XMLDocument.documentElement.attributes;;
				storage.load(localStorageName);
				for (var i = 0, attr; attr = attributes[i]; i++)
				{
					storage.removeAttribute(attr.name);

				}
				storage.save(localStorageName);

			};

		}
		return api;

	})();
//==============================================
var WX =
{
	API_URL:'app/app.php',
};
WX.loading =
{
	show: function()
	{
		if ($('.loading').size() == 0)
		{
			$('body').append('<div class="loading">加载中...</div>')
		}
		$('.loading').show();
	},
	hide: function()
	{
		$('.loading').hide();
	}
};
WX.get = function(act, callback, parms)
{
	parms = parms || '';
	WX.loading.show();
	$.getJSON(WX.API_URL + '?act=' + act + parms,
		function(data)
		{
			WX.loading.hide();
			callback(data)
		});
}
WX.creatfmtContent = function(d)
{
	var html = [];
	console.log(d);
	if (d.length == 0)
	{
		html.push('<li>暂无任何玩家加入，请将房号告诉你旁边的人，所有玩家都加入后，点击『分配关键词』开始游戏。</li>');
	}
	for (var i = 0; i < d.length; i++)
	{
		html.push('<li>' + (i + 1) + '. ' + d[i]['name'] + '<font color="' + ['#000000', '#ff0000'][d[i]['IsUndercover']] + '"> （' + d[i]['IdentityWord'] + '）</font><img src="js/del.png" onclick="WX.creatDel(\'' + d[i]['name'] + '\')" /></li>');
	}
	$('#memlist').html(html.join(''));
};
WX.wordNum = function()
{
	WX.get('IdentityWordNumber',
		function(d)
		{
			$('#wdnb').html(d);
		});
};
WX.joinCheck = function()
{
	WX.get('joinRoomCheck',
		function(d)
		{
			if(d == 0)
			{
				location.href = 'join.html';
			}
			else
			{
				location.href = 'joiner.html';
			}
		});
};
WX.join = function()
{
	var joinroomid = $('#joinroomid').val(),
	joinroomname = $('#joinroomname').val();
	if (joinroomid == '' || joinroomname == '')
	{
		alert('请输入房间号和昵称');
		return;
	}
	store.set('joinroomid', joinroomid);
	store.set('joinroomname', joinroomname);
	WX.get('joinRoomIn',
		function(d)
		{
			if(d == 0)
			{
				alert('房间号不存在');
				return;
			}
			else
			{
				location.href = 'joiner.html';
			}
		},
		'&id=' + joinroomid + '&name=' + joinroomname);
};
WX.roominfo = function()
{
	WX.get('joinRoomCheck',
		function(d)
		{
			if (d == 0)
			{
				location.href = 'index.html';
			}
			else if(d == -1)
			{
				alert('房主已经将你踢出！');
				location.href = 'join.html';
			}
			else if(d == -2)
			{
				alert('房间已经解散！');
				location.href = 'join.html';
			}
			else
			{
				$('#roominfo').html('<li class="home">房间号：<b>' + d.roomkey + '</b><li class="wordico">身份词：<b>' + d.myword + '</b></li></li>');
				for (var i = 0; i < d.list.length; i++)
				{
					d.list[i] = (i + 1) + '. ' + d.list[i];
				}
				$('#joinmemlist').html('<li>' + d.list.join('， ') + '</li>');
			}
		});
};
WX.createrRoomInfo = function()
{
	WX.get('createRoom',function(d)
		{
			if(d != null)
			{
				$('.home').html('房间号：<b>' + d.roomId + '</b>');
				WX.creatfmtContent(d.roomInfo);
			}
		});
};
WX.joinOut = function()
{
	if (confirm("确定要退出房间吗？"))
	{
		WX.get('joinRoomOut',
			function(d)
			{
				location.href = 'join.html';
			});
	}
};
WX.creatF5 = function()
{
	WX.get('reflushRoomUser',
		function(d)
		{
			WX.creatfmtContent(d);
		});
}
WX.creatSFC = function()
{
	if (confirm("确定要重新分配关键词吗？"))
	{
		WX.get('reflushIdentityWord',
			function(d)
			{
				if(d == -1)
				{
					alert('没有成员');
					return;
				}
				if (d.indexOf('error') != -1)
				{
					alert(d.replace('error', '错误'));
					return;
				}
				WX.creatfmtContent(d);
			});
	}
};
WX.creatOut = function()
{
	if (confirm("确定要销毁房间吗？"))
	{
		WX.get('createrRoomOut',
			function(d)
			{
				location.href = 'index.html';
			});
	}
};
WX.creatDel = function(name)
{
	if (confirm("确定要将 " + name + " 移除游戏吗？"))
	{
		WX.get('createrRemoveUser',
			function(d)
			{
				WX.creatfmtContent(d);
			},
			'&name=' + name);
	}
}
WX.upload = function()
{
	var k1 = $('#key1').val(),
	k2 = $('#key2').val();
	if (k1 == '' || k2 == '')
	{
		alert('请填写两个身份词');
		return;
	}
	WX.get('uploadIdentityWord',
		function(d)
		{
			alert('您的身份词已提交，非常感谢！');
			$('#key1,#key2').val('');
		},
		'&k1=' + k1 + '&k2=' + k2);
};
