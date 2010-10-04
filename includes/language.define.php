<?php
/**
 * language.define.php
 * 
 * Defines needed for the language
 * 
 * PHP versions 5
 * 
 * @category  UserAccessManager-NextGenGalleryExtension
 * @package   UserAccessManager-NextGenGalleryExtension
 * @author    Alexander Schneider <alexanderschneider85@googlemail.com>
 * @copyright 2008-2010 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $Id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/nextgen-gallery/
 */

// --- For user groups box ---
define('TXT_UAM_GROUP_MEMBERSHIP_BY_NGGALBUM', __('Group membership given by albums', 'uam-ngg'));
define('TXT_UAM_GROUP_MEMBERSHIP_BY_NGGGALLERY', __('Group membership given by galleries', 'uam-ngg'));
define('TXT_UAM_GROUP_MEMBERSHIP_BY_NGGIMAGE', __('Group membership given by images', 'uam-ngg'));

// --- Error Messages ---
define('TXT_UAMNGG_NGG_MISSING', __('User Access Manager - NextGEN Gallery Extension: For this extention the NextGEN Gallery is needed.', 'uam-ngg'));
define('TXT_UAMNGG_NGG_TO_LOW', __('User Access Manager - NextGEN Gallery Extension: Your version of the NextGEN Gallery is not supported. You need at least version 1.7. Your version is: %s', 'uam-ngg'));

// --- Menu entry ---
define('TXT_UAMNGG_NGG_GALLERY_SETTING', __('NextGEN Gallery Settings', 'uam-ngg'));

// --- Settings ---
define('TXT_UAMNGG_SETTINGS', __('NextGEN Gallery Extension Settings', 'uam-ngg'));
define('TXT_UAMNGG_YES', __('Yes', 'uam-ngg'));
define('TXT_UAMNGG_NO', __('No', 'uam-ngg'));
define('TXT_UAMNGG_UPDATE_SETTINGS', __('Settings updated.', 'uam-ngg'));
define('TXT_UAMNGG_NGG_UPDATE_SETTING', __('Update settings', 'uam-ngg'));

// --- Settings - Album ---
define('TXT_UAMNGG_ALBUM_SETTING', __('Album Settings', 'uam-ngg'));
define('TXT_UAMNGG_ALBUM_SETTING_DESC', __('Set up the behaviour of locked albums', 'uam-ngg'));
define('TXT_UAMNGG_HIDE_ALBUM', __('Hide album', 'uam-ngg'));
define('TXT_UAMNGG_HIDE_ALBUM_DESC', __('Selecting "Yes" will hide albums if the user has no access. ', 'uam-ngg'));
define('TXT_UAMNGG_ALBUM_CONTENT', __('Album content', 'uam-ngg'));
define('TXT_UAMNGG_ALBUM_CONTENT_DESC', __('Displayed text as album content if user has no access.', 'uam-ngg'));

// --- Settings - Gallery ---
define('TXT_UAMNGG_GALLERY_SETTING', __('Gallery Settings', 'uam-ngg'));
define('TXT_UAMNGG_GALLERY_SETTING_DESC', __('Set up the behaviour of locked galleries', 'uam-ngg'));
define('TXT_UAMNGG_HIDE_GALLERY', __('Hide Gallery', 'uam-ngg'));
define('TXT_UAMNGG_HIDE_GALLERY_DESC', __('Selecting "Yes" will hide galleries if the user has no access. ', 'uam-ngg'));
define('TXT_UAMNGG_GALLERY_TITLE', __('Gallery title', 'uam-ngg'));
define('TXT_UAMNGG_GALLERY_TITLE_DESC', __('Displayed text as gallery title if user has no access.', 'uam-ngg'));
define('TXT_UAMNGG_HIDE_GALLERY_TITLE', __('Hide gallery title', 'uam-ngg'));
define('TXT_UAMNGG_HIDE_GALLERY_TITLE_DESC', __('Selecting "Yes" will show the text which is defined at "'.TXT_UAMNGG_GALLERY_TITLE.'" if user has no access. ', 'uam-ngg'));
define('TXT_UAMNGG_GALLERY_CONTENT', __('Gallery content', 'uam-ngg'));
define('TXT_UAMNGG_GALLERY_CONTENT_DESC', __('Displayed text as gallery content if user has no access.', 'uam-ngg'));

// --- Settings - Image ---
define('TXT_UAMNGG_IMAGE_SETTING', __('Image Settings', 'uam-ngg'));
define('TXT_UAMNGG_IMAGE_SETTING_DESC', __('Set up the behaviour of locked images', 'uam-ngg'));
define('TXT_UAMNGG_HIDE_IMAGE', __('Hide image', 'uam-ngg'));
define('TXT_UAMNGG_HIDE_IMAGE_DESC', __('Selecting "Yes" will hide images if the user has no access. ', 'uam-ngg'));