<?php

/**
 * WebIM-for-OneThink
 * 
 * OneThink在线聊天插件
 *
 * @author      yangweijie <917647288@qq.com>
 * @author      Feng Lee <feng.lee at nextalk.im>
 * @copyright   2014 NexTalk.IM
 * @link        http://github.com/webim/webim-plugin-onethink
 * @license     MIT LICENSE
 * @version     0.3
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Addons\Webim\Controller;
use Home\Controller\AddonsController;
use Addons\Webim\WebimPlugin;
use Addons\Webim\Lib\Client;
use Addons\Webim\Lib\GeoIP;

/**
 * WebIM Controller
 *
 * @autho Ery Lee
 * @since 5.4.2
 */
class WebimController extends AddonsController {

    /**
     * Current user
     */
    private $user = null;

    /**
     * Model list
     */
	private $models;

	/*
	 * Webim Plugin
	 */
	private $plugin;

	/*
	 * WebIM Client
	 */
	private $client;

	public function _initialize() {
		C('URL_HTML_SUFFIX','');
        //define path
		define('WEBIM_PATH', ONETHINK_ADDON_PATH . 'Webim');
		define('WEBIM_IMAGE', WEBIM_PATH . '/static/images');
		define('WEBIM_PRODUCT', 'onethink');
        //webim config
		$IMC = get_addon_config('Webim');
        $IMC = array_merge(array(
            'debug'     => true,		//DEBUG模式
            'isopen'	=> true,		//开启webim
            'domain' 	=> 'localhost',	//消息服务器通信域名
            'apikey'	=> 'public',	//消息服务器通信APIKEY
            'server'    => array('t.nextalk.im:8000'),//IM服务器
            'theme'		=> 'base',		//界面主题，根据webim/static/themes/目录内容选择
            'local'		=> 'zh-CN',		//本地语言，扩展请修改webim/static/i18n/内容
            'emot'		=> 'default',	//表情主题
            'opacity'	=> 80,			//TOOLBAR背景透明度设置
            'visitor'	=> true, 		//支持访客聊天(默认好友为站长),开启后通过im登录无效
            'upload'	=> false,	//是否支持文件(图片)上传
            'show_realname'		=> false,	//是否显示好友真实姓名
            'show_unavailable'	=> true, //支持显示不在线用户
            'enable_login'		=> false,	//允许未登录时显示IM，并可从im登录
            'enable_menu'		=> false,	//隐藏工具条
            'enable_room'		=> true,	//禁止群组聊天
            'discussion'        => true,   //临时讨论组
            'enable_noti'		=> true,	//禁止通知
            'enable_chatlink'	=> true,	//禁止页面名字旁边的聊天链接
            'enable_shortcut'	=> false,	//支持工具栏快捷方式
        ), $IMC);
		C('IMC', $IMC);

        if( !$IMC['isopen'] ) exit(json_encode("Webim Not Opened"));

		//Models
        $this->models = array();
		$this->models['setting'] = D('Addons://Webim/Setting');
		$this->models['history'] = D('Addons://Webim/History');
        $this->models['visitor'] = D('Addons://Webim/Visitor');
        $this->models['room'] = D('Addons://Webim/Room');
        $this->models['member'] = D('Addons://Webim/Member');
        $this->models['blocked'] = D('Addons://Webim/Blocked');

		//Plugin
		$this->plugin = new WebimPlugin();
        //User
        $user = $this->plugin->user();
        if($user == null && $IMC['visitor']) {
            $user = $this->models['visitor']->findOrCreate();
        }

        if(!$user) exit(json_encode("Login Required"));

        $this->user = $user;

		//Ticket
		$ticket = I('ticket');
		if($ticket) $ticket = stripslashes($ticket);	

		//Client
        $this->client = new Client(
            $this->user, 
            $IMC['domain'], 
            $IMC['apikey'], 
            $IMC['server'], 
            $ticket
        );
	}

	public function boot() {

	
		$IMC = C('IMC');

		$fields = array(
			'theme', 
			'local', 
			'emot',
			'opacity',
            'discussion',
			'enable_room', 
			'enable_chatlink', 
			'enable_shortcut',
			'enable_noti',
			'enable_menu',
			'show_unavailable',
			'upload');

        $this->user->show = "unavailable";
        $uid = $this->user->id;

		$scriptVar = array(
		    'version' => "5.8",
			'product' => WEBIM_PRODUCT,
			'path' => addons_url('Webim://Webim/'),
			'static_path' => WEBIM_PATH . "/static",
			'is_login' => '1',
            'is_visitor' => $this->isvid($uid),
			'login_options' => '',
			'user' => $this->user,
			'setting' => $this->models['setting']->get($uid),
            'jsonp' => false,
			'min' => $IMC['debug'] ? "" : ".min"
		);

		foreach($fields as $f) { $scriptVar[$f] = $IMC[$f];	}

		echo "var _IMC = " . json_encode($scriptVar) . ";" . PHP_EOL;

		$script = <<<EOF
_IMC.script = window.webim ? '' : ('<link href="' + _IMC.static_path + '/webim' + _IMC.min + '.css?' + _IMC.version + '" media="all" type="text/css" rel="stylesheet"/><link href="' + _IMC.static_path + '/themes/' + _IMC.theme + '/jquery.ui.theme.css?' + _IMC.version + '" media="all" type="text/css" rel="stylesheet"/><script src="' + _IMC.static_path + '/webim' + _IMC.min + '.js?' + _IMC.version + '" type="text/javascript"></script><script src="' + _IMC.static_path + '/i18n/webim-' + _IMC.local + '.js?' + _IMC.version + '" type="text/javascript"></script>');
_IMC.script += '<script src="' + _IMC.static_path + '/webim.' + _IMC.product + '.js?vsn=' + _IMC.version + '" type="text/javascript"></script>';
document.write( _IMC.script );
EOF;
		header("Content-type: application/javascript");
		header("Cache-Control: no-cache");
		exit($script);
	}

    /**
     * Chatbox
     */
    public function chatbox() {
        $webim_path = addons_url('Webim://Webim/');
	    $static_path = WEBIM_PATH . "/static";
        $uid = I('uid');
        $buddies = $this->plugin->buddiesByIds($this->user->id, array($uid));
        if($buddies && isset($buddies[0])) {
            $buddy = $buddies[0];
        }
        if(!$buddy) {
			header("HTTP/1.0 404 Not Found");
			exit("User Not Found");
        }
	header('Content-Type',	'text/html; charset=utf-8');
	$page = <<<EOF
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" name="viewport">
<title>Webim ChatBox</title>
<link rel="stylesheet" type="text/css" href="{$static_path}/webim-chatbox.css"/>
<script type="text/javascript" src="{$static_path}/webim-chatbox.js"></script>
</head><body>
<body id="chatbox">
<div id="header">
<img id="avatar" class="avatar" src="{$buddy->avatar}"></img>
<h4 id="user">{$buddy->nick}</h4>
</div>
<div id="notice" class="chatbox-notice ui-state-highlight" style="display: none;">
</div>
<div id="content"><div id="histories"></div></div>
<div id="footer">
<table style="width:100%"><tbody><tr><td width="100%">
<input type="hidden" id="to" value="{$buddy->id}">
<input type="text" data-inline="true" placeholder="请这里输入消息..." name="" id="inputbox">
</td><tr><tbody></table>
</div>
<script>
(function(webim, options) {
var path = options.path || "";
function url(api) { return path + api; }
webim.route({
online: url("/_action/online"),
offline: url("/_action/offline"),
deactivate: url("/_action/refresh"),
message: url("/_action/message"),
presence: url("/_action/presence"),
status: url("/_action/status"),
setting: url("/_action/setting"),
history: url("/_action/history"),
buddies: url("/_action/buddies")
});
var im = new webim(null, options);
var chatbox = new webim.chatbox(im, options);
im.online();
})(webim, {touid: '{$buddy->id}', path:'{$webim_path}'})
</script>
</body>
</html>
EOF;
    exit($page);
    }

    /**
     * Online
     */
	public function online() {
		$IMC = C('IMC');

		$uid = $this->user->id;
        $show = I('show');

        //buddy, room, chatlink ids
		$chatlinkIds= $this->idsArray(I('chatlink_ids', '') );
		$activeRoomIds = $this->idsArray( I('room_ids') );
		$activeBuddyIds = $this->idsArray( I('buddy_ids') );

		//active buddy who send a offline message.
		$offlineMessages = $this->models['history']->getOffline($uid);
		foreach($offlineMessages as $msg) {
			if(!in_array($msg->from, $activeBuddyIds)) {
				$activeBuddyIds[] = $msg->from;
			}
		}
        //buddies of uid
		$buddies = $this->plugin->buddies($uid);
        $buddyIds = array_map(array($this, 'buddyId'), $buddies);
        $buddyIdsWithoutInfo = array();
        foreach(array_merge($chatlinkIds, $activeBuddyIds) as $id) {
            if( !in_array($id, $buddyIds) ) {
                $buddyIdsWithoutInfo[] = $id;
            }
        }
        //buddies by ids
		$buddiesByIds = $this->plugin->buddiesByIds($uid, $buddyIdsWithoutInfo);

        //all buddies
        $buddies = array_merge($buddies, $buddiesByIds);
        $allBuddyIds = array();
        foreach($buddies as $buddy) { $allBuddyIds[] = $buddy->id; }

        $rooms = array(); $roomIds = array();
		if( $IMC['enable_room'] ) {

            //persistent rooms
			$persistRooms = $this->plugin->rooms($uid);
            //temporary rooms
			$temporaryRooms = $this->models['room']->rooms($uid);
            $rooms = array_merge($persistRooms, $temporaryRooms);
            $roomIds = array_map(array($this, 'roomId'), $rooms);
		}

		//===============Online===============
		$data = $this->client->online($allBuddyIds, $roomIds, $show);
		if( $data->success ) {
            $rtBuddies = array();
            $presences = $data->presences;
            foreach($buddies as $buddy) {
                $id = $buddy->id;
                //fix invisible problem
                if( isset($presences->$id) && $presences->$id != "invisible") {
                    $buddy->presence = 'online';
                    $buddy->show = $presences->$id;
                } else {
                    $buddy->presence = 'offline';
                    $buddy->show = 'unavailable';
                }
                $rtBuddies[$id] = $buddy;
            }
			//histories for active buddies and rooms
			foreach($activeBuddyIds as $id) {
                if( isset($rtBuddies[$id]) ) {
                    $rtBuddies[$id]->history = $this->models['history']->get($uid, $id, "chat" );
                }
			}
            if( !$IMC['show_unavailable'] ) {
                $olBuddies = array();
                foreach($rtBuddies as $buddy) {
                    if($buddy->presence === 'online') $olBuddies[] = $buddy;
                }
                $rtBuddies = $olBuddies;
            }
            $rtRooms = array();
            if( $IMC['enable_room'] ) {
                foreach($rooms as $room) {
                    $rtRooms[$room->id] = $room;
                }
                foreach($activeRoomIds as $id){
                    if( isset($rtRooms[$id]) ) {
                        $rtRooms[$id]->history = $this->models['history']->get($uid, $id, "grpchat" );
                    }
                }
            }

			$this->models['history']->offlineReaded($uid);

            if($show) $this->user->show = $show;

            $data = (array)$data;
            $data['user'] = $this->user;
            $data['buddies'] = array_values($rtBuddies);
            $data['rooms'] = array_values($rtRooms);
            $data['new_messages'] = $offlineMessages;
            $data['server_time'] = microtime(true) * 1000;
            $this->ajaxReturn($data, 'JSON');
		} else {
			$this->ajaxReturn(array ( 
				'success' => false,
                'error' => $data
            ), 'JSON'); 
        }
	}

    /**
     * Offline
     */
	public function offline() {
		$this->client->offline();
		$this->okReturn();
	}

    /**
     * Browser Refresh, may be called
     */
	public function refresh() {
		$this->client->offline();
		$this->okReturn();
	}

    /**
     * Buddies by ids
     */
	public function buddies() {
        $uid = $this->user->id;
		$ids = I('ids');
        $vids = array();
        $uids = array();
        foreach(explode(',', $ids) as $id) {
            if($this->isvid($id)) { 
                $vids[] = $id;
            } else {
                $uids[] = $id;
            }
        }
        $buddies = array_merge(
            $this->plugin->buddiesByIds($uid, $uids),
            $this->models['visitor']->visitorsByIds($vids)
        );
        $buddyIds = array_map(array($this, 'buddyId'), $buddies);
        $presences = $this->client->presences($buddyIds);
        foreach($buddies as $buddy) {
            $id = $buddy->id;
            if( isset($presences->$id) ) {
                $buddy->presence = 'online';
                $buddy->show = $presences->$id;
            } else {
                $buddy->presence = 'offline';
                $buddy->show = 'unavailable';
            }
        }
		$this->ajaxReturn($buddies, 'JSON');
	}
    
    /**
     * Buddies by ids
     */
	public function remove_buddy() {
        $uid = $this->user->id;
		$id = I('id');
		$this->okReturn();
    }

    /**
     * Send Message
     */
	public function message() {
		$type = I("type");
		$offline = I("offline");
		$to = I("to");
		$body = I("body");
		$style = I("style");
		$send = $offline == "true" || $offline == "1" ? 0 : 1;
		$timestamp = microtime(true) * 1000;
		if( strpos($body, "webim-event:") !== 0 ) {
			$this->models['history']->insert(array(
				"send" => $send,
				"type" => $type,
				"to" => $to,
                'from' => $this->user->id,
                'nick' => $this->user->nick,
				"body" => $body,
				"style" => $style,
				"timestamp" => $timestamp,
			));
		}
		//if($send == 1){//Why? for invisible user as offline
			$this->client->message(null, $to, $body, $type, $style, $timestamp);
		//}
		$this->okReturn();
	}

    /**
     * Update Presence
     */
	public function presence() {
		$show = I('show');
		$status = I('status');
		$this->client->presence($show, $status);
		$this->okReturn();
	}

    /**
     * Send Status
     */
    public function status() {
		$to = I("to");
		$show = I("show");
		$this->client->status($to, $show);
		$this->okReturn();
	}

    /**
     * Read History
     */
	public function history() {
		$uid = $this->user->id;
		$with = I('id');
		$type = I('type');
		$histories = $this->models['history']->get($uid, $with, $type);
		$this->ajaxReturn($histories, "JSON");
	}

    /**
     * Clear History
     */
	public function clear_history() {
        $uid = $this->user->id;
		$id = I('id'); //$with
		$this->models['history']->clear($uid, $id);
		$this->okReturn();
	}

	//FIXME Later
	public function clearHistory() {
			if($_POST) {
			    switch( $_POST['ago'] ) {
				case 'weekago':
					$ago = 7*24*60*60;break;
				case 'monthago':
					$ago = 30*24*60*60;break;
				case '3monthago':
					$ago = 3*30*24*60*60;break;
				default:
					$ago = 0;
				}
				$ago = ( time() - $ago ) * 1000;
				$db_prefix = C('DB_PREFIX');
				$sql = "DELETE FROM `{$db_prefix}webim_histories` WHERE `timestamp` < {$ago}";
			    D()->execute($sql);
			    $this->success('清除成功: ' . $sql);
		    }
	}


    /**
     * Download History
     */
	public function download_history() {
        $uid = $this->user->id;
		$id = I('id');
		$type = I('type');
		$histories = $this->models['history']->get($uid, $id, $type, 1000 );
		$date = date( 'Y-m-d' );
		if( I('date') ) {
			$date = I('date');
		}
		header('Content-Type',	'text/html; charset=utf-8');
		header('Content-Disposition: attachment; filename="histories-'.$date.'.html"');
		echo "<html><head>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
		echo "</head><body>";
		echo "<h1>Histories($date)</h1>".PHP_EOL;
		echo "<table><thead><tr><td>用户</td><td>消息</td><td>时间</td></tr></thead><tbody>";
		foreach($histories as $history) {
			$nick = $history->nick;
			$body = $history->body;
			$style = $history->style;
			$time = date( 'm-d H:i', (float)$history->timestamp/1000 ); 
			echo "<tr><td>{$nick}:</td><td style=\"{$style}\">{$body}</td><td>{$time}</td></tr>";
		}
		echo "</tbody></table>";
		echo "</body></html>";
	}

    /**
     * Get rooms
     */
	public function rooms() {
        $uid = $this->user->id;
		$ids = I("ids");
        $ids = explode(',', $ids);
        $persistRooms = $this->plugin->roomsByIds($uid, $ids);
        $temporaryRooms = $this->models['room']->roomsByIds($uid, $ids);
		$this->ajaxReturn(array_merge($persistRooms, $temporaryRooms), 'JSON');	
	}

    /**
     * Invite room
     */
    public function invite() {
        $uid = $this->user->id;
        $roomId = I('id');
        $nick = I('nick');
        if(strlen($nick) === 0) {
			header("HTTP/1.0 400 Bad Request");
			exit("Nick is Null");
        }
        //find persist room 
        $room = $this->findRoom($this->models['room'], $roomId);
        if(!$room) {
            //create temporary room
            $room = $this->models['room']->insert(array(
                'owner' => $uid,
                'name' => $roomId, 
                'nick' => $nick
            ));
        }
        //join the room
        $this->models['member']->join($roomId, $uid, $this->user->nick);
        //invite members
        $members = explode(",", I('members'));
        $members = $this->plugin->buddiesByIds($uid, $members);
        $this->models['room']->invite($roomId, $members);
        //send invite message to members
        foreach($members as $m) {
            $body = "webim-event:invite|,|{$roomId}|,|{$nick}";
            $this->client->message(null, $m->id, $body); 
        }
        //tell server that I joined
        $this->client->join($roomId);
        $this->ajaxReturn(array(
            'id' => $room->name,
            'nick' => $room->nick,
            'temporary' => true,
            'avatar' => WEBIM_IMAGE('room.png')
        ), 'JSON');
    }

    /**
     * Join room
     */
	public function join() {
        $uid = $this->user->id;
        $roomId = I('id');
        $nick = I('nick');
        $room = $this->findRoom($this->plugin, $roomId);
        if(!$room) {
            $room = $this->findRoom($this->models['room'], $roomId);
        }
        if(!$room) {
			header("HTTP/1.0 404 Not Found");
			exit("Can't found room: {$roomId}");
        }
        $this->models['room']->join($roomId, $uid, $this->user->nick);
        $data = $this->client->join($roomId);
        $this->ajaxReturn(array(
            'id' => $roomId,
            'nick' => $nick,
            'temporary' => true,
            'avatar' => WEBIM_IMAGE('room.png')
        ), 'JSON');
	}

    /**
     * Leave room
     */
	public function leave() {
        $uid = $this->user->id;
		$room = I('id');
		$this->client->leave( $room );
        $this->models['room']->leave($room, $uid);
		$this->okReturn();
	}

    /**
     * Room members
     */
	public function members() {
        $members = array();
        $roomId = I('id');
        $room = $this->findRoom($this->plugin, $roomId);
        if($room) {
            $members = $this->plugin->members($roomId);
        } else {
            $room = $this->findRoom($this->models['room'], $roomId);
            if($room) {
                $members = $this->models['member']->allInRoom($roomId);
            }
        }
        if(!$room) {
			header("HTTP/1.0 404 Not Found");
			exit("Can't found room: {$roomId}");
        }
        $presences = $this->client->members($roomId);
        $rtMembers = array();
        foreach($members as $m) {
            $id = $m->id;
            if( isset($presences->$id) && $presences->$id != "invisible" ) {
                $m->presence = 'online';
                $m->show = $presences->$id;
            } else {
                $m->presence = 'offline';
                $m->show = 'unavailable';
            }
            $rtMembers[] = $m;
        }
        usort($rtMembers, function($m1, $m2) {
            if($m1->presence === $m2->presence) return 0;
            if($m1->presence === 'online') return 1;
            return -1;
        });
        $this->ajaxReturn($rtMembers, 'JSON');
	}

    /**
     * Block room
     */
    public function block() {
        $uid = $this->user->id;
        $room = I('id');
        $this->models['blocked']->insert($room, $uid);
        $this->okReturn();
    }

    /**
     * Unblock room
     */
    public function unblock() {
        $uid = $this->user->id;
        $room = I('id');
        $this->models['blocked']->remove($room, $uid);
        $this->okReturn();
    }

    /**
     * Notifications
     */
	public function notifications() {
        $uid = $this->user->id;
		$notifications = $this->plugin->notifications($uid);
		$this->ajaxReturn($notifications, 'JSON');
	}

    /**
     * Asks
     */
    public function asks() {
        $uid = $this->user->id;
		$asks = $this->models['ask']->all($uid);
		$this->ajaxReturn($asks, 'JSON');
    }

    /**
     * Accept Ask
     */
    public function accept_ask() {
        $uid = $this->user->id;
        $askid = I('askid');
        //TODO: insert into buddies
        //TODO: should send presence
		$this->models['ask']->accept($uid, $askid);
        $this->okReturn();
    }

    /**
     * Reject Ask
     */
    public function reject_ask() {
        $uid = $this->user->id;
        $askid = I('askid');
        //TODO: should send presence
		$this->models['ask']->reject($uid, $askid);
        $this->okReturn();
    }

    /**
     * Setting
     */
	public function setting() {
		if(isset($_GET['data'])) {
			$data = $_GET['data'];
		} 
		if(isset($_POST['data'])) {
			$data = $_POST['data'];
		}
		$uid = $this->user->id;
		$this->models['setting']->set($uid, $data);
		$this->okReturn();
	}

	private function okReturn() {
		$this->ajaxReturn(array('status' => 'ok'), 'JSON');
	}

    private function findRoom($obj, $id) {
        $uid = $this->user->id;
        $rooms = $obj->roomsByIds($uid, array($id));
        if($rooms && isset($rooms[0])) return $rooms[0];
        return null;
    }

	private function idsArray( $ids ){
		return ($ids===null || $ids==="") ? array() : (is_array($ids) ? array_unique($ids) : array_unique(explode(",", $ids)));
	}

    private function isvid($id) {
        return strpos($id, 'vid:') === 0;
    }

    private function roomId($room) {
        return $room->id;
    }

    private function buddyId($buddy) {
        return $buddy->id;
    }

}
