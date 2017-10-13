$(document).ready(function(e) {
    $("#increase").numberOnly();
	$("#decrease").numberOnly();
});
$("#paCode").autocomplete({
	source: "controller/autoComplete.php?get_product_attribute",
	minLength: 1,
	autoFocus: true,
	close: function(event, ui){
		var rs = $(this).val();
		if( rs != 'ไม่พบข้อมูล'){
			var ar = rs.split(' | ');
			var ref = ar[0];
			var id_pa = ar[1];
			if( ! isNaN( id_pa ) ){
				$("#id_pa").val(id_pa);
				$("#paCode").val(ref);
				$("#paCode").removeClass('has-error');	
			}else if( rs != '' ){
				$("#id_pa").val('');
				$("#paCode").addClass('has-error');	
			}
		}
	}
});

$("#paCode").keyup(function(e) {
    if( e.keyCode == 13 ){
		setTimeout(function(){
			var id_pa = $("#id_pa").val();
			if( id_pa != '' ){
				$("#increase").focus();
			}
		},100);
	}
});

$("#increase").keyup(function(e){
	if(e.keyCode == 13 ){
		if($(this).val() === ''){
			$(this).val(0);
		}
		$("#decrease").focus();
	}
});

$("#decrease").keyup(function(e){
	if( e.keyCode == 13 ){
		if($(this).val() === '' ){
			$(this).val(0);
		}
		insertDetail();	
	}
});

function insertDetail()
{
	var id_adj 	= $("#id_adjust").val();
	var id_emp	= $("#id_user").val();
	var id_zone = $("#id_zone").val();
	var id_wh	= $("#id_wh").val();
	var id_pa	= $("#id_pa").val();
	var incr		= $("#increase").val();
	var decr		= $("#decrease").val();
	if( id_zone == '' || id_wh == '' ){
		swal('โซนสินค้าไม่ถูกต้อง');
		return false;
	}
	if( id_pa == '' ){
		swal('รหัสสินค้าไม่ถูกต้อง');
		return false;
	}
	if( (incr == '0' && decr == '0' ) || ( incr == decr ) ){
		swal('จำนวนไม่ถูกต้อง');
		return false;
	}
	
	load_in();
	$.ajax({
		url:"controller/productAdjustController.php?insertDetail",
		type:"POST", cache:"false", data:{ "id_adjust" : id_adj, "id_emp" : id_emp, "id_zone" : id_zone, "id_wh" : id_wh, "id_pa" : id_pa, "increase" : incr, "decrease" : decr },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs == 'success')
			{
				
			}
		}
	});
	
}

function reloadAdjustTable()
{
		
}

$("#zoneName").autocomplete({
	source: "controller/autoComplete.php?getZone",
	minLength: 1,
	autoFocus: true,
	close: function(event, ui){
		var rs = $(this).val();
		if( rs != 'ไม่พบข้อมูล' )
		{
			var ar = rs.split(' | ');
			var zone_name = ar[0];
			var id_zone = ar[1];
			var id_wh	= ar[2];
			if( ! isNaN(id_zone) && ! isNaN( id_wh) ){
				$("#id_zone").val(id_zone);
				$("#zoneName").val(zone_name);
				$("#id_wh").val(id_wh);
				$("#zoneName").removeClass('has-error');
			}else if( rs != ''){
				$("#id_zone").val('');
				$("#id_wh").val('');
				$("#zoneName").addClass('has-error');
				swal({ title: 'โซนไม่ถูกต้อง', type: 'warning'}, function(){ $("#zoneName").focus(); });	
			}
		}
	}
});

$("#zoneName").keyup(function(e) {
    if( e.keyCode == 13 ){
		setTimeout(function(){ 
			setZone();
		}, 100);
	}
});

function setZone()
{
	var id_zone = $("#id_zone").val();
	var id_wh	= $("#id_wh").val();
	if( id_zone != '' && id_wh != '' ){
		$("#zoneName").attr('disabled', 'disabled');
		$(".adj").removeAttr('disabled');
		$("#btn-setZone").addClass('hide');
		$("#btn-changeZone").removeClass('hide');
		$("#paCode").focus();
	}	
}

function changeZone()
{
	$(".adj").val('');
	$(".adj").attr('disabled', 'disabled');
	$("#id_zone").val('');
	$("#id_wh").val('');
	$("#id_pa").val('');
	$("#zoneName").val('');
	$("#zoneName").removeAttr('disabled');
	$("#btn-changeZone").addClass('hide');
	$("#btn-setZone").removeClass('hide');
	$("#zoneName").focus();
}

function addNewAdjust()
{
	var adj_ref	= $("#adj_ref").val();
	var id_emp	= $("#id_user").val();
	var date 		= $("#date").val();
	var remark	= $("#remark").val();
	if( ! isDate(date) ){ 
		swal('วันที่ไม่ถูกต้อง');
		$("#date").focus();
		return false;
	}
	load_in();
	$.ajax({
		url:"controller/productAdjustController.php?addNewAdjust",
		type:"POST", cache:"false", data:{ "adj_ref" : adj_ref, "date" : date, "remark" : remark, "id_employee" : id_emp },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			var rs = parseInt(rs);
			if( ! isNaN(rs) ){
				window.location.href = "index.php?content=ProductAdjust&add&id_adjust="+rs;
			}else{
				swal('บันทึกรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');	
			}
		}
	});
}


$(".sf").keyup(function(e) {
    if(e.keyCode == 13 ){
		getSearch();
	}
});

function toggleStatus(id)
{
	var id = parseInt(id);
	var vt = parseInt($("#adj_vt").val());
	if( id === 0 ){
		if( vt === id ){
			$("#btn-unsave").removeClass('btn-info');
			$("#adj_vt").val('');
		}else{
			$("#btn-saved").removeClass('btn-info');
			$("#btn-unsave").addClass('btn-info');
			$("#adj_vt").val(0);
		}
	}else if( id === 1 ){
		if( vt === id ){
			$("#btn-saved").removeClass('btn-info');
			$("#adj_vt").val('');
		}else{
			$("#btn-unsave").removeClass('btn-info');
			$("#btn-saved").addClass('btn-info');
			$("#adj_vt").val(1);
		}
	}
	getSearch();
}

function getSearch()
{
	var from = $("#from").val();
	var to 	= $("#to").val();
	if( from != "" || to != "" ){
		if( ! isDate(from) || ! isDate(to) ){ 
			swal('วันที่ไม่ถูกต้อง');
			return false;
		}
	}
	$("#searchForm").submit();
}

$("#from").datepicker({ 
	dateFormat: 'dd-mm-yy', 
	onClose: function(sd){ 
		$("#to").datepicker('option', 'minDate', sd);
		if( $(this).val() != '' ){ 
			$("#to").focus();
		}
	}
});

$("#to").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#from").datepicker('option', 'maxDate', sd);
	}
});

$("#date").datepicker({	dateFormat: 'dd-mm-yy' });

function clearFilter()
{
	$.ajax({
		url:"controller/productAdjustController.php?clearFilter",
		type:"POST", cache:"false", success: function(rs){
			goBack();
		}
	});
}

function newAdjust()
{
	window.location.href = "index.php?content=ProductAdjust&add";	
}

function editAdjust(id)
{
	window.location.href = "index.php?content=ProductAdjust&edit&id_adjust="+id;	
}

function goBack()
{
	window.location.href = "index.php?content=ProductAdjust";	
}

function newAdjust()
{
	window.location.href = "index.php?content=ProductAdjust&add";	
}