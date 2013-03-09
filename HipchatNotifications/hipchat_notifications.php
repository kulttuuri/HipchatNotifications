<?php
/**#@+
 * This extension integrates Hipchat with MediaWiki. Sends HipChat notifications
 * for selected actions that have occurred in your MediaWiki sites.
 *
 * This file contains functionality for the extension.
 *
 * @ingroup Extensions
 * @link https://github.com/kulttuuri/hipchat_mediawiki
 * @author Aleksi Postari / kulttuuri <riialin@gmail.com>
 * @copyright Copyright © 2013, Aleksi Postari
 * @license http://en.wikipedia.org/wiki/MIT_License MIT
 */

if (!defined('MEDIAWIKI')) die();

$hpc_attached = true;
require_once("hipchat_default_config.php");

if ($wgHipchatNotificationEditedArticle)
	$wgHooks['ArticleSaveComplete'][] = array('article_saved');			// When article has been saved
if ($wgHipchatNotificationAddedArticle)
	$wgHooks['ArticleInsertComplete'][] = array('article_inserted');	// When new article has been inserted
if ($wgHipchatNotificationRemovedArticle)
	$wgHooks['ArticleDeleteComplete'][] = array('article_deleted');		// When article has been removed
if ($wgHipchatNotificationNewUser)
	$wgHooks['AddNewAccount'][] = array('new_user_account');			// When new user account is created
if ($wgHipchatNotificationBlockedUser)
	$wgHooks['BlockIpComplete'][] = array('user_blocked');				// When user or IP has been blocked
if ($wgHipchatNotificationFileUpload)
	$wgHooks['UploadComplete'][] = array('file_uploaded');				// When file has been uploaded

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'HipChat Notifications',
	'author' => 'Aleksi Postari',
	'description' => 'Sends HipChat notifications for selected actions that have occurred in your MediaWiki sites.',
	'url' => 'https://github.com/kulttuuri/hipchat_mediawiki',
	"version" => "1.0"
);

/**
 * Gets nice HTML text for user containing link to user
 * and also links to user site, groups editing, talk and contribs pages.
 */
function getUserText($user)
{
	global $wgWikiUrl, $wgWikiUrlEnding, $wgWikiUrlEndingUserPage,
		   $wgWikiUrlEndingBlockUser, $wgWikiUrlEndingUserRights, 
		   $wgWikiUrlEndingUserTalkPage, $wgWikiUrlEndingUserContributions;
	
	return sprintf(
		"<b>%s</b> (%s | %s | %s | %s)",
		"<a href='".$wgWikiUrl.$wgWikiUrlEnding.$wgWikiUrlEndingUserPage.$user."'>$user</a>",
		"<a href='".$wgWikiUrl.$wgWikiUrlEnding.$wgWikiUrlEndingBlockUser.$user."'>block</a>",
		"<a href='".$wgWikiUrl.$wgWikiUrlEnding.$wgWikiUrlEndingUserRights.$user."'>groups</a>",
		"<a href='".$wgWikiUrl.$wgWikiUrlEnding.$wgWikiUrlEndingUserTalkPage.$user."'>talk</a>",
		"<a href='".$wgWikiUrl.$wgWikiUrlEnding.$wgWikiUrlEndingUserContributions.$user."'>contribs</a>"
		);
}

/**
 * Occurs after the save page request has been processed.
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/PageContentSaveComplete
 */
function article_saved($article, $user, $text, $summary, $isminor, $iswatch, $section)
{
	global $wgWikiUrl, $wgWikiUrlEnding;
	$message = sprintf(
		"%s has edited the <a href=\"%s\">%s</a> article (summary: %s)",
		getUserText($user->mName),
		$wgWikiUrl . $wgWikiUrlEnding . $article->mTitle->mTextform,
		$article->mTitle->mTextform,
		$summary);
	push_hipchat_notify($message);
	return true;
}

/**
 * Occurs after a new article has been created.
 * @see http://www.mediawiki.org/wiki/Manual:Hooks/ArticleInsertComplete
 */
function article_inserted($article, $user, $text, $summary, $isminor, $iswatch, $section, $flags, $revision)
{
	global $wgWikiUrl, $wgWikiUrlEnding;
	$message = sprintf(
		"%s has created the <a href=\"%s\">%s</a> article (summary: %s)",
		getUserText($user->mName),
		$wgWikiUrl . $wgWikiUrlEnding . $article->mTitle->mTextform,
		$article->mTitle->mTextform,
		$summary);
	push_hipchat_notify($message);
	return true;
}

/**
 * Occurs after the delete article request has been processed.
 * @see http://www.mediawiki.org/wiki/Manual:Hooks/ArticleDeleteComplete
 */
function article_deleted($article, $user, $reason, $id)
{
	global $wgWikiUrl, $wgWikiUrlEnding;
	$message = sprintf(
		"%s has deleted the <a href=\"%s\">%s</a> article (reason: %s)",
		getUserText($user->mName),
		$wgWikiUrl . $wgWikiUrlEnding . $article->mTitle->mTextform,
		$article->mTitle->mTextform,
		$reason);
	push_hipchat_notify($message);
	return true;
}

/**
 * Called after a user account is created.
 * @see http://www.mediawiki.org/wiki/Manual:Hooks/AddNewAccount
 */
function new_user_account($user, $byEmail)
{
	$message = sprintf(
		"New user account %s was just created (email: %s, real name: %s)",
		getUserText($user->mName),
		$user->getEmail(),
		$user->getRealName());
	push_hipchat_notify($message);
	return true;
}

/**
 * Called when a file upload has completed.
 * @see http://www.mediawiki.org/wiki/Manual:Hooks/UploadComplete
 */
function file_uploaded($image)
{
	$message = sprintf(
		"%s has uploaded file <a href=\"%s\">%s</a> (format: %s, size: %s MB)",
		getUserText($image->getLocalFile()->user_text),
		$image->getLocalFile()->url,
		$image->getLocalFile()->getTitle(),
		$image->getLocalFile()->mime,
		$image->getLocalFile()->size / 1024 / 1024);
	push_hipchat_notify($message);
	return true;
}

/**
 * Occurs after the request to block an IP or user has been processed
 * @see http://www.mediawiki.org/wiki/Manual:MediaWiki_hooks/BlockIpComplete
 */
function user_blocked($block, $user)
{
	global $wgWikiUrl, $wgWikiUrlEnding, $wgWikiUrlEndingBlockList;
	$message = sprintf(
		"%s has blocked %s (reason: %s, expiry: %s) %s",
		$user,
		$block->getTarget(),
		$block->mReason,
		$block->mExpiry,
		"<a href='".$wgWikiUrl.$wgWikiUrlEnding.$wgWikiUrlEndingBlockList."'>block list</a>");
	push_hipchat_notify($message);
	return true;
}

/**
 * Sends the message into HipChat room.
 * @param message Message to be sent.
 * @see https://www.hipchat.com/docs/api/method/rooms/message
*/
function push_hipchat_notify($message)
{
	global $wgHipchatRoomMessageApiUrl, $wgHipchatToken, $wgHipchatFromName,
		   $wgHipchatRoomID, $wgHipchatBackgroundColor, $wgHipchatNotification;
	
	$url = sprintf(
		"%s?auth_token=%s&from=%s&room_id=%s&color=%s&notify=%s&message_format=html&message=%s",
		$wgHipchatRoomMessageApiUrl,
		$wgHipchatToken,
		$wgHipchatFromName,
		$wgHipchatRoomID,
		$wgHipchatBackgroundColor,
		$wgHipchatNotification == true ? "1" : "0",
		urlencode($message));
	
	// Call the HipChat API through curl
	$h = curl_init();
	curl_setopt($h, CURLOPT_URL, $url);
	curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
	curl_exec($h);
	curl_close($h);
}
?>