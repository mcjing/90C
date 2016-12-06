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

namespace Addons\Webim\Model;
use Think\Model;

class RoomModel extends Model {

	protected $tableName = 'webim_rooms';

    public function insert($data) {
        $name = $data['name'];
		$room = $this->where("name={$name}")->find();
        if($room) return $room;
        $this->create($data);
        $this->created = date( 'Y-m-d H:i:s' );
        $this->add();
        return (object)$data;
    }

    public function rooms($uid) {
        $rooms = D('Addons://Webim/Member')->rooms($uid);
        if(empty($rooms)) return array();
        $names = implode("','", $rooms);
        $rows = $this->where("name in ('{$names}')")->select();
        $rooms = array();
        foreach($rows as $row) {
            $rooms[] = (object)array(
               'id' => $row['name'],
               'name' => $row['name'],
               'nick' => $row['nick'],
               "url" => $row['url'],
               "avatar" => WEBIM_IMAGE("room.png"),
               "status" => "",
               "temporary" => true,
               "blocked" => false
            );
        }
        return $rooms;
    }

    public function roomsByIds($uid, $ids) {
       if(empty($ids)) return array();
       $ids = implode("','",  $ids);
       $rows = $this->where("name in ('{$ids}')")->select();
       $rooms = array();
       foreach($rows as $row) {
           $rooms[] = (object)array(
               'id' => $row['name'],
               'name' => $row['name'],
               'nick' => $row['nick'],
               "url" => $row['url'],
               "avatar" => WEBIM_IMAGE . "/room.png",
               "status" => "",
               "temporary" => true,
               "blocked" => false);     
       }
        return $rooms;
    }

    public function invite($room, $members) {
        foreach($members as $member) {
            D('Addons://Webim/member')->join($room, $member->id, $member->nick);
        }
    }

    public function leave($room, $uid) {
        D('Addons://Webim/member')->where("room = '{$room}' and uid = '{$uid}'")->delete();
        $count = D('Addons://Webim/member')->where("room = '{$room}'")->count('id');
        if($count === 0) {
            $this->where("name = '$room'")->delete();
        }
    }

}

?>
