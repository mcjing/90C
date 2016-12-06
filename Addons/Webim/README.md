# WebIM-for-OneThink

## 概述

[NexTalk](http://nextalk.im)为OneThink框架提供的WebIM在线聊天插件。可为OneThink框架开发的社区网站、电子商务、企业应用等提供立即可用的站内即时消息。

WebIM聊天插件的前端界面，集成后直接嵌入站点右下角。并支持在站点页面的任意位置，添加聊天按钮。

## 界面截图

![OneThink Screenshot](http://nextalk.im/static/img/screenshots/onethink.png)

![配置界面](http://ww1.sinaimg.cn/mw1024/50075709gw1eake9xkruaj211s0lbdi5.jpg)

![设置主题](http://ww4.sinaimg.cn/mw1024/50075709gw1eake7ijtg3j211x0lb0x7.jpg)

## 环境要求
```
PHP 5.3＋
cURL support
OneThink 1.0+
```

## 项目演示

[http://onethink.slimpp.io/](http://onethink.slimpp.io/)

## 安装指南

1. [下载插件](https://github.com/webim/webim-for-onethink/archive/master.zip)；

2. 上传到OneThink站点的Addons/Webim目录;

3. OneThink的管理后台: 扩展->插件管理->Webim->安装；

4. Webim插件启用后，进入'设置'页面，设置插件参数(主要是消息服务器地址、通信域名、通信APIKEY)。

5. 访问OneThink站点首页，站点右下角出现Webim前端界面。

## 功能列表

功能 | 发布版本
---- | ----
集成在浏览器右下⾓前端界⾯ | 1.0
一对一聊天 (站点访客、⽤户、管理员间即时聊天) | 1.0
群组聊天(聊天室)，临时讨论组聊天 | 1.0
⺴站在线客服，访客与客服聊天 | 3.0
⺴站⻚⾯嵌⼊聊天按钮，例如"在线客服" | 3.0
离线好友显⽰，发送离线消息 | 5.0
⽤户现场状态设置 | 1.0
⽤户间发送表情 | 1.0
用户间传送图⽚、⽂件 | 3.0
消息拦截、过滤、敏感词处理 | 4.0
简单的聊天机器人支持 | 5.0
可移动聊天窗口支持 | 5.0
手机版独立聊天窗口 | 5.5
界⾯菜单隐藏或定制，界⾯透明背景、缩放⽀持 | 3.0
⽤户界⾯提⽰⾳、收缩⼯具条、弹出窗⼝设置 | 3.0
简单的开源桌⾯客户端 | 5.0
Android手机客户端SDK | 6.0
iOS手机客户端SDK | 6.0

## 开发指南

Webim插件通过WebimPlugin.class.php与OneThink的用户体系、好友关系等数据集成。

WebimPlugin.class.php的集成方法列表:

方法 | 参数 | 返回 | 说明
---- | ---- | ---- | ---- 
user() | | 当前登陆用户(属性详见代码注释) | 初始化WebIM当前的用户对象,一般从SESSION和数据库读取
buddies($uid) | $uid | 好友对象列表 | 根据当前用户uid，读取用户的好友列表
buddiesByIds($uid, $ids) | $uid: 用户id, $ids: 好友id列表 | 好友对象列表 | 根据ids列表读取好友列表
rooms($uid) | $uid: 用户id | 群组对象列表 | 读取当前用户所属的群组，以支持群聊
roomsByIds($uid, $ids) | $uid: 用户id, $ids: 群组id列表 | 群组对象列表 | 根据群组id列表读取群组对象列表
members($room) | $room: 群组id | 群组成员对象列表 | 根据群组Id，读取群组成员对象
notifications($uid) | $uid: 用户id | 通知对象列表 | 读取当前用户的通知信息
menu($uid)  | $uid: 用户id | 菜单列表 | 读取当前用户的菜单项(显示在底栏)

## 配置参数

WebIM插件的相关的配置参数，详细说明如下:

参数 | 名称 | 类型  | 默认 | 说明
---- | ---- | ---- |---- | -----
isopen | 是否开启 | bool |  true | 是否开启WebIM
server | 消息服务器地址 | string  | t.nextalk.im:8000 | WebIM消息服务器列表,逗号分割列表支持集群
domain | 消息服务器通信域名 | string  | localhost | WebIM插件与消息服务器通信的认证域名
apikey | 消息服务器通信APIKEY | string  | public | WebIM插件与消息服务器通信的认证APIKEY
theme | 界面Theme | string  | base | WebIM插件界面Theme
local | 本地语言 | string  | zh-CN | WebIM插件本地语言
emot | 表情包 | string  | default | WebIM插件表情库: emot, qq
opacity | 界面透明度  | inteter | 80 | WebIM插件工具条透明度
enable_room | 群组聊天 | bool | true | WebIM插件是否支持群组聊天
enable_discussion | 临时讨论组 | bool | true  | WebIM插件支持临时讨论组
enable_noti | 通知按钮 | bool | true   | WebIM插件显示通知按钮
enable_shortcut | 快捷工具栏 | bool | false  |  WebIM插件支持快捷工具栏
enable_chatlink | 页面聊天按钮 | bool | true  |  WebIM插件支持聊天按钮
enable_menu | 菜单栏 | bool | false  |  WebIM插件显示菜单栏
show_unavailable | 显示离线好友 | bool | true  |  WebIM插件显示不在线好友
visitor | 支持访客 | bool | true  |  WebIM插件支持访客
upload  | 支持文件上传 | bool | false  |  WebIM插件支持文件上传
censor | 开启敏感词过滤 | bool | false |  是否开启敏感词过滤
robot | 支持聊天机器人 | bool | true  |  WebIM插件是否支持机器人

## 源码说明

WebIM插件代码结构，主要目录和文件说明:

目录或文件 | 说明
--------- | ----
WebimAddon.class.php | OneThink Addon
WebimPlugin.class.php | Webim与OneThink集成类
Controller/ | Webim控制器
Lib/ | Webim通用库
Model/ | Webim模型类
View/ | 配置页面
static | WebIM前端静态资源文件

## 模型类说明

Webim/Model目录下模型类:

类 | 数据库表 | 是否必须 | 说明
---- | ---- | ---- | ---- | 
HistoryModel | webim_histories | 是  | 历史聊天记录存储和查询
SettingModel | webim_settings | 是 | 用户WebIM设置存储和访问
RoomModel | webim_rooms | 否 | 临时讨论组(注: 如不支持讨论组，无需实现)
MemberModel | webim_members | 临时讨论组成员(注: 如不支持讨论组，无需实现) 
BlockModel | webim_blocks | 否 | 群组屏蔽
BuddyModel | webim_buddies | 否 | 好友关系存储和查询(注: 如站点自身有好友个关系，无需实现)
VisitorModel | webim_visitors | 否 | 访客存储和查询(注: 如不支持访客，无需实现)

## 数据库表

WebIM自身创建几张数据库表，用于保存聊天记录、用户设置、临时讨论组、访客信息。

数据库表 | 说明
--------- | ------
webim_histories |  历史聊天记录表
webim_settings | 用户个人WebIM设置表
webim_buddies | 好友关系表(注: 如果项目没有自身的好友关系，可以通过该表存储)
webim_visitors | 访客信息表
webim_rooms | 临时讨论组表(注: Plugin.php是集成项目的固定群组，webim_rooms表是存储WebIM自己的临时讨论组
webim_members | 临时讨论组成员表
webim_blocked | 群组是否block

## NexTalk

***NexTalk***是基于WEB标准协议设计的，主要应用于WEB站点的，简单开放的即时消息系统。可快速为社区微博、电子商务、企业应用集成即时消息服务。

NexTalk架构上分解为：***WebIM业务服务器*** + ***消息路由服务器*** 两个独立部分，遵循 ***Open Close***的架构设计原则。WebIM插件方式与第三方的站点或应用的用户体系开放集成，独立的消息服务器负责稳定的连接管理、消息路由和消息推送。

![NexTalk Architecture](http://nextalk.im/static/img/design/WebimForThinkPHP.png)

## 开发者

公司: [NexTalk.IM](http://nextalk.im)

作者: [yangweijie](mailto: 917647288@qq.com) [Feng Lee](mailto:feng@nextalk.im) 

版本: 0.3 (基于NexTalk5.8开发)

