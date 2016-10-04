	<script type="text/javascript" src="<?php echo base_url().'js/number_format.js'; ?>"></script>
	
	<script>
	<!--
	detailsCalculateSummary=function() {
		// totals global

		// get values

		var quantity=0, total_sub=0, tax_ieps=0, tax_iva=0, tax_iva_retained=0, tax_isr=0, total=0,
			quantity_tmp=null, discount_tmp=null, tax_ieps_tmp=null, tax_iva_tmp=null, tax_iva_retained_tmp=null, tax_isr_tmp=null, total_sub_tmp=null;

	$("div.articleListContainer > div.area2 > div.data > div.item").each(function(i) {

		var cond=$(this).find("div.quantity > :input:eq(0)").length;

		// ...

		quantity_tmp=parseFloat( !cond ? $(this).children("div.quantity").text() : $(this).find("div.quantity > :input:eq(0)").val(),10 );
		total_sub_tmp=parseFloat( $(this).children("div.totalSub").text() );
		tax_ieps_tmp=parseFloat( $(this).children("div.taxIeps").text() );
		tax_iva_tmp=parseFloat( $(this).children("div.taxIva").text() );
		tax_iva_retained_tmp=parseFloat( $(this).children("div.taxIvaRetained").text() );
		tax_isr_tmp=parseFloat( $(this).children("div.taxIsr").text() );

		quantity+=!isNaN(quantity_tmp) ? quantity_tmp : 0 ;
		total_sub+=!isNaN(total_sub_tmp) ? total_sub_tmp : 0 ;
		tax_ieps+=!isNaN(tax_ieps_tmp) ? tax_ieps_tmp : 0 ;
		tax_iva+=!isNaN(tax_iva_tmp) ? tax_iva_tmp : 0 ;
		tax_iva_retained+=!isNaN(tax_iva_retained_tmp) ? tax_iva_retained_tmp : 0 ;
		tax_isr+=!isNaN(tax_isr_tmp) ? tax_isr_tmp : 0 ;

	});

function toFixed(num, fixed) {
    fixed = fixed || 0;
    fixed = Math.pow(10, fixed);
    return Math.floor(num * fixed) / fixed;
}
		// additional calculation

		quantity=parseFloat(number_format(quantity,2,".",""));
		total_sub=parseFloat(number_format(total_sub,2,".",""));

		tax_ieps=parseFloat(number_format(tax_ieps,2,".","")); /* individual tax should preserve all decimals ( at most 2 ) */
		tax_iva=parseFloat(number_format(tax_iva,2,".","")); /* individual tax should preserve all decimals ( at most 2 ) */
		tax_iva_retained=parseFloat(number_format(tax_iva_retained,2,".","")); /* individual tax should preserve all decimals ( at most 2 ) */
		tax_isr=parseFloat(number_format(tax_isr,2,".","")); /* individual tax should preserve all decimals ( at most 2 ) */

		total=((total_sub+tax_ieps+tax_iva)-tax_iva_retained)-tax_isr;
		total=parseFloat(number_format(total,2,".",""));
	
		// set visual

		var el=$("div.articleListSummaryContainer > div.area2").get(0);

		$(el).find("div.quantity > span.number").html( quantity );
		$(el).find("div.totalSub > span.number").html( number_format(toFixed(total_sub,2),2,".",",") );
		$(el).children("div.taxIeps").css("display",( !tax_ieps ? "none" : "block" )).children("span.number").html( number_format(toFixed(tax_ieps,2),2,".",",") );
		$(el).find("div.taxIva > span.number").html(number_format(toFixed(tax_iva, 2),2,'.',','));
		$(el).children("div.taxIvaRetained").css("display",( !tax_iva_retained ? "none" : "block" )).children("span.number").html( number_format(toFixed(tax_iva_retained,2),2,".",",") );
		$(el).children("div.taxIsr").css("display",( !tax_isr ? "none" : "block" )).children("span.number").html( number_format(toFixed(tax_isr,2),2,".",",") );
		$(el).find("div.total > span.number").html(number_format(toFixed(total, 2),2,".",","));

	};

	detailsCalculateSummary();

	-->
	</script>