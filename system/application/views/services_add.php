<?=$this->load->view('header')?>

</head>

<body>

<?=$this->load->view('main_menu')?>
<?=$this->load->view('local_menu_services')?>

<div class="content">

<?php if (isset($message)): ?>
 
    <div id="message_confirm"><?=$message?></div>
 
<?php endif; ?>

<?=$this->load->helper('form')?>

<?=form_open('services/add')?>

<div>
	<?=form_label('Service Name', 'name')?>
	<?=form_input('name', '', 'id="name"')?>
</div>

<div class="action_buttons">
	<?=form_submit('submit', 'Add Service')?>
</div>

<?=form_close()?>

</div>

</body>
</html>