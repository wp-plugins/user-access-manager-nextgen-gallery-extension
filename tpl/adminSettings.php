<?php
/**
 * adminSettings.php
 * 
 * Shows the setting page at the admin panel.
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
global $uamNgg;
$uamNggOptions = $uamNgg->getAdminOptions();

if (isset($_POST['update_uamngg_settings'])) {    
    foreach ($uamNggOptions as $option => $value) {
        if (isset($_POST['uamngg_' . $option])) {
            $uamNggOptions[$option] = $_POST['uamngg_' . $option];
        }
    }
    
    update_option($this->adminOptionsName, $uamNggOptions);
    ?>
    <div class="updated">
    	<p><strong><?php echo TXT_UAMNGG_UPDATE_SETTINGS; ?></strong></p>
    </div>
    <?php
}
?>

<div class="wrap">
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <h2><?php echo TXT_UAMNGG_SETTINGS; ?></h2>
        <h3><?php echo TXT_UAMNGG_ALBUM_SETTING; ?></h3>
        <p><?php echo TXT_UAMNGG_ALBUM_SETTING_DESC; ?></p>
        <table class="form-table">
        	<tbody>
        		<tr valign="top">
        			<th scope="row"><?php echo TXT_UAMNGG_HIDE_ALBUM; ?></th>
        			<td>
        				<label for="uamngg_hide_album_yes">
        					<input type="radio" id="uamngg_hide_album_yes" class="uamngg_hide_post" name="uamngg_hide_album" value="true" <?php 
if ($uamNggOptions['hide_album'] == "true") { 
    echo 'checked="checked"';
} 
                            ?> />
        				    <?php echo TXT_UAMNGG_YES; ?> 
        				</label>&nbsp;&nbsp;&nbsp;&nbsp; 
        				<label for="uamngg_hide_album_no">
        					<input type="radio" id="uamngg_hide_album_no" class="uamngg_hide_post" name="uamngg_hide_album" value="false" <?php
if ($uamNggOptions['hide_album'] == "false") {
    echo 'checked="checked"';
} 
                            ?> />
        				    <?php echo TXT_UAMNGG_NO; ?>
        				</label> <br />
        				<?php echo TXT_UAMNGG_HIDE_ALBUM_DESC; ?>
        			</td>
        		</tr>
				<tr>
        			<th><?php echo TXT_UAMNGG_ALBUM_CONTENT; ?></th>
        			<td>
        				<textarea name="uamngg_album_content" style="width: 80%; height: 100px;" cols="40" rows="10" ><?php 
echo apply_filters('format_to_edit', $uamNggOptions['album_content']); 
                        ?></textarea>
        				<br />
        			    <?php echo TXT_UAMNGG_ALBUM_CONTENT_DESC; ?>
        			</td>
        		</tr>
        	</tbody>
        </table>
        <h3><?php echo TXT_UAMNGG_GALLERY_SETTING; ?></h3>
        <p><?php echo TXT_UAMNGG_GALLERY_SETTING_DESC; ?></p>
        <table class="form-table">
        	<tbody>
        		<tr valign="top">
        			<th scope="row"><?php echo TXT_UAMNGG_HIDE_GALLERY; ?></th>
        			<td>
        				<label for="uamngg_hide_gallery_yes">
        					<input type="radio" id="uamngg_hide_gallery_yes" class="uamngg_hide_gallery" name="uamngg_hide_gallery" value="true" <?php 
if ($uamNggOptions['hide_gallery'] == "true") { 
    echo 'checked="checked"';
} 
                            ?> />
        				    <?php echo TXT_UAMNGG_YES; ?> 
        				</label>&nbsp;&nbsp;&nbsp;&nbsp; 
        				<label for="uamngg_hide_gallery_no">
        					<input type="radio" id="uamngg_hide_gallery_no" class="uamngg_hide_gallery" name="uamngg_hide_gallery" value="false" <?php
if ($uamNggOptions['hide_gallery'] == "false") {
    echo 'checked="checked"';
} 
                            ?> />
        				    <?php echo TXT_UAMNGG_NO; ?>
        				</label> <br />
        				<?php echo TXT_UAMNGG_HIDE_GALLERY_DESC; ?>
        			</td>
        		</tr>
        		<tr valign="top">
        			<th scope="row"><?php echo TXT_UAMNGG_HIDE_GALLERY_TITLE; ?></th>
        			<td>
        				<label for="uamngg_hide_gallery_title_yes">
        					<input type="radio" id="uamngg_hide_gallery_yes" class="uamngg_hide_gallery_title" name="uamngg_hide_gallery_title" value="true" <?php 
if ($uamNggOptions['hide_gallery_title'] == "true") { 
    echo 'checked="checked"';
} 
                            ?> />
        				    <?php echo TXT_UAMNGG_YES; ?> 
        				</label>&nbsp;&nbsp;&nbsp;&nbsp; 
        				<label for="uamngg_hide_gallery_title_no">
        					<input type="radio" id="uamngg_hide_gallery_no" class="uamngg_hide_gallery_title" name="uamngg_hide_gallery_title" value="false" <?php
if ($uamNggOptions['hide_gallery_title'] == "false") {
    echo 'checked="checked"';
} 
                            ?> />
        				    <?php echo TXT_UAMNGG_NO; ?>
        				</label> <br />
        				<?php echo TXT_UAMNGG_HIDE_GALLERY_TITLE_DESC; ?>
        			</td>
        		</tr>
        		<tr valign="top">
            		<th scope="row"><?php echo TXT_UAMNGG_GALLERY_TITLE; ?></th>
            		<td>
            			<input name="uamngg_gallery_title" value="<?php echo $uamNggOptions['gallery_title']; ?>" /> <br />
            			<?php echo TXT_UAMNGG_GALLERY_TITLE_DESC; ?>
            		</td>
            	</tr>
				<tr>
        			<th><?php echo TXT_UAMNGG_GALLERY_CONTENT; ?></th>
        			<td>
        				<textarea name="uamngg_gallery_content" style="width: 80%; height: 100px;" cols="40" rows="10" ><?php 
echo apply_filters('format_to_edit', $uamNggOptions['gallery_content']); 
                        ?></textarea>
        				<br />
        			    <?php echo TXT_UAMNGG_GALLERY_CONTENT_DESC; ?>
        			</td>
        		</tr>
        	</tbody>	
        </table>
        <h3><?php echo TXT_UAMNGG_IMAGE_SETTING; ?></h3>
        <p><?php echo TXT_UAMNGG_IMAGE_SETTING_DESC; ?></p>
        <table class="form-table">
        	<tbody>
        		<tr valign="top">
        			<th scope="row"><?php echo TXT_UAMNGG_HIDE_IMAGE; ?></th>
        			<td>
        				<label for="uamngg_hide_image_yes">
        					<input type="radio" id="uamngg_hide_image_yes" class="uamngg_hide_image" name="uamngg_hide_image" value="true" <?php 
if ($uamNggOptions['hide_image'] == "true") { 
    echo 'checked="checked"';
} 
                            ?> />
        				    <?php echo TXT_UAMNGG_YES; ?> 
        				</label>&nbsp;&nbsp;&nbsp;&nbsp; 
        				<label for="uamngg_hide_image_no">
        					<input type="radio" id="uamngg_hide_image_no" class="uamngg_hide_image" name="uamngg_hide_image" value="false" <?php
if ($uamNggOptions['hide_image'] == "false") {
    echo 'checked="checked"';
} 
                            ?> />
        				    <?php echo TXT_UAMNGG_NO; ?>
        				</label> <br />
        				<?php echo TXT_UAMNGG_HIDE_IMAGE_DESC; ?>
        			</td>
        		</tr>
        	</tbody>
        </table>
        <div class="submit">
        	<input type="submit" name="update_uamngg_settings" value="<?php echo TXT_UAMNGG_NGG_UPDATE_SETTING; ?>" />
        </div>
	</form>
</div>