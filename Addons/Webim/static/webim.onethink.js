//custom
(function(webim) {
	var path = _IMC.path;
	webim.extend(webim.setting.defaults.data, _IMC.setting);
    var cookie_key = "_webim_cookie_";
	if( _IMC.is_visitor ) { cookie_key = "_webim_v_cookie_"; }
    if( _IMC.user != "" ) { cookie_key = cookie_key + _IMC.user.id; }
    webim.status.defaults.key = cookie_key;
	webim.route( {
		online: path + "/_action/online",
		offline: path + "/_action/offline",
		deactivate: path + "/_action/refresh",
		message: path + "/_action/message",
		presence: path + "/_action/presence",
		status: path + "/_action/status",
		setting: path + "/_action/setting",
		history: path + "/_action/history",
		clear: path + "/_action/clear_history",
		download: path + "/_action/download_history",
		buddies: path + "/_action/buddies",
		remove_buddy: path + "/_action/remove_buddy",
        //room actions
		invite: path + "/_action/invite",
		join: path + "/_action/join",
		leave: path + "/_action/leave",
		block: path + "/_action/block",
		unblock: path + "/_action/unblock",
		members: path + "/_action/members",
        //notifications
		notifications: path + "/_action/notifications",
        //upload files
		upload: _IMC.static_path + "/images/upload.php"
	} );

	webim.ui.emot.init({"dir": _IMC.static_path + "/images/emot/default"});
	var soundUrls = {
		lib: _IMC.static_path + "/assets/sound.swf",
		msg: _IMC.static_path + "/assets/sound/msg.mp3"
	};
	var ui = new webim.ui(document.body, {
		imOptions: {
			jsonp: _IMC.jsonp
		},
		soundUrls: soundUrls,
		//layout: "layout.popup",
        layoutOptions: {
            unscalable: _IMC.is_visitor,
            //detachable: true
            maximizable: true
        },
		buddyChatOptions: {
            downloadHistory: !_IMC.is_visitor,
			//simple: _IMC.is_visitor,
			upload: _IMC.upload && !_IMC.is_visitor
		},
		roomChatOptions: {
            downloadHistory: !_IMC.is_visitor,
			upload: _IMC.upload
		}
	}), im = ui.im;
    //全局化
    window.webimUI = ui;

	if( _IMC.user ) im.setUser( _IMC.user );
	if( _IMC.menu ) ui.addApp("menu", { "data": _IMC.menu } );
	if( _IMC.enable_shortcut ) ui.layout.addShortcut( _IMC.menu );

	ui.addApp("buddy", {
		showUnavailable: _IMC.show_unavailable,
		is_login: _IMC['is_login'],
		disable_login: true,
		collapse: false,
		//disable_user: _IMC.is_visitor,
        //simple: _IMC.is_visitor,
        //online_group: false,
		loginOptions: _IMC['login_options']
	});
    if(!_IMC.is_visitor) {
        if( _IMC.enable_room )ui.addApp("room", { discussion: (_IMC.discussion && !_IMC.is_visitor) });
        if(_IMC.enable_noti )ui.addApp("notification");
    }
    if(_IMC.enable_chatlink) {
        ui.addApp("chatbtn", {
            elmentId: null,
            //chatbox: true,
            classRe: /^$/,
            hrefRe: [/webim\/_action\/chatbox&uid=(\d+)$/i]
        });
    }
    ui.addApp("setting", {"data": webim.setting.defaults.data, "copyright": true});
	ui.render();
	_IMC['is_login'] && im.autoOnline() && im.online();
})(webim);

//window.webimUI.layout.addChat('buddy', '20');
