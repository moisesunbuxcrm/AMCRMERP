<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\amreltype_edit.js.php 

In create or edit customer form, move the mobile/secondary phone just below the primary phone

-->
<script type="text/javascript">
$(document).ready(function () 
{
	var menu = $("div.menu_contenu").has("a[title='List of Customers'],a[title='New Customer']");
	//var to_menu = $("div.menu_contenu").has("a[title='List'][href*='societe/list.php']");
	var to_menu = $("div.menu_titre").has("a[title='Third-party']");
	to_menu.before(menu);
});
</script>
