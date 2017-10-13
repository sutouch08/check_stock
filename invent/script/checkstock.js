
function saveChecked(){
  var id = $("#idCheck").val();
  var id_user = $("#id_user").val();
  var barcodeZone = $("#barcodeZone").val();
  var checked = $('.checked').length;
  if( barcodeZone !== '' || checked != 0){
    load_in();
    $.ajax({
      url: 'controller/checkstockController.php?saveChecked',
      type: 'POST', cach:'false', data: {'idCheck' : id, 'barcodeZone' : barcodeZone, 'id_user' : id_user},
      success: function(rs){
        load_out();
        var rs = $.trim(rs);
        if( rs === 'success'){
          loadUnsaved();
          loadSaved();
          countChecked();
          setFocus($("#barcodeItem"), 1000);
        }else{
          swal('ข้อผิดพลาด !!', 'บันทึกข้อมูลไม่สำเร็จ', 'error')
        }
      }
    }); //--
  }
}


function changeToUnsaved(barcode){
    var id = $("#idCheck").val();
    var id_user = $("#id_user").val();
    var barcodeZone = $("#barcodeZone").val();
    
    $.ajax({
        url:'controller/checkstockController.php?changeToUnsaved',
        type:'POST', cache:'false', data:{'idCheck':id, 'id_user':id_user, 'barcodeZone':barcodeZone, 'barcodeItem': barcode},
        success:function(rs){
            var rs = $.trim(rs);
            if(rs === 'success'){
                loadSaved();
                loadUnsaved();
            }else{
                swal('ข้อผิดพลาด', 'เปลี่ยนสถานะไม่สำเร็จ', 'error');
            }
        }
    });
}


function checkItem(){
	
	var barcode = $.trim($("#barcodeItem").val());
	$("#barcodeItem").val('');
        //$("#barcodeItem").attr('disabled','disabled');
	//$("#btn-checkItem").attr('disabled', 'disabled');
        var id_user = $("#id_user").val();
	var id = $("#idCheck").val();
        var barcodeZone = $("#barcodeZone").val();

	if(barcode !==''){
	
	$.ajax({
            url:'controller/checkstockController.php?checkItem',
            type:'POST', cach: 'false', data:{'idCheck':id, 'barcodeItem':barcode, 'barcodeZone':barcodeZone, 'id_user':id_user},
            success:function(rs){
		var rs = $.trim(rs);
		if(rs ===''){
			swal('ข้อผิดพลาด', 'การส่งข้อมูลล้มเหลว','error');
		}else if(rs == 'fail'){
			swal('ข้อผิดพลาด!!', 'Insert data fali! กรุณาติดต่อผู้ดูแลระบบ','error');
		}else if(rs == 'noItem'){
                      beep();
			swal({
			title : "ไม่พบสินค้า",
			text : "ไม่พบบาร์โค้ด "+barcode+" ในฐานข้อมูล<br/>หากคุณต้องการยืนยันการใช้บาร์โค้ดนี้ <br/> <span style='color:red'>พิมพ์ 1234 ในช่อง แล้วกดยืนยัน</span>",
			type : "input",
                        html: true,
		  	showCancelButton: true,
		  	confirmButtonColor: "#DD6B55",
		  	confirmButtonText: "ใช่ ยืนยันบาร์โค้ด",
		  	cancelButtonText: "ไม่ ยิงบาร์โค้ดใหม่",
                        inputPlaceholder:"ใส่ตัวเลข 1234",
		  	closeOnConfirm: false
			},function(inputValue){
                            if(inputValue === false){
                                return false;
                            }
                            if(inputValue ===''){
                                swal.showInputError("คุณต้องใส่รหัส!");
                                return false
                            }
                            if(inputValue != '1234'){
                                swal.showInputError('ตัวเลขไม่ถูกต้อง');
                                return false;
                            }
                            if (inputValue =='1234'){
                                swal.close();
                                forceCheck(id, barcode, barcodeZone, id_user);
                                
                            }else{
                                swal.close();
                                $("#barcodeItem").removeAttr('disabled');
                                $("#btn-checkItem").removeAttr('disabled');
                                countChecked();
                                setFocus($("#barcodeItem"), 100);
                            }
			});
		}else{
                    var source 	= $("#check_template").html();
                    var data 	= $.parseJSON(rs);
                    var output 	= $("#rs");
                    render_prepend(source, data, output);
                    //$("#barcodeItem").removeAttr('disabled');
                    //$("#btn-checkItem").removeAttr('disabled');
                    countChecked();
                    //setFocus($("#barcodeItem"), 100);
		}
            }
	});
    }
}


function countChecked(){
  var checked = $('.checked').length;
  $("#countUnsaved").text(checked);
  if(checked > 0){
    $("#btn-save-check").removeClass('hide');
  }else{
    $("#btn-save-check").addClass('hide');
  }
}

function countSaved(){
	var i = $(".saved_qty").length;
	if( i > 0 ){
		var qty = 0;
		$(".saved_qty").each(function(index, element) {
            qty += parseInt($(this).html());
			i--;
			if( i == 0 ){
				$("#totalSaved").html(qty);
			}
        });
	}else{
		$("#totalSaved").html(0);	
	}
}


function forceCheck(id, barcodeItem, barcodeZone, id_user){
	$.ajax({
		url:'controller/checkstockController.php?forceCheck',
		type:'POST', cach: 'false', data: {'idCheck' : id, 'barcodeItem' : barcodeItem, 'barcodeZone' : barcodeZone, 'id_user' : id_user},
		success: function(rs){
			var rs = $.trim(rs);
			if(rs ===''){
				swal('ข้อผิดพลาด', 'การส่งข้อมูลล้มเหลว','error');
			}else if(rs == 'fail'){
				swal('ข้อผิดพลาด!!', 'Insert data fali! กรุณาติดต่อผู้ดูแลระบบ','error');
			}else{
				var source 	= $("#check_template").html();
				var data 		= $.parseJSON(rs);
				var output 	= $("#rs");
				render_prepend(source, data, output);
				$("#barcodeItem").removeAttr('disabled');
				$("#btn-checkItem").removeAttr('disabled');
        countChecked();
				setFocus($("#barcodeItem"), 100);
			}
		}
	});
}


function setZone(){
		var barcodeZone = $.trim($("#barcodeZone").val());
		$("#barcodeZone").val(barcodeZone);
		if(barcodeZone ===''){
			swal("บาร์โค้ดไม่ถูกต้อง");
			return false;
		}
		$.ajax({
			url:"controller/checkstockController.php?getZoneName",
			type:"POST", cach:"false", data:{"barcodeZone":barcodeZone},
			success:function(rs){
				var rs = $.trim(rs);
				if(rs =='fail'){
					swal({
						title: 'ไม่พบโซน',
						text:'ตรวจดูบาร์โค้ดให้ดีว่าถูกต้องหรือไม่',
						type:'error',
						showCancelButton: true,
						confirmButtonColor: '#DD6855',
						confirmButtonText: 'ใช้บาร์โค้ดนี้',
						cancelButtonText: 'ยิงบาร์โค้ดใหม่',
						closeOnConfirm: true

					}, function(isConfirm){
						if(isConfirm){
							zoneName = '';
							useZone(zoneName);
						}else{
							$("#barcodeZone").val('');
							$("#barcodeZone").focus();
						}
					});
				}else if(rs != ''){
					useZone(rs);
				}
			}
		});
	}
        

function loadUnsaved(){
	var id = $("#idCheck").val();
	var id_user = $("#id_user").val();
	var barcodeZone = $("#barcodeZone").val();
	$.ajax({
		url:'controller/checkstockController.php?loadUnsaved',
		type:'POST', cach:'false', data:{'idCheck' : id, 'barcodeZone' : barcodeZone, 'id_user':id_user},
		success:function(rs){
			var rs = $.trim(rs);
			if(rs == 'none'){
        $("#rs").html('');
      }else{
				var source 	= $("#unsaved_template").html();
				var data 		= $.parseJSON(rs);
				var output 	= $("#rs");
				render(source, data, output);
			}
      countChecked();
		}
	});
}


function loadSaved(){
	var id = $("#idCheck").val();
	var id_user = $("#id_user").val();
	var barcodeZone = $("#barcodeZone").val();
	$.ajax({
		url:'controller/checkstockController.php?loadSaved',
		type:'POST', cach:'false', data:{'idCheck' : id, 'barcodeZone' : barcodeZone, 'id_user':id_user},
		success:function(rs){
			var rs = $.trim(rs);
			if(rs == 'none'){
        $("#res").html('');
		countSaved();
      }else{
				var source 	= $("#saved_template").html();
				var data 		= $.parseJSON(rs);
				var output 	= $("#res");
				render(source, data, output);
				countSaved();
			}
		}
	});
}


function useZone(zoneName){
		if(zoneName ==''){ zoneName = 'นอกระบบ';}
		$("#zoneName").text(zoneName);
		$("#barcodeZone").attr('disabled','disabled');
		$("#btn-setZone").addClass('hide');
		$("#btn-changeZone").removeClass('hide');
		$("#barcodeItem").removeAttr('disabled');
		$("#btn-checkItem").removeAttr('disabled');
		loadUnsaved();
    loadSaved();
		setFocus($("#barcodeItem"),100);
}


function changeZone(){
	$("#zoneName").text('');
	$("#barcodeItem").attr('disabled','disabled');
	$("#btn-checkItem").attr('disabled','disabled');
	$("#btn-changeZone").addClass('hide');
	$("#btn-setZone").removeClass('hide');
	$("#barcodeZone").val('');
	$("#barcodeZone").removeAttr('disabled');
  clearSheet();
	$("#barcodeZone").focus();
}


function delete_check(id, barcode){
    swal({
        title: 'ต้องการลบรายการ!!',
        text: 'คุณแน่ใจว่าต้องการลบรายการนี้จริงๆ',
        type: 'warning',
        showCancelButton: true,
	confirmButtonColor: "#DD6B55",
	confirmButtonText: "ใช่ ต้องการลบ",
	cancelButtonText: "ไม่ต้องการลบ",
        closeOnConfirm: false        
        },function(isConfirm){
           if(isConfirm){
               $.ajax({
                  url:'controller/checkstockController.php?deleteCheckedDetail',
                  type:'POST', cache:'false', data:{'id_check_detail':id },
                  success:function(rs){
                      var rs = $.trim(rs);
                      if(rs === 'success'){
                          $('#rs_'+id).remove();
                          countChecked();
                          swal({title:'เรียบร้อย', text:'ลบรายการเรียบรอ้ยแล้ว', type:'success', timer: 1000});
                          setFocus($('#barcodeItem'), 1100);
                      }else{
                          swal('ข้อผิดพลาด !!', 'ลบรายการไม่สำเร็จ', 'error');
                          setFocus($('#barcodeItem'), 100);
                      }
                  }
               });
            }else{
                setFocus($('#barcodeItem'), 100);
            }
        }
    );
}


function clearSheet(){
  $("#rs").html('');
  $("#res").html('');
}


$("#barcodeZone").keyup(function(e){
	if(e.keyCode == 13){
		setZone();
	}
});


$("#barcodeItem").keyup(function(e){
	if(e.keyCode == 13){
		checkItem();
	}
});


$("#barcodeItem").focusout(function(){
    if($('.showSweetAlert').length == 0){
        setFocus($("#barcodeItem"),1000);
    }
});


$(document).keyup(function(e){
    if(e.keyCode == 113){
        changeZone();
    }
})


function setFocus(el, time){
		setTimeout(function(){ el.focus();},time);
}


setInterval(function(){
  var id = $("#idCheck").val();
  $.ajax({
    url:'controller/checkstockController.php?isActiveCheck',
    type:'POST', cach:'false', data:{ 'idCheck' : id},
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'closed'){
        window.location.reload();
      }
    }
  });
}, 30000);
