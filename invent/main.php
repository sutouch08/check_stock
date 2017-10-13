<?php require 'function/stock_helper.php'; ?>
<div class="container">
	<div class="row">
		<div class="col-sm-12">
		<?php if(isOpenCheck()===TRUE) : ?>
			<?php $qs = dbQuery("SELECT * FROM tbl_check WHERE active = 1"); ?>
			<?php if( dbNumRows($qs) == 1) : ?>
				<?php $rs = dbFetchArray($qs); ?>
				<table class="table" style="border:solid 1px #ccc;">
					<tr>
						<td style="width:75%;"><h4><?php echo $rs['title']; ?></h4></td>
						<td style="width:25%;"><button type="button" class="btn btn-lg btn-block btn-info" onclick="doChecking(<?php echo $rs['id']; ?>)">ตรวจนับ</td>
					</tr>
				</table>
			<?php endif; ?>
		<?php else : ?>
			<center><h4>ไม่มีการตรวจนับ</h4></center>
		<?php endif; ?>
		</div>
	</div><!--/ row -->
	<script>
		function doChecking(id){
			window.location.href = "index.php?content=checkstock&id_check="+id;
		}
	</script>

</div><!--/ container -->
