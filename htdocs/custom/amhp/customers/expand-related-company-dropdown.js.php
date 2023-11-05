<!-- Customization by pdermody@elementalley.com from custom module amhp\customers\expand-related-company-dropdown.js.php 

Add phone numbers to related company dropdown

-->
<script type="text/javascript">
$(document).ready(function () 
{
	$("select#socid option").each((i,o) => {
		const $o = $(o);
		const n = window.companynumbers[$o.val()];
		if (n) {
			let s = "";
			if (n.phone)
				s += n.phone;
			if (n.mobilephone) {
				if (s.length > 0)
					s+= ", ";
				s += n.mobilephone;
			}

			if (s.length > 0) {
				s = " ["+s+"]"
				$o.text($o.text() + s)
			}
		}
	})
});
</script>

<?php
require_once '../../main.inc.php';

function numbers_map()
{
	global $db;
	$data = array();

	$sql = "SELECT";
	$sql .= " s.rowid, s.phone, sef.mobilephone";
	$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
	$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."societe_extrafields as sef ON s.rowid = sef.fk_object";
	$resql=$db->query($sql);
	if ($resql)
	{
		while ($row = $db->fetch_object($resql))
		{
			if ($row->phone != null || $row->mobilephone != null)
			{
				$nums = array();
				if ($row->phone != null) $nums['phone'] = $row->phone;
				if ($row->mobilephone != null) $nums['mobilephone'] = $row->mobilephone;
				$data[$row->rowid] = $nums;
			}
		}
		$db->free($resql);
	}

	return $data;
}

print '<script type="text/javascript">';
print 'window.companynumbers = '.json_encode(numbers_map()) . ';';
print '</script>';
?>
