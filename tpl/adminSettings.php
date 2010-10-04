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
global $userAccessManager;
$uamOptions = $userAccessManager->getAdminOptions();

if (isset($_POST['update_uam_settings'])) {    
    foreach ($uamOptions as $option => $value) {
        if (isset($_POST['uam_' . $option])) {
            $uamOptions[$option] = $_POST['uam_' . $option];
        }
    }
    
    update_option($this->adminOptionsName, $uamOptions);
    
    if ($_POST['uam_lock_file'] == 'false') {
        $userAccessManager->deleteHtaccessFiles();
    } else {
        $userAccessManager->createHtaccess();
        $userAccessManager->createHtpasswd(true);
    }
    
    do_action('uam_update_options', $uamOptions);
    ?>
    <div class="updated">
    	<p><strong><?php echo TXT_UPDATE_SETTINGS; ?></strong></p>
    </div>
    <?php
}
?>

<div class="wrap">
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <h2><?php echo TXT_SETTINGS; ?></h2>
        <h3><?php echo TXT_POST_SETTING; ?></h3>
        <p><?php echo TXT_POST_SETTING_DESC; ?></p>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><?php echo TXT_HIDE_POST; ?></th>
			<td>
				<label for="uam_hide_post_yes">
					<input type="radio" id="uam_hide_post_yes" class="uam_hide_post" name="uam_hide_post" value="true" <?php 
if ($uamOptions['hide_post'] == "true") { 
    echo 'checked="checked"';
} 
                    ?> />
				    <?php echo TXT_YES; ?> 
				</label>&nbsp;&nbsp;&nbsp;&nbsp; 
				<label for="uam_hide_post_no">
					<input type="radio" id="uam_hide_post_no" class="uam_hide_post" name="uam_hide_post" value="false" <?php
if ($uamOptions['hide_post'] == "false") {
    echo 'checked="checked"';
} 
                    ?> />
				    <?php echo TXT_NO; ?>
				</label> <br />
				<?php echo TXT_HIDE_POST_DESC; ?>
			</td>
		</tr>
	</tbody>
</table>
<div class="submit">
	<input type="submit" name="update_uam_ngg_settings" value="<?php echo TXT_UPDATE_SETTING; ?>" />
</div>
</form>
</div>