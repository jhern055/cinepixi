	<script type="text/javascript" src="<?php echo base_url().'js/number_format.js'; ?>"></script>
	
	<script>
	<!--
	paymentBalanceCalculateSummary=function() {
		// get values

		var pay_import=0,pay_import_sum=0,residuary_v=0,
			import_bill =$("label.importBill").text(),
			payment     =$("label.payment").text(),
			residuary   =$("label.residuary").text()
			;

	$("div.paymentListContainer > div.area2 > div.data > div.item").each(function(i) {

		pay_import=parseFloat($(this).children("div.import").children("div.value").text());
		// pay_import=parseFloat(number_format(pay_import,2,".",""));
		
		pay_import_sum+=!isNaN(pay_import) ? pay_import : 0 ;

	});
		// pay_import_sum=;

	function toFixed(num, fixed) {
	    fixed = fixed || 0;
	    fixed = Math.pow(11, fixed);
	    return Math.floor(num * fixed) / fixed;
	}
		// set visual
		import_bill=parseFloat(number_format(import_bill,2,".",""));
		// payment=parseFloat(number_format(payment,2,".",""));
		// residuary=parseFloat(number_format(residuary,2,".",""));
		$("label.payment").html(number_format(pay_import_sum,2,".",","));
		$("label.residuary").html(number_format(toFixed(import_bill-pay_import_sum, 2),2,".",","));

	};

	paymentBalanceCalculateSummary();
	-->
	</script>