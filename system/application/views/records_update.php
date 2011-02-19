<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
<link rel="stylesheet" href="<?=base_url()?>assets/css/base.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="<?=base_url()?>assets/css/smoothness/jquery-ui-1.8.6.css" type="text/css" media="screen"/>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script language="javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js"></script>
<script language="javascript" src="<?=base_url()?>assets/js/jquery.alphanumeric.pack.js"></script>

<style>
	.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
	#city { width: 25em; }
</style>
<script>
//This function is the person autocomplete
$(document).ready(function(){
	$("input[id^=person_name]").autocomplete({
		source: function(req, add){
			$.ajax({
				url: '<?=site_url('people/list_autocomplete')?>',
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data){
					if(data.response =='true'){
					   add(data.message);
					}
				}
			});
		},
		minLength: 3,
		delay: 0,
		select: function(event, ui){
			var i = $(this).index();
			$(this).val(ui.item.value);
			$(this).next("input[id^=person_id]").val(ui.item.id);
			
			return false;
		}
	});
})


//This function is the offering autocomplete
$(document).ready(function(){
	$("input[id^=offering_name]").autocomplete({
		source: function(req, add){
			$.ajax({
				url: '<?=site_url('offerings/list_autocomplete')?>',
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data){
					if(data.response =='true'){
					   add(data.message);
					}
				}
			});
		},
		minLength: 2,
		delay: 0,
		select: function(event, ui){
			var i = $(this).index();
			$(this).val(ui.item.value);
			$(this).next("input[id^=offering_id]").val(ui.item.id);
			
			return false;
		}
	});
})


//This function is the method autocomplete
$(document).ready(function(){
	$("input[id^=method_name]").autocomplete({
		source: function(req, add){
			$.ajax({
				url: '<?=site_url('methods/list_autocomplete')?>',
				dataType: 'json',
				type: 'POST',
				data: req,
				success: function(data){
					if(data.response =='true'){
					   add(data.message);
					}
				}
			});
		},
		minLength: 1,
		delay: 0,
		select: function(event, ui){
			var i = $(this).index();
			$(this).val(ui.item.value);
			$(this).next("input[id^=method_id]").val(ui.item.id);
			
			return false;
		}
	});
})
</script>	


<script>
//Restrict characters in Amount
$(function(){
	$('.amount').numeric({allow:"."});
});
</script>

</head>

<body>

<?php if (isset($message)): ?>
 
    <div id="message_confirm"><?=$message?></div>
 
<?php endif; ?>

<?=$this->load->helper('form')?>

<?php echo form_open('records/update/'.$record_id); ?>
  
<table>
	<tr>
		<td>Person</td>
		<td>Offering</td>
		<td>Method</td>
		<td>Amount ($)</td>
	</tr>

	<tr>
		<td>
			<div class="ui-widget">
				<?=form_input('', $record['firstname'].$record['lastname'], 'id="person_name"')?>
				<input type="hidden" id="person_id[<?=$i?>]" name="record[<?=$i?>][person_id]" />
			</div>
		</td>
	
		<td>
			<div class="ui-widget">
				<?=form_input('', $record['offering_'], 'id="offering_name"')?>
				<input type="hidden" id="offering_id" name="record[offering_id]" />
			</div>
		</td>
		
		<td>
			<div class="ui-widget">
				<?=form_input('', '', 'id="method_name"')?>
				<input type="hidden" id="method_id" name="record[method_id]" />
			</div>
		</td>
		
		<td><?=form_input('record[amount]', '', 'class="amount"')?></td>
	</tr>

	<tr>
		<td><input type="hidden" id="record_id" name="record[record_id]" value="<?=$record_id?>" /></td>
		<td><?=form_submit('submit', 'Submit')?></td>
	</tr>
	
	<?=form_close()?>

</body>
</html>