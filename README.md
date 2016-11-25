# Hipchat MediaWiki

This is a extension for [MediaWiki](https://www.mediawiki.org/wiki/MediaWiki) that sends notifications of actions in your Wiki like editing, adding or removing a page into [HipChat](https://www.hipchat.com/) channel.

> Looking for extension that can send notifications to [Slack](https://github.com/kulttuuri/slack_mediawiki) or [Discord](https://github.com/kulttuuri/discord_mediawiki)?

![Screenshot](http://i.imgur.com/cIINiBm.jpg)

## Supported MediaWiki operations to send notifications

* When article is added.
* When article is removed.
* When article is moved.
* When article is edited.
* When new user is added.
* When user is blocked.
* When file is uploaded.

## Requirements

* [cURL](http://curl.haxx.se/). As of version 1.04 this extension also supports using file_get_contents for sending the data (but only with Hipchat API v1). See the configuration parameter $wgHipchatSendMethod below to change this.
* MediaWiki 1.8+ (tested with version 1.8, also tested and works with 1.25+)
* Apache should have NE (NoEscape) flag on to prevent issues in URLs. By default you should have this enabled so usually no configuration is required from your part. Check [this](https://github.com/kulttuuri/hipchat_mediawiki/issues/8) thread for more information if you run into this issue.

## How to install

Note that as of version 1.05 we use Hipchat API v2 by default. To switch to old v1, check section "API URL (Switch between Hipchat API v2 or v1)".

1) Send folder HipchatNotifications into your `mediawiki_installation/extensions` folder.

2) Add settings listed below in your `localSettings.php`. Note that it is mandatory to set these settings for this extension to work:

```php
require_once("$IP/extensions/HipchatNotifications/HipchatNotifications.php");
// HipChat API token. You should use API key with scope "Send Notification". Manage API keys for Hipchat API v2 here: https://hipchat.com/account/api
$wgHipchatToken = "";
// HipChat room ID or name where you want all the notifications to go into. You can directly use your room name or if that does not work, you can find your room ID from here: https://api.hipchat.com/v2/room?auth_token=YOUR_AUTH_TOKEN
$wgHipchatRoomID = "";
// Required. Name the message will appear be sent from. Must be less than 15 characters long. May contain letters, numbers, -, _, and spaces.
$wgHipchatFromName = "Wiki";
// URL into your MediaWiki installation with the trailing /.
$wgWikiUrl		= "http://your_wiki_url/";
// Wiki script name. Leave this to default one if you do not have URL rewriting enabled.
$wgWikiUrlEnding = "index.php?title=";
// What method will be used to send the data to HipChat server. This setting only works with Hipchat API v1, in V2 we always use curl.
// By default this is "curl" which only works if you have the curl extension enabled. This can be: "curl" or "file_get_contents". Default: "curl".
$wgHipchatSendMethod = "curl";
```

3) Enjoy the notifications in your HipChat room!
	
## Additional options

These options can be set after including your plugin in your localSettings.php file.

### Trigger notification

Whether or not this message should trigger a notification for people in the room (change the tab color, play a sound, etc). Each recipient's notification preferences are taken into account.

```php
$wgHipchatNotification = true;
```

### API URL (Switch between Hipchat API v2 or v1)

URL to HipChat rooms/message sent script. By default we use Hipchat API v2. To use the old Hipchat API v1, change this url to: https://api.hipchat.com/v1/rooms/message

```php
$wgHipchatRoomMessageApiUrl = "https://api.hipchat.com/v2/room";
```

### Actions to notify of

MediaWiki actions that will be sent notifications of into HipChat. Set desired options to false to disable notifications of those actions.

```php
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
```
	
## Additional MediaWiki URL Settings

Should any of these default MediaWiki system page URLs differ in your installation, change them here.

```php
$wgWikiUrlEndingUserRights          = "Special%3AUserRights&user=";
$wgWikiUrlEndingBlockUser           = "Special:Block/";
$wgWikiUrlEndingUserPage            = "User:";
$wgWikiUrlEndingUserTalkPage        = "User_talk:";
$wgWikiUrlEndingUserContributions   = "Special:Contributions/";
$wgWikiUrlEndingBlockList           = "Special:BlockList";
$wgWikiUrlEndingEditArticle         = "action=edit";
$wgWikiUrlEndingDeleteArticle       = "action=delete";
$wgWikiUrlEndingHistory             = "action=history";
```

## License

[MIT License](http://en.wikipedia.org/wiki/MIT_License)
