<div class="wrap">
	<h2>İncil Ayet Linkleri</h2>
	<form method="post" action="options.php">
		<?php settings_fields('incilinfo_secenekler'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Varsayılan seçenekler</th>
				<td>
					<label for="incilinfo_aracipucu"><input type="checkbox" name="incilinfo_aracipucu" id="incilinfo_aracipucu" value="1" <?php echo get_option('incilinfo_aracipucu') ? 'checked="checked"' : ''; ?> /> Araçipucu olarak gösterilsin</label><br />
					<label for="incilinfo_yenipencere"><input type="checkbox" name="incilinfo_yenipencere" id="incilinfo_yenipencere" value="1" <?php echo get_option('incilinfo_yenipencere') ? 'checked="checked"' : ''; ?> /> Linkler yeni pencerede açılsın</label>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>
