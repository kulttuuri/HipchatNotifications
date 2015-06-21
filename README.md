# Hipchat MediaWiki

This is a extension for [MediaWiki](https://www.mediawiki.org/wiki/MediaWiki) that sends notifications of actions in your Wiki like editing, adding or removing a page into [HipChat](https://www.hipchat.com/) channel.

![Screenshot](http://i.imgur.com/cIINiBm.jpg)

## License

[MIT License](http://en.wikipedia.org/wiki/MIT_License)

## Supported MediaWiki operations to send notifications

* When article is added.
* When article is removed.
* When article is moved.
* When article is edited.
* When new user is added.
* When user is blocked.
* When file is uploaded.

## Requirements

* [cURL](http://curl.haxx.se/). As of version 1.04 this extension also supports using file_get_contents for sending the data. See the configuration parameter $wgHipchatSendMethod below to change this.
* MediaWiki 1.8+ (tested with version 1.8, also tested and works with 1.25+)
* Apache should have NE (NoEscape) flag on to prevent issues in URLs. By default you should have this enabled. Check [this](https://github.com/kulttuuri/hipchat_mediawiki/issues/8) thread for more information if you run into this issue.

## How to install

1) Send folder HipchatNotifications into your `mediawiki_installation/extensions` folder.

2) Add settings listed below in your `localSettings.php`. Note that it is mandatory to set these settings for this extension to work:

	require_once("$IP/extensions/HipchatNotifications/hipchat_notifications.php");
	// HipChat API token. Create or view your API keys here: https://hipchat.com/admin/api
	$wgHipchatToken = "";
	// HipChat room ID where you want all the notifications to go into. You can get the room ID by visiting (replace YOUR_AUTH_TOKEN in the end with your own API key): https://api.hipchat.com/v1/rooms/list?format=xml&auth_token=YOUR_AUTH_TOKEN
	$wgHipchatRoomID = ;
	// Required. Name the message will appear be sent from. Must be less than 15 characters long. May contain letters, numbers, -, _, and spaces.
	$wgHipchatFromName = "Wiki";
	// URL into your MediaWiki installation with the trailing /.
	$wgWikiUrl		= "http://your_wiki_url/";
	// Wiki script name. Leave this to default one if you do not have URL rewriting enabled.
	$wgWikiUrlEnding = "index.php?title=";
	// What method will be used to send the data to HipChat server. By default this is "curl" which only works if you have the curl extension enabled. This can be: "curl" or "file_get_contents". Default: "curl".
	$wgHipchatSendMethod = "curl";

3) Enjoy the notifications in your HipChat room!
	
## Additional options

These options can be set after including your plugin in your localSettings.php file.

### Trigger notification

Whether or not this message should trigger a notification for people in the room (change the tab color, play a sound, etc). Each recipient's notification preferences are taken into account.

	$wgHipchatNotification = true;

### API URL

URL to HipChat rooms/message sent script. Mostly just leave to default value.

	$wgHipchatRoomMessageApiUrl = "https://api.hipchat.com/v1/rooms/message";

### Actions to notify of

MediaWiki actions that will be sent notifications of into HipChat. Set desired options to false to disable notifications of those actions.

	// New user added into MediaWiki
	$wgHipchatNotificationNewUser = true;
	// User or IP blocked in MediaWiki
	$wgHipchatNotificationBlockedUser = true;
	// Article added to MediaWiki
	$wgHipchatNotificationAddedArticle = true;
	// Article removed from MediaWiki
	$wgHipchatNotificationRemovedArticle = true;
	// Article moved under new title in MediaWiki
	$wgHipchatNotificationMovedArticle = true;
	// Article edited in MediaWiki
	$wgHipchatNotificationEditedArticle = true;
	// File uploaded
	$wgHipchatNotificationFileUpload = true;
	
## Additional MediaWiki URL Settings

Should any of these default MediaWiki system page URLs differ in your installation, change them here.

	$wgWikiUrlEndingUserRights          = "Special%3AUserRights&user=";
	$wgWikiUrlEndingBlockUser           = "Special:Block/";
	$wgWikiUrlEndingUserPage            = "User:";
	$wgWikiUrlEndingUserTalkPage        = "User_talk:";
	$wgWikiUrlEndingUserContributions   = "Special:Contributions/";
	$wgWikiUrlEndingBlockList           = "Special:BlockList";
	$wgWikiUrlEndingEditArticle         = "action=edit";
	$wgWikiUrlEndingDeleteArticle       = "action=delete";
	$wgWikiUrlEndingHistory             = "action=history";
