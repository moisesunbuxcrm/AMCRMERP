<!-- Customization by pdermody@elementalley.com from custom module amhp\products\product_dimensions_validation.js 

Validate width, height, and length and copy to foat field

-->
<script type="text/javascript">
$(document).ready(function () 
{
	$(document.forms[1]).submit(e => {
		let failedValue = null;
		let w = $("#options_amwidthtxt").val();
		let h = $("#options_amheighttxt").val();
		let l = $("#options_amlengthtxt").val();

		try {
			if (w)
				$("#options_amwidth").val(Fraction(w).valueOf());
		}
		catch(err) {
			failedValue = "width";
		}

		try {
			if (h)
				$("#options_amheight").val(Fraction(h).valueOf());
		}
		catch(err) {
			failedValue = "height";
		}

		try {
			if (l)
				$("#options_amlength").val(Fraction(l).valueOf());
		}
		catch(err) {
			failedValue = "length";
		}

		if (failedValue) {
			e.preventDefault();
			alert("Invalid " + failedValue + ". Please try again.");
		}
	});
});
</script>
