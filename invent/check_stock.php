<script src="../library/js/jquery.slimscroll.js"></script>
<?php require 'function/stock_helper.php'; ?>
<?php $id = isset($_GET['id_check']) ? $_GET['id_check'] : FALSE;	?>
<?php if( $id !== FALSE && isActiveCheck($id) === TRUE) : ?>
	<input type="hidden" id="id_user" value="<?php echo $_COOKIE['user_id']; ?>" />
	<input type="hidden" name="idCheck" id="idCheck" value="<?php echo $id; ?>" />
<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-bordered">
				<tr>
					<td style="width:15%;">
						<label class="label-right" style="width:150px;">บาร์โค้ดโซน</label>
					</td>
					<td style="width:35%;">
						<input type="text" class="form-control input-sm input-large inline" id="barcodeZone" placeholder="ยิงบาร์โค้ดโซน" autofocus />
						<button type="button" class="btn btn-sm btn-primary" id="btn-setZone" onclick="setZone()">เลือกโซน</button>
						<button type="button" class="btn btn-sm btn-warning hide" id="btn-changeZone" onclick="changeZone()" >เปลี่ยนโซน</button>
					</td>
					<td rowspan="2" align="center" valign="middle" style="width:20%;"><h3 id="countUnsaved" style="color:red;">0</h3></td>
					<td rowspan="2" align="center" valign="middle"><h3 id="zoneName">กรุณาเลือกโซน</h3></td>
				</tr>
				<tr>
					<td style="width:15%">
						<label class="label-right" style="width:150px;">ยิงบาร์โค้ดสินค้า</label>
					</td>
					<td style="width:35%;">
						<input type="text" class="form-control input-sm input-large inline" id="barcodeItem" placeholder="บาร์โค้ดสินค้า" disabled />
						<button type="button" class="btn btn-sm btn-success" id="btn-checkItem" onclick="checkItem()" disabled>ตรวจนับ</button>
					</td>
				</tr>
			</table>
		</div>
	</div><!--/ row -->

	<hr/>

	<div class="row">
		<div class="col-sm-6">
			<div  id="sc" >
	    <table class="table table-striped" style="margin-bottom:0px;">
	    <tr>
	    <th colspan="4" style="text-align:center;">
	    	<span>กำลังตรวจนับ </span>
				<p class="pull-right top-p">
					<button class="btn btn-success btn-xs hide" id="btn-save-check" onclick="saveChecked()">บันทึกรายการ</button>
				</p>
	    </th>
	    </tr>
	    <tr>
				<th style="width:25%;">บาร์โค้ด</th>
				<th>รหัสสินค้า</th>
				<th style="width:25%; text-align:center;">เวลา</th>
				<th style="width:10%; text-align:right;">ลบ</th>
			</tr>
	    <tbody id="rs">

	    </tbody>
	    </table>
	    </div>

		</div><!--/ col-sm-6 -->

		<div class="col-sm-6">
    <div id="cs">
    <table class="table table-striped" style="margin-bottom:0px;">
	    	<tr>
					<th colspan="4" style="text-align:center;">บันทึกแล้ว :  <span style=" margin-left:30px;" id="totalSaved">0</span></th>
                   
				</tr>
				<tr id="head">
					<th style="width:30%;">บาร์โค้ด</th>
					<th>รหัสสินค้า</th>
					<th style="width:15%; text-align:right;">จำนวน</th>
                    <th style="width:10%; text-align: right;"></th>
				</tr>
      <tbody id="res"></tbody>
    </table>
    </div>
    </div>
	</div><!--/ row -->
</div><!--/ container -->



<script id="check_template" type="text/x-handlebars-template">
    {{#if product}}
	<tr id="rs_{{id}}" class="checked" style="font-size:9px;">
    {{else}}
        <tr id="rs_{{id}}" class="checked" style="font-size:9px; color:red;">
    {{/if}}
		<td>{{ barcode }}</td>
		<td>{{ product }}</td>
		<td align="center">{{ timestamp }}</td>
		<td align="right"><button type="button" class="btn btn-xs btn-danger" onclick="delete_check({{id}}, '{{barcode}}')">ลบ</button></td>
	</tr>
        
</script>


<script id="unsaved_template" type="text/x-handlebars-template">
	{{#each this}}
        {{#if product}}
	<tr id="rs_{{id}}" class="checked" style="font-size:9px;">
		<td>{{ barcode }}</td>
		<td>{{ product }}</td>
		<td align="center">{{ timestamp }}</td>
		<td align="right"><button type="button" class="btn btn-xs btn-danger" onclick="delete_check({{id}}, '{{barcode}}')">ลบ</button></td>
	</tr>
        {{else}}
        <tr id="rs_{{id}}" class="checked" style="font-size:9px; color:red;">
		<td>{{ barcode }}</td>
		<td>{{ product }}</td>
		<td align="center">{{ timestamp }}</td>
		<td align="right"><button type="button" class="btn btn-xs btn-danger" onclick="delete_check({{id}}, '{{barcode}}')">ลบ</button></td>
	</tr>
        {{/if}}
	{{/each}}
</script>


<script id="saved_template" type="text/x-handlebars-template">
	{{#each this}}
	<tr id="row_{{barcode}}" class="saved" style="font-size:10px;">
		<td>{{ barcode }}</td>
		<td>{{ product }}</td>
		<td align="right" id="{{barcode}}" class="saved_qty">{{ qty }}</td>
                <td align="right" ><button type="button" class="btn btn-xs btn-warning" onClick="changeToUnsaved('{{barcode}}')">Unsaved</button></td>
	</tr>
	{{/each}}
</script>

<script src="script/checkstock.js"></script>
<script>
	$("#sc").slimScroll({ position: 'left', height : '500px', railVisible: false, alwaysVisible: true});
	$("#cs").slimScroll({ position: 'left', height : '500px', railVisible: false, alwaysVisible: true});
</script>
<script src="../library/js/beep.js"></script>
<?php else : ?>
	<center><h3>การตรวจนับถูกปิดแล้ว</h3></center>
<?php endif; ?>
