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

?>