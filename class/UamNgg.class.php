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
    protected $adminOptions;
    protected $adminOptionsName = "uamNggAdminOptions";
    
    /**
     * The constructor.
     * 
     * @return null
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
        global $userAccessManager;
        
        $uamOptions = $userAccessManager->getAdminOptions();
        
        if ($uamOptions['lock_file'] == 'true') {
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
        if (empty($this->adminOptions)) {
            $uamAdminOptions = array(
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
            
            $uamOptions = get_option($this->adminOptionsName);
            
            if (!empty($uamOptions)) {
                foreach ($uamOptions as $key => $option) {
                    $uamAdminOptions[$key] = $option;
                }
            }
            
            update_option($this->adminOptionsName, $uamAdminOptions);
            $this->adminOptions = $uamAdminOptions;
        }

        return $this->adminOptions;
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
     * @param array $uamOptions The User Access Manager Options
     * 
     * @return null
     */
    public function updateUamSettings($uamOptions)
    {
        if ($uamOptions['lock_file'] == 'false') {
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
        $dir = str_replace("\\", "/", ABSPATH);
        
        global $ngg;
        $dir .= $ngg->options['gallerypath'];
        
        return $dir;
    }
    
    /**
     * Creates the htaccess files.
     * 
     * @param boolean $withPasswd If true create also the htpasswd file.
     * 
     * @return null
     */
    private function _createHtaccessFiles($withPasswd = true)
    {
        global $userAccessManager;
        $dir = $this->_getGalleryDir();
        
        $userAccessManager->createHtaccess($dir, 'nggImage');
        
        if ($withPasswd) {
            $userAccessManager->createHtpasswd(true, $dir);
        }
    }
    
    /**
     * Remove the htaccess files.
     * 
     * @return null
     */
    private function _removeHtaccessFiles()
    {
        global $userAccessManager;
        
        $dir = $this->_getGalleryDir();
        
        $userAccessManager->deleteHtaccessFiles($dir);
    }
    
    
    /*
     * Admin output functions
     */
    
    /**
     * Add additional content to the album box.
     * 
     * @param integer $galleryId The gallery id.
     * 
     * @return null
     */
    public function showAlbumItemContent($galleryId)
    {
        global $userAccessManager;
        
        $content = $userAccessManager->getPlColumn(
            'nggGallery',
            $galleryId
        );
        
        return '<p><b>'.__('Access', 'uam-ngg').':</b> '.$content.'</p>';
    }
    
    /**
     * Adds a column header to the gallery columns.
     * 
     * @param array $galleryColumns The gallery columns.
     * 
     * @return array
     */
    public function showGalleryHeadColumn($galleryColumns)
    {
        $galleryColumns['uamAccess'] = __('Access', 'uam-ngg');
        
        return $galleryColumns;
    }
    
    /**
     * Add the column content for the uamAccess column.
     * 
     * @param string  $column   The column name.
     * @param integer $gallerId The gallery id.
     * 
     * @return null
     */
    public function showGalleryColumn($column, $gallerId)
    {
        if ($column == 'uamAccess') {
            global $userAccessManager;
            
            echo $userAccessManager->getPlColumn(
                'nggGallery',
                $gallerId
            );
        }
    }
    
    /**
     * Adds a column header to the image columns.
     * 
     * @param array $imageColumns The gallery columns.
     * 
     * @return array
     */
    public function showImageHeadColumn($imageColumns)
    {
        $imageColumns['uamAccess'] = __('Access', 'uam-ngg');
        
        return $imageColumns;
    }
    
    /**
     * Add the column content for the uamAccess column.
     * 
     * @param string  $column  The column name.
     * @param integer $imageId The image id.
     * 
     * @return null
     */
    public function showImageColumn($column, $imageId)
    {
        if ($column == 'uamAccess') {
            global $userAccessManager;
            
            echo $userAccessManager->showPlGroupSelectionForm(
                'nggImage',
                $imageId,
                'nggImage['.$imageId.']'
            );
        }
    }
    
    /**
     * Shows the user group selection form at the album settings page.
     * 
     * @param integer $albumId The id of the album.
     * 
     * @return null
     */
    public function showAlbumEditForm($albumId)
    {
        global $userAccessManager;
        
        $output = '<tr>';
        $output .= '<th>';
        $output .= 'User Groups<br/>';
        
        $output .= $userAccessManager->showPlGroupSelectionForm(
            'nggAlbum',
            $albumId
        );
        
        $output .= '</th>';
        $output .= '</tr>';
        
        echo $output;
    }
    
    /**
     * Saves the user groups for the album.
     * 
     * @param integer $albumId The id of the album.
     * 
     * @return null
     */
    public function updateAlbum($albumId)
    {
        global $userAccessManager;
        
        $userAccessManager->savePlObjectData(
            'nggAlbum', 
            $albumId
        );
    }
    
    /**
     * Shows the user group selection form at the gallery settings page.
     * 
     * @param integer $galleryId The id of the gallery.
     * 
     * @return null
     */
    public function showGalleryEditForm($galleryId)
    {
        global $userAccessManager;
        
        $output = '<tr>';
        $output .= '<th class="left">';
        $output .= 'User Groups';
        $output .= '</th>';
        $output .= '<th class="left">';
        
        $output .= $userAccessManager->showPlGroupSelectionForm(
            'nggGallery', 
            $galleryId
        );
        
        $output .= '</th>';
        $output .= '</tr>';
        
        echo $output;
    }
    
    /**
     * Saves the user groups for the gallery.
     * 
     * @param integer $galleryId The id of the gallery.
     * 
     * @return null
     */
    public function updateGallery($galleryId)
    {
        global $userAccessManager;
        
        $userAccessManager->savePlObjectData(
            'nggGallery', 
            $galleryId
        );
        
        if (isset($_POST['nggImage'])) {
            $nggImages = $_POST['nggImage'];
            
            foreach ($nggImages as $nggImageId => $nggImageGroups) {
                print_r($nggImageGroups);
                
                $userAccessManager->savePlObjectData(
                    'nggImage', 
                    $nggImageId,
                    $nggImageGroups
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
            $curAdminPage = $_GET['page'];
        }
        
        if ($curAdminPage == 'uam_ngg_settings') {
            include UAM_NGG_REALPATH."tpl/adminSettings.php";
        }
    }
    
    
    /*
     * Image path functions.
     */
    
    /**
     * Manipulates the image url.
     * 
     * @param object $image The image object.
     * 
     * @return null
     */
    public function loadImage($image)
    {
        global $userAccessManager;
        
        $uamOptions = $userAccessManager->getAdminOptions();
        $suffix = 'uamfiletype=nggImage';
        
        if (!$userAccessManager->isPermalinksActive()
            && $uamOptions['lock_file'] == 'true'
        ) {
            $prefix = home_url('/').'?uamgetfile=';

            $image->imageURL = $prefix.$image->imageURL.'&'.$suffix;
            $image->thumbURL = $prefix.$image->thumbURL.'&'.$suffix;
        } else {
            $image->imageURL = $image->imageURL.'?'.$suffix;
            $image->thumbURL = $image->thumbURL.'?'.$suffix;
        }
    }
    
    /**
     * Returns the image file object.
     * 
     * @param string $fileUrl The url of the image.
     * 
     * @return object
     */
    public function getNggImageFileObject($fileUrl)
    {
        $image = $this->_getImageFromUrl($fileUrl);
        $object->id = $image->pid;
        $object->isImage = true;
        $object->type = 'nggImage';
        
        if ($image->isThumb) {
            $object->file = $image->thumbPath;
        } else {
            $object->file = $image->imagePath;
        }
        
        return $object;
    }
    
    /**
     * Returns the id of the image by the given url.
     * 
     * @param string $url The url of the image.
     * 
     * @return integer
     */
    private function _getImageFromUrl($url)
    {
        global $ngg;
        $url = $ngg->options['gallerypath'].$url;
        $url = str_replace(site_url().'/', '', $url);        
        $thumbsStr = '/thumbs/thumbs_';
        $thumb = false;
        
        echo $url."<br>";
        
        if (strpos($url, $thumbsStr)) {
            $expUrl = explode($thumbsStr, $url);
            $fileName = $expUrl[count($expUrl)-1];
            $galleryPath = $expUrl[0];
            $thumb = true;
        } else {
            $expUrl = explode('/', $url);
            $fileName = $expUrl[count($expUrl)-1];
            unset($expUrl[count($expUrl)-1]);
            $galleryPath = implode('/', $expUrl);
        }
        
        global $wpdb;
        
        $galleryId = $wpdb->get_var(
            "SELECT gid
            FROM $wpdb->nggallery
            WHERE path = '".$galleryPath."'"
        );
        
        $imageId = $wpdb->get_var(
            "SELECT pid
            FROM $wpdb->nggpictures
            WHERE galleryid = ".$galleryId."
            AND filename = '".$fileName."'"
        );
        
        global $nggdb;
        $image = $nggdb->find_image($imageId);
        
        $image->id = $id;
        $image->isThumb = $thumb;

        return $image;
    }
    
    
    /*
     * Pluggable functions.
     */
    
    /**
     * Returns the full album by the given id.
     * 
     * @param integer $albumId   The id of the album.
     * @param object  $userGroup The current user group.
     * 
     * @return object
     */
    public function getNggAlbumFull($albumId, $userGroup)
    {
        return array();
    }
    
    /**
     * Returns all albums which are assigned to the usergroup.
     * 
     * @param array  $realAlbums The albums which are assigned directly.
     * @param object $userGroup  The current user group.
     * 
     * @return array
     */
    public function getNggAlbumFullObjects($realAlbums, $userGroup)
    {
        return $realAlbums;
    }
    
    /**
     * Returns the full gallery by the given id.
     * 
     * @param integer $galleryId The id of the gallery.
     * @param object  $userGroup The current user group.
     * 
     * @return object
     */
    public function getNggGalleryFull($galleryId, $userGroup)
    {
        global $nggdb;
        $gallery = $nggdb->find_gallery($galleryId);
        $albums = $nggdb->find_all_album();

        $isRecursiveMember = array();
        
        foreach ($albums as $album) {
            if (in_array($galleryId, unserialize($album->sortorder))
                && $userGroup->objectIsMember('nggAlbum', $album->id)            
            ) {
                $albumObject->name = $album->name;
                $isRecursiveMember['nggAlbum'][] = $albumObject;
            }
        }
        
        return $isRecursiveMember;
    }
    
    /**
     * Returns all galleries which are assigned to the usergroup.
     * 
     * @param array  $realGalleries The galleries which are assigned directly.
     * @param object $userGroup     The current user group.
     * 
     * @return array
     */
    public function getNggGalleryFullObjects($realGalleries, $userGroup)
    {
        //TODO
        return $realGalleries;
    }
    
    /**
     * Returns the full image by the given id.
     * 
     * @param integer $imageId   The id of the gallery.
     * @param object  $userGroup The user group.
     * 
     * @return object
     */
    public function getNggImageFull($imageId, $userGroup)
    {
        global $nggdb;
        $image = $nggdb->find_image($imageId);

        $isRecursiveMember = array();

        if ($userGroup->objectIsMember('nggGallery', $image->galleryid)) {
            $gallery = $nggdb->find_gallery($image->galleryid);
            
            $galleryObject->name = $gallery->name;
            
            $galleryIsRecursiveMember 
                = $this->getNggGalleryFull($image->galleryid, $userGroup);

            if ($galleryIsRecursiveMember !== array()) {
                $galleryObject->recursiveMember = $galleryIsRecursiveMember;
            }
            
            $isRecursiveMember['nggGallery'][] = $galleryObject;
        }
        
        return $isRecursiveMember;
    }
    
    /**
     * Returns all images which are assigned to the usergroup.
     * 
     * @param array  $realImages The galleries which are assigned directly.
     * @param object $userGroup  The current user group.
     * 
     * @return array
     */
    public function getNggImageFullObjects($realImages, $userGroup)
    {
        //TODO
        return $realImages;
    }
    
    
    /*
     * Output functions.
     */
    
    /**
     * Manupulates the output of a gallery.
     * 
     * @param string  $output   The output.
     * @param integer $gallerId The gallery id.
     * 
     * @return string
     */
    public function showGalleryContent($output, $gallerId)
    {
        global $userAccessManager;
        
        $uamAccessHandler = $userAccessManager->getAccessHandler();
        $options = $this->getAdminOptions();
        
        if (!$uamAccessHandler->checkObjectAccess('nggGallery', $gallerId)) {
            $output = $options['gallery_content'];
        }
        
        return $output;
    }
    
    /**
     * Filters the images.
     * 
     * @param array $images The images of the gallery.
     * 
     * @return array
     */
    public function showGalleryImages($images)
    {
        $options = $this->getAdminOptions();
        
        if ($options['hide_image'] == 'true') {
            global $userAccessManager;
            
            $uamAccessHandler = $userAccessManager->getAccessHandler();
            $options = $this->getAdminOptions();
            $filterdImages = array();
            
            foreach ($images as $key => $image) {
                if ($uamAccessHandler->checkObjectAccess('nggImage', $image->galleryid)) {
                    $filterdImages[$key] = $image;
                }
            }
    
            return $filterdImages;
        }
        
        return $images;
    }
    
    /**
     * Manipulates the gallery for a album.
     * 
     * @param object $gallery The gallery.
     * 
     * @return object
     */
    public function showGalleryObjectForAlbum($gallery)
    {
        global $userAccessManager;

        //Manipulate gallery title
        $uamAccessHandler = $userAccessManager->getAccessHandler();
        $options = $this->getAdminOptions();
        
        if ($options['hide_gallery_title'] == 'true'
            && !$uamAccessHandler->checkObjectAccess('nggGallery', $gallery->gid)
        ) {
            $gallery->title = $options['gallery_title'];
        }
        
        //Manipulate preview image
        $uamOptions = $userAccessManager->getAdminOptions();
        $suffix = 'uamfiletype=nggImage';
        
        if (!$userAccessManager->isPermalinksActive()
            && $uamOptions['lock_file'] == 'true'
        ) {
            $prefix = home_url('/').'?uamgetfile=';

            $gallery->previewurl = $prefix.$gallery->previewurl.'&'.$suffix;
        } else {
            $gallery->previewurl = $gallery->previewurl.'?'.$suffix;
        }
        
        return $gallery;
    }
    
    /**
     * Filters the galleries.
     * 
     * @param array $galleries The galleries of the album.
     * 
     * @return array
     */
    public function showGalleriesForAlbum($galleries)
    {
        $options = $this->getAdminOptions();
        
        if ($options['hide_gallery'] == 'true') {
            global $userAccessManager;
            
            $uamAccessHandler = $userAccessManager->getAccessHandler();
            $options = $this->getAdminOptions();
            $filteredGalleries = array();
            
            foreach ($galleries as $gallerId => $gallery) {
                if ($uamAccessHandler->checkObjectAccess('nggGallery', $gallerId)) {
                    $filteredGalleries[$gallerId] = $gallery;
                }
            }
    
            return $filteredGalleries;
        }
        
        return $galleries;
    }

    
    /**
     * Manupulates the output of a album.
     * 
     * @param string  $output  The output.
     * @param integer $albumId The album id.
     * 
     * @return string
     */
    public function showAlbumContent($output, $albumId)
    {
        global $userAccessManager;
        
        $uamAccessHandler = $userAccessManager->getAccessHandler();
        $options = $this->getAdminOptions();
        
        if (!$uamAccessHandler->checkObjectAccess('nggAlbum', $albumId)) {
            $output = $options['album_content'];
        }
        
        return $output;
    }
}