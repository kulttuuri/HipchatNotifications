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
	$wgHooks['ArticleSaveComplete'][] = array('article_saved');		// When article has been saved
if ($wgHipchatNotificationAddedArticle)
	$wgHooks['ArticleInsertComplete'][] = array('article_inserted');	// When new article has been inserted
if ($wgHipchatNotificationRemovedArticle)
	$wgHooks['ArticleDeleteComplete'][] = array('article_deleted');		// When article has been removed
if ($wgHipchatNotificationNewUser)
	$wgHooks['AddNewAccount'][] = array('new_user_account');		// When new user account is created
if ($wgHipchatNotificationBlockedUser)
	$wgHooks['BlockIpComplete'][] = array('user_blocked');			// When user or IP has been blocked
if ($wgHipchatNotificationFileUpload)
	$wgHooks['UploadComplete'][] = array('file_uploaded');			// When file has been uploaded

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'HipChat Notifications',
	'author' => 'Aleksi Postari',
	'description' => 'Sends HipChat notifications for selected actions that have occurred in your MediaWiki sites.',
	'url' => 'https://github.com/kulttuuri/hipchat_mediawiki',
	"version" => "1.02"
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
 * Occurs after the save page request has been processed.
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/PageContentSaveComplete
 */
function article_saved(WikiPage $article, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId)
{
        // Skip new articles that have view count below 1 (this is already handled in article_added function)
        if ($article->getCount() == null || $article->getCount() < 1) return true;
	
	$message = sprintf(
		"%s has %s article %s %s",
		getUserText($user),
                $isminor == true ? "made minor edit to" : "edited",
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
        global $wgWikiUrl, $wgWikiUrlEnding;
        
	$message = sprintf(
		"%s has uploaded file <a href=\"%s\">%s</a> (format: %s, size: %s MB, summary: %s)",
		getUserText($image->getLocalFile()->user_text),
		$wgWikiUrl . $wgWikiUrlEnding . $image->getLocalFile()->getTitle(),
		$image->getLocalFile()->getTitle(),
		$image->getLocalFile()->mime,
		round($image->getLocalFile()->size / 1024 / 1024, 3),
                $image->getLocalFile()->description);
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
		   $wgHipchatRoomID, $wgHipchatNotification;
	
	$url = sprintf(
		"%s?auth_token=%s&from=%s&room_id=%s&color=%s&notify=%s&message_format=html&message=%s",
		$wgHipchatRoomMessageApiUrl,
		$wgHipchatToken,
		$wgHipchatFromName,
		$wgHipchatRoomID,
		$bgColor,
		$wgHipchatNotification == true ? "1" : "0",
		urlencode($message));
	
	// Call the HipChat API through curl
	$h = curl_init();
	curl_setopt($h, CURLOPT_URL, $url);
	curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
        // I know this shouldn't be done, but because it wouldn't otherwise work because of SSL...
        curl_setopt ($h, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($h, CURLOPT_SSL_VERIFYPEER, 0);
        // ... Aaand execute the curl script!
	curl_exec($h);
	curl_close($h);
}
?>