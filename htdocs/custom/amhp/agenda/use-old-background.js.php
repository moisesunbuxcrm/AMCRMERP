<!-- Customization by pdermody@elementalley.com from custom module amhp\agenda\use-old-background.js.php 

In the Month view, apply left border color to whole event background like Dolibarr did in v12

-->
<script type="text/javascript">
	$(document).ready(function () 
	{
		let events = $("table.cal_event")
		events.each(function(index, e) {
			e.style.background = e.style.borderLeftColor
		})
	});
</script>
