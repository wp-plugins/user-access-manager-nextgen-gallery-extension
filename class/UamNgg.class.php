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
    protected $userAccessManager;
    
    /**
     * The constructor.
     * 
     * @param object &$userAccessManager The user access manager class.
     * 
     * @return null
     */
    function __construct(&$userAccessManager)
    {
        $this->userAccessManager = $userAccessManager;
        
        //Instead of a activation hook we need this.
        $dir = $this->_getGalleryDir();
        
        if (!file_exists($dir.".htaccess")
            || !file_exists($dir.".htpasswd")
        ) {
            $this->activate();
        }
    }
    
    /**
     * Returns the user access manager object.
     * 
     * @return object
     */
    function &getUserAccessManager()
    {
        return $this->userAccessManager;
    }
    
    /**
     * The activation function.
     * 
     * @return null
     */
    public function activate()
    {
        $uamOptions = $this->getUserAccessManager()->getAdminOptions();
        
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
        $dir = $this->_getGalleryDir();
        
        $this->getUserAccessManager()->createHtaccess($dir, 'nggImage');
        
        if ($withPasswd) {
            $this->getUserAccessManager()->createHtpasswd(true, $dir);
        }
    }
    
    /**
     * Remove the htaccess files.
     * 
     * @return null
     */
    private function _removeHtaccessFiles()
    {
        $dir = $this->_getGalleryDir();
        
        $this->getUserAccessManager()->deleteHtaccessFiles($dir);
    }
    
    /*
     * Admin output functions
     */
    
    /**
     * Add additional content to the album box.
     * 
     * @param integer $albumId The album id.
     * 
     * @return null
     */
    public function showAlbumItemContent($albumId)
    {
        $content = $this->getUserAccessManager()->getPlColumn(
            $albumId, 
            'nggAlbum'
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
            echo $this->getUserAccessManager()->getPlColumn(
                $gallerId, 
                'nggGallery'
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
            echo $this->getUserAccessManager()->getPlColumn(
                $imageId, 
                'nggImage'
            );
        }
    }
    
    
    /*
     * Output functions
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
        $uamOptions = $this->getUserAccessManager()->getAdminOptions();
        $suffix = 'uamfiletype=nggImage';
        
        if (!$this->getUserAccessManager()->isPermalinksActive()
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
        $url = str_replace(site_url().'/', '', $url);        
        $thumbsStr = '/thumbs/thumbs_';
        $thumb = false;
        
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
    
    /**
     * Shows the user group selection form at the album settings page.
     * 
     * @param integer $albumId The id of the album.
     * 
     * @return null
     */
    public function showAlbumEditForm($albumId)
    {
        $output = '<tr>';
        $output .= '<th>';
        $output .= 'User Groups<br/>';
        
        $output .= $this->getUserAccessManager()->showPlGroupSelectionForm(
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
        $this->getUserAccessManager()->savePlObjectData(
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
        $output = '<tr>';
        $output .= '<th class="left">';
        $output .= 'User Groups';
        $output .= '</th>';
        $output .= '<th class="left">';
        
        $output .= $this->getUserAccessManager()->showPlGroupSelectionForm(
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
        $this->getUserAccessManager()->savePlObjectData(
            'nggGallery', 
            $galleryId
        );
    }
    
    public function showSlideShow($out, $object)
    {
        echo "showSlideShow - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
    
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
        $uamAccessHandler = $this->getUserAccessManager()->getAccessHandler();
        
        if (!$uamAccessHandler->checkObjectAccess('nggGallery', $gallerId)) {
            //TODO
            $output = "No access";
        }
        
        return $output;
    }
    
    public function showGalleryOutput($out, $images)
    {
        /*echo "showGalleryOutput - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";*/
        
        /*$fiterdImages = array();
        
        foreach ($images as $key => $image) {
            if () {
                $fiterdImages[$key] = $image;
            }
        }
        
        return $fiterdImages;*/
        
        return $out;
    }
    
    public function showGalleryObject($out, $object)
    {
        echo "showGalleryObject - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
    
    public function showGalleries($out, $object)
    {
        echo "showGalleries - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
    
    public function showGalleryRelatedContent($out, $object)
    {
        echo "showGalleryRelatedContent - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
    
    public function showGalleryTagsContent($out, $object)
    {
        echo "showGalleryTagsContent - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
    
    public function showAlbumContent($out, $object)
    {
        echo "showAlbumContent - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
    
    public function showAlbumTagsContent($out, $object)
    {
        echo "showAlbumTagsContent - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
    
    public function showImageContent($out, $object)
    {
        echo "showImageContent - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
    
    public function showImageBrowserContent($out, $object)
    {
        echo "showImageBrowserContent - ";
        echo "out: ";
        print_r($out);
        echo "<br>object: ";
        print_r($object);
        echo "<br>";
        
        return $out;
    }
}