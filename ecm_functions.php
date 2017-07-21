<?php

	

//Create admin page and subpage

function ecm_admin_menu() {

	add_menu_page(EC_PLUGIN_NAME, EC_PLUGIN_NAME, 'manage_options', EC_PLUGIN_FOLDER.'/admin/ecm-settings.php', '', EC_PLUGIN_URL.'/images/icon_16x16.png');
	add_submenu_page(EC_PLUGIN_FOLDER.'/admin/ecm-settings.php', __('Support','ecm'), __('Support','ecm'), 'manage_options', EC_PLUGIN_FOLDER.'/admin/ecm-support.php');

	add_action('admin_enqueue_scripts','ecm_enqueue');

}





function ecm_enqueue(){

	wp_register_style(  'ecm_style', EC_PLUGIN_URL.'/css/ecm_style.css', false, '1.0.0' );
	wp_enqueue_style( 'ecm_style' );

}



//Adding default option settings

function ecm_init_fn() {

	

	$current_options = get_option('ecm_settings');

	

	if($current_options == false) {

	

		global $wp_roles;

			

		$admin = get_role( 'administrator' );

	

		$post_types = get_post_types('','objects');



		$post_types_default = array();

		

		foreach( $post_types as $post_type => $post_type_args) {

			

			// remove all previous ec user capabilities

			$editable_roles = get_editable_roles();

			

			foreach ( $editable_roles as $role => $details ) {

		

				$wp_roles->remove_cap( $role, 'ecm_manage_locked_'.$post_type );

				$wp_roles->remove_cap( $role, 'ecm_read_locked_'.$post_type );

				$wp_roles->remove_cap( $role, 'ecm_modify_locked_'.$post_type );

		

			}

		

			// set option for administrator (this role can always manage ec settings and all the elements)

			$post_types_default[$post_type] = array(

			

				'administrator' => 'true'

				

			);

			

			// add capabilities to administrator

			$admin->add_cap( 'ecm_manage_locked_'.$post_type );

			$admin->add_cap( 'ecm_read_locked_'.$post_type );

			$admin->add_cap( 'ecm_modify_locked_'.$post_type );

			

		}

	

		$arr = array( 

			'post-types' 	=> $post_types_default,

			'activated'		=> true

		);

	

		update_option('ecm_settings', $arr);

		

	}



}





//Register settings 

function ecm_register_settings() {



	register_setting( 'ecm_settings', 'ecm_settings' );

	

	//Create section and fields for each post types

	$post_types = get_post_types('','objects');

	foreach( $post_types as $post_type => $post_type_args) {



		add_settings_section('ecm_settings_section_'.$post_type, __($post_type_args->labels->name,'ecm'), '' , EC_PLUGIN_FOLDER.'/admin/ecm-settings.php');

		add_settings_field('ecm_settings_field_'.$post_type, __('Select the roles allowed to change this post type, even if it is locked','ecm'), 'ecm_settings_field_fn', EC_PLUGIN_FOLDER.'/admin/ecm-settings.php','ecm_settings_section_'.$post_type, array('ecm_post_type' => $post_type));

	

	}



}







//Input output

function ecm_settings_field_fn($args) {

	

	$post_types = 'post-types';

	$post_type 	= $args['ecm_post_type'];

	$options 	= get_option('ecm_settings');  



	$editable_roles = get_editable_roles();



	foreach ( $editable_roles as $role => $details ) {

		$role_name = translate_user_role($details['name'] );



		$checked = "";

		if ( $role == 'administrator') {

			?>

				<div class="field checkbox">

					<input type="hidden" name="ecm_settings[<?php echo $post_types ?>][<?php echo $post_type ?>][<?php echo esc_attr($role) ?>]" value="true" />

					<input disabled="disabled" type="checkbox" name="ec_settings[<?php echo $post_types ?>][<?php echo $post_type ?>][<?php echo esc_attr($role) ?>]" value="true" id="ecm_role_<?php echo esc_attr($role) ?>" checked="checked" />

					<label for="ecm_role_<?php echo esc_attr($role) ?>"><?php echo $role_name ?></label>

				</div>

			<?php

		} else {



			$post_type_object = get_post_type_object( $post_type );

			$post_type_cap_edit = $post_type_object->cap->edit_posts;

			

			$wp_role_obj = get_role( $role );

			

			if($wp_role_obj->has_cap($post_type_cap_edit)){

				

				if ( isset($options[$post_types][$post_type][$role]) ) {  

					if ( $options[$post_types][$post_type][$role] == 'true') {  

						$checked = 'checked="checked"';  

					}  

				}  

				?>

					<div class="field checkbox">

						<input type="checkbox" name="ecm_settings[<?php echo $post_types ?>][<?php echo $post_type ?>][<?php echo esc_attr($role) ?>]" value="true" id="ecm_role_<?php echo $post_type ?>_<?php echo esc_attr($role) ?>" <?php echo $checked ?> />

						<label for="ecm_role_<?php echo $post_type ?>_<?php echo esc_attr($role) ?>"><?php echo $role_name ?></label>

					</div>

				<?php

				

			}

			

		}

		

	}



}







function ecm_add_box_element() {



	$current_post_type = get_post_type();



	if( current_user_can( 'ecm_manage_locked_'.$current_post_type ) ) {

	

		add_meta_box( 'ecm_meta_box_locked', 'Lock this '.$current_post_type , 'ecm_meta_box_output', $current_post_type, 'side', 'high');



	}

	

}



function ecm_meta_box_output( $object, $box ) {



	$post_type = $object->post_type;

	

	if( current_user_can( 'ecm_manage_locked_'.$post_type ) ) {

	

		$checked = "";

		

		if( get_post_meta( $object->ID, 'ecm_locked', true ) == 1 ) {

		

			$checked = 'checked = "checked"';

		

		}



		?>

			<p><?php _e('If you lock this ','ecm'); echo $post_type.', '; _e('only the enabled user roles will see it.','ecm') ?></p>

		

			<input type="checkbox" value="locked" name="ecm_locked" id="ecm_locked" <?php echo $checked ?> />

			<label for="ecm_locked"><?php _e('Lock this','ecm') ?> <?php echo $post_type ?></label>

		<?php

	

	}



}



function ecm_save_post_meta( $post_id , $post ) {

	

	if(isset($_POST['ecm_locked']) && $_POST['ecm_locked'] == 'locked') {

	

		$ecm_locked_meta = 1;

	

	} else {



		$ecm_locked_meta = "";

		

	}

	

	$meta_key	= 'ecm_locked';

	$meta_value = get_post_meta( $post_id, $meta_key, true );



	update_post_meta( $post_id, $meta_key, $ecm_locked_meta );

	

}





function ecm_filter_pages($query){

	global $pagenow, $post_type, $currentuser;

	

	if( $pagenow == 'edit.php' && !current_user_can( 'ecm_manage_locked_'.$post_type )) {

			

		$args = array(

			

			'relation' => 'OR',

			

			$args = array(

				'key' => 'ecm_locked',

				'value' => '',

				'compare' => 'LIKE'

			),

			array(

				

				'key' => 'ecm_locked',

				'compare' => 'NOT EXISTS'

			

			)

			

		);

		

		$query->set( 'meta_query', array($args) );

	

	}



}





function ecm_filter_admin_page( $allcaps, $cap, $args ) {

	global $pagenow;

	

	if ( isset( $args[2] ) && ($pagenow == 'edit.php' || $pagenow == 'post.php')) {

	

		$post = get_post( $args[2] );

			

		if ( $post instanceof WP_Post ) {

		

			$post_type = $post->post_type;

			$post_type_object = get_post_type_object($post_type);

			$post_type_capabilities = $post_type_object->cap;

			

			$meta_key	= 'ecm_locked';

			$meta_value = get_post_meta( $post->ID, $meta_key, true );

			

			foreach( $post_type_capabilities as $post_type_capability => $capability) {

				

				//for different $post_type_capabilities change the ability or not in base of settings (read, edit, delete, edit other ecc...)

				if( $meta_value != 1 || current_user_can( 'ecm_manage_locked_'.$post_type ) ) { 

				

					$allcaps[$cap[0]] = true;

				

				} else {

				

					$allcaps[$cap[0]] = false;

				

				}

				

			}



		}

	}

	

	return $allcaps;

	

}

?>