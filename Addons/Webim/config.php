<?php

/**
 * WebIM-for-OneThink
 * 
 * OneThink在线聊天插件
 *
 * @authro      yangweijie <917647288@qq.com>
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

return array(
	'group'=>array(
		'type'=>'group',
		'options'=>array(
			'setting'=>array(
				'title'=>'设置',
				'options'=>array(
                    'isopen' => array(
						'title'=>'开启WebIM',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'1',
                    ),
					'domain'=>array(
						'title'=>'IM服务器通信域名:',
						'type'=>'text',
						'value'=>'localhost',
						'tip'=>'在线版用户输入在nextalk.im中注册的域名'
					),
					'apikey'=>array(
						'title'=>'IM服务器通信APIKEY:',
						'type'=>'text',
						'value'=>'public',
					),
					'server'=>array(
						'title'=>'IM服务器地址',
						'type'=>'text',
						'value'=>'t.nextalk.im:8000',
					),
					'local'=>array(
						'title'=>'本地语言:',
						'type'=>'select',
						'options'=>array(
							'zh-CN'=>'简体中文',
							'zh-TW'=>'繁体中文',
							'en'=>'English',
						),
						'value'=>'zh-CN',
					),
					'emot'=>array(
						'title'=>'表情：',
						'type'=>'text',
						'value'=>'default',
					),
					'opacity'=>array(
						'title'=>'界面透明度:',
						'type'=>'text',
						'value'=>'80',
					),
					'visitor'=>array(
						'title'=>'支持访客:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'1',
					),
					'upload'=>array(
						'title'=>'支持文件上传:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'1',
					),
					'enable_room'=>array(
						'title'=>'群组聊天:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'1',
					),
					'enable_chatlink'=>array(
						'title'=>'页面聊天按钮:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'1',
					),
					'show_realname'=>array(
						'title'=>'显示真实姓名:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'0',
					),
					'enable_menu'=>array(
						'title'=>'显示菜单:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'0',
					),
                    'enable_shortcut' => array(
						'title'=>'显示工具栏快捷方式:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'1',
                    ),
                    'enable_noti' => array(
						'title'=>'显示通知:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'1',
                    ),
                    'show_unavailable' => array(
						'title'=>'显示离线好友:',
						'type'=>'radio',
						'options'=>array(
							'1'=>'是',
							'0'=>'否',
						),
						'value'=>'1',
                    ),
				)
			),
			'theme'=>array(
				'title'=>'主题',
				'options'=>array(
					'theme'=>array(
						'title'=>'主题',
						'type'=>'hidden',
						'value'=>'base'
					)
				)
			),
			'history'=>array(
				'title'=>'历史',
				'options'=>array(
					'ago'=>array(
						'title'=>'清除历史：',
						'type'=>'select',
						'options'=>array(
							'weekago'=>'清除一周前历史',
							'monthago'=>'清除一月前历史',
							'3monthago'=>'清除三月前历史'
						),
					)
				)
			)
		)
	)

);

?>
