<?php
function isOpenCheck(){
	$sc = FALSE;
	$qs = dbQuery("SELECT id FROM tbl_check WHERE active = 1");
	if( dbNumRows($qs)==1)
	{
		$sc = TRUE;
	}
	return $sc;

}

function isActiveCheck($id){
	$sc = FALSE;
	$qs = dbQuery("SELECT id FROM tbl_check WHERE id=".$id." AND active =1");
	if( dbNumRows($qs)==1)
	{
		$sc = TRUE;
	}
	return $sc;
}

function getProduct($barcode){
	$sc = FALSE;
	$qs = dbQuery("SELECT reference FROM tbl_product WHERE barcode ='".$barcode."'");
	if(dbNumRows($qs) == 1){
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}


function getZoneName($barcode){
    $sc = FALSE;
    $qs = dbQuery("SELECT zone_name FROM tbl_zone WHERE barcode = '".$barcode."'");
    if(dbNumRows($qs) == 1){
        list($sc) = dbFetchArray($qs);
    }
    return $sc;
}


function getUserName($id_user){
    $sc = FALSE;
    $qs = dbQuery("SELECT user_name FROM tbl_user WHERE id_user = ".$id_user);
    if(dbNumRows($qs) == 1){
        list( $sc ) = dbFetchArray($qs);
    }
    return $sc;
}


function hasSaved($id_check, $barcodeZone, $id_user ){
	$sc = FALSE;
	$qs = dbQuery("SELECT id FROM tbl_check_detail WHERE id_check = ".$id_check." AND barcode_zone = '".$barcodeZone."' AND id_user = ".$id_user." AND saved = 1");
	if(dbNumRows($qs) > 0){
		$sc = TRUE;
	}
	return $sc;
}


function setImported($id_check, $val){
    $sc = TRUE;
    $qs = dbQuery("UPDATE tbl_check SET imported = ".$val." WHERE id = ".$id_check);
    if( ! $qs ){
        $sc = FALSE;
    }
    return $sc;
}


function getActiveCheck(){
    $sc = FALSE;
    $qs = dbQuery("SELECT id FROM tbl_check WHERE active = 1");
    if( dbNumRows($qs) == 1){
        list( $sc ) = dbFetchArray($qs);
    }
    return $sc;
}
?>
