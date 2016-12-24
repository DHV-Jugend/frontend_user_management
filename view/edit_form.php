<form name="profile" id="your-profile" action="" method="post">
	<?php wp_nonce_field( 'update-profile_' . $user_id ) ?>
	<?php if ( $wp_http_referer ) : ?>
		<input type="hidden" name="wp_http_referer" value="<?php echo esc_url( $wp_http_referer ); ?>" />
	<?php endif; ?>
	<input type="hidden" name="from" value="profile" />
	<input type="hidden" name="checkuser_id" value="<?php echo $user_id; ?>" />
	<table class="wpuf-table">
		<?php do_action( 'personal_options', $profileuser ); ?>
	</table>
	<?php do_action( 'profile_personal_options', $profileuser ); ?>
	<table class="form-table">
		<tr>
			<th><label for="user_login1"><?php _e( 'Username' ); ?></label></th>
			<td>
				<input type="text" name="user_login" id="user_login1" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" class="regular-text" /><br /><em><span class="description"><?php _e( 'Usernames cannot be changed.' ); ?></span></em>
			</td>
		</tr>
		<tr>
			<th><label for="first_name"><?php _e( 'First Name' ) ?></label></th>
			<td>
				<input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $profileuser->first_name ) ?>" class="regular-text" />
			</td>
		</tr>

		<tr>
			<th><label for="last_name"><?php _e( 'Last Name' ) ?></label></th>
			<td>
				<input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $profileuser->last_name ) ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th><label for="email"><?php _e( 'E-mail' ); ?>
					<span class="description"><?php _e( '(required)' ); ?></span></label></th>
			<td>
				<input type="text" name="email" id="email" value="<?php echo esc_attr( $profileuser->user_email ) ?>" class="regular-text" />
			</td>
		</tr>
		<?php
		foreach ( wp_get_user_contact_methods() as $name => $desc ) {
			?>
			<tr>
				<th>
					<label for="<?php echo $name; ?>"><?php echo apply_filters( 'user_' . $name . '_label', $desc ); ?></label>
				</th>
				<td>
					<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $profileuser->$name ) ?>" class="regular-text" />
				</td>
			</tr>
		<?php
		}
		?>
	</table>
	<p class="submit">
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $user_id ); ?>" />
		<input type="submit" value="<?php _e( 'Update Profile', 'wpuf' ); ?>" name="submit" />
	</p>
</form>