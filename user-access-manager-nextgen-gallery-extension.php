<?php
/**
 * Plugin Name: User Access Manager - NextGEN Gallery Extension
 * Plugin URI: http://www.gm-alex.de/projects/wordpress/plugins/user-access-manager/nextgen-gallery/
 * Author URI: http://www.gm-alex.de/
 * Version: 0.1.4.1
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
 * @copyright 2008-2013 Alexander Schneider
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
    define('UAM_NGG_REALPATH', plugin_basename(dirname(__FILE__)).'/'); //ONLY FOR MY LOCAL DEBUG
} else {
    define('UAM_NGG_REALPATH', WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/');
}

//includes
require_once 'includes/language.define.php';

//Check requirements
global $ngg, $oUserAccessManager;

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
    $oUamNgg = new UamNgg($oUserAccessManager);
}

if (!function_exists("initUamToNggExtension")) {
    /**
     * Initialise the User Access Manager.
     * 
     * @param object $oUserAccessManager The user access manager object.
     * 
     * @return null
     */
    function initUamToNggExtension($oUserAccessManager)
    {
        if (version_compare($oUserAccessManager->getVersion(), '1.1.2') === -1) {
            add_action(
                'admin_notices', 
                create_function(
                    '', 
                    'echo \'<div id="message" class="error"><p><strong>'. 
                    sprintf(TXT_UAMNGG_UAM_TO_LOW, $oUserAccessManager->getVersion()).
                    '</strong></p></div>\';'
                )
            );
            
            return;
        }
        
        global $oUamNgg;
        
        /*
         * Register objects.
         */
        
        $nggAlbum = array(
            'name' => 'nggAlbum',
            'reference' => $oUamNgg,
            'getFull' => 'getNggAlbumFull',
            'getFullObjects' => 'getNggAlbumFullObjects'
        );
        
        $oUserAccessManager->getAccessHandler()->registerPlObject($nggAlbum);
        
        
        $nggGallery = array(
            'name' => 'nggGallery',
            'reference' => $oUamNgg,
            'getFull' => 'getNggGalleryFull',
            'getFullObjects' => 'getNggGalleryFullObjects'
        );
        
        $oUserAccessManager->getAccessHandler()->registerPlObject($nggGallery);
        
        
        $nggImage = array(
            'name' => 'nggImage',
            'reference' => $oUamNgg,
            'getFull' => 'getNggImageFull',
            'getFullObjects' => 'getNggImageFullObjects',
            'getFileObject' => 'getNggImageFileObject'
        );
        
        $oUserAccessManager->getAccessHandler()->registerPlObject($nggImage);
        
        /*
         * Create actions and filters.
         */
        
        if (function_exists('add_action')) {
            add_action('uam_add_submenu', 'uamNggAPMenu');
            
            add_action('ngg_edit_album_settings', array($oUamNgg, 'showAlbumEditForm'));
            add_action('ngg_update_album', array($oUamNgg, 'updateAlbum'));
            
            add_action('ngg_manage_gallery_settings', array($oUamNgg, 'showGalleryEditForm'));
            add_action('ngg_update_gallery', array($oUamNgg, 'updateGallery'));
            
            add_action('ngg_display_album_item_content', array($oUamNgg, 'showAlbumItemContent'));
            add_action('ngg_manage_gallery_custom_column', array($oUamNgg, 'showGalleryColumn'), 10, 2);
            add_action('ngg_manage_image_custom_column', array($oUamNgg, 'showImageColumn'), 10, 2);
            
            add_action('update_option_permalink_structure', array($oUamNgg, 'updatePermalink'));
            add_action('uam_update_options', array($oUamNgg, 'updateUamSettings'));
        }
        
        if (function_exists('add_filter')) {
            //add_filter('ngg_show_slideshow_content', array($uamNgg, 'showSlideShow'), 10, 2);
            
            add_filter('ngg_show_gallery_content', array($oUamNgg, 'showGalleryContent'), 10, 2);
            add_filter('ngg_picturelist_object', array($oUamNgg, 'showGalleryImages'), 10, 2);
            
            //add_filter('ngg_show_related_gallery_content', array($uamNgg, 'showGalleryRelatedContent'), 10, 2);
            //add_filter('ngg_show_gallery_tags_content', array($uamNgg, 'showGalleryTagsContent'), 10, 2);
            
            add_filter('ngg_show_album_content', array($oUamNgg, 'showAlbumContent'), 10, 2);
            add_filter('ngg_show_album_tags_content', array($oUamNgg, 'showAlbumTagsContent'), 10, 2);
            add_filter('ngg_album_galleryobject', array($oUamNgg, 'showGalleryObjectForAlbum'), 10);
            add_filter('ngg_album_galleries', array($oUamNgg, 'showGalleriesForAlbum'), 10);
            
            //add_filter('ngg_show_images_content', array($uamNgg, 'showImageContent'), 10, 2); 
            //add_filter('ngg_show_imagebrowser_content', array($uamNgg, 'showImageBrowserContent'), 10, 2);
            
            add_filter('ngg_manage_gallery_columns', array($oUamNgg, 'showGalleryHeadColumn'));
            add_filter('ngg_manage_images_columns', array($oUamNgg, 'showImageHeadColumn'));
            add_filter('ngg_get_image', array($oUamNgg, 'loadImage'));
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
        global $oUamNgg;
        
        if (!isset($oUamNgg)) {
            return;
        }
        
        /*
         * Admin sub menus
         */
        
        if (function_exists('add_submenu_page')) {
            add_submenu_page('uam_usergroup', TXT_UAMNGG_NGG_GALLERY_SETTING, TXT_UAMNGG_NGG_GALLERY_SETTING, 'read', 'uam_ngg_settings', array($oUamNgg, 'printSettingsPage'));
        }
    }
}



if (isset($oUamNgg)) {
    if (function_exists('add_action')) {
        add_action('uam_init', 'initUamToNggExtension');
    }
    
    
    /*
     * install
     */
    
    if (function_exists('register_activation_hook')) {
        register_activation_hook(__FILE__, array($oUamNgg, 'activate'));
    }
    
    
    /*
     * uninstall
     */
    
    if (function_exists('register_uninstall_hook')) {
        register_uninstall_hook(__FILE__, array($oUamNgg, 'deactivate'));
    } elseif (function_exists('register_deactivation_hook')) {
        //Fallback
        register_deactivation_hook(__FILE__, array($oUamNgg, 'deactivate'));
    }
    
    
    /*
     * deactivation
     */
    
    if (function_exists('register_deactivation_hook')) {
        register_deactivation_hook(__FILE__, array($oUamNgg, 'deactivate'));
    }
}