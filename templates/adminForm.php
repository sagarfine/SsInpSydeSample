<?php
//This is the form template
declare(strict_types=1);
$ffApiNounce=wp_create_nonce('ffApiNounce');
$ssApiEndPoint=$this->ssApiEndPoint;
$ssCustomSlug=$this->ssCustomSlug;
$ssCacheExpiry=$this->ssCacheExpiry;
$ssSuccess=get_transient('ssSuccess');
$ssError=get_transient('ssError');
$cssClass='notice notice-[ssReplace] is-dismissible';
$message='';
if (!empty($ssSuccess) && $ssSuccess!=='') {
    $cssClass=str_replace("[ssReplace]", 'success', $cssClass);
    $message=$ssSuccess;
    delete_transient('ssSuccess');
} elseif (!empty($ssError) && $ssError!=='') {
    $cssClass=str_replace("[ssReplace]", 'warning', $cssClass);
    $message=$ssError;
    delete_transient('ssError');
}
$arrMessage=explode('[ss]', $message);
?>
<div class="ssFormWrapper">
    <?php
    if ($message!=='') {
        ?>
            <div class="<?php echo esc_html($cssClass);?>">
                <?php foreach ($arrMessage as $msg) {?>
                    <p><?php echo esc_html($msg);?></p>
                <?php }?>
            </div>
        <?php
    }
    ?>
    <form method="post" name="ssFrmAPIConfig">
        <input type="hidden" name="ffApiNounce" value="<?php echo esc_html($ffApiNounce); ?>" />
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th>
                        <label for="ssApiEndPoint">
                            <?php echo esc_html__('API Endpoint (Users)', 'ssinpsyde');?>
                        </label>
                    </th>
                    <td>
                        <input name="ssApiEndPoint" id="ssApiEndPoint" type="text"
                               value="<?php echo esc_html($ssApiEndPoint);?>" class="regular-text code">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="ssCustomSlug">
                            <?php echo esc_html__('Custom Slug', 'ssinpsyde');?>
                        </label>
                    </th>
                    <td>
                        <input name="ssCustomSlug" id="ssCustomSlug" type="text"
                               value="<?php echo esc_html($ssCustomSlug);?>" class="regular-text code">
                        <?php if ($ssCustomSlug!=='') { ?>
                    <p class="info">
                        <a href="<?php echo esc_html(site_url('/').$ssCustomSlug); ?>" target="_blank">
                            <?php echo esc_html__('Visit Page', 'ssinpsyde');?>
                        </a>
                    </p>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="ssCacheExpiry">
                            <?php echo esc_html__('Cache Expiry (Seconds)', 'ssinpsyde');?>
                        </label>
                    </th>
                    <td>
                        <input name="ssCacheExpiry" id="ssCacheExpiry" type="number"
                               value="<?php echo esc_html($ssCacheExpiry);?>" class="regular-text code">
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="ssSubmit" id="ssSubmit" class="button button-primary"
                   value="<?php echo esc_html(__('Save Changes', 'ssinpsyde')); ?>">
        </p>
    </form>
</div>