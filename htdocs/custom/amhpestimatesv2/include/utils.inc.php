<?php
function getInitialsFor($user)
{
    $initials = '';

    $name = '';
    if ($user->firstname)
        $name = $user->firstname;
    if ($user->lastname)
    {
        if ($user->firstname)
            $name .= ' ';
        $name .= $user->lastname;
    }

    $words = preg_split("/[\s,_.-]+/", $name);
    foreach ($words as $w) {
        $initials .= $w[0];
    }

    if ($initials == '')
        $initials = 'XX';
    return strtoupper($initials);
}

function cleanTxt($txt, $trim=true)
{
	$txt = str_replace('"', '', $txt);
    $txt = preg_replace('/\s+/', ' ', $txt);
    if ($trim)
    	$txt = trim($txt);
	return $txt;
}

function cleanQuotes($txt, $trim=true)
{
    dol_syslog("before clean: ".$txt, LOG_DEBUG);
   
	$txt = str_replace('"', "&qt;", $txt);
	$txt = str_replace("\"", '\"\"', $txt);
	$txt = str_replace("\r\n", '\n', $txt);
	$txt = str_replace("\n", '\n', $txt);
    if ($trim)
    	$txt = trim($txt);

    dol_syslog("after clean: ".$txt, LOG_DEBUG);
    return $txt;
}
?>