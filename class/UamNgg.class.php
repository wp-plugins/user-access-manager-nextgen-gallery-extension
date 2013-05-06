<?php
/**
 * UamNgg.class.php
 * 
 * The UamNgg class file.
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

/**
 * The user access manager to the NextGen Gallery connector class.
 * 
 * @category UserAccessManager-NextGenGalleryExtension
 * @package  UserAccessManager-NextGenGalleryExtension
 * @author   Alexander Schneider <alexanderschneider85@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @link     http://wordpress.org/extend/plugins/user-access-manager/nextgen-gallery/
 */
 
class UamNgg
{
    protected $_aAdminOptions;
    protected $_sAdminOptionsName = "uamNggAdminOptions";
    
    /**
     * The constructor.
     */
    function __construct()
    {
        
    }
    
    /**
     * The activation function.
     * 
     * @return null
     */
    public function activate()
    {
        global $oUserAccessManager;
        
        $aUamOptions = $oUserAccessManager->getAdminOptions();
        
        if ($aUamOptions['lock_file'] == 'true') {
            $this->_createHtaccessFiles();
        }
    }
    
    /**
     * The deactivation function.
     * 
     * @return null
     */
    public function deactivate()
    {
        $this->_removeHtaccessFiles();
    }
    
    /**
     * Returns the current settings
     * 
     * @return array
     */
    public function getAdminOptions()
    {
        if (empty($this->_aAdminOptions)) {
            $aUamAdminOptions = array(
                'hide_album' => 'false', 
                'album_content' => __(
                    'Sorry you have no rights to view this album!', 
                    'uam-ngg'
                ),
                'hide_gallery' => 'false',
                'hide_gallery_title' => 'false', 
                'gallery_title' => __('No rights!', 'uam-ngg'),
                'gallery_content' => __(
                    'Sorry you have no rights to view this gallery!', 
                    'uam-ngg'
                ),
                'hide_image' => 'false',
            );
            
            $aUamOptions = get_option($this->_sAdminOptionsName);
            
            if (!empty($aUamOptions)) {
                foreach ($aUamOptions as $sKey => $mOption) {
                    $aUamAdminOptions[$sKey] = $mOption;
                }
            }
            
            update_option($this->_sAdminOptionsName, $aUamAdminOptions);
            $this->_aAdminOptions = $aUamAdminOptions;
        }

        return $this->_aAdminOptions;
    }
    
    /**
     * The function for the update_option_permalink_structure action.
     * 
     * @return null
     */
    public function updatePermalink()
    {
        $this->_createHtaccessFiles();
    }
    
    /**
     * The function for the uam_update_options action.
     * 
     * @param array $aUamOptions The User Access Manager Options
     * 
     * @return null
     */
    public function updateUamSettings($aUamOptions)
    {
        if ($aUamOptions['lock_file'] == 'false') {
            $this->_removeHtaccessFiles();
        } else {
            $this->_createHtaccessFiles();
        }
    }
    
    /**
     * Returns the gallery directory.
     * 
     * @return string
     */
    private function _getGalleryDir()
    {
        $sDir = str_replace("\\", "/", ABSPATH);
        
        global $ngg;
        $sDir .= $ngg->options['gallerypath'];
        
        return $sDir;
    }
    
    /**
     * Creates the htaccess files.
     * 
     * @param boolean $blWithPassword If true create also the htpasswd file.
     * 
     * @return null
     */
    private function _createHtaccessFiles($blWithPassword = true)
    {
        global $oUserAccessManager;
        $sDir = $this->_getGalleryDir();
        
        $oUserAccessManager->createHtaccess($sDir, 'nggImage');
        
        if ($blWithPassword) {
            $oUserAccessManager->createHtpasswd(true, $sDir);
        }
    }
    
    /**
     * Remove the htaccess files.
     * 
     * @return null
     */
    private function _removeHtaccessFiles()
    {
        global $oUserAccessManager;
        
        $sDir = $this->_getGalleryDir();
        
        $oUserAccessManager->deleteHtaccessFiles($sDir);
    }
    
    
    /*
     * Admin output functions
     */
    
    /**
     * Add additional content to the album box.
     * 
     * @param integer $sGalleryId The gallery id.
     * 
     * @return null
     */
    public function showAlbumItemContent($sGalleryId)
    {
        global $oUserAccessManager;
        
        $sContent = $oUserAccessManager->getPlColumn(
            'nggGallery',
            $sGalleryId
        );
        
        return '<p><b>'.__('Access', 'uam-ngg').':</b> '.$sContent.'</p>';
    }
    
    /**
     * Adds a column header to the gallery columns.
     * 
     * @param array $aGalleryColumns The gallery columns.
     * 
     * @return array
     */
    public function showGalleryHeadColumn($aGalleryColumns)
    {
        $aGalleryColumns['uamAccess'] = __('Access', 'uam-ngg');
        
        return $aGalleryColumns;
    }
    
    /**
     * Add the column content for the uamAccess column.
     * 
     * @param string  $sColumn   The column name.
     * @param integer $iGalleryId The gallery id.
     * 
     * @return null
     */
    public function showGalleryColumn($sColumn, $iGalleryId)
    {
        if ($sColumn == 'uamAccess') {
            global $oUserAccessManager;
            
            echo $oUserAccessManager->getPlColumn(
                'nggGallery',
                $iGalleryId
            );
        }
    }
    
    /**
     * Adds a column header to the image columns.
     * 
     * @param array $aImageColumns The gallery columns.
     * 
     * @return array
     */
    public function showImageHeadColumn($aImageColumns)
    {
        $aImageColumns['uamAccess'] = __('Access', 'uam-ngg');
        
        return $aImageColumns;
    }
    
    /**
     * Add the column content for the uamAccess column.
     * 
     * @param string  $sColumn  The column name.
     * @param integer $iImageId The image id.
     * 
     * @return null
     */
    public function showImageColumn($sColumn, $iImageId)
    {
        if ($sColumn == 'uamAccess') {
            global $oUserAccessManager;
            
            echo $oUserAccessManager->showPlGroupSelectionForm(
                'nggImage',
                $iImageId,
                'nggImage['.$iImageId.']'
            );
            
            echo '<input type="hidden" name="nggUpdateImage[]" value="'.$iImageId.'" />';
        }
    }
    
    /**
     * Shows the user group selection form at the album settings page.
     * 
     * @param integer $iAlbumId The id of the album.
     * 
     * @return null
     */
    public function showAlbumEditForm($iAlbumId)
    {
        global $oUserAccessManager;
        
        $sOutput = '<tr>';
        $sOutput .= '<th>';
        $sOutput .= 'User Groups<br/>';
        
        $sOutput .= $oUserAccessManager->showPlGroupSelectionForm(
            'nggAlbum',
            $iAlbumId
        );
        
        $sOutput .= '</th>';
        $sOutput .= '</tr>';
        
        echo $sOutput;
    }
    
    /**
     * Saves the user groups for the album.
     * 
     * @param integer $iAlbumId The id of the album.
     * 
     * @return null
     */
    public function updateAlbum($iAlbumId)
    {
        global $oUserAccessManager;
        
        $oUserAccessManager->savePlObjectData(
            'nggAlbum', 
            $iAlbumId
        );
    }
    
    /**
     * Shows the user group selection form at the gallery settings page.
     * 
     * @param integer $iGalleryId The id of the gallery.
     * 
     * @return null
     */
    public function showGalleryEditForm($iGalleryId)
    {
        global $oUserAccessManager;
        
        $sOutput = '<tr>';
        $sOutput .= '<th class="left">';
        $sOutput .= 'User Groups';
        $sOutput .= '</th>';
        $sOutput .= '<th class="left">';
        
        $sOutput .= $oUserAccessManager->showPlGroupSelectionForm(
            'nggGallery', 
            $iGalleryId
        );
        
        $sOutput .= '</th>';
        $sOutput .= '</tr>';
        
        echo $sOutput;
    }
    
    /**
     * Saves the user groups for the gallery.
     * 
     * @param integer $iGalleryId The id of the gallery.
     * 
     * @return null
     */
    public function updateGallery($iGalleryId)
    {
        global $oUserAccessManager;
        
        $oUserAccessManager->savePlObjectData(
            'nggGallery', 
            $iGalleryId
        );
        
        $aNggImagesGroups = array();
        
        if (isset($_POST['nggImage'])) {
            $aNggImagesGroups = $_POST['nggImage'];
        }
        
        if (isset($_POST['nggUpdateImage'])) {
            $aNggUpdateImages = $_POST['nggUpdateImage'];
            
            foreach ($aNggUpdateImages as $nggImageId) {
                $iImageGroups = array();
                
                if (isset($aNggImagesGroups[$nggImageId])) {
                    $iImageGroups = $aNggImagesGroups[$nggImageId];
                }
                
                $oUserAccessManager->savePlObjectData(
                    'nggImage', 
                    $nggImageId,
                    $iImageGroups
                );
            }
        }
        
        

    }
    
    /**
     * Prints the settings page.
     * 
     * @return null
     */
    public function printSettingsPage()
    {
        if (isset($_GET['page'])) {
            $sCurAdminPage = $_GET['page'];

            if ($sCurAdminPage == 'uam_ngg_settings') {
                include UAM_NGG_REALPATH."tpl/adminSettings.php";
            }
        }
    }
    
    
    /*
     * Image path functions.
     */
    
    /**
     * Manipulates the image url.
     * 
     * @param object $oImage The image object.
     * 
     * @return null
     */
    public function loadImage($oImage)
    {
        global $oUserAccessManager;
        
        $aUamOptions = $oUserAccessManager->getAdminOptions();
        $sSuffix = 'uamfiletype=nggImage';
        
        if (!$oUserAccessManager->isPermalinksActive()
            && $aUamOptions['lock_file'] == 'true'
        ) {
            //Adding '.jpg' to the prefix prevents thickbox display error
            $sPrefix = home_url('/').'.jpg?uamgetfile=';

            $oImage->imageURL = $sPrefix.$oImage->imageURL.'&'.$sSuffix;
            $oImage->thumbURL = $sPrefix.$oImage->thumbURL.'&'.$sSuffix;
        } else {
            $oImage->imageURL = $oImage->imageURL.'?'.$sSuffix;
            $oImage->thumbURL = $oImage->thumbURL.'?'.$sSuffix;
        }
    }
    
    /**
     * Returns the image file object.
     * 
     * @param string $sFileUrl The url of the image.
     * 
     * @return object
     */
    public function getNggImageFileObject($sFileUrl)
    {
        $oImage = $this->_getImageFromUrl($sFileUrl);

        $oObject = new stdClass();
        $oObject->id = $oImage->pid;
        $oObject->isImage = true;
        $oObject->type = 'nggImage';
        
        if ($oImage->isThumb) {
            $oObject->file = $oImage->thumbPath;
        } else {
            $oObject->file = $oImage->imagePath;
        }
        
        return $oObject;
    }
    
    /**
     * Returns the id of the image by the given url.
     * 
     * @param string $sUrl The url of the image.
     * 
     * @return object
     */
    private function _getImageFromUrl($sUrl)
    {
        global $ngg, $oUserAccessManager;
        
        if ($oUserAccessManager->isPermalinksActive()) {
            $sUrl = $ngg->options['gallerypath'].$sUrl;
        }
        
        $sUrl = str_replace(site_url().'/', '', $sUrl);
        $sThumbs = '/thumbs/thumbs_';
        $blThumb = false;
        
        if (strpos($sUrl, $sThumbs)) {
            $sExpUrl = explode($sThumbs, $sUrl);
            $sFileName = $sExpUrl[count($sExpUrl)-1];
            $sGalleryPath = $sExpUrl[0];
            $blThumb = true;
        } else {
            $sExpUrl = explode('/', $sUrl);
            $sFileName = $sExpUrl[count($sExpUrl)-1];
            unset($sExpUrl[count($sExpUrl)-1]);
            $sGalleryPath = implode('/', $sExpUrl);
        }
        
        global $wpdb;
        
        $iGalleryId = $wpdb->get_var(
            "SELECT gid
            FROM $wpdb->nggallery
            WHERE path = '".$sGalleryPath."'"
        );
        
        $iImageId = $wpdb->get_var(
            "SELECT pid
            FROM $wpdb->nggpictures
            WHERE galleryid = ".$iGalleryId."
            AND filename = '".$sFileName."'"
        );
        
        global $nggdb;
        $oImage = $nggdb->find_image($iImageId);
        
        $oImage->id = $iImageId;
        $oImage->isThumb = $blThumb;

        return $oImage;
    }
    
    
    /*
     * Pluggable functions.
     */
    
    /**
     * Returns the full album by the given id.
     * 
     * @param integer $iAlbumId   The id of the album.
     * @param object  $oUserGroup The current user group.
     * 
     * @return object
     */
    public function getNggAlbumFull($iAlbumId, $oUserGroup)
    {
        return array();
    }
    
    /**
     * Returns all albums which are assigned to the user group.
     * 
     * @param array  $aRealAlbums The albums which are assigned directly.
     * @param object $oUserGroup  The current user group.
     * 
     * @return array
     */
    public function getNggAlbumFullObjects($aRealAlbums, $oUserGroup)
    {
        return $aRealAlbums;
    }
    
    /**
     * Returns the full gallery by the given id.
     * 
     * @param integer $iGalleryId The id of the gallery.
     * @param object  $oUserGroup The current user group.
     * 
     * @return object
     */
    public function getNggGalleryFull($iGalleryId, $oUserGroup)
    {
        global $nggdb;
        //$oGallery = $nggdb::find_gallery($iGalleryId);
        $aAlbums = $nggdb->find_all_album();

        $isRecursiveMember = array();
        
        foreach ($aAlbums as $oAlbum) {
            if (is_array(unserialize($oAlbum->sortorder))
                && in_array($iGalleryId, unserialize($oAlbum->sortorder))
                && $oUserGroup->objectIsMember('nggAlbum', $oAlbum->id)
            ) {
                $oAlbumObject = new stdClass();
                $oAlbumObject->name = $oAlbum->name;
                $isRecursiveMember['nggAlbum'][] = $oAlbumObject;
            }
        }
        
        return $isRecursiveMember;
    }
    
    /**
     * Returns all galleries which are assigned to the user group.
     * 
     * @param array  $aRealGalleries The galleries which are assigned directly.
     * @param object $oUserGroup     The current user group.
     * 
     * @return array
     */
    public function getNggGalleryFullObjects($aRealGalleries, $oUserGroup)
    {
        //TODO
        return $aRealGalleries;
    }
    
    /**
     * Returns the full image by the given id.
     * 
     * @param integer $iImageId   The id of the gallery.
     * @param object  $oUserGroup The user group.
     * 
     * @return object
     */
    public function getNggImageFull($iImageId, $oUserGroup)
    {
        global $nggdb;
        $image = $nggdb->find_image($iImageId);

        $isRecursiveMember = array();

        if ($oUserGroup->objectIsMember('nggGallery', $image->galleryid)) {
            $oGallery = $nggdb->find_gallery($image->galleryid);

            $oGalleryObject = new stdClass();
            $oGalleryObject->name = $oGallery->name;
            
            $galleryIsRecursiveMember 
                = $this->getNggGalleryFull($image->galleryid, $oUserGroup);

            if ($galleryIsRecursiveMember !== array()) {
                $oGalleryObject->recursiveMember = $galleryIsRecursiveMember;
            }
            
            $isRecursiveMember['nggGallery'][] = $oGalleryObject;
        }
        
        return $isRecursiveMember;
    }
    
    /**
     * Returns all images which are assigned to the user group.
     * 
     * @param array  $aRealImages The galleries which are assigned directly.
     * @param object $oUserGroup  The current user group.
     * 
     * @return array
     */
    public function getNggImageFullObjects($aRealImages, $oUserGroup)
    {
        //TODO
        return $aRealImages;
    }
    
    
    /*
     * Output functions.
     */
    
    /**
     * Manipulates the output of a gallery.
     * 
     * @param string  $sOutput   The output.
     * @param integer $iGalleryId The gallery id.
     * 
     * @return string
     */
    public function showGalleryContent($sOutput, $iGalleryId)
    {
        global $oUserAccessManager;
        
        $oUamAccessHandler = $oUserAccessManager->getAccessHandler();
        $aOptions = $this->getAdminOptions();
        
        if (!$oUamAccessHandler->checkObjectAccess('nggGallery', $iGalleryId)) {
            $sOutput = $aOptions['gallery_content'];
        }
        
        return $sOutput;
    }
    
    /**
     * Filters the images.
     * 
     * @param array $aImages The images of the gallery.
     * 
     * @return array
     */
    public function showGalleryImages($aImages)
    {
        $aOptions = $this->getAdminOptions();
        
        if ($aOptions['hide_image'] == 'true') {
            global $oUserAccessManager;
            
            $uamAccessHandler = $oUserAccessManager->getAccessHandler();
            $aOptions = $this->getAdminOptions();
            $aFilteredImages = array();
            
            foreach ($aImages as $sKey => $oImage) {
                if ($uamAccessHandler->checkObjectAccess('nggImage', $oImage->pid)) {
                    $aFilteredImages[$sKey] = $oImage;
                }
            }
            
            return $aFilteredImages;
        }
        
        return $aImages;
    }
    
    /**
     * Manipulates the gallery for a album.
     * 
     * @param object $oGallery The gallery.
     * 
     * @return object
     */
    public function showGalleryObjectForAlbum($oGallery)
    {
        global $oUserAccessManager;

        //Manipulate gallery title
        $oUamAccessHandler = $oUserAccessManager->getAccessHandler();
        $aOptions = $this->getAdminOptions();
        
        if ($aOptions['hide_gallery_title'] == 'true'
            && !$oUamAccessHandler->checkObjectAccess('nggGallery', $oGallery->gid)
        ) {
            $oGallery->title = $aOptions['gallery_title'];
        }
        
        //Manipulate preview image
        $aUamOptions = $oUserAccessManager->getAdminOptions();
        $sSuffix = 'uamfiletype=nggImage';
        
        if (!$oUserAccessManager->isPermalinksActive()
            && $aUamOptions['lock_file'] == 'true'
        ) {
            $sPrefix = home_url('/').'?uamgetfile=';

            $oGallery->previewurl = $sPrefix.$oGallery->previewurl.'&'.$sSuffix;
        } else {
            $oGallery->previewurl = $oGallery->previewurl.'?'.$sSuffix;
        }
        
        return $oGallery;
    }
    
    /**
     * Filters the galleries.
     * 
     * @param array $aGalleries The galleries of the album.
     * 
     * @return array
     */
    public function showGalleriesForAlbum($aGalleries)
    {
        $aOptions = $this->getAdminOptions();
        
        if ($aOptions['hide_gallery'] == 'true') {
            global $oUserAccessManager;
            
            $oUamAccessHandler = $oUserAccessManager->getAccessHandler();
            //$aOptions = $this->getAdminOptions();
            $aFilteredGalleries = array();
            
            foreach ($aGalleries as $sGalleryId => $oGallery) {
                if ($oUamAccessHandler->checkObjectAccess('nggGallery', $sGalleryId)) {
                    $aFilteredGalleries[$sGalleryId] = $oGallery;
                }
            }
    
            return $aFilteredGalleries;
        }
        
        return $aGalleries;
    }

    
    /**
     * Manipulates the output of a album.
     * 
     * @param string  $sOutput  The output.
     * @param integer $iAlbumId The album id.
     * 
     * @return string
     */
    public function showAlbumContent($sOutput, $iAlbumId)
    {
        global $oUserAccessManager;
        
        $uamAccessHandler = $oUserAccessManager->getAccessHandler();
        $options = $this->getAdminOptions();
        
        if (!$uamAccessHandler->checkObjectAccess('nggAlbum', $iAlbumId)) {
            $sOutput = $options['album_content'];
        }
        
        return $sOutput;
    }
}