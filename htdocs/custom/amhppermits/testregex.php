<?php

include_once('include/parse.inc.php');

$obj=new stdClass();
$obj->p = "prop";
$obj->split = 'one two three four';
$obj->pick = 'one,two,three,four';

$exprs = array(
	"p", 				"prop",
	"notp", 			"notp",
	"'p'", 				"p",
	"\"p\"",			"p",
	"'f(\')'",			"f(\\",
	"1", 				"1",
	"2.5", 				"2.5",
	"0000", 			"0",
	"f()",				"F()",
	"notf()",			"notf()",
	"f(p)",				"F(prop)",
	"f( 'str' )",		"F(str)",
	"f(p , 'str')",		"F(prop,str)",
	"f(p,'str', p)",	"F(prop,str,prop)",
	"f(f())",			"F(F())",
	"f(f(f()))",		"F(F(F()))",
	"f( f(p))",			"F(F(prop))",
	"f(f(f( p)))",		"F(F(F(prop)))",
	"f(p,f(p),p)",		"F(prop,F(prop),prop)",
	"f( p ,f( p ), p )",		"F(prop,F(prop),prop)",
	"f(f(' '),f(p,f(p)),p)",	"F(F( ),F(prop,F(prop)),prop)",
	"f(p, p, f(f(p), p), f(), 'str', f('str'))",	"F(prop,prop,F(F(prop),prop),F(),str,F(str))",
	"splitwords(split, 5, 0)", "one",
	"splitwords(split, 5, 1)", "two three four",
	"pick(pick, 1)", "one",
	"pick(pick, 4)", "four",
	"money(1234.5678)", "$1,234.57",
	"money(1234.5678, TRUE)", "$1,234.57",
	"money(1234.5678, false)", "1,234.57",
	"date('2019/1/2')", "01/02/2019",
	"concat(p,split,pick)", "prop one two three four one,two,three,four",
	"concat(p,date('2019/1/2'),money('123.4'))", "prop 01/02/2019 $123.40",
	"concat('h',concat('e','l', concat('l','o')),'world')", "h e l l o world",
	"concatx('h',concatx('e','l', concatx('l','o')),' ', 'world')", "hello world",
	"when(true,1,2)", "1",
	"when('true',1,2)", "1",
	"when(false,1,2)", "2",
	"when('false',1,2)", "1",
	"when(1,1,2)", "1",
	"when('','notempty','empty')", "empty",
	"when(' ','notempty','empty')", "notempty",
 );

$errors = 0;
print "<pre>";
for ($i=0; $i < count($exprs); $i++) { 
	$expr = $exprs[$i++];
	$expectedResult = $exprs[$i];

	$regex = new ExpressionResolver();
	$result = $regex->resolve($expr, $obj, $isResolved);
	if ($result != $expectedResult) {
		$errors++;
		print "Original: ".$expr."\n";
		print " Results: ".$result."\n";
		print "Expected: ".$expectedResult."\n";
		print "\n";
	}
}
if ($errors>0)
	print "Found the above unexpected results!";
else
	print "Done!";
print "</pre>";

?>