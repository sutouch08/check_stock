<?php
function countMember($id_group)
	{
		$sc = 0;
		$qs = dbQuery("SELECT COUNT(*) FROM tbl_customer WHERE id_default_group = ".$id_group);
		list( $rs ) = dbFetchArray($qs);
		if( ! is_null( $rs ) )
		{
			$sc = $rs;
		}
		return $sc;
	}

function selectGroup($se = '' )
{
	$sc = '';
	$qs = dbQuery("SELECT * FROM tbl_group");
	while( $rs = dbFetchArray($qs) )
	{
		$sc .= '<option value="'.$rs['id_group'].'" '.isSelected($se, $rs['id_group']).' >'.$rs['group_name'].'</option>';
	}
	return $sc;
}
?>