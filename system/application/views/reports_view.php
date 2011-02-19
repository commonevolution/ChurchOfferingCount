<?=$this->load->view('header')?>

</head>

<body>

<?=$this->load->view('main_menu')?>
<?=$this->load->view('local_menu_reports')?>

<div class="content">

<?php if (isset($message)): ?>
 
    <div id="message_confirm"><?=$message?></div>
 
<?php endif; ?>

<?php if (isset($report)): ?>

<div class="page_title">
	<h2>Summary for Report: <?=$report['date']?></h2>
	<div class="page_subtitle">
		<?=anchor('reports/add_offerings/' . $report['report_id'], 'Add more Offerings', array('title' => 'Add Offerings to this Report'))?>
	</div>
</div>

<table class="summary" id="offering_summary" cellpadding="0" cellspacing="0">

	<tr>
		<th class="left_header">Offering</th>
		<th>Offering Total</th>
		<th>Deposit Total</th>
		<th>Cash</th>
		<th>Checks</th>
		<th>Credit Total</th>
		<th>Amex</th>
		<th>Visa</th>
		<th>Mastercard</th>
		<th>Discover</th>
<!--		<th>Reconciled</th>-->
	</tr>

<?php foreach ($offerings as $key => $value): ?>

	<tr>
		<td class="left_header">
			<?=anchor('offerings/view/' . $value['offering_id'], $value['offering_name'], array('title' => 'View records for this offering', 'class' => 'offering_name'))?><?=nbs(4)?><br>
			<span class="offering_date"><?=$value['offering_date']?></span>
		</td>
	
		<td class="offering_total"><?=$value['offering_total']?></td>
		<td class="deposit_subtotal"><?=$value['deposit_total']?></td>
		<td class="deposit"><?=$value['cash']?></td>
		<td class="deposit"><?=$value['checks']?></td>
		<td class="credit_subtotal"><?=$value['credit_total']?></td>
		<td class="credit"><?=$value['amex']?></td>
		<td class="credit"><?=$value['visa']?></td>
		<td class="credit"><?=$value['mastercard']?></td>
		<td class="credit"><?=$value['discover']?></td>
<!--		<td class="reconciled"></td>-->
	
	</tr>

<?php endforeach; ?>
	
</table>

<table class="summary" id="offering_totals" cellpadding="0" cellspacing="0">
	<tr>
		<th class="header" colspan="2">Report Totals</th>
	</tr>
	<tr>
		<th class="left_header">Credit Grand Total</td>
		<td><?=$totals['credit_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Deposit Total</td>
		<td><?=$totals['deposit_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Report Grand Total</td>
		<td><?=$totals['report_grand_total']?></td>
	</tr>
</table>

<table class="summary" id="offering_breakdowns" cellpadding="0" cellspacing="0">
	<tr>
		<th class="header" colspan="2">Offering Breakdown</th>
	</tr>
	<tr>
		<th class="left_header">Love</td>
		<td><?=$totals['love_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Tithe</td>
		<td><?=$totals['tithe_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Kingdom Builders</td>
		<td><?=$totals['kb_grand_total']?></td>
	</tr>
</table>

<table class="summary" id="method_breakdowns" cellpadding="0" cellspacing="0">
	<tr>
		<th class="header" colspan="2">Giving Methods Breakdown</th>
	</tr>
	<tr>
		<th class="left_header">Cash</td>
		<td><?=$totals['cash_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Checks</td>
		<td><?=$totals['checks_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Amex</td>
		<td><?=$totals['amex_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Visa</td>
		<td><?=$totals['visa_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Mastercard</td>
		<td><?=$totals['mastercard_grand_total']?></td>
	</tr>
	<tr>
		<th class="left_header">Discover</td>
		<td><?=$totals['discover_grand_total']?></td>
	</tr>
</table>

<?php else: ?>

    <h2>Report is invalid</h2>
 
<?php endif; ?>

</div>

</body>
</html>