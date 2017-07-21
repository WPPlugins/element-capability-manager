<?php 

	if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == true) {

		global $wp_roles;
		
		$editable_roles = get_editable_roles();

		$options 	= get_option('ecm_settings');

		$post_types = get_post_types('','objects');

		foreach( $post_types as $post_type => $post_type_args) {

			foreach ( $editable_roles as $role => $details ) {

				$role_temp = get_role( $role );

				if( isset($options['post-types'][$post_type][$role]) && $options['post-types'][$post_type][$role] == true) {


					$role_temp->add_cap( 'ecm_manage_locked_'.$post_type );

					$role_temp->add_cap( 'ecm_read_locked_'.$post_type );

					$role_temp->add_cap( 'ecm_modify_locked_'.$post_type );


				} else {


					$role_temp->remove_cap( 'ecm_manage_locked_'.$post_type );

					$role_temp->remove_cap( 'ecm_read_locked_'.$post_type );

					$role_temp->remove_cap( 'ecm_modify_locked_'.$post_type );


				}
				

			}

		}

	}

?>

<div class="wrap">

    <div id="icon-ecm" class="icon32" ><br /></div>

    <p><?php printf(__('Here are the user roles that %1$s have the capabilities to edit each post type.'), sprintf('<strong>%1$s</strong>' , __('already','ecm')) ) ?></p>
	<p><?php _e( 'If you want to add other roles you must change its permissions (you can use a plugin such as Members or you can edit the capabilities in the functions.php file of your theme).','ecm') ?></p>

	<form id="ecm_settings_form" action="options.php"  method="post">

		<?php 

			settings_fields( 'ecm_settings' ); 

			do_settings_sections( EC_PLUGIN_FOLDER.'/admin/ecm-settings.php' );

			submit_button(); 

		?>

	</form>

</div>