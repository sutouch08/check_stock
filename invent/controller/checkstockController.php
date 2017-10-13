<?php
include "../../library/config.php";
include "../../library/functions.php";
include "../function/tools.php";
include "../function/stock_helper.php";

//----- Import File ----//
if( isset( $_GET['importItemFile'])){
    require "../../library/class/class.upload.php";
    require "../../library/class/PHPExcel.php";
    
    $sc     = 'success';
    $id     = $_POST['idCheck'];
    $file   = isset($_FILES['importFile'])? $_FILES['importFile'] : FALSE;
    $file_path 	= "../../upload/";
    $upload	= new upload($file);
    if($upload->uploaded)
    {
	$upload->file_new_name_body = 'importItem';
	$upload->file_overwrite     = TRUE;
	$upload->auto_create_dir    = FALSE;
	
	$upload->process($file_path);
	if( ! $upload->processed)
	{
            $sc = $upload->error;
        }else{
            
            $excel = PHPExcel_IOFactory::load($upload->file_dst_pathname);
            $max = $excel->getActiveSheet()->getHighestRow();
            
            $row = 2;
            $suc = 2;
            $date_add = date('Y-m-d H:i:s');
            while($row <= $max){
                
                    
                    $barcodeZone = $excel->getActiveSheet()->getCell('A'.$row)->getValue();
                    $barcodeItem = $excel->getActiveSheet()->getCell('B'.$row)->getValue();
                    $reference   = $excel->getActiveSheet()->getCell('C'.$row)->getValue();
                    $style       = $excel->getActiveSheet()->getCell('D'.$row)->getValue();
                    $qty         = $excel->getActiveSheet()->getCell('E'.$row)->getValue();
                    $id_wh       = $excel->getActiveSheet()->getCell('F'.$row)->getValue();
                    $qr = "INSERT INTO tbl_import_item (id_check, barcode_zone, barcode_item, reference, style, qty, warehouse, date_add) VALUES ";
                    $qr .= "(".$id.", '".$barcodeZone."', '".$barcodeItem."', '".$reference."', '".$style."', ".$qty.", ".$id_wh.", '".$date_add."')";
                    $qs = dbQuery($qr);
                    if($qs){ $suc++; }
                
                $row++; 
            }
            if($row != $suc){ $sc = 'imported: '.$suc.' of '.$max; }
        }
    }
    $upload->clean();
    if( $sc == 'success'){
        setImported($id, 1); 
    }
    
    echo $sc;
}


if( isset( $_GET['removeImportItem'])){
    $sc = 'fail';
    $id = $_POST['idCheck'];
    $qs = dbQuery("DELETE FROM tbl_import_item WHERE id_check = ".$id);
    if( $qs ){
        setImported($id,0);
        $sc = 'success';    
    }
    echo $sc;
}

if( isset( $_GET['saveChecked'])){
	$sc = 'fail';
	$id = $_POST['idCheck'];
	$id_user = $_POST['id_user'];
	$barcodeZone = $_POST['barcodeZone'];
	$timeStamp = date('Y-m-d H:i:s');

	$qs = dbQuery("UPDATE tbl_check_detail SET saved = 1, date_upd = '".$timeStamp."' WHERE id_check = ".$id." AND barcode_zone = '".$barcodeZone."' AND id_user = ".$id_user." AND saved = 0");
	if( $qs ){
		$sc = 'success';
	}
	echo $sc;
}


if( isset( $_GET['changeToUnsaved'])){
    $sc = 'fail';
    $id = $_POST['idCheck'];
    $id_user = $_POST['id_user'];
    $barcodeItem = $_POST['barcodeItem'];
    $barcodeZone = $_POST['barcodeZone'];
    
    $qs = dbQuery("UPDATE tbl_check_detail SET saved = 0 WHERE id_check = ".$id." AND barcode_zone = '".$barcodeZone."' AND barcode_item = '".$barcodeItem."' AND id_user = ".$id_user." AND saved = 1");
    if($qs){
        $sc = 'success';
    }
    echo $sc;
}


if( isset( $_GET['loadUnsaved'])){
	$sc         = 'none';
	$id         = $_POST['idCheck'];
	$barcodeZone= $_POST['barcodeZone'];
	$id_user    = $_POST['id_user'];

	$qs = dbQuery("SELECT * FROM tbl_check_detail WHERE id_check = ".$id." AND barcode_zone = '".$barcodeZone."' AND id_user = ".$id_user." AND saved = 0");
	if( dbNumRows($qs) > 0)
	{
		$ds = array();
		while($rs = dbFetchArray($qs))
		{
                    $reference = getProduct($rs['barcode_item']);
			$arr = array(
							'id'	=> $rs['id'],
							'barcode'	=> $rs['barcode_item'],
							'product'	=> $reference === FALSE ? '' : $reference,
							'timestamp' => thaiDateTime($rs['date_upd'])
						);
			array_push($ds, $arr);
		}
		$sc = json_encode($ds);
	}
	echo $sc;
}


if( isset( $_GET['loadSaved'])){
	$sc = 'none';
	$id = $_POST['idCheck'];
	$barcodeZone = $_POST['barcodeZone'];
	$id_user		= $_POST['id_user'];

	if(hasSaved($id, $barcodeZone, $id_user) === TRUE){

		$qr = "SELECT barcode_item, SUM(qty) AS qty FROM tbl_check_detail ";
		$qr .= "WHERE id_check = ".$id." AND barcode_zone = '".$barcodeZone."' AND id_user = ".$id_user." AND saved = 1 GROUP BY barcode_item";

		$qs = dbQuery($qr);
		if( dbNumRows($qs) > 0)
		{
			$ds = array();
			while($rs = dbFetchArray($qs))
			{
                            $reference = getProduct($rs['barcode_item']);   
                            $arr = array(
                                	'barcode'   => $rs['barcode_item'],
									'product'   => $reference === FALSE ? '' : $reference,
									'qty'       => number_format($rs['qty'])
					);
                            array_push($ds, $arr);
			}
			$sc = json_encode($ds);
		}
	}
		echo $sc;
}


if( isset($_GET['checkItem'])){
	$sc = 'fail';
	$id = $_POST['idCheck'];
	$barcodeZone = $_POST['barcodeZone'];
	$barcodeItem = $_POST['barcodeItem'];
	$id_user		= $_POST['id_user'];
	$reference 	= getProduct($barcodeItem);
	if( $reference !== FALSE)
	{

		$qs = dbQuery("INSERT INTO tbl_check_detail (id_check, barcode_zone, barcode_item, id_user) VALUES (".$id.", '".$barcodeZone."', '".$barcodeItem."', ".$id_user.")");
		if($qs)
		{
			$idc = dbInsertId();
			$qr = dbQuery("SELECT * FROM tbl_check_detail WHERE id = ".$idc);
			if(dbNumRows($qr)== 1)
			{
				$rs = dbFetchArray($qr);
				$ds = array(
								'id' => $rs['id'],
								'barcode' => $rs['barcode_item'],
								'product'	=> $reference,
								'timestamp' => thaiDateTime($rs['date_upd'])
							);
				$sc = json_encode($ds);
			}
		}
	}
	else
	{
		$sc = 'noItem';
	}

	echo $sc;
}


if( isset( $_GET['deleteCheckedDetail'])){
    $sc = 'fail';
    $id = $_POST['id_check_detail'];
    $qs = dbQuery("DELETE FROM tbl_check_detail WHERE id = ".$id." AND saved = 0");
    if( $qs ){
        $sc = 'success';
    }
    echo $sc;    
}

if( isset($_GET['forceCheck'])){
    $sc = 'fail';
    $id = $_POST['idCheck'];
    $barcodeZone    = $_POST['barcodeZone'];
    $barcodeItem    = $_POST['barcodeItem'];
    $id_user        = $_POST['id_user'];

    $qs = dbQuery("INSERT INTO tbl_check_detail (id_check, barcode_zone, barcode_item, id_user) VALUES (".$id.", '".$barcodeZone."', '".$barcodeItem."', ".$id_user.")");
    if($qs){
        $idc = dbInsertId();
        $qr = dbQuery("SELECT * FROM tbl_check_detail WHERE id = ".$idc);
        if(dbNumRows($qr)== 1){
            $rs = dbFetchArray($qr);
            $ds = array(
                    'id' => $rs['id'],
                    'barcode' => $rs['barcode_item'],
                    'product'	=> '',
                    'timestamp' => thaiDateTime($rs['date_upd'])
                );
            $sc = json_encode($ds);
        }
    }
    echo $sc;
}


if(isset($_GET['addNewCheck'])){
	$sc = "fail";
	$fromDate = dbDate($_POST['fromDate']);
	$title		= $_POST['title'];
	if( isOpenCheck() === FALSE){
		$qs = dbQuery("INSERT INTO tbl_check (title, date_start, active) VALUES ('".$title."', '".$fromDate."', 1)");
		if($qs){
			$sc = "success";
		}
	}
	echo $sc;
}


if( isset( $_GET['getZoneName'])){
	$sc = 'fail';
	$barcode = $_POST['barcodeZone'];
	$qs = dbQuery("SELECT zone_name FROM tbl_zone WHERE barcode = '".$barcode."'");
	if(dbNumRows($qs) == 1)
	{
		list( $sc ) = dbFetchArray($qs);
	}
	echo $sc;
}


if( isset( $_GET['isActiveCheck'])){
	$sc = 'actived';
	$id = $_POST['idCheck'];
	$rs = isActiveCheck($id);
	if( $rs === FALSE){
		$sc = 'closed';
	}
	echo $sc;
}


if( isset( $_GET['closeCheck'])){
    $sc = 'fail';
    $id = getActiveCheck();
    if( $id !== FALSE ){
        $qs = dbQuery("SELECT * FROM tbl_check_detail WHERE id_check = ".$id." AND saved = 0 GROUP BY barcode_zone, barcode_item, id_user");
        if(dbNumRows($qs) > 0 ){
            $ms = 'มีรายการที่ยังไม่บันทึกค้างอยู่ดังนี้ <br/>';
            
            while($rs = dbFetchArray($qs)){
                $zone = getZoneName($rs['barcode_zone']);
                $item = getProduct($rs['barcode_item']);
                $user = getUserName($rs['id_user']);
                $ms .= '<span style="font-size:12px; display:block;">';
                $ms .= 'Zone: '.($zone === FALSE ? $rs['barcode_zone'] : $zone) ;
                $ms .= ', Product: '.($item === FALSE ? $rs['barcode_item'] : $item);
                $ms .= ', User: '. ($user === FALSE ? '' : $user);
                $ms .= '</span>';
            }
            $sc = $ms;
        }else{
            $qs = dbQuery("UPDATE tbl_check SET active = 0 WHERE id = ".$id);
            if($qs){
                $sc = 'success';
            }
        }
    }
    echo $sc;
}
?>
