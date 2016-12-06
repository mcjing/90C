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
 * @version     0.2
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

namespace Addons\Webim;

/**
 * WebIM与OneThink的插件接口
 *
 * @since 0.2
 */
class WebimPlugin {

	/*
	 * Init User
	 */
    public function __construct() { 
    }

    /**
     * API: current user
     *
     * @return object current user
     */
    public function user() {
        $uid = is_login();
        if( !$uid ) { return null; }
		return (object)array(
            'id' => $uid,
            'nick' => get_username($uid),
            'presence' => 'online',
            'show' => "available",
            'avatar' => WEBIM_IMAGE. "/male.png",
            'url' => "#",
            'role' => 'user',
            'status' => "",
        );
    }

	/*
	 * API: Buddies of current user.
     *
     * @param string $uid current uid
	 *
     * @return array Buddy list
     *
	 * Buddy:
	 *
	 * 	id:         uid
	 * 	uid:        uid
	 *	nick:       nick
	 *	avatar:    url of photo
     *	presence:   online | offline
	 *	show:       available | unavailable | away | busy | hidden
	 *  url:        url of home page of buddy 
	 *  status:     buddy status information
	 *  group:      group of buddy
	 *
	 */
	public function buddies($uid) {
		//TODO: 返回全部用户
		$users = M('Member')->select();
		//TODO: DEMO Code
		return array_map( array($this, '_buddy'),  $users);
	}

	/*
	 * API: buddies by ids
	 *
     * @param array $ids buddy id array
     *
     * @return array Buddy list
     *
	 * Buddy
	 */
	public function buddiesByIds($uid, $ids) {
	if( !count($ids) ) return array();
	$ids = implode($ids, ',');
	$users = M('Member')->where("uid in ($ids)")->select();
        return array_map( array($this, '_buddy'), $users );
	}

    /**
     * Demo Buddy
     */
    private function _buddy($user) {
        return (object) array(
            'id' => $user['uid'],
            'group' => 'friend',
            'nick' => $user['nickname'],
            'presence' => 'offline',
            'show' => 'unavailable',
            'status' => '#',
            'avatar' => WEBIM_IMAGE . ($user['sex'] ? '/female.png' : '/male.png')
        );
    }

	/*
	 * API：rooms of current user
     * 
     * @param string $uid 
     *
     * @return array rooms
     *
	 * Room:
	 *
	 *	id:		    Room ID,
	 *	nick:	    Room Nick
	 *	url:	    Home page of room
	 *	avatar:    Pic of Room
	 *	status:     Room status 
	 *	count:      count of online members
	 *	all_count:  count of all members
	 *	blocked:    true | false
	 */
	public function rooms($uid) {
        //TODO: DEMO CODE
		$room = (object)array(
			'id' => 'room',
            'name' => 'room',
			'nick' => '聊天室',
			'url' => "#",
			'avatar' => WEBIM_IMAGE . '/room.png',
			'status' => "Room",
			'blocked' => false,
            'temporary' => false
		);
		return array( $room );	
	}

	/*
	 * API: rooms by ids
     *
     * @param array id array
     *
     * @return array rooms
	 *
	 * Room
     *
	 */
	public function roomsByIds($uid, $ids) {
        $rooms = array();
        foreach($ids as $id) {
            if($id === 'room') { 
                $rooms[] = (object)array(
                    'id' => $id,
                    'name' => $id,
                    'nick' => '聊天室',
                    'url' => "#",
                    'avatar' => WEBIM_IMAGE . '/room.png'
                );
            }
        }
		return $rooms;
	}

    /**
     * API: members of room
     *
     * $param $room string roomid
     * 
     */
    public function members($room) {
        //TODO: DEMO CODE
	$users = M('Member')->select();
        return array_map( array($this, '_member'), $users );
    }

    /**
     * Demo member
     */
    private function _member($user) {
        return (object)array(
            'id' => $user['uid'],
            'nick' => $user['nickname'],
        ); 
    }

	/*
	 * API: notifications of current user
	 *
     * @return array  notification list
     *
	 * Notification:
	 *
	 * 	text: text
	 * 	link: link
	 */	
	public function notifications($uid) {
        $noti = (object)array('text' => 'Notification', 'link' => '#');
		return array($noti);
	}

    /**
     * API: menu
     *
     * @return array menu list
     *
     * Menu:
     *
     * icon
     * text
     * link
     */
    public function menu($uid) {
        return array();
    }

    private function isvid($id) {
        return strpos($id, 'vid:') === 0;
    }

}

