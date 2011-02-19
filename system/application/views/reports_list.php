<?=$this->load->view('header')?>

<script language="javascript" src="<?=base_url()?>assets/js/jquery.jeditable.mini.js"></script>
<script language="javascript" src="<?=base_url()?>assets/js/jquery.jeditable.datepicker.js"></script>
<script>
$(document).ready(function() {
//Edit field inline functions
	$('.edit_date').editable('<?=base_url()?>reports/update', {
		indicator	: 'Saving...',
		type		: 'datepicker',
		tooltip		: 'Click to edit...'
	});

//Delete report function
	$(".delete").click(function() {
		var row = $(this).parents('tr:first');
		var id = $(this).attr('id');
		//var report_id = id.split('_');
		$.ajax({
			type: "POST",
			url: "<?=base_url()?>reports/delete/"+id,
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
					alert("Report deleted successfully.");
				} else {
					alert("This report is already linked to offerings. So, you can't delete it or the world will end.");
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
<?=$this->load->view('local_menu_reports')?>

<div class="content">

<?php if (isset($message)): ?>
 
    <div id="message_confirm"><?=$message?></div>
 
<?php endif; ?>

<?php if (isset($results)): ?>

<table class="lister" id="reports_list" cellpadding="0" cellspacing="0">

	<tr>
		<th class="a_left">Report Date</th>
		<th class="a_left"></th>
	</tr>

<?php foreach ($results as $row): ?>
 	
	<tr>
		<td id="date_<?=$row['report_id']?>" class="edit_date"><?=$row['date']?></td>
		<td id="actions">
			<?=anchor('reports/view/'.$row['report_id'], 'View', array('title' => 'View this report', 'class' => 'link_button'))?>
			<a href="#" class="delete link_button" id="<?=$row['report_id']?>" title="Delete this report">Delete</a>
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