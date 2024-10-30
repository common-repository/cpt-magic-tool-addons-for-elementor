<?php
/**
 * Get All Pods CPTs
 */
function get_pods_cpts(){
	global $wpdb;
    $pods_cpts = $wpdb->get_results( "SELECT ID,post_title FROM {$wpdb->prefix}posts where post_type='_pods_pod'", ARRAY_A );
	return $pods_cpts;
}

/**
 * Get All Pods Fields for Specific CPTs
 */
function get_pods_fields($cpt){
	global $wpdb;
	$fields = array();
	$pods_fields = $wpdb->get_results( "SELECT ID,post_title,post_name FROM {$wpdb->prefix}posts where post_type='_pods_field' && post_parent=".$cpt, ARRAY_A ); 
	$cnt=0;
	if( !empty($pods_fields) ){
		foreach( $pods_fields as $p ){
			$pid = $p['ID'];
			$fields[$cnt]['ID'] = $pid;
			$fields[$cnt]['label'] = $p['post_title'];
			$fields[$cnt]['name'] = $p['post_name'];
			$type = get_post_meta($pid,'type',true);
			$fields[$cnt]['type'] = $type;
			$cnt++;
		}
	}
	return $fields;
}

/**
 * Get Pods Field for Specific CPTs
 */
function get_pods_field_by_slug($cpt, $slug){
	global $wpdb;
	$fields = array();
	$p = $wpdb->get_row( "SELECT ID,post_title,post_name FROM {$wpdb->prefix}posts where post_name = '".$slug."' && post_type='_pods_field' && post_parent=".$cpt, ARRAY_A ); 
	
	if( !empty($p) ){
		$pid = $p['ID'];
		$fields['ID'] = $pid;
		$fields['label'] = $p['post_title'];
		$fields['name'] = $p['post_name'];
		$type = get_post_meta($pid,'type',true);
		$fields['type'] = $type;
	}
	return $fields;
}

/**
 * Get All ACF Groups
 */
function get_acf_groups(){
	global $wpdb;
    $acf_groups = $wpdb->get_results( "SELECT ID,post_title FROM {$wpdb->prefix}posts where post_type='acf-field-group'", ARRAY_A );
	return $acf_groups;
}

/**
 * Get all ACF fields
 *
 * @return void
 */
function get_acf_fields($group){
	global $wpdb;
	$qry =  "SELECT post_excerpt as 'field_name', post_name as 'field_key' FROM {$wpdb->prefix}posts where post_type = 'acf-field' && post_parent=".$group;
	$results = $wpdb->get_results( $qry, ARRAY_A );
	return $results;
}

/**
 * Get ACF fields
 *
 * @return void
 */
function get_acf_field_by_slug($group,$slug){
	global $wpdb;
	$qry =  "SELECT post_excerpt as 'field_name', post_name as 'field_key' FROM {$wpdb->prefix}posts where post_excerpt = '".$slug."'  && post_type = 'acf-field' && post_parent=".$group;
	// echo $qry;
	$results = $wpdb->get_results( $qry, ARRAY_A );
	return $results;
}

/**
 * Display field data accordingly
 *
 * @param [string] $field_name
 * @param [string] $type
 * @return void
 */
function display($field_name, $type, $data, $post_id, $mType,  $label=''){
    if($mType == 'acf'){
        switch ($type){
            case 'text':
            case 'textarea':
            case 'email':
            case 'range':
            case 'url':
            case 'password':
            case 'radio':
            case 'button_group':
            case 'date_picker':
            case 'date_time_picker':
            case 'time_picker':
            case 'color_picker':
            case 'wysiwyg':
            case 'number': display_text($data['value'], $label);
            break;    
            case 'image': display_image($data['value'], $label);
            break; 
            case 'file': display_file($data, $label, $mType);
            break; 
            case 'select': display_select($data, $label);
            break;   
            case 'checkbox': display_checkbox($data, $label);
            break;  
            case 'true_false': display_boolean($data['value'], $label);
            break;         
        }
    }else{
        switch ($type){
            case 'text':
            case 'paragraph':
            case 'email':
            case 'website':
            case 'phone':
            case 'password':
            case 'datetime':
            case 'date':
            case 'time':
            case 'currency':
            case 'color':
            case 'wysiwyg':
            case 'number': display_text(get_post_meta($post_id, $field_name)[0], $label);
            break;    
            case 'file': display_file(get_post_meta($post_id, $field_name)[0], $label, $mType);
            break; 
            case 'boolean': display_boolean(get_post_meta($post_id, $field_name)[0], $label);
            break; 
        }
    }
    
}

/**
 * Display Field Text Value
 *
 * @param [string] $data
 * @param [string] $label
 * @return void
 */
function display_text($data, $label){
    if($label != ''){
        echo '<span class="cpt-text"><strong>'.$label.' : </strong>' . $data . '</span>' ;
    }else{
        echo '<span class="cpt-text">' . $data . '</span>' ;
    }
}

/**
 * Display Field Select Value
 *
 * @param [array] $data
 * @param [string] $label
 * @return void
 */
function display_select($data, $label){
    if($data['multiple'] == true){
        $value = implode(', ', $data['value']);
    }else{
        $value = $data['value'];
    }
    if($label != ''){
        echo '<span class="cpt-text"><strong>'.$label.' : </strong>' . $value . '</span>' ;
    }else{
        echo '<span class="cpt-text">' . $value . '</span>' ;
    }
}

/**
 * Display Field Checkbox Value
 *
 * @param [array] $data
 * @param [string] $label
 * @return void
 */
function display_checkbox($data, $label){
    $value = implode(', ', $data['value']);
    if($label != ''){
        echo '<span class="cpt-text"><strong>'.$label.' : </strong>' . $value . '</span>' ;
    }else{
        echo '<span class="cpt-text">' . $value . '</span>' ;
    }
}

/**
 * Display Field Boolean Value
 *
 * @param [boolean] $data
 * @param [string] $label
 * @return void
 */
function display_boolean($data, $label){
    $data = true ? 'Yes' : 'No';
    if($label != ''){
        echo '<span class="cpt-boolean"><strong>'.$label.' : </strong>' . $data . '</span>' ;
    }else{
        echo '<span class="cpt-boolean">' . $data . '</span>' ;
    }
}

/**
 * Display Field Image
 *
 * @param [string] $data
 * @param [string] $label
 * @return void
 */
function display_image($data, $label){
    
    if(is_int($data)){
		$url = wp_get_attachment_url($data);
		echo '<figure class="cpt-image"><img src="'.$url.'" alt="No Image Available"><figcaption><strong>'.$label.'</strong></figcaption></figure>' ;
	}else if(is_array($data)){
		echo '<figure class="cpt-image"><img src="'.$data['url'].'" alt="No Image Available"><figcaption><strong>'.$label.'</strong></figcaption></figure>' ;
	}else if($label != ''){
        echo '<figure class="cpt-image"><img src="'.$data.'" alt="No Image Available"><figcaption><strong>'.$label.'</strong></figcaption></figure>' ;
    }else{
        echo '<figure class="cpt-image"><img src="'.$data.'" alt="No Image Available"></figure>' ;
    }
}
/**
 * Display Field File
 *
 * @param [string] $data
 * @param [string] $label
 * @return void
 */
function display_file($data, $label, $mType){
    if (!is_array($data)) {
        $data = get_post($data);
        $data = (array) $data;
    }
    if($mType == 'pods'){
        if(strpos($data['post_mime_type'], 'video') !== false){
            if($label != ''){
                echo '<div class="cpt-video"><strong>'.$label.'</strong><video controls><source src="'.$data['guid'].'"/></video></div>' ;
            }else{
                echo '<div class="cpt-video"><video controls><source src="'.$data['guid'].'"/></video></div>' ;
            }
        }else if(strpos($data['post_mime_type'], 'image') !== false){
            if($label != ''){
                echo '<figure class="cpt-image"><img src="'.$data['guid'].'" alt="No Image Available"><figcaption><strong>'.$label.'</strong></figcaption></figure>' ;
            }else{
                echo '<figure class="cpt-image"><img src="'.$data['guid'].'" alt="No Image Available"></figure>' ;
            }
        }else if(strpos($data['post_mime_type'], 'audio') !== false){
            if($label != ''){
                echo '<div class="cpt-audio"><strong>'.$label.'</strong><br/><audio controls><source src="'.$data['guid'].'"/></audio></div>' ;
            }else{
                echo '<div class="cpt-audio"><audio controls><source src="'.$data['guid'].'"/></audio></div>' ;
            }
        }else if(strpos($data['post_mime_type'], 'text') !== false){
            if($label != ''){
                echo '<div class="cpt-text"><strong>'.$label.' : </strong><a target="_blank" href="'.$data['guid'].'">'.$data['post_title'].'</a></div>' ;
            }else{
                echo '<div class="cpt-text"><a target="_blank" href="'.$data['guid'].'">'.$data['post_title'].'</a></div>' ;
            }
        }else{
            if($label != ''){
                echo '<div class="cpt-file"><strong>'.$label.' : </strong><a target="_blank" href="'.$data['guid'].'">'.$data['post_title'].'</a></div>' ;
            }else{
                echo '<div class="cpt-file"><a target="_blank" href="'.$data['guid'].'">'.$data['post_title'].'</a></div>' ;
            }
        }
        unset($data['post_mime_type']);
    }else{
        if ($data['return_format']=='id') {
            $data = get_post($data['value']);
            $data = (array) $data;
        }elseif($data['return_format']=='url'){
            $filetype = wp_check_filetype($data['value']);
            $video_type = array('mp4', 'm4v', 'webm', 'ogv', 'wmv','flv','video/mp4');
            $images_type = array('jpg','jpeg','png','gif','ico','webp');
            $audio_type = array('mp3','m4a', 'ogg', 'wav');
            if(in_array($filetype['ext'], $video_type)){
                $data['post_mime_type'] = 'video';
            }else if (in_array($filetype['ext'], $images_type)) {
                $data['post_mime_type'] = 'image';
            }elseif(in_array($filetype['ext'], $audio_type)){
                $data['post_mime_type'] = 'audio';
            }

            $data['guid'] = $data['value'];

        }elseif($data['return_format']=='array'){
            $video_type = array('mp4', 'm4v', 'webm', 'ogv', 'wmv','flv','video/mp4');
            $images_type = array('jpg','jpeg','png','gif','ico','webp');
            $audio_type = array('mp3','m4a', 'ogg', 'wav');
            $flag = $data['value'];
             $filetype = wp_check_filetype($flag['url']);
            if(in_array($filetype['ext'], $video_type)){
                $data['post_mime_type'] = $flag['type'];
            }else if (in_array($filetype['ext'], $images_type)) {
                $data['post_mime_type'] = $flag['type'];
            }elseif(in_array($filetype['ext'], $audio_type)){
                $data['post_mime_type'] = $flag['type'];
            }
            $data['guid'] = $flag['url'];
        }
        if(strpos($data['post_mime_type'], 'video') !== false){
            if($label != ''){
                echo '<div class="cpt-video"><strong>'.$label.'</strong><br/><video controls><source src="'.$data['guid'].'"/></video></div>' ;
            }else{
                echo '<div class="cpt-video"><video controls><source src="'.$data['guid'].'"/></video></div>' ;
            }
        }else if(strpos($data['post_mime_type'], 'image') !== false){
            if($label != ''){
                echo '<figure class="cpt-image"><img src="'.$data['guid'].'" alt="No Image Available"><figcaption><strong>'.$label.'</strong></figcaption></figure>' ;
            }else{
                echo '<figure class="cpt-image"><img src="'.$data['guid'].'" alt="No Image Available"></figure>' ;
            }
        }else if(strpos($data['post_mime_type'], 'audio') !== false){
            if($label != ''){
                echo '<div class="cpt-audio"><strong>'.$label.'</strong><br/><audio controls><source src="'.$data['guid'].'"/></audio></div>' ;
            }else{
                echo '<div class="cpt-audio"><audio controls><source src="'.$data['guid'].'"/></audio></div>' ;
            }
        }else if(strpos($data['post_mime_type'], 'text') !== false){
            if($label != ''){
                echo '<div class="cpt-text"><strong>'.$label.' : </strong><a target="_blank" href="'.$data['guid'].'">'.$data['post_title'].'</a></div>' ;
            }else{
                echo '<div class="cpt-text"><a target="_blank" href="'.$data['guid'].'">'.$data['post_title'].'</a></div>' ;
            }
        }else{
            if($label != ''){
                echo '<div class="cpt-file"><strong>'.$label.' : </strong><a target="_blank" href="'.$data['guid'].'">'.$data['post_title'].'</a></div>' ;
            }else{
                echo '<div class="cpt-file"><a target="_blank" href="'.$data['guid'].'">'.$data['post_title'].'</a></div>' ;
            }
        }
        unset($data['post_mime_type']);
    }
    
}