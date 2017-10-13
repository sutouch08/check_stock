
<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><i class="fa fa-users"></i> <?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">

    </div>
  </div><!-- /row -->
  <hr/>
  <?php accessDeny(getCookie('isAdmin')); ?>

<?php   $qs = dbQuery("SELECT * FROM tbl_user");  ?>
  <div class="row">
    <div class="col-sm-2">
      <label>Login Name</label>
      <input type="text" class="form-control input-sm" id="userName" name="userName" autofocus />
    </div>
    <div class="col-sm-2">
      <label>รหัสผ่าน</label>
      <input type="password" class="form-control input-sm" id="password" name="password" />
    </div>
    <div class="col-sm-2">
      <label>ชื่อ</label>
      <input type="text" class="form-control input-sm" id="firstName" name="firstName"  />
    </div>
    <div class="col-sm-2">
      <label>นามสกุล</label>
      <input type="text" class="form-control input-sm" id="lastName" name="lastName" />
    </div>
    <div class="col-sm-1">
      <label>สิทธิ์</label>
      <select class="form-control input-sm" name="admin" id="admin">
        <option value="0">User</option>
        <option value="1">Admin</option>
      </select>
    </div>
    <div class="col-sm-1">
      <label style="display:block; visibility:hidden">active</label>
      <label><input type="checkbox" name="active" id="active" value="1" style="margin-right:15px;" checked /> Active</label>
    </div>
    <div class="col-sm-1">
      <label style="display:block; visibility:hidden">btn</label>
      <button type="button" class="btn btn-sm btn-success btn-block" onclick="addUser()"><i class="fa fa-plus"></i> เพิ่ม</button>
    </div>

  </div><!--/ row -->
  <hr class="margin-top-15"/>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-striped table-bordered">
        <thead>
          <tr style="font-12px;">
            <th style="width:5%; text-align:center;">No.</th>
            <th style="width:20%;text-align:center;">Login Name</th>
            <th style="width:40%;text-align:center;">ชื่อพนักงาน</th>
            <th style="width:10%; text-align:center;">is Admin</th>
            <th style="width:10%; text-align:center;">สถานะ</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody id="table">
        <?php if(dbNumRows($qs) > 0 ) : ?>
          <?php $n = 1; ?>
        <?php   while($rs = dbFetchArray($qs)) : ?>
          <tr>
            <td align="center"><?php echo $n; ?></td>
            <td><?php echo $rs['user_name']; ?></td>
            <td><?php echo $rs['first_name'] .' '.$rs['last_name']; ?></td>
            <td align="center"><?php echo isActived($rs['is_admin']); ?></td>
            <td align="center"><?php echo isActived($rs['active']); ?></td>
            <td align="right"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i> แก้ไข</button></td>
          </tr>
          <?php $n++; ?>
        <?php endwhile; ?>

      <?php endif; ?>

        </tbody>
      </table>
      <input type="hidden" id="no" value="<?php echo $n; ?>">

    </div>
  </div>

<script>
function addUser(){
  var UserName = $("#userName").val();
  var password  = $("#password").val();
  var firstName = $("#firstName").val();
  var lastName = $("#lastName").val();
  var admin     = $("#admin").val();
  var active  = $("#active");
  var ac = 0;
  if(active.is(":checked") == true){ ac = 1; }
  if( UserName == "" || password == "" || firstName == ""){
    swal("ข้อมูลไม่ครบถ้วน");
    return false;
  }



  load_in();
  $.ajax({
    url: "controller/userController.php?addUser",
    type:"POST", cach:"false", data: { "userName" : UserName, "password" : password, "firstName" : firstName, "lastName" : lastName, "isAdmin" : admin, "active" : ac},
    success: function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'fail'){
        swal("เพิ่มข้อมูลไม่สำเร็จ");
      }else if(rs == 'success'){

        window.location.reload();

      }
    }
  })

}
</script>
</div><!--/ container -->
