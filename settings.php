<?php 
	if($_POST['facebook_albums_sync'] == 'Y') {
		//Form data sent
		$fbas_page =trim ($_POST['fbas_page']);
		update_option('fbas_page', $fbas_page);
		?>
		<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
		<?php
	} else {
	//Display a normal page
	$fbas_page = get_option('fbas_page');
	}
?>

<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="facebook_albums_sync" value="Y">
<?php    echo "<h4>" . __( 'Facebook Page Settings', 'facebook_albums_sync' ) . "</h4>"; ?>
<p><?php _e("facebook.com/ " ); ?><input type="text" name="fbas_page" value="<?php echo $fbas_page; ?>" size="20"><?php _e(" <= you page name in her, no need for the full URL" ); ?></p>

<?php if(!trim(get_option('fbas_page'))=="" ){ ?>
<br/>
<br/>
<br/>
<p><strong>Add this code to the page where you want your albums to be displayed:</strong>
<br /> 
[fbalbumsync] 

</p>
<?php } ?>

<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Update Options', 'oscimp_trdom' ) ?>" />
</p>
</form>