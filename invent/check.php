<?php accessDeny(getCookie('isAdmin')); ?>
<?php require 'function/stock_helper.php'; ?>
<?php $isOpen = isOpenCheck(); ?>
<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><i class="fa fa-users"></i> <?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
    <?php if( $isOpen === TRUE) : ?>
        <button type="button" class="btn btn-sm btn-danger" onclick="closeCheck()"><i class="fa fa-close"></i> ปิดการตรวจนัด</button>
    <?php else : ?>
        <button type="button" class="btn btn-sm btn-info" onclick="openCheck()"><i class="fa fa-check-circle"></i> เปิดการตรวจนับ</button>
    <?php endif; ?>
      </p>
    </div>
  </div><!-- /row -->
  <hr/>
  <?php $qs = dbQuery("SELECT * FROM tbl_check ORDER BY date_upd DESC"); ?>
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-striped">
        <thead>
          <tr>
            <th style="width:5%; text-align:center;">ลำดับ</th>
            <th style="width:40%;">หัวข้อการตรวจนับ</th>
            <th style="width:15%; text-align:center;">เริ่มต้น</th>
            <th style="width:15%; text-align:center;">สิ้นสุด</th>
            <th style="width:10%; text-align:center;">สถานะ</th>
            <th style="width:15%;"></th>
          </tr>
        </thead>
        <tbody>
        <?php if( dbNumRows($qs) > 0): ?>
          <?php $n = 1; ?>
          <?php while( $rs = dbFetchArray($qs)): ?>
          <tr>
            <td align="center"><?php echo $n; ?></td>
            <td><?php echo $rs['title']; ?></td>
            <td align="center"><?php echo thaiDate($rs['date_start'],'/'); ?></td>
            <td align="center"><?php echo $rs['date_end'] == '0000-00-00 00:00:00' ? 'กำลังนับ' : thaiDate($rs['date_end'], '/'); ?></td>
            <td align="center"><?php echo isActived($rs['active']); ?></td>
            <td align="right">
              <?php if( $rs['imported'] == 0) : ?>
                  <button type="button" class="btn btn-sm btn-primary" onclick="getFile(<?php echo $rs['id']; ?>)">นำเข้ายอดตั้งต้น</button>
              <?php else : ?>
                  <button type="button" class="btn btn-sm btn-danger" onclick="removeImport(<?php echo $rs['id']; ?>)">ลบยอดตั้งต้น</button>
              <?php endif; ?>

            </td>
          </tr>
          <?php $n++; ?>
          <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div><!--/ col-sm-12 -->
  </div><!--/ row -->


  <div class='modal fade' id='addModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
  	<div class='modal-dialog ' style='width:350px;'>
  		<div class='modal-content'>
  			<div class='modal-header'>
  				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
  				<h4 class='modal-title-site text-center' > รายละเอียดการตรวจนับ</h4>
  			</div>
  			<div class='modal-body'>
                            <div class="row">
                              <div class="col-sm-12">
                                <label>หัวข้อการตรวจนับ</label>
                                <input type="text" class="form-control" name="checkTitle" id="checkTitle" placeholder="ระบุหัวข้อการตรวจนับ" />
                              </div>
                              <div class="divider-hidden"></div>
                              <div class="col-sm-12">
                                <label>วันที่</label>
                                <input type="text" class="form-control text-center" name="fromDate" id="fromDate" placeholder="ระบุวันที่เริ่มตรวจนับ" />
                              </div>
                              <div class="divider-hidden"></div>
                              <div class="col-sm-12">
                                <button type="button" class="btn btn-info btn-block" onclick="newCheck()"><i class="fa fa-plus"></i> เปิดการตรวจนับ</button>
                              </div>
                            </div><!--/ row -->
                        </div><!--/ modal-body -->
  			<div class='modal-footer'>
  			</div><!--/ modal-footer-->
  		</div>
  	</div>
  </div>
  
  <div class='modal fade' id='importModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
  	<div class='modal-dialog ' style='width:350px;'>
  		<div class='modal-content'>
  			<div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
                            <h4 class='modal-title-site text-center' > นำเข้ายอดตั้งต้น</h4>
  			</div>
  			<div class='modal-body'>
                            
                            <div class="row">
                              <div class="col-sm-12">
                                  <input type="file" name="importFile" id="importFile" class="form-control input-sm inline" accept=".xlsx" />
                                  <button type="button" class="btn btn-sm btn-success pull-right margin-top-15" onclick="doImport()">นำเข้า</button>
                              </div>
                                <input type="hidden" name="importId" id="importId" />
                            </div><!--/ row -->
                            
                        </div><!--/ modal-body -->
  			<div class='modal-footer'>
  			</div><!--/ modal-footer-->
  		</div>
  	</div>
  </div>

<script>
function doImport(){
    var id = $("#importId").val();
    var file = $("#importFile")[0].files[0];
    if( id === ''){ swal('ข้อผิดพลาด!!','ไม่พบ id การตรวจนับ กรุณาลองใหม่อีกครั้ง', 'error'); return false; }
    if( file === ''){ swal('ข้อผิดพลาด!!', 'กรุณาเลือกไฟล์ที่ต้องการนำเข้า', 'error'); return false;}
    var fd = new FormData();
    fd.append('importFile', $('input[type=file]')[0].files[0]);
    fd.append('idCheck', id);
    $("#importModal").modal('hide');
    load_in();
    $.ajax({
      url: 'controller/checkstockController.php?importItemFile',
      type: 'POST', cache: 'false', data: fd, processData: false, contentType: false,
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if(rs === 'success'){
          window.location.reload();
        }else{
          swal('ข้อผิดพลาด!!', rs, 'error');
        }
      }
    });
}


function removeImport(id){
    swal({
        title: 'ลบยอดตั้งต้น',
        text: 'คุณแน่ใจนะ ว่าต้องการลบยอดตั้งต้นออกทั้งหมด โปรดจำไว้ว่า การกระทำนี้ไม่สามารถย้อนคืนได้',
        showCancelButton: true,
	confirmButtonColor: "#DD6B55",
	confirmButtonText: "ใช่ ต้องการลบ",
	cancelButtonText: "ไม่ต้องการลบ",
        closeOnConfirm: false
    },function(isConfirm){
        if(isConfirm){
            load_in();
            $.ajax({
                url:'controller/checkstockController.php?removeImportItem',
                type:'POST', cache:'false', data:{'idCheck':id},
                success:function(rs){
                    load_out();
                    var rs = $.trim(rs);
                    if(rs === 'success'){
                        swal({title: 'เรียบร้อย', text: 'ลบยอดตั้งต้นทั้งหมดเรียบร้อยแล้ว', type: 'success', timer: 1000 });
                        setTimeout(function(){ window.location.reload(); }, 1100);
                    }else{
                        swal('ข้อผิดพลาด !!', 'ลบยอดตั้งต้นไม่สำเร็จ', 'error');
                    }
                }
            });
        }
        
    });
}


function getFile(id){
    $("#importId").val(id);
    $("#importModal").modal('show');
}

function newCheck(){
  var title = $("#checkTitle").val();
  var fromDate = $("#fromDate").val();
  if( title === ''){ swal("หัวข้อไม่ถูกต้อง"); return false;}
  if( ! isDate(fromDate)){ swal("วันที่ไม่ถูกต้อง"); return false;}
  $.ajax({
    url: "controller/checkstockController.php?addNewCheck",
    type:"POST", cache:"false", data:{'title': title, 'fromDate': fromDate},
    success: function(rs){
      var rs = $.trim(rs);
      if( rs == 'success'){
        window.location.reload();
      }else{
        swal("เปิดการตรวจนับไม่สำเร็จ");
      }
    }
  });
}

function openCheck(){
  $("#addModal").modal('show');
}

function closeCheck(){
  swal({
    title: 'ต้องการปิดการตรวจนับ !!',
    text:'คุณแน่ใจนะว่าต้องการปิดการตรวจนับ',
    type:'warning',
    showCancelButton: true,
    confirmButtonColor: '#DD6855',
    confirmButtonText: 'ใช่ ฉันต้องการปิด',
    cancelButtonText: 'ยกเลิกการปิด',
    closeOnConfirm: false

  }, function(isConfirm){
    if(isConfirm){
      $.ajax({
        url:'controller/checkstockController.php?closeCheck',
        type:'POST', cache: 'false',
        success: function(rs){
            var rs = $.trim(rs);
            if(rs === 'success'){
                swal({
                    title: 'เรียบร้อย',
                    text: 'ปิดการตรวจนับเรียบร้อยแล้ว',
                    type: 'success',
                    timer: 1000
                });
                setTimeout(function(){ window.location.reload(); }, 1100);                
            }else if(rs === 'fail'){
                swal('ข้อผิดพลาด !', 'ปิดการตรวจนับไม่สำเร็จ', 'error');
            }else{
                swal({
                    title: 'ข้อผิดพลาด',
                    text: rs,
                    type: 'error',
                    html: true
                });
            }
          
        }
      });
    }
  });
}

$("#fromDate").datepicker({
  dateFormat: 'dd-mm-yy'
});


</script>
</div><!--/ container -->
