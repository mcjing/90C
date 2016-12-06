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

class MemberModel extends Model {

    protected $tableName = 'webim_members';

    public function allInRoom($room) {
        $rows = $this->where("room = '{$room}'")->select();
        $members = array();
        foreach($rows as $row) {
            $members[] = (object)array(
                'id' => $row['uid'],
                'nick' => $row['nick']
            );
        }
        return $members;
    }

    public function rooms($uid) {
        $rows = $this->where("uid = '{$uid}'")->select();
        $rooms = array();
        foreach($rows as $row) {
            $rooms[] = $row['room'];
        }
        return $rooms;
    }

    public function join($room, $uid, $nick) {
        $m = $this->where("room='{$room}' and uid='{$uid}'")->find();
        if($m) return;
        $this->create(array(
            'uid' => $uid,
            'room' => $room,
            'nick' => $nick
        ));
        $this->joined = date( 'Y-m-d H:i:s' );
        $this->add();
    }

}

?>
