<?=$this->load->view('header')?>

<script language="javascript" src="<?=base_url()?>assets/js/jquery.jeditable.mini.js"></script>
<script>
$(document).ready(function() {
	$('.edit_amount').editable('<?=base_url()?>records/update', {
		indicator	: 'Saving...',
		style		: "display: inline; margin: 0; padding: 0;",
		tooltip		: 'Click to edit...'
	});
	$('.edit_method').editable('<?=base_url()?>records/update', {
		loadurl		: '<?=base_url()?>methods/list_dropdown',
		loadtype	: 'POST',
		type		: 'select',
		submit		: 'OK',
		indicator	: 'Saving...',
		style		: "display: inline; margin: 0; padding: 0;",
		tooltip		: 'Click to edit...'
	});

//Delete record function
	$(".delete").click(function() {
		var row = $(this).parents('tr:first');
		var id = $(this).attr('id');
		//var record_id = id.split('_');
		$.ajax({
			type: "POST",
			url: "<?=base_url()?>records/delete/"+id,
			//data: "id="+ id,
			beforeSend:function(){
				// this is where we append a loading image
				$('#ajax-panel').html('<div class="loading"><img src="<?=base_url()?>assets/img/loading.gif" alt="Loading..." /></div>');
			},
			success: function(response){
				// successful request; do something with the data
				$('#ajax-panel').empty();
				
				//Process response
				if( response == 'true' ) {
					$(row).slideUp(6000);
					$(row).remove();
					alert("Record deleted successfully.");
				} else {
					alert("This record is linked to an archived report. So, you can't delete it or the world will end.");
				}
			},
			error:function(){
				// failed request; give feedback to user
				$('#ajax-panel').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
			}
			
		});
	});
});

//Restrict characters in Amount
$(function(){
	$('.edit_amount').numeric({allow:"."});
});
</script>

</head>

<body>

<?=$this->load->view('main_menu')?>
<?=$this->load->view('local_menu_records')?>

<div class="content">

<?php if (isset($message)): ?>
 
    <div id="message_confirm"><?=$message?></div>
 
<?php endif; ?>

<?php if (isset($results)): ?>

<table class="lister" id="records_list" cellpadding="0" cellspacing="0">

	<tr>
		<th class="a_left">Person Name</th>
		<th class="a_left">Offering</th>
		<th class="a_left">Giving Method</th>
		<th class="a_left">Amount</th>
		<th class="a_left"></th>
	</tr>

<?php foreach ($results as $row): ?>

	<tr>
		<td id="person"><?=$row['first_name']?> <?=$row['last_name']?></td>
		<td id="offering"><?=$row['offering_type_name']?> <?=$row['service_name']?> (<?=$row['offering_date']?>)</td>
		<td id="method_<?=$row['record_id']?>" class="edit_method"><?=$row['method_name']?></td>
		<td id="amount_<?=$row['record_id']?>" class="edit_amount"><?=$row['amount']?></td>
		<td id="actions">
			<a href="#" class="delete link_button" id="<?=$row['record_id']?>" title="Delete this record">Delete</a>
		</td>
	</tr>

<?php endforeach; ?>

</table>

<?php else: ?>

    <h2>Nothing found</h2>
 
<?php endif; ?>

</div>

</body>
</html>