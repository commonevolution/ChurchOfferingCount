<?=$this->load->view('header')?>

</head>

<body>

<?=$this->load->view('main_menu')?>
<?=$this->load->view('local_menu_reports')?>

<div class="content">

<?php if (isset($message)): ?>
 
    <div id="message_confirm"><?=$message?></div>
 
<?php endif; ?>

<?=$this->load->helper('form')?>

<?=form_open('reports/add_offerings')?>

	<div class="page_title">
		<h2>Add Offerings to Report: <?=$report['date']?></h2>
	</div>
	
	<div id="available_offerings" class="action_buttons">
		
		<h3>These Offerings are available to add</h3>
		
		<?php if ( $available > 0 ): ?>
		
		<ul id="offering_list">
		
		<?php $i=0; ?>
		<?php foreach ($available as $key => $value): ?>
			
			<li><input type='checkbox' id='item[<?=$i?>][offering_id]' name='item[<?=$i?>][offering_id]' value='<?=$value['offering_id']?>' />
			<?=nbs(2)?><?=$value['offering_name']?><?=nbs(2)?><span class='offering_date'><?=$value['offering_date']?></span></li>
		
		<?php $i++; ?>
		<?php endforeach; ?>
		
		</ul>
	
		<?=form_submit('submit', 'Add Offerings to Report')?>
		
		<?php else: ?>
		
		<p>
			There are no more Offerings within range of this Report
			<?=anchor('offerings/add', 'Add more Offerings', array('title' => 'Add Offerings'))?>
		</p>
		
		<?php endif; ?>
		
	</div>

	<div id="existing_offerings" class="action_buttons">
		<h3>These Offerings are already attached</h3>

		<ul id="offering_list">

		<?php foreach ($existing as $key => $value): ?>
			
			<li><?=$value['offering_name']?><?=nbs(2)?><span class='offering_date'><?=$value['offering_date']?></span></li>
		
		<?php endforeach; ?>

		</ul>
	</div>
	
	<input type="hidden" name="report_id" value="<?=$report['id']?>">
	
<?=form_close()?>

</div>

</body>
</html>