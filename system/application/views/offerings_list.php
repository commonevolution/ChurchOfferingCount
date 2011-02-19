<?=$this->load->view('header')?>

<script language="javascript" src="<?=base_url()?>assets/js/jquery.jeditable.mini.js"></script>
<script language="javascript" src="<?=base_url()?>assets/js/jquery.jeditable.datepicker.js"></script>
<script>
$(document).ready(function() {
//Edit field inline functions
	$('.edit_date').editable('<?=base_url()?>offerings/update', {
		indicator	: 'Saving...',
		type		: 'datepicker',
		tooltip		: 'Click to edit...'
	});
	$('.edit_service').editable('<?=base_url()?>offerings/update', {
		loadurl		: '<?=base_url()?>services/list_dropdown',
		loadtype	: 'POST',
		type		: 'select',
		submit		: 'OK',
		indicator	: 'Saving...',
		style		: "display: inline; margin: 0; padding: 0;",
		tooltip		: 'Click to edit...'
	});
	$('.edit_offering_type').editable('<?=base_url()?>offerings/update', {
		loadurl		: '<?=base_url()?>offering_types/list_dropdown',
		loadtype	: 'POST',
		type		: 'select',
		submit		: 'OK',
		indicator	: 'Saving...',
		style		: "display: inline; margin: 0; padding: 0;",
		tooltip		: 'Click to edit...'
	});

//Delete offering function
	$(".delete").click(function() {
		var row = $(this).parents('tr:first');
		var id = $(this).attr('id');
		//var offering_id = id.split('_');
		$.ajax({
			type: "POST",
			url: "<?=base_url()?>offerings/delete/"+id,
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
					alert("Offering deleted successfully.");
				} else if( response == 'false - record' ) {
					alert("This offering is linked to records. So, you can't delete it or the world will end.");
				} else if( response == 'false - report' ) {
					alert("This offering is linked to a report. So, you can't delete it or the world will end.");
				} else {
					alert("This offering is linked to reports and records. So, you can't delete it or the world will end.");
				}
			},
			error:function(){
				// failed request; give feedback to user
				$('#ajax-panel').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
			}
			
		});
	});

});
</script>

</head>

<body>

<?=$this->load->view('main_menu')?>
<?=$this->load->view('local_menu_offerings')?>

<div class="content">

<?php if (isset($message)): ?>
 
    <div id="message_confirm"><?=$message?></div>
 
<?php endif; ?>

<?php if (isset($results)): ?>

<table class="lister" id="offerings_list" cellpadding="0" cellspacing="0">

	<tr>
		<th class="a_left">Offering Type</th>
		<th class="a_left">Service</th>
		<th class="a_left">Date</th>
		<th class="a_left"></th>
	</tr>

<?php foreach ($results as $row): ?>
 	
	<tr>
		<td id="offeringtype_<?=$row['offering_id']?>" class="edit_offering_type"><?=$row['offering_type_name']?></td>
		<td id="service_<?=$row['offering_id']?>" class="edit_service"><?=$row['service_name']?></td>
		<td id="date_<?=$row['offering_id']?>" class="edit_date"><?=$row['date']?></td>
		<td id="actions">
			<?=anchor('offerings/view/'.$row['offering_id'], 'View Records', array('title' => 'View Records for this Offering', 'class' => 'link_button'))?>
			<a href="#" class="delete link_button" id="<?=$row['offering_id']?>" title="Delete this offering">Delete</a>
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