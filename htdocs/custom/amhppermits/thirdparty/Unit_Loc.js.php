<script type="text/javascript">
$(document).ready(function () 
{
	var unit_row = $("tr.societe_extras_unit");
	var address_row = $("tr").has("#address");
	address_row.after(unit_row);
});
</script>
