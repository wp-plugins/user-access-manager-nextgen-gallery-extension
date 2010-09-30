<?php
/**
 * Plugin Name: User Access Manager - NextGEN Gallery Extension
 * Plugin URI: http://www.gm-alex.de/projects/wordpress/plugins/user-access-manager/nextgen-gallery/
 * Author URI: http://www.gm-alex.de/
 * Version: 0.1
 * Author: Alexander Schneider
 * Description: With this plugin you can use the user access manager to control the access for the NextGen Gallery.
 * 
 * user-access-manager-nextgen-gallery-extension.php
 *
 * PHP versions 5
 * 
 * @category  UserAccessManager-NextGenGalleryExtension
 * @package   UserAccessManager-NextGenGalleryExtension
 * @author    Alexander Schneider <alexanderschneider85@gmail.com>
 * @copyright 2008-2010 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $Id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/nextgen-gallery/
*/

//Check requirements
global $ngg;

if (!isset($ngg)) {
    add_action(
        'admin_notices', 
        create_function(
            '', 
            'echo \'<div id="message" class="error"><p><strong>'. 
            __('User Access Manager - NextGEN Gallery Extension: For this extention the NextGEN Gallery is needed.', 'uam-ngg').
            '</strong></p></div>\';'
        )
    );
    
    return;
} elseif (doubleval($ngg->version) < 1.7) {
    add_action(
        'admin_notices', 
        create_function(
            '', 
            'echo \'<div id="message" class="error"><p><strong>'. 
            __('User Access Manager - NextGEN Gallery Extension: Your version of the NextGEN Gallery is not supported. You need at least version 1.7. Your version is: '.doubleval($ngg->version), 'uam-ngg').
            '</strong></p></div>\';'
        )
    );
    
    return;
}

define('TXT_GROUP_MEMBERSHIP_BY_NGGALBUM', __('Group membership given by albums', 'uam-ngg'));
define('TXT_GROUP_MEMBERSHIP_BY_NGGGALLERY', __('Group membership given by galleries', 'uam-ngg'));
define('TXT_GROUP_MEMBERSHIP_BY_NGGIMAGE', __('Group membership given by images', 'uam-ngg'));

require_once 'class/UamNgg.class.php';

if (!function_exists("initUamToNggExtension")) {
    /**
     * Initialise the User Access Manager.
     * 
     * @param object $userAccessManager The user access manager object.
     * 
     * @return null
     */
    function initUamToNggExtension($userAccessManager) 
    {
        if (class_exists("UamNgg")) {
            $uamNgg = new UamNgg($userAccessManager);
            
            /*
             * Register objects.
             */
            
            $nggAlbum = array(
                'name' => 'nggAlbum',
                'reference' => &$uamNgg,
                'getFull' => 'getNggAlbumFull',
                'getFullObjects' => 'getNggAlbumFullObjects'
            );
            
            $userAccessManager->getAccessHandler()->registerPlObject($nggAlbum);
            
            
            $nggGallery = array(
                'name' => 'nggGallery',
                'reference' => &$uamNgg,
                'getFull' => 'getNggGalleryFull',
                'getFullObjects' => 'getNggGalleryFullObjects'
            );
            
            $userAccessManager->getAccessHandler()->registerPlObject($nggGallery);
            
            
            $nggImage = array(
                'name' => 'nggImage',
                'reference' => &$uamNgg,
                'getFull' => 'getNggImageFull',
                'getFullObjects' => 'getNggImageFullObjects',
                'getFileObject' => 'getNggImageFileObject'
            );
            
            $userAccessManager->getAccessHandler()->registerPlObject($nggImage);
  
            
            /*
             * uninstall
             */
            
            if (function_exists('register_uninstall_hook')) {
                register_uninstall_hook(__FILE__, array(&$uamNgg, 'deactivate'));
            } elseif (function_exists('register_deactivation_hook')) {
                //Fallback
                register_deactivation_hook(__FILE__, array(&$uamNgg, 'deactivate'));
            }
            
            
            /*
             * deactivation
             */
            
            if (function_exists('register_deactivation_hook')) {
                register_deactivation_hook(__FILE__, array(&$uamNgg, 'deactivate'));
            }
            
            
            /*
             * Create actions and filters.
             */
            
            if (function_exists('add_action')) {
                add_action('ngg_edit_album_settings', array(&$uamNgg, 'showAlbumEditForm'));
                add_action('ngg_update_album', array(&$uamNgg, 'updateAlbum'));
                
                add_action('ngg_manage_gallery_settings', array(&$uamNgg, 'showGalleryEditForm'));
                add_action('ngg_update_gallery', array(&$uamNgg, 'updateGallery'));
                
                add_action('ngg_display_album_item_content', array(&$uamNgg, 'showAlbumItemContent'));
                add_action('ngg_manage_gallery_custom_column', array(&$uamNgg, 'showGalleryColumn'), 10, 2);
                add_action('ngg_manage_image_custom_column', array(&$uamNgg, 'showImageColumn'), 10, 2);
                
                add_action('update_option_permalink_structure', array(&$uamNgg, 'updatePermalink'));
                add_action('uam_update_options', array(&$uamNgg, 'updateUamSettings'));
            }
            
            if (function_exists('add_filter')) {
                add_filter('ngg_show_slideshow_content', array(&$uamNgg, 'showSlideShow'), 10, 2);
                
                add_filter('ngg_show_gallery_content', array(&$uamNgg, 'showGalleryContent'), 10, 2);
                add_filter('ngg_gallery_output', array(&$uamNgg, 'showGalleryOutput'), 10, 2);
                add_filter('ngg_album_galleryobject', array(&$uamNgg, 'showGalleryObject'), 10, 2);
                add_filter('ngg_album_galleries', array(&$uamNgg, 'showGalleries'), 10, 2);
                add_filter('ngg_show_related_gallery_content', array(&$uamNgg, 'showGalleryRelatedContent'), 10, 2);
                add_filter('ngg_show_gallery_tags_content', array(&$uamNgg, 'showGalleryTagsContent'), 10, 2);
                
                add_filter('ngg_show_album_content', array(&$uamNgg, 'showAlbumContent'), 10, 2);
                add_filter('ngg_show_album_tags_content', array(&$uamNgg, 'showAlbumTagsContent'), 10, 2);
                
                add_filter('ngg_show_images_content', array(&$uamNgg, 'showImageContent'), 10, 2); 
                add_filter('ngg_show_imagebrowser_content', array(&$uamNgg, 'showImageBrowserContent'), 10, 2);
                
                add_filter('ngg_manage_gallery_columns', array(&$uamNgg, 'showGalleryHeadColumn'));
                add_filter('ngg_manage_images_columns', array(&$uamNgg, 'showImageHeadColumn'));
                add_filter('ngg_get_image', array(&$uamNgg, 'loadImage'));
            }
        }
    }
}

if (function_exists('add_action')) {
    add_action('uam_init', 'initUamToNggExtension');
}