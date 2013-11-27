<?php

class Permission{

	function permission_check_layout_permissions($db, $layout, $user){
		$permissions=$db->db_check_layout_permission($layout, $user);
		if(count($permissions)==1){
			if( $permissions[0]['oikeus'] == 1 ){
				return 1;
			}
			if( $permissions[0]['oikeus'] == 2 ){
				return 2;
			}
		}else{
			return -1;
		}
	}

	function permission_set_permission($db, $layout, $user, $permission){
		$db->db_set_layout_permission($layout, $user, $permission);
	}

	function permission_set_admin($db, $user){
		$db->db_set_admin($user);
	}

	function permission_is_admin($db, $user){
		$permissions=$db->db_check_admin($user);
		if(count($permissions)==1){
			if( $permissions[0]['tyyppi']==-1 && $permissions[0]['kohde']==-1 && $permissions[0]['oikeus'] == 0 ){
				return 1;
			}
		}else{
			return -1;
		}
	}

	function permission_create_layout_list($db, $layout_list){

	}

}

?>
