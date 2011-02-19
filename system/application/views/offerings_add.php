<?=$this->load->view('header')?>

<style>
	.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
	#city { width: 25em; }
</style>

<script language="javascript" src="<?=base_url()?>assets/js/jquery.ui.autocomplete.selectFirst.js"></script>
<script>
//This function is the person autocomplete
$(document).ready(function(){

	//Row clone function
	function addrow(destination) {
		parent_row = $( "#offerings tbody>tr:last" );
		rowcount = parseInt(parent_row.attr('id').replace('row_',''))+1;
		clonecopy = destination.clone();
		clonecopy.attr( "class", "iterable" );
		clonecopy.find('input').val('');
		
		// update numerical suffixes
		clonecopy.attr("id","row_"+rowcount);


//Clone and datepicker for the date
		clonecopy.find( "input[id^=datepicker]" )
			.attr({
				"name"	: "item["+rowcount+"][datepicker]",
				"id"	: "datepicker["+rowcount+"]",
			})
			.datepicker({
				dateFormat: "D, M d, yy",
				altFormat: "yy-mm-dd",
				altField: $(this).next( "input[id^=datevalue]" ),
				onSelect: function (){
					$(this).focusNextInputField();
				}
			});
		clonecopy.find("input[id^='datevalue']")
			.attr({
				"name"	: "item["+rowcount+"][date]",
				"id"	: "datevalue["+rowcount+"]"
			});


//Clone and autocomplete for the offering_type
		clonecopy.find("input[id^='offering_type_name']")
			.attr({
				"name"	: "item["+rowcount+"][offering_type_name]",
				"id"	: "offering_type_name["+rowcount+"]"
			})
			.autocomplete({
				source: function(req, add){
					$.ajax({
						url: '<?=site_url('offering_types/list_autocomplete')?>',
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
				selectFirst: true,
				select: function(event, ui){
					$(this).val(ui.item.value);
					$(this).next("input[id^=offering_type_id]").val(ui.item.id);
					$(this).focusNextInputField();
		
					return false;
				},
				change: function(event, ui){
					$(this).next("input[id^=offering_type_id]").val('');
					
					return false;
				}
			});
		clonecopy.find("input[id^='offering_type_id']")
			.attr({
				"name"	: "item["+rowcount+"][offering_type_id]",
				"id"	: "offering_type_id["+rowcount+"]"
			});



//Clone and autocomplete for the service
		clonecopy.find("input[id^='service_name']")
			.attr({
				"name"	: "item["+rowcount+"][service_name]",
				"id"	: "service_name["+rowcount+"]"
			})
			.autocomplete({
				source: function(req, add){
					$.ajax({
						url: '<?=site_url('services/list_autocomplete')?>',
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
				selectFirst: true,
				select: function(event, ui){
					var i = $(this).index();
					$(this).val(ui.item.value);
					$(this).next("input[id^=service_id]").val(ui.item.id);
					$(this).focusNextInputField();
	
					return false;
				},
				change: function(event, ui){
					$(this).next("input[id^=service_id]").val('');
					
					return false;
				}
			});
		clonecopy.find("input[id^='service_id']")
			.attr({
				"name"	: "item["+rowcount+"][service_id]",
				"id"	: "service_id["+rowcount+"]"
			});
	

//Adds the new row at the end of the list
		clonecopy.insertAfter(destination);
		$( "#row_0" ).remove();
		$( ".date" ).removeClass('hasDatepicker')
			.each(function(){
				$(this).datepicker({
					dateFormat: "D, M d, yy",
					altFormat: "yy-mm-dd",
					altField: $(this).next(),
					onSelect: function (){
						$(this).focusNextInputField();
					}
				});
			});
	}


	//create first row on page load
	addrow($( "#row_0" ));
	
	$( "#offerings tbody>tr:last .service" ).live( 'focus', function(){
		parent_row = $( "#offerings tbody>tr:last" );
		addrow(parent_row);
	});

	//Add row function
	$( "#add" ).click(function() {
		parent_row = $( "#offerings tbody>tr:last" );
		addrow(parent_row);
    });
	
	//Remove row function
	$( "#remove" ).live( 'click', function(){
		if ($( "tr.iterable" ).length > 1) { // disable delete on only row
			parent_row = $(this).parent().parent();
			parent_row.remove();
		} else {
			$( "#offerings" ).find( "input" ).val('');
		}
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

<?=$this->load->helper('form')?>

<?=form_open('offerings/add')?>

<table id="offerings">
	<thead>
	<tr>
		<th>Offering Date</th>
		<th>Offering Type</th>
		<th>Service</th>
	</tr>
	</thead>

	<tbody>
	<tr class="iterable" id="row_0">
		<td>
			<div class="ui-widget">
				<input type="text" id="datepicker[]" name="item[][datepicker]" class="date" />
				<input type="hidden" id="datevalue[]" name="item[][date]" />
			</div>
		</td>
	
		<td>
			<div class="ui-widget">
				<input type="text" id="offering_type_name[]" name="item[][offering_type_name]" class="offering_type" />
				<input type="hidden" id="offering_type_id[]" name="item[][offering_type_id]" />
			</div>
		</td>
	
		<td>
			<div class="ui-widget">
				<input type="text" id="service_name[]" name="item[][service_name]" class="service" />
				<input type="hidden" id="service_id[]" name="item[][service_id]" />
			</div>
		</td>
		
		<td><a href="javascript:void(0);" id="remove" class="icons icon-0">remove</a>
	</tr>
	</tbody>
</table>

<a href="javascript:void(0);" id="add" class="icons icon-new">add</a>

</table>

<div class="action_buttons">
	<?=form_submit('submit', 'Submit')?>
</div>

<?=form_close()?>

</div>

</body>
</html>