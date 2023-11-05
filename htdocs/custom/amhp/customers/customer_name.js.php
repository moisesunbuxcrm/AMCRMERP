<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\customer_name.js.php 

In the form to create a new Customer, type some text into the Third Party Name, when focus is moved to a 
different field, a popup will open allowing you to see existing customers with the same or similar name

-->
<script type="text/javascript">
$(document).ready(function () 
{
	$( "input#name" ).change(function() {		
		if ($("input#name").val() != "")
			$.ajax({
				url: "<?php echo DOL_URL_ROOT; ?>/custom/amhp/customers/search.php?q="+$("input#name").val(),
				dataType: 'json'
			}).then(function(data) {
				if (data.length == 0)
					return;
				
				var table = $($.parseHTML($("#ea_found_customers_table").html())); // Makes a copy to start with a clean table
				for (var i = 0, len = data.length; i < len; i++) {
					// alert(JSON.stringify(data[i]));
					var rowtemplate = $($.parseHTML($("#ea_found_customers_rowtemplate").html()));					
					rowtemplate.find('span[class="ea_replace_span"]').replaceWith(function(){
						return data[i][$(this).attr("id")];
					});				
					rowtemplate.find('[class*="ea_replace_text"]').html(function(j, html){
						for(var p in data[i])
						{
							var reStr = "\\$\\{"+p+"\\}";
							var re = new RegExp(reStr, "gi");
							html=html.replace(re,data[i][p]);
						}
						return html;
					});
					table.append(rowtemplate);
				}
				
				$("#ea_found_customers_popup").html(table);
				$("#ea_found_customers_popup").dialog({
					closeOnEscape: true,
					resizable: true,
					modal: true,
					width: 800,
					height:500,
					title: "Found similar customers..."
				});
			});
	}); 
});

if (typeof ea === 'undefined') ea = {};
ea.copyFromCustomer = function(custId)
{
	$("#ea_found_customers_popup").dialog('close');
	$.ajax({
		url: "<?php echo DOL_URL_ROOT; ?>/custom/amhp/customers/getcustomer.php?id="+custId,
		dataType: 'json'
	}).then(function(data) {
		if (data.length != 1)
			return;
		
		$("#name").val(data[0]["nom"]);
		$("#name_alias_input").val(data[0]["name_alias"]);
		$("#customerprospect").val(1).change(); // Customer
		$("#fournisseur").val(0).change(); // Not supplier
		$("#address").val(data[0]["address"]);
		$("#zipcode").val(data[0]["zip"]);
		$("#town").val(data[0]["town"]);
		//$("#selectcountry_id").val(data[0]["fk_pays"]).change();
		$("#state_id").val(data[0]["fk_departement"]).change();
		$("#email").val(data[0]["email"]);
		$("#url").val(data[0]["url"]);
		$("#skype").val(data[0]["skype"]);
		$("#phone").val(data[0]["phone"]);
		$("#fax").val(data[0]["fax"]);
		$("#idprof1").val(data[0]["siren"]);
		$("#typent_id").val(data[0]["fk_typent"]).change();
		$("#capital").val(data[0]["capital"]);
		$("#default_lang").val(data[0]["default_lang"]).change();
		$("#effectif_id").val(data[0]["fk_effectif"]).change();
	});
}

</script>

<div id="ea_found_customers_popup" style="display: none;"></div>
<script id="ea_found_customers_table" type="text/x-custom-template">
	<table class="tagtable liste">
		<tbody>
			<tr class="liste_titre">
				<th class="liste_titre">Customer name</th>
				<th class="liste_titre">Customer code</th>
				<th class="liste_titre">Address</th>
				<th class="liste_titre">City</th>
				<th class="liste_titre">Zip</th>
				<th class="liste_titre">Phone</th>
				<th class="liste_titre">Email</th>
				<th class="liste_titre" align="center">Status</th>
				<th class="liste_titre" align="center">Actions</th>
			</tr>
		</tbody>
	</table>
</script>
<script id="ea_found_customers_rowtemplate" type="text/x-custom-template">
	<tr class="oddeven">
		<td class="tdoverflowmax200 ea_replace_text"><a href="<?php echo DOL_URL_ROOT; ?>/societe/card.php?socid=${rowid}&amp;save_lastsearch_values=1" class="classfortooltip"><img src="<?php echo DOL_URL_ROOT; ?>/theme/eldy/img/object_company.png" alt="" class="classfortooltip valigntextbottom"></a> <a href="<?php echo DOL_URL_ROOT; ?>/societe/card.php?socid=${rowid}&amp;save_lastsearch_values=1" class="classfortooltip"><span class="ea_replace_span" id="nom"></a></td>
		<td class="nowrap"><span class="ea_replace_span" id="code_client"></span></td>
		<td><span class="ea_replace_span" id="address"></span></td>
		<td class="nowrap"><span class="ea_replace_span" id="town"></span></td>
		<td class="nowrap"><span class="ea_replace_span" id="zip"></span></td>
		<td class="nowrap"><span class="ea_replace_span" id="phone"></span></td>
		<td class="nowrap"><span class="ea_replace_span" id="email"></span></td>
		<td align="center" class="nowrap"><span class="ea_replace_span" id="statusIcon"></span></td>
		<td align="center" class="nowrap ea_replace_text">
			<a href="<?php echo DOL_URL_ROOT; ?>/societe/card.php?socid=${rowid}&amp;save_lastsearch_values=1" class="classfortooltip"><img src="<?php echo DOL_URL_ROOT; ?>/theme/eldy/img/edit.png" alt="" title="Edit" class="classfortooltip valigntextbottom"></a>
			<a href="javascript:ea.copyFromCustomer(${rowid})" class="classfortooltip"><img src="<?php echo DOL_URL_ROOT; ?>/theme/eldy/img/filenew.png" alt="" title="Copy" class="classfortooltip valigntextbottom"></a>
			</td>
	</tr>
</script>