<?php 

	global $CONFIG;

	$group = $vars["entity"];
	$user = elgg_get_logged_in_user_entity();
	
	if(!empty($group) && ($group instanceof ElggGroup) && !empty($user)){
		if(($group->getOwnerGUID() == $user->getGUID()) || $user->isAdmin()){
			$dbprefix = elgg_get_config("dbprefix");
			
			$friends_options = array(
				"type" => "user",
				"relationship" => "friend",
				"relationship_guid" => $user->getGUID(),
				"limit" => false,
				"joins" => array("JOIN " . $dbprefix . "users_entity ue ON e.guid = ue.guid"),
				"order_by" => "ue.name"
			);
			
			$member_options = array(
				"type" => "user",
				"relationship" => "member",
				"relationship_guid" => $group->getGUID(),
				"inverse_relationship" => true,
				"limit" => false,
				"joins" => array("JOIN " . $dbprefix . "users_entity ue ON e.guid = ue.guid"),
				"order_by" => "ue.name"
			);
			
			$friends = elgg_get_entities_from_relationship($friends_options);
			$members = elgg_get_entities_from_relationship($member_options);
			
			if(!empty($friends) || !empty($members)){
				$add_myself = false;
				$add_friends = false;
				$add_members = false;
				
				$result = elgg_echo("group_tools:admin_transfer:transfer") . ": ";
		 		$result .= "<select name='owner_guid'>\n";
		 		
		 		if($group->getOwnerGUID() != $user->getGUID()){
		 			$add_myself = true;
		 			
		 			$result .= "<option value='" . $user->getGUID() . "'>" . elgg_echo("group_tools:admin_transfer:myself") . "</option>\n";
		 		}
		 		
		 		if(!empty($friends)){
		 			$friends_block = "<optgroup label='" . elgg_echo("friends") . "'>\n";
			 		
		 			foreach($friends as $friend){
		 				if($group->getOwnerGUID() != $friend->getGUID()){
		 					$add_friends = true;
		 					
			 				$friends_block .= "<option value='" . $friend->getGUID() . "'>" . $friend->name . "</option>\n";
		 				}
			 		}
			 		
			 		$friends_block .= "</optgroup>\n";
			 		
			 		if($add_friends){
			 			$result .= $friends_block;
			 		}
		 		}
		 		
		 		if(!empty($members)){
		 			$members_block = "<optgroup label='" . elgg_echo("groups:members") . "'>\n";
			 		
		 			foreach($members as $member){
		 				if(($group->getOwnerGUID() != $member->getGUID()) && ($user->getGUID() != $member->getGUID())){
		 					$add_members = true;
		 					
			 				$members_block .= "<option value='" . $member->getGUID() . "'>" . $member->name . "</option>\n";
		 				}
			 		}
			 		
			 		$members_block .= "</optgroup>\n";
			 		
			 		if($add_members){
			 			$result .= $members_block;
			 		}
		 		}
		 		
		 		$result .= "</select>\n";
		 		$result .= "<br />";
		 		$result .= "<br />";
		 		
		 		if($add_myself || $add_friends || $add_members){
			 		
			 		$result .= elgg_view("input/hidden", array("name" => "group_guid", "value" => $group->getGUID()));
			 		$result .= elgg_view("input/submit", array("value" => elgg_echo("group_tools:admin_transfer:submit")));
			 		
			 		$result = elgg_view("input/form", array("body" => $result,
			 												"action" => $vars["url"] . "action/group_tools/admin_transfer",
			 												"id" => "group_tools_admin_transfer_form"));
		 		} else {
		 			$result = elgg_echo("group_tools:admin_transfer:no_users");
		 		}
		 	} else {
		 		$result = elgg_echo("group_tools:admin_transfer:no_users");
		 	}
		
		 	echo elgg_view_module("info", elgg_echo("group_tools:admin_transfer:title"), $result);
			
		}
	}

?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#group_tools_admin_transfer_form').submit(function(){
			return confirm("<?php echo elgg_echo("group_tools:admin_transfer:confirm"); ?>");
		});
	});
</script>