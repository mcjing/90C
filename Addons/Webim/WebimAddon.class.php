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
use Common\Controller\Addon;

class WebimAddon extends Addon{
    
    public $custom_config = 'View/config.html';

    public $info = array(
        'name'=>'Webim',
        'title'=>'WebIM',
        'description'=>'WebIM在线聊天插件',
        'status'=>1,
        'author'=>'nextalk',
        'version'=>'0.2'
    );

    public function install(){
        if( !ini_get('allow_url_fopen') ){
            session('addons_install_error', ',请先将php.ini中的allow_url_fopen开启');
            return false;
        }

        $db_prefix = C('DB_PREFIX') . 'webim_';
        $sql = "CREATE TABLE `{$db_prefix}settings` (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `uid` varchar(40) NOT NULL DEFAULT '',
                  `data` text,
                  `created` datetime DEFAULT NULL,
                  `updated` datetime DEFAULT NULL,
                  UNIQUE KEY `webim_setting_uid` (`uid`),
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        D()->execute($sql);

        $sql = "CREATE TABLE `{$db_prefix}histories` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `send` tinyint(1) DEFAULT NULL,
              `type` varchar(20) DEFAULT NULL,
              `to` varchar(50) NOT NULL,
              `from` varchar(50) NOT NULL,
              `nick` varchar(20) DEFAULT NULL COMMENT 'from nick',
              `body` text,
              `style` varchar(150) DEFAULT NULL,
              `timestamp` double DEFAULT NULL,
              `todel` tinyint(1) NOT NULL DEFAULT '0',
              `fromdel` tinyint(1) NOT NULL DEFAULT '0',
              `created` date DEFAULT NULL,
              `updated` date DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `webim_history_timestamp` (`timestamp`),
              KEY `webim_history_to` (`to`),
              KEY `webim_history_from` (`from`),
              KEY `webim_history_send` (`send`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        D()->execute($sql);

        $sql = "CREATE TABLE `{$db_prefix}rooms` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `owner` varchar(40) NOT NULL,
              `name` varchar(40) NOT NULL,
              `nick` varchar(60) NOT NULL DEFAULT '',
              `topic` varchar(60) DEFAULT NULL,
              `url` varchar(100) DEFAULT '#',
              `created` datetime DEFAULT NULL,
              `updated` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `webim_room_name` (`name`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        D()->execute($sql);

        $sql = "CREATE TABLE `{$db_prefix}members` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `room` varchar(60) NOT NULL,
              `uid` varchar(40) NOT NULL,
              `nick` varchar(60) NOT NULL,
              `joined` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `webim_member_room_uid` (`room`,`uid`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        D()->execute($sql);

        $sql = "CREATE TABLE `{$db_prefix}blocked` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `room` varchar(60) NOT NULL,
              `uid` varchar(40) NOT NULL,
              `blocked` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `webim_blocked_room_uid` (`uid`,`room`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        D()->execute($sql);

        $sql = "CREATE TABLE `{$db_prefix}visitors` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(60) DEFAULT NULL,
              `ipaddr` varchar(60) DEFAULT NULL,
              `url` varchar(100) DEFAULT NULL,
              `referer` varchar(100) DEFAULT NULL,
              `location` varchar(100) DEFAULT NULL,
              `created` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `webim_visitor_name` (`name`)
        )ENGINE=MyISAM AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8";
        D()->execute($sql);

        $tables = array('histories', 'settings', 'rooms', 'members', 'blocked', 'visitors');
        $count = 0;
        foreach($tables as $t) {
            $res = M()->query("SHOW TABLES LIKE '{$db_prefix}{$t}'");
            $count += count($res);
        }
        if($count == count($tables)) {
            return true;
        }
        session('addons_install_error', ',部分表未创建成功，请手动检查插件中的sql，修复后重新安装');
        return false;
    }

    public function uninstall(){
        $db_prefix = C('DB_PREFIX') . 'webim_';
        $tables = array('histories', 'settings', 'rooms', 'members', 'blocked', 'visitors');
        foreach($tables as $table) {
            $sql = "DROP TABLE IF EXISTS `{$db_prefix}{$table}`;";
            D()->execute($sql);
        }
        return true;
    }

    //实现的pageFooter钩子方法
    public function pageFooter($param) {
        $run = addons_url('Webim://Webim/boot');
        echo <<<EOL
        <script type="text/javascript" src="{$run}"></script>
EOL;

    }

}
