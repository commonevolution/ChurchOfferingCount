<?=$this->load->view('header')?>

<script>
$(document).ready(function(){
//Datepicker function
	$('#date').datepicker({
		dateFormat: 'yy-mm-dd',
		onSelect:
			function() {
				var date = $('#date').val();
				if($('#offering_list li')) {
					$('#offering_list li').remove();
				} else {
				}
				$.ajax({
					dataType: 'json',
					type: 'POST',
					url: "<?=site_url('offerings/lister') ?>/"+date,
					success: function(data){
						var results = new Array();
						results = data.results;
						list_offerings(results);
						
						if(results.length<1) {
							$("#add_offerings").hide();
							$("#no_offerings").show();
						} else {
							$("#no_offerings").hide();
							$("#add_offerings").show();
						}
					}
				});
			}
	});
	
	function list_offerings(offerings) {
		$.each(offerings, function(i,offering) {
			$('#offering_list').append("<li><input type='checkbox' id='item[" + i + "][offering_id]' name='item[" + i + "][offering_id]' value='" + offering.offering_id + "' /><?=nbs(2)?>" + 
				offering.offering_name +
				"<?=nbs(2)?><span class='offering_date'>" + offering.date + "</span></li>"
			);
		});
	return false;
	}
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

<?=$this->load->helper('form')?>

<?=form_open('reports/add')?>

	<div>
		<?=form_label('Report Date', 'date')?>
		<?=form_input('date', '', 'id="date"')?>
	</div>
	
	<div id="add_offerings" class="action_buttons">
		<h3>Add Offerings to this Report</h3>

		<ul id="offering_list">
		</ul>
	
		<?=form_submit('submit', 'Add Report')?>
		
	</div>

	<div id="no_offerings" class="action_buttons">
		<h4>No offerings found within 1 week of Report date</h4>
		
		<?=form_submit('submit', 'Add Report with no Offerings')?>
	</div>

<?=form_close()?>

</div>

</body>
</html>