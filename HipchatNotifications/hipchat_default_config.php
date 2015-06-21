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
 * @copyright Copyright © 2015, Aleksi Postari
 * @license http://en.wikipedia.org/wiki/MIT_License MIT
 */

if(!defined('MEDIAWIKI')) die();
if (!isset($hpc_attached)) die();

##########################
# HIPCHAT API PARAMETERS #
##########################
// Basic HipChat configurations.
	
	// MANDATORY
	
	// HipChat API token. Create or view your API keys here: https://hipchat.com/admin/api
	$wgHipchatToken = "";
	// HipChat room ID where you want all the notifications to go into. You can get the room ID by visiting (replace YOUR_AUTH_TOKEN in the end with your own API key): https://api.hipchat.com/v1/rooms/list?format=xml&auth_token=YOUR_AUTH_TOKEN
	$wgHipchatRoomID = 0;
	// Required. Name the message will appear be sent from. Must be less than 15 characters long. May contain letters, numbers, -, _, and spaces.
	$wgHipchatFromName = "Wiki";
	
	// OPTIONAL
	
	// Whether or not this message should trigger a notification for people in the room (change the tab color, play a sound, etc). Each recipient's notification preferences are taken into account.
	$wgHipchatNotification = false;
	// URL to HipChat rooms/message sent script. Mostly just leave to default value.
	$wgHipchatRoomMessageApiUrl = "https://api.hipchat.com/v1/rooms/message";
	// What method will be used to send the data to HipChat server. By default this is "curl" which only works if you have the curl extension enabled. This can be: "curl" or "file_get_contents". Default: "curl".
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