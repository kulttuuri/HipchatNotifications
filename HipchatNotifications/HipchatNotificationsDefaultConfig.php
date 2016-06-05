<?php
/**#@+
 * This extension integrates Hipchat with MediaWiki. Sends HipChat notifications
 * for selected actions that have occurred in your MediaWiki sites.
 *
 * This file contains configuration options for the extension.
 *
 * @ingroup Extensions
 * @link https://github.com/kulttuuri/hipchat_mediawiki
 * @author Aleksi Postari / kulttuuri <aleksi@postari.net>
 * @copyright Copyright © 2016, Aleksi Postari
 * @license http://en.wikipedia.org/wiki/MIT_License MIT
 */

if(!defined('MEDIAWIKI')) die();
if (!isset($hpc_attached)) die();

##########################
# HIPCHAT API PARAMETERS #
##########################
// Basic HipChat configurations.
// Note that by default we use Hipchat API v2.
// To use old API v1, change $wgHipchatRoomMessageApiUrl to old Hipchat api V1 url: https://api.hipchat.com/v1/rooms/message
	// You can manage API keys for old API v1 here: https://hipchat.com/admin/api
	// You can list rooms and get room ID for old API v1 here: https://api.hipchat.com/v1/rooms/list?format=xml&auth_token=YOUR_AUTH_TOKEN
	
	// MANDATORY
	
	// HipChat API token. Manage API keys for Hipchat API v2 here: https://hipchat.com/account/api
	$wgHipchatToken = "";
	// HipChat room ID or name where you want all the notifications to go into. You can directly use your room name or if that does not work, you can find your room ID from here: https://api.hipchat.com/v2/room?auth_token=YOUR_AUTH_TOKEN
	$wgHipchatRoomID = "0";
	// Required. Name the message will appear be sent from. Must be less than 15 characters long. May contain letters, numbers, -, _, and spaces.
	$wgHipchatFromName = "Wiki";
	
	// OPTIONAL
	
	// Whether or not this message should trigger a notification for people in the room (change the tab color, play a sound, etc). Each recipient's notification preferences are taken into account.
	$wgHipchatNotification = false;
	// URL to HipChat rooms/message notification script. Defaults to Hipchat API v2.
	// For Hipchat API v2 use (default): https://api.hipchat.com/v2/room
	// For Hipchat API v1 use: https://api.hipchat.com/v1/rooms/message
	$wgHipchatRoomMessageApiUrl = "https://api.hipchat.com/v2/room";
	// What method will be used to send the data to HipChat server. This setting only works with Hipchat API v1, in V2 we always use curl.
	// By default this is "curl" which only works if you have the curl extension enabled. This can be: "curl" or "file_get_contents". Default: "curl".
	$wgHipchatSendMethod = "curl";
	
##################
# MEDIAWIKI URLS #
##################
// URLs into your MediaWiki installation.
	
	// MANDATORY
	
	// URL into your MediaWiki installation with the trailing /.
	$wgWikiUrl		= "";
	// Wiki script name. Leave this to default one if you do not have URL rewriting enabled.
	$wgWikiUrlEnding = "index.php?title=";
	
	// OPTIONAL
	
	$wgWikiUrlEndingUserRights          = "Special%3AUserRights&user=";
	$wgWikiUrlEndingBlockUser           = "Special:Block/";
	$wgWikiUrlEndingUserPage            = "User:";
	$wgWikiUrlEndingUserTalkPage        = "User_talk:";
	$wgWikiUrlEndingUserContributions   = "Special:Contributions/";
	$wgWikiUrlEndingBlockList           = "Special:BlockList";
        $wgWikiUrlEndingEditArticle         = "action=edit";
        $wgWikiUrlEndingDeleteArticle       = "action=delete";
        $wgWikiUrlEndingHistory             = "action=history";

#####################
# MEDIAWIKI ACTIONS #
#####################
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
	// Article moved under another title
	$wgHipchatNotificationMovedArticle = true;
	// Article edited in MediaWiki
	$wgHipchatNotificationEditedArticle = true;
	// File uploaded
	$wgHipchatNotificationFileUpload = true;
?>