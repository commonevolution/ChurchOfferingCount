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
		parent_row = $('#records tbody>tr:last');
		rowcount = parseInt(parent_row.attr('id').replace('row_',''))+1;
		clonecopy = destination.clone();
		clonecopy.attr("class","iterable");
		clonecopy.find('input').val('');
		
		// update numerical suffixes
		clonecopy.attr("id","row_"+rowcount);


//Clone and autocomplete for the person
		clonecopy.find("input[id^='person_name']")
			.attr({
				"name"	: "item["+rowcount+"][person_name]",
				"id"	: "person_name["+rowcount+"]"
			})
			.autocomplete({
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
				//selectFirst: true,
				select: function(event, ui){
					var i = $(this).index();
					$(this).val(ui.item.value);
					$(this).next("input[id^=person_id]").val(ui.item.id);
					$(this).focusNextInputField();
					
					return false;
				},
				change: function(event, ui){
					$(this).next("input[id^=person_id]").val('');
					
					return false;
				}
			});
		clonecopy.find("input[id^='person_id']")
			.attr({
				"name"	: "item["+rowcount+"][person_id]",
				"id"	: "person_id["+rowcount+"]"
			});




<?php if( !$offering ): ?>
//Clone and autocomplete for the offering
		clonecopy.find("input[id^='offering_name']")
			.attr({
				"name"	: "item["+rowcount+"][offering_name]",
				"id"	: "offering_name["+rowcount+"]"
			})
			.autocomplete({
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
				minLength: 1,
				delay: 0,
				selectFirst: true,
				select: function(event, ui){
					var i = $(this).index();
					$(this).val(ui.item.value);
					//$(this).val(ui.item.date);
					$(this).next("input[id^=offering_id]").val(ui.item.id);
					$(this).focusNextInputField();
					
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.value + "<br><span class='autocomplete_offering_date'>" + item.date + "</span></a>" )
						.appendTo( ul );
			};
		clonecopy.find("input[id^='offering_id']")
			.attr({
				"name"	: "item["+rowcount+"][offering_id]",
				"id"	: "offering_id["+rowcount+"]"
			});
<? endif; ?>
		


//Clone and autocomplete for the method
		clonecopy.find("input[id^='method_name']")
			.attr({
				"name"	: "item["+rowcount+"][method_name]",
				"id"	: "method_name["+rowcount+"]"
			})
			.autocomplete({
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
				selectFirst: true,
				select: function(event, ui){
					var i = $(this).index();
					$(this).val(ui.item.value);
					$(this).next("input[id^=method_id]").val(ui.item.id);
					$(this).focusNextInputField();
					
					return false;
				}
			});
		clonecopy.find("input[id^='method_id']")
			.attr({
				"name"	: "item["+rowcount+"][method_id]",
				"id"	: "method_id["+rowcount+"]"
			});

		
//Clone for the amount
		clonecopy.find("input[id^='amount']")
			.attr({
				"name"	: "item["+rowcount+"][amount]",
				"id"	: "amount["+rowcount+"]"
			})
			//Restrict characters in Amount
			.numeric({
				allow: "."
			});

//Adds the new row at the end of the list
		clonecopy.insertAfter(destination);
		$( "#row_0" ).remove();
	}


	//create first row on page load
	addrow($( "#row_0" ));
	//addrow($( "#row_1" ));
	
	$( "#records tbody>tr:last .amount" ).live( 'focus', function(){
		parent_row = $( "#records tbody>tr:last" );
		addrow(parent_row);
	});

	//Add row function
	$( "#add" ).click(function() {
		parent_row = $( "#records tbody>tr:last" );
		addrow(parent_row);
    });
	
	//Remove row function
	$( "#remove" ).live( 'click', function(){
		if ($( "tr.iterable" ).length > 1) { // disable delete on only row
			parent_row = $(this).parent().parent();
			parent_row.remove();
		} else {
			$( "#records" ).find( "input" ).val('');
		}
    });

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

<?php if( isset($offering['name']) ): ?>

	<h2>Offering Name: <?=$offering['name']?> (<?=$offering['date']?>)</h2>
	
<?php endif; ?>

<?=$this->load->helper('form')?>

<?=form_open('records/add')?>

<table id="records">
<thead>
	<tr>
		<th>Person</th>
	<?php if( !$offering ): ?>
		<th>Offering</th>
	<?php endif; ?>	
		<th>Method</th>
		<th>Amount ($)</th>
	</tr>
</thead>
<tbody>
	<tr class="iterable" id="row_0">
		<td>
			<div class="ui-widget">
				<input type="text" id="person_name[1]" name="item[1][person_name]" class="person" />
				<input type="hidden" id="person_id[1]" name="item[1][person_id]" />
			</div>
		</td>
	
	<?php if( !$offering ): ?>
		<td>
			<div class="ui-widget">
				<input type="text" id="offering_name[1]" name="item[1][offering_name]" class="offering" />
				<input type="hidden" id="offering_id[1]" name="item[1][offering_id]" />
			</div>
		</td>
	<?php endif; ?>
	
		<td>
			<div class="ui-widget">
				<input type="text" id="method_name[1]" name="item[1][method_name]" class="method" />
				<input type="hidden" id="method_id[1]" name="item[1][method_id]" />
			</div>
		</td>
		
		<td>
			<input type="text" id="amount[1]" name="item[1][amount]" class="amount" />
		</td>
		<td><a href="javascript:void(0);" id="remove" class="icons icon-0">remove</a>
	</tr>
</tbody>
</table>

<a href="javascript:void(0);" id="add" class="icons icon-new">add</a>


<?php if( isset($offering['id']) ): ?>
	<input type="hidden" id="offering_id" name="offering_id" value="<?=$offering['id']?>" />
<?php endif; ?>

<div class="action_buttons">
	<?=form_submit('submit', 'Submit')?>
</div>

<?=form_close()?>

</div>

</body>
</html>