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

class HistoryModel extends Model {

	protected $tableName = 'webim_histories';

	public function get($uid, $with, $type='chat', $limit=30) {
		if( $type == "chat" ) {
			$where = "`type` = 'chat' AND ((`to`='$with' AND `from`='$uid' AND `fromdel` != 1)
					 OR (`send` = 1 AND `from`='$with' AND `to`='$uid' AND `todel` != 1))";
		} else {
			$where = "`to`='$with' AND `type`='grpchat' AND send = 1";
		}
		$rows = $this->where($where)->order('timestamp DESC')->limit(0, $limit)->select();
		return array_reverse( array_map( array($this, '_toObj'), $rows ) );
	}

	public function getOffline($uid, $limit = 50) {
		$rows = $this->where("`to`='$uid' and send != 1")->order('timestamp DESC ')->limit(0, $limit)->select();
		return array_reverse( array_map( array($this, '_toObj'), $rows ) );
	}

	public function insert($message) {
		$this->create($message);
		$this->created = date( 'Y-m-d H:i:s' );
		$this->add();
	}

	public function clear($uid, $with) {
		$this->where("`from`='$uid' and `to`='$with'")->save( array( "fromdel" => 1, "type" => "chat" ) );
		$this->where("`to`='$uid' and `from`='$with'")->save( array( "todel" => 1, "type" => "chat" ) );
		$this->where("todel=1 AND fromdel=1")->delete();
	}

	public function offlineReaded($uid) {
		$this->where("`to`='$uid' and send=0")->save(array("send" => 1));
	}

    private function _toObj($v) { return (object)$v; }

}

?>
