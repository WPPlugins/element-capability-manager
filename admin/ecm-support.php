<div class="wrap">

    <div id="icon-ecm" class="icon32" ><br /></div>

    <h2><?php _e('Element Capability Manager Support', 'ecm') ?></h2>
    <p class="ecm-highlighted">
		<?php printf(
			__('Technical support is available via support tab on %1$s or %2$s.','ecm'),
			'<a href="http://wordpress.org/plugins/element-capability-manager/">http://wordpress.org/plugins/element-capability-manager/</a>',
			'<a href="mailto:ecm@synapse-lab.it">ecm@synapse-lab.it</a>'		
		) ?>
    </p>
    <div class="ecm-support-sector">
        <h3><?php _e('Debug','ecm') ?></h3>
        <h4><?php _e('User roles and capabilities in Wordpress option','ecm') ?></h4>
        <?php 
        
        $options_user_roles = get_option($wpdb->base_prefix.'user_roles','not in database'); 
        
        if($options_user_roles != 'not in database'){ 
        
            ?>
        
                <pre><?php print_r($options_user_roles); ?></pre>
           
            <?php 
        
        }else{
        
            ?>
           
                <pre><?php printf(__('%1$s option isn\'t in the database','ecm'), 'user_roles') ?></pre>
            
            <?php
        
        }
        
        ?>
    </div>

</div>