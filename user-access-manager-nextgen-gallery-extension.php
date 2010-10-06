<?php
/**
 * Plugin Name: User Access Manager - NextGEN Gallery Extension
 * Plugin URI: http://www.gm-alex.de/projects/wordpress/plugins/user-access-manager/nextgen-gallery/
 * Author URI: http://www.gm-alex.de/
 * Version: 0.1.1 Beta
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

//Paths
load_plugin_textdomain(
    'user-access-manager-nextgen-gallery-extension', 
    false, 
    'user-access-manager-nextgen-gallery-extension/lang'
);

if (defined('UAM_LOCAL_DEBUG')) {
    //ONLY FOR MY LOCAL DEBUG
    define(
        'UAM_NGG_REALPATH',
        '/'.plugin_basename(dirname(__FILE__)).'/'
    );
} else {
    define(
        'UAM_NGG_REALPATH',
        WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/'
    );
}

//includes
require_once 'includes/language.define.php';

//Check requirements
global $ngg, $userAccessManager;

if (!isset($ngg)) {
    add_action(
        'admin_notices', 
        create_function(
            '', 
            'echo \'<div id="message" class="error"><p><strong>'. 
            TXT_UAMNGG_NGG_MISSING.
            '</strong></p></div>\';'
        )
    );
    
    return;
} elseif (version_compare($ngg->version, '1.7') === -1) {
    add_action(
        'admin_notices', 
        create_function(
            '', 
            'echo \'<div id="message" class="error"><p><strong>'. 
            sprintf(TXT_UAMNGG_NGG_TO_LOW, $ngg->version).
            '</strong></p></div>\';'
        )
    );
    
    return;
}


require_once 'class/UamNgg.class.php';

if (class_exists("UamNgg")) {
    $uamNgg = new UamNgg($userAccessManager);
}

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
        if (version_compare($userAccessManager->getVersion(), '1.1.2') === -1) {
            add_action(
                'admin_notices', 
                create_function(
                    '', 
                    'echo \'<div id="message" class="error"><p><strong>'. 
                    sprintf(TXT_UAMNGG_UAM_TO_LOW, $userAccessManager->getVersion()).
                    '</strong></p></div>\';'
                )
            );
            
            return;
        }
        
        global $uamNgg;
        
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
         * Create actions and filters.
         */
        
        if (function_exists('add_action')) {
            add_action('uam_add_submenu', 'uamNggAPMenu');
            
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
            //add_filter('ngg_show_slideshow_content', array(&$uamNgg, 'showSlideShow'), 10, 2);
            
            add_filter('ngg_show_gallery_content', array(&$uamNgg, 'showGalleryContent'), 10, 2);
            add_filter('ngg_picturelist_object', array(&$uamNgg, 'showGalleryImages'), 10, 2);
            
            //add_filter('ngg_show_related_gallery_content', array(&$uamNgg, 'showGalleryRelatedContent'), 10, 2);
            //add_filter('ngg_show_gallery_tags_content', array(&$uamNgg, 'showGalleryTagsContent'), 10, 2);
            
            add_filter('ngg_show_album_content', array(&$uamNgg, 'showAlbumContent'), 10, 2);
            add_filter('ngg_show_album_tags_content', array(&$uamNgg, 'showAlbumTagsContent'), 10, 2);
            add_filter('ngg_album_galleryobject', array(&$uamNgg, 'showGalleryObjectForAlbum'), 10);
            add_filter('ngg_album_galleries', array(&$uamNgg, 'showGalleriesForAlbum'), 10);
            
            //add_filter('ngg_show_images_content', array(&$uamNgg, 'showImageContent'), 10, 2); 
            //add_filter('ngg_show_imagebrowser_content', array(&$uamNgg, 'showImageBrowserContent'), 10, 2);
            
            add_filter('ngg_manage_gallery_columns', array(&$uamNgg, 'showGalleryHeadColumn'));
            add_filter('ngg_manage_images_columns', array(&$uamNgg, 'showImageHeadColumn'));
            add_filter('ngg_get_image', array(&$uamNgg, 'loadImage'));
        }
    }
}


if (!function_exists("uamNggAPMenu")) {
    /**
     * Creates the menu at the admin panel
     * 
     * @return null;
     */
    function uamNggAPMenu()
    {
        global $uamNgg;
        
        if (!isset($uamNgg)) {
            return;
        }
        
        /*
         * Admin sub menus
         */
        
        if (function_exists('add_submenu_page')) {
            add_submenu_page('uam_usergroup', TXT_UAMNGG_NGG_GALLERY_SETTING, TXT_UAMNGG_NGG_GALLERY_SETTING, 'read', 'uam_ngg_settings', array(&$uamNgg, 'printSettingsPage'));
        }
    }
}



if (isset($uamNgg)) {
    if (function_exists('add_action')) {
        add_action('uam_init', 'initUamToNggExtension');
    }
    
    
    /*
     * install
     */
    
    if (function_exists('register_activation_hook')) {
        register_activation_hook(__FILE__, array(&$uamNgg, 'activate'));
    }
    
    
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
}