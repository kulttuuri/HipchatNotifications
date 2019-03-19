<?php
/**#@+
 * This extension integrates Hipchat with MediaWiki. Sends HipChat notifications
 * for selected actions that have occurred in your MediaWiki sites.
 *
 * This file contains functionality for the extension.
 *
 * @ingroup Extensions
 * @link https://github.com/kulttuuri/hipchat_mediawiki
 * @author Aleksi Postari / kulttuuri <aleksi@postari.net>
 * @copyright Copyright © 2016, Aleksi Postari
 * @license http://en.wikipedia.org/wiki/MIT_License MIT
 */

if (!defined('MEDIAWIKI')) die();

$hpc_attached = true;
require_once("HipchatNotificationsDefaultConfig.php");

if ($wgHipchatNotificationEditedArticle)
	$wgHooks['ArticleSaveComplete'][] = array('article_saved');				// When article has been saved
if ($wgHipchatNotificationAddedArticle)
	$wgHooks['ArticleInsertComplete'][] = array('article_inserted');		// When new article has been inserted
if ($wgHipchatNotificationRemovedArticle)
	$wgHooks['ArticleDeleteComplete'][] = array('article_deleted');			// When article has been removed
if ($wgHipchatNotificationMovedArticle)
	$wgHooks['TitleMoveComplete'][] = array('article_moved');				// When article has been moved
if ($wgHipchatNotificationNewUser)
	$wgHooks['AddNewAccount'][] = array('new_user_account');				// When new user account is created
if ($wgHipchatNotificationBlockedUser)
	$wgHooks['BlockIpComplete'][] = array('user_blocked');					// When user or IP has been blocked
if ($wgHipchatNotificationFileUpload)
	$wgHooks['UploadComplete'][] = array('file_uploaded');					// When file has been uploaded

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'HipChat Notifications',
	'author' => 'Aleksi Postari',
	'description' => 'Sends HipChat notifications for selected actions that have occurred in your MediaWiki sites.',
	'url' => 'https://github.com/kulttuuri/hipchat_mediawiki',
	"version" => "1.06"
);

/**
 * Gets nice HTML text for user containing the link to user page
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
 * Gets nice HTML text for article containing the link to article page
 * and also into edit, delete and article history pages.
 */
function getArticleText(WikiPage $article)
{
        global $wgWikiUrl, $wgWikiUrlEnding, $wgWikiUrlEndingEditArticle,
               $wgWikiUrlEndingDeleteArticle, $wgWikiUrlEndingHistory;

        return sprintf(
                "<b>%s</b> (%s | %s | %s)",
                "<a href='".$wgWikiUrl.$wgWikiUrlEnding.$article->getTitle()->getFullText()."'>".$article->getTitle()->getFullText()."</a>",
                "<a href='".$wgWikiUrl.$wgWikiUrlEnding.$article->getTitle()->getFullText()."&".$wgWikiUrlEndingEditArticle."'>edit</a>",
                "<a href='".$wgWikiUrl.$wgWikiUrlEnding.$article->getTitle()->getFullText()."&".$wgWikiUrlEndingDeleteArticle."'>delete</a>",
                "<a href='".$wgWikiUrl.$wgWikiUrlEnding.$article->getTitle()->getFullText()."&".$wgWikiUrlEndingHistory."'>history</a>"/*,
                "move",
                "protect",
                "watch"*/
                );
}

/**
 * Gets nice HTML text for title object containing the link to article page
 * and also into edit, delete and article history pages.
 */
function getTitleText(Title $title)
{
        global $wgWikiUrl, $wgWikiUrlEnding, $wgWikiUrlEndingEditArticle,
               $wgWikiUrlEndingDeleteArticle, $wgWikiUrlEndingHistory;

        $titleName = $title->getFullText();
        return sprintf(
                "<b>%s</b> (%s | %s | %s)",
                "<a href='".$wgWikiUrl.$wgWikiUrlEnding.$titleName."'>".$titleName."</a>",
                "<a href='".$wgWikiUrl.$wgWikiUrlEnding.$titleName."&".$wgWikiUrlEndingEditArticle."'>edit</a>",
                "<a href='".$wgWikiUrl.$wgWikiUrlEnding.$titleName."&".$wgWikiUrlEndingDeleteArticle."'>delete</a>",
                "<a href='".$wgWikiUrl.$wgWikiUrlEnding.$titleName."&".$wgWikiUrlEndingHistory."'>history</a>"/*,
                "move",
                "protect",
                "watch"*/
                );
}

/**
 * Occurs after the save page request has been processed.
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/PageContentSaveComplete
 */
function article_saved(WikiPage $article, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId)
{
    // Skip new articles that have view count below 1. Adding new articles is already handled in article_added function and
	// calling it also here would trigger two notifications!
	$isNew = $status->value['new']; // This is 1 if article is new
	if ($isNew == 1) {
		return true;
	}
	
	$message = sprintf(
		"%s has %s article %s %s",
		getUserText($user),
                $isMinor == true ? "made minor edit to" : "edited",
                getArticleText($article),
		$summary == "" ? "" : "Summary: $summary");
	push_hipchat_notify($message, "yellow");
	return true;
}

/**
 * Occurs after a new article has been created.
 * @see http://www.mediawiki.org/wiki/Manual:Hooks/ArticleInsertComplete
 */
function article_inserted(WikiPage $article, $user, $text, $summary, $isminor, $iswatch, $section, $flags, $revision)
{
        // Do not announce newly added file uploads as articles...
        if ($article->getTitle()->getNsText() == "File") return true;
        
	$message = sprintf(
		"%s has created article %s %s",
		getUserText($user),
		getArticleText($article),
		$summary == "" ? "" : "Summary: $summary");
	push_hipchat_notify($message, "green");
	return true;
}

/**
 * Occurs after the delete article request has been processed.
 * @see http://www.mediawiki.org/wiki/Manual:Hooks/ArticleDeleteComplete
 */
function article_deleted(WikiPage $article, $user, $reason, $id)
{
	$message = sprintf(
		"%s has deleted article %s Reason: %s",
		getUserText($user),
		getArticleText($article),
		$reason);
	push_hipchat_notify($message, "red");
	return true;
}

/**
 * Occurs after a page has been moved.
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/TitleMoveComplete
 * @since HipchatNotifications 1.04
 */
function article_moved($title, $newtitle, $user, $oldid, $newid, $reason = null)
{
	$message = sprintf(
		"%s has moved article %s to %s. Reason: %s",
		getUserText($user),
		getTitleText($title),
		getTitleText($newtitle),
		$reason);
	push_hipchat_notify($message, "green");
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
		getUserText($user),
		$user->getEmail(),
		$user->getRealName());
	push_hipchat_notify($message, "green");
	return true;
}

/**
 * Called when a file upload has completed.
 * @see http://www.mediawiki.org/wiki/Manual:Hooks/UploadComplete
 */
function file_uploaded($image)
{
    global $wgWikiUrl, $wgWikiUrlEnding, $wgUser;
	$message = sprintf(
		"%s has uploaded file <a href=\"%s\">%s</a> (format: %s, size: %s MB, summary: %s)",
		getUserText($wgUser->mName),
		$wgWikiUrl . $wgWikiUrlEnding . $image->getLocalFile()->getTitle(),
		$image->getLocalFile()->getTitle(),
		$image->getLocalFile()->getMimeType(),
		round($image->getLocalFile()->size / 1024 / 1024, 3),
            $image->getLocalFile()->getDescription());

	push_hipchat_notify($message, "green");
	return true;
}

/**
 * Occurs after the request to block an IP or user has been processed
 * @see http://www.mediawiki.org/wiki/Manual:MediaWiki_hooks/BlockIpComplete
 */
function user_blocked(Block $block, $user)
{
	global $wgWikiUrl, $wgWikiUrlEnding, $wgWikiUrlEndingBlockList;
	$message = sprintf(
		"%s has blocked %s %s Block expiration: %s. %s",
		getUserText($user),
                getUserText($block->getTarget()),
		$block->mReason == "" ? "" : "with reason '".$block->mReason."'.",
		$block->mExpiry,
		"<a href='".$wgWikiUrl.$wgWikiUrlEnding.$wgWikiUrlEndingBlockList."'>List of all blocks</a>.");
	push_hipchat_notify($message, "red");
	return true;
}

/**
 * Writes debug message into file. 
 */
function writeDebug($message)
{
    wfErrorLog($message + "\n", "hipchatdebug.txt");
}

/**
 * Sends the message into HipChat room.
 * @param message Message to be sent.
 * @param color Background color for the message. One of "yellow", "red", "green", "purple", "gray", or "random". (default: yellow)
 * @see https://www.hipchat.com/docs/api/method/rooms/message
*/
function push_hipchat_notify($message, $bgColor)
{
	global $wgHipchatRoomMessageApiUrl, $wgHipchatToken, $wgHipchatFromName,
		   $wgHipchatRoomID, $wgHipchatNotification, $wgHipchatSendMethod;
	
	// Determine Hipchat API version from setting
	$apiVersion = $wgHipchatRoomMessageApiUrl == "https://api.hipchat.com/v1/rooms/message" ? 1 : 2;

	// API v1
	if ($apiVersion == 1) {
		$url = sprintf(
			"%s?auth_token=%s&from=%s&room_id=%s&color=%s&notify=%s&message_format=html&message=%s",
			$wgHipchatRoomMessageApiUrl,
			$wgHipchatToken,
			$wgHipchatFromName,
			$wgHipchatRoomID,
			$bgColor,
			$wgHipchatNotification == true ? "1" : "0",
			urlencode($message));
	}
	// API v2
	else {
		$url = sprintf(
			"%s/%s/notification?auth_token=%s",
			$wgHipchatRoomMessageApiUrl,
			$wgHipchatRoomID,
			$wgHipchatToken);
		$post = sprintf("from=%s&color=%s&notify=%s&message_format=html&message=%s",
			$wgHipchatFromName,
			$bgColor,
			$wgHipchatNotification == true ? "true" : "false",
			urlencode($message));
	}
	
	// Use file_get_contents to send the data. Note that this only works with Hipchat API v1 and you will need to have allow_url_fopen enabled in php.ini for this to work.
	if ($wgHipchatSendMethod == "file_get_contents" && $apiVersion == 1) {
		$result = file_get_contents($url, false);
	}
	// Call the HipChat API through cURL (default way). Note that you will need to have cURL enabled for this to work.
	else {
		$h = curl_init();
		curl_setopt($h, CURLOPT_URL, $url);
		curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
		if ($apiVersion == 2) {
			curl_setopt($h, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
			curl_setopt($h, CURLOPT_POSTFIELDS, $post);
		}
        // Commented out lines below. Using default curl settings for host and peer verification.
        //curl_setopt ($h, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt ($h, CURLOPT_SSL_VERIFYPEER, 0);
	    // ... Aaand execute the curl script!
		curl_exec($h);
		curl_close($h);
	}
}
?>
