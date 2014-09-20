# Hipchat MediaWiki

This is a extension for MediaWiki that sends notifications into HipChat channel.

## License

[MIT License](http://en.wikipedia.org/wiki/MIT_License)

## Supported MediaWiki operations to send notifications

* When article is added.
* When article is removed.
* When article is edited.
* When new user is added.
* When user is blocked.
* When file is uploaded.

## Requirements

* [cURL](http://curl.haxx.se/)
* [MediaWiki](https://www.mediawiki.org/wiki/MediaWiki) 1.8+ (only tested with as low as version 1.8)
* Apache should have NE (NoEscape) flag on to prevent issues in URLs. By default this is enabled. Check this thread for more information: https://github.com/kulttuuri/hipchat_mediawiki/issues/8

## How to install

1. Send folder HipchatNotifications into your mediawiki_installation/extensions folder.
2. Add these settings in your localSettings.php (remember to set these settings or otherwise your extension won't work):
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

4. Enjoy the notifications in your HipChat room!
	
## Optional options

// Hipchat options

	// Whether or not this message should trigger a notification for people in the room (change the tab color, play a sound, etc). Each recipient's notification preferences are taken into account.
	$wgHipchatNotification = true;
	// URL to HipChat rooms/message sent script. Mostly just leave to default value.
	$wgHipchatRoomMessageApiUrl = "https://api.hipchat.com/v1/rooms/message";

// MediaWiki actions that will be sent notifications of into HipChat.
// Set desired options to false to disable notifications of those actions.

	// New user added into MediaWiki
	$wgHipchatNotificationNewUser = true;
	// User or IP blocked in MediaWiki
	$wgHipchatNotificationBlockedUser = true;
	// Article added to MediaWiki
	$wgHipchatNotificationAddedArticle = true;
	// Article removed from MediaWiki
	$wgHipchatNotificationRemovedArticle = true;
	// Article edited in MediaWiki
	$wgHipchatNotificationEditedArticle = true;
	// File uploaded
	$wgHipchatNotificationFileUpload = true;
	
// Url endings

	$wgWikiUrlEndingUserRights          = "Special%3AUserRights&user=";
	$wgWikiUrlEndingBlockUser           = "Special:Block/";
	$wgWikiUrlEndingUserPage            = "User:";
	$wgWikiUrlEndingUserTalkPage        = "User_talk:";
	$wgWikiUrlEndingUserContributions   = "Special:Contributions/";
	$wgWikiUrlEndingBlockList           = "Special:BlockList";
	$wgWikiUrlEndingEditArticle         = "action=edit";
	$wgWikiUrlEndingDeleteArticle       = "action=delete";
	$wgWikiUrlEndingHistory             = "action=history";
