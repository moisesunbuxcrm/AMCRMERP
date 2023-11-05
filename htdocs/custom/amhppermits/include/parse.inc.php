<?php
/**
 * A simple expression parser for use in creating building department permit application PDFs. Works fine as long as there are no syntax errors in the input.
 * 
 * Parses the expressions:
 * 
 * - 'a string literal' or number
 * - fieldname
 * - splitwords(expr, index, segment) - splits expr in segments of max index characters long on word boundaries, and returns the given segment
 * - pick(expr, index) - treats expr like a comma-seperated-values list and picks the value at the given index
 * - pick2(expr, separator, index) - sames as pick() but allows other separators
 * - money(expr [,showDollarSign]) - Formats the expr as money. Shows dollar sign by default.
 * - moneyv(expr [,showDollarSign]) - Same as money() but displays nothing if the expr is zero
 * - date(expr) - Formats the field as a "m/d/Y" date.
 * - concat(expr1, expr2, ...) - Concatenates the expressions separated by spaces. Each expression must be a field name or a literal value surrounded by apostrophe's or another function call 
 * - concatx(expr1, expr2, ...) - same as concat() but without spaces
 * - when(expr1, expr2 [, expr3]) - If expr1 is a non empty string, return expr2, otherwise expr3
 * - equals(expr12, expr2) - Compares first 2 arguments
 * - contains(expr12, expr2) - Checks if first string contains the second string
 * - char(expr1, index) - extracts the character at the given index
 * - substr(text, start, end) - Get substring of a value
 * - monthpart(date) - Picks month part from a date
 * - datepart(date) - Picks date part from a date
 * - yearpart(date) - Picks year part from a date
 * - upper(text) - displays the given text in uppercase
 * - phone_areacode(phone) - Displays the area code of the given phone number
 * - phone_local(phone) - Displays the given phone number without the areacode
 * - phone(phone) - Displays the full phone number formated as NNN-NNN-NNNN
 * - checkbox(expr1, expr2) - Draws a checkmark if expr2 is a substring of expr1
 * - raise_when_too_long(text) - Tries to first reduce font size and if that does not work, raises text a little so it is above labels in PDF
 * - wrap(text) - Draws text but allows it to wrap
 * - oval(a, b) - Draws an oval if the first and second parameters are equal
 */
class ExpressionResolver
{
	var $pdf;
	var $fieldInfo;

	/**
	 * Executes the function call(s) in the expression relative to the given object returns the results
	 */
	function resolve($pdf, $fieldInfo, $expr, $obj)
	{
		$this->pdf = $pdf;
		$this->fieldInfo = $fieldInfo;
		$isResolved = $this->_consumeExpression($expr, $obj, $result);
		if (!$isResolved)
			$this->pdf->writeErrorText($fieldInfo->rowid.":".trim($fieldInfo->fieldname), $fieldInfo->x, $fieldInfo->y, $fieldInfo->w, $fieldInfo->h, 0);
		else if (!$result->isRendered)
			$this->pdf->writeFieldInfoValue($fieldInfo, $result->expr, 0);
	}

	function _consumeExpression($expr, $obj, &$result) 
	{
		$expr = trim($expr);

		// Check for number literal
		if ($this->_consumeNumberLiteral($expr, $r)) {
			$result = $r;
			return true;
		}

		// Check for string literal
		if ($this->_consumeStringLiteral($expr, $r)) {
			$result = $r;
			return true;
		}

		// Check for boolean literal
		if ($this->_consumeBooleanLiteral($expr, $r)) {
			$result = $r;
			return true;
		}

		//Check for function call
		if ($this->_consumeFunction($expr, $obj, $r)) {
			$result = $r;
			return true;
		}

		// Check for property
		if ($this->_consumeProperty($expr, $obj, $r)) {
			$result = $r;
			return true;
		}

		return false;
	}

	/**
	 * Checks if the next part of the $expr contains a numeric literal. If so it returns true. If not it returns false.
	 * The $result parameter will contain an instance of the ConsumeResult class
	 */
	function _consumeNumberLiteral($expr, &$result) {
		if (!preg_match("/^([0-9.]+)/", $expr, $matches))
			return false;
		$result = new ConsumeResult((float) $matches[1], strlen($matches[1]), false);
		return true;
	}

	/**
	 * Checks if the next part of the $expr contains a boolean literal. If so it returns true. If not it returns false.
	 * The $result parameter will contain an instance of the ConsumeResult class
	 */
	function _consumeBooleanLiteral($expr, &$result) {
		if (!preg_match("/^(true|false)/i", $expr, $matches))
			return false;
		$result = new ConsumeResult(strtolower($matches[1])==="true", strlen($matches[1]), false);
		return true;
	}

	/**
	 * Checks if the next part of the $expr contains a string literal. If so it returns true. If not it returns false.
	 * The $result parameter will contain an instance of the ConsumeResult class
	 */
	function _consumeStringLiteral($expr, &$result) {
		if (preg_match("/^['](.*?)[']/", $expr, $matches)) {
			$result = new ConsumeResult($matches[1], strlen($matches[1])+2, false);
			return true;
		}
		else if (preg_match("/^[\"](.*?)[\"]/", $expr, $matches)) {
			$result = new ConsumeResult($matches[1], strlen($matches[1])+2, false);
			return true;
		}
		return false;
	}

	/**
	 * Checks if the next part of the $expr contains a property name. If so it returns true. If not it returns false.
	 * The $result parameter will contain an instance of the ConsumeResult class
	 */
	function _consumeProperty($expr, $obj, &$result) {
		if (!preg_match("/^(\w+)/", $expr, $matches))
			return false;
		if (!property_exists($obj, $matches[1]))
			return false;

		$prop = $matches[1];
		$result = new ConsumeResult($obj->$prop, strlen($matches[1]), false);
		return true;
	}

	/**
	 * Checks if the next part of the $expr contains a property name. If so it returns true. If not it returns false.
	 * The $result parameter will contain an instance of the ConsumeResult class
	 */
	function _consumeFunction($expr, $obj, &$result) {
		if (!preg_match("/^(\w+)/", $expr, $matches))
			return false;
		
		$nextIndex = strlen($matches[1]);
		$paren = trim(substr($expr, $nextIndex, 1));
		if ($paren !== "(")
			return false;

		$fname = $matches[1];
		if (!$this->_isFunction($fname) && !$this->_isRenderingFunction($fname))
			return false;

		// Consume expressions and commas until we get to a close parenthesis
		$params = array();
		$nextIndex++; // Skip open parens
		
		while (strlen($expr)>$nextIndex) {
			$nextIndex = $this->_skipWS($expr, $nextIndex);
			if (substr($expr,$nextIndex,1) === ")")
				break;

			if (!$this->_consumeExpression(substr($expr, $nextIndex), $obj, $r))
				return false;
			$params[] = $r->expr;
			$nextIndex = $nextIndex+$r->nextIndex;

			$nextIndex = $this->_skipWS($expr, $nextIndex);
			if (substr($expr,$nextIndex,1) === ",") {
				$nextIndex++;
				$nextIndex = $this->_skipWS($expr, $nextIndex);
				// We are going to ignore any trailing comma in the arguments
			}
		}

		if (substr($expr,$nextIndex,1) !== ")")
			return false;

		$nextIndex++;
		$returnValue = -1;
		if ($this->_isFunction($fname)) {
			$returnValue = $this->$fname($obj, $params);
			$result = new ConsumeResult($returnValue, $nextIndex, false);
			return ($returnValue !== -1);
		}
		
		if ($this->_isRenderingFunction($fname)) {
			$fname = "render_".$fname;
			$returnValue = $this->$fname($obj, $params);
			$result = new ConsumeResult($returnValue, $nextIndex, true);
			return ($returnValue !== -1);
		}

		return false;
	}

	function _isFunction($fname) {
		return method_exists($this, $fname);
	}

	function _isRenderingFunction($fname) {
		return method_exists($this, "render_".$fname);
	}

	function _skipWS($expr, $index) {
		$max = strlen($expr);
		while($index<$max && substr($expr, $index, 1) === " ")
			$index++;
		return $index;
	}

	/**
	 * Splits field on word boundary (if possible) 
	 * 
	 * Syntax: splitwords(fieldname, index, segment) - index indicates after how many characters to split the string. Segment 
     * indicates which segment to print after splitting the data. The first segment has index 0.
	 */
	function splitwords($obj, $args) {
		$value = $args[0];
		$splitIndex = (int) $args[1];
		$segmentIndex = (int) $args[2];

		// Check if value shorter than split index
		if (strlen($value) <= $splitIndex) {
			if ($segmentIndex == 0)
                return $value;
            else
                return "";
		}
		
		// Check if already on a word boundary
		else if ($value[$splitIndex-1] == ' ' || $value[$splitIndex] == ' ') {
			if ($segmentIndex == 0)
				return trim(substr($value, 0, $splitIndex));
			else
				return trim(substr($value, $splitIndex));
		}
		else {
			$leftvalue = substr($value, 0, $splitIndex);
			$rightvalue = substr($value, $splitIndex);
			// Check if there is a space in the left hand side to split the value at
			$indexOfSpace = strrpos($leftvalue, ' ', -1);
			if ($indexOfSpace === false) {
				// Use a hyphen
				if ($segmentIndex == 0)
					return trim($leftvalue).'-';
				else
					return trim($rightvalue);
			}
			else {
				if ($segmentIndex == 0)
					return substr($leftvalue, 0, $indexOfSpace);
				else
					return substr($leftvalue, $indexOfSpace+1).$rightvalue;
			}
		}

		return -1;
	}

    /**
	 * Picks an indexed value from a comma-separated list of values
	 * 
	 * Syntax: pick(fieldname, index) - treats fieldname like a comma-seperated-values list and picks the value at the given index. 
     * Unlike the splitwords() function, in this case the first index is 1.
	 */
	function pick($obj, $args) {
		$value = $args[0];
		$pickIndex = $args[1];
		$valueArray = explode(",",$value);
		$value = $valueArray[$pickIndex-1];

		return $value;
    }

    /**
	 * Picks an indexed value from a list of values
	 * 
	 * Syntax: pick2(fieldname, separator, index) - treats fieldname like a list of values separated by the given separator and picks the value at the given index. 
     * Unlike the splitwords() function, in this case the first index is 1.
	 */
	function pick2($obj, $args) {
		$value = $args[0];
		$separator = $args[1];
		$pickIndex = $args[2];
		$valueArray = explode($separator,$value);
		if ($pickIndex<=count($valueArray))
			$value = $valueArray[$pickIndex-1];

		return $value;
    }

	/**
	 * Formats the field as money
	 * 
	 * Syntax: money(fieldname, showDollarSign) - Formats the field as money. Shows dollar sign by default.
	 */
	public function money($obj, $args) {
		$value = $args[0];
		if (count($args)>1 && $args[1]===false)
            $currency = '';
        else
            $currency = "$";
        return $currency.number_format($value, 2);
	}

	/**
	 * Formats the field as money or displays nothing if amount is 0
	 * 
	 * Syntax: moneyv(fieldname, showDollarSign) - Formats the field as money. Shows dollar sign by default. Shows nothing if 0
	 */
	public function moneyv($obj, $args) {
		$value = $args[0];
		if (count($args)>1 && $args[1]===false)
            $currency = '';
        else
            $currency = "$";
		if ((int)$value === 0)
			return "";
        return $currency.number_format($value, 2);
	}

	/**
	 * Formats the field as a date
	 * 
	 * Syntax: date(fieldname) - Formats the field as a date.
	 */
	function date($obj, $args) {
		$value = $args[0];

		if (is_string($value)) 
		{
			if ($value=="")
				return "";

			$a = date_parse_from_format("n/j/Y", $value);
			$ts = mktime( $a['hour'],$a['minute'], $a['second'], $a['month'], $a['day'], $a['year']);
			$d = date("m/d/Y", $ts);
			return $d;
		}

		if (((int)$value)>0)
			return date("m/d/Y", $value);
		return "";
	}

	/**
	 * Concatenates the parameters separated by spaces
	 * 
	 * concat(expr1, expr2, ...) - Contatenates the expressions separated by spaces.
	 */
	function concat($obj, $args) {
		$value = "";
		foreach ($args as $p) { 
			if ($value != "")
                $value = $value . " ";
            $value .= trim($p);
		}
		return $value;
	}

    /**
	 * Concatenates the parameters without spaces or trimming - exactly as they are
	 * 
	 * concatx(expr1, expr2, ...) - Contatenates the expressions.
	 */
	function concatx($obj, $args) {
		$value = "";
		foreach ($args as $p) { 
            $value .= $p;
		}
		return $value;
	}

    /**
	 * If the first parameter is true, or a non-empty string, then returns the second parameter, otherwise returns the last parameter or an empty string.
	 * 
	 * when(expr1, expr2, expr3).
	 */
	function when($obj, $args) {
        $value = "";
		if ($args[0]===true || (!is_numeric($args[0]) && strlen($args[0])>0) || (is_numeric($args[0]) && ((float)$args[0])>0))
            $value = $args[1];
        else if (count($args)==3)
            $value = $args[2];

		return $value;
	}

	/**
	 * Compares first 2 arguments
	 */
	function equals($obj, $args) {
		return ($args[0]==$args[1]);
	}

	/**
	 * Checks if first string contains the second string
	 */
	function contains($obj, $args) {
		return strpos($args[0], $args[1]) !== false;
	}

	/**
	 * Picks one character from value
	 */
	function char($obj, $args) {
		$value = $args[0];
		$index = $args[1];
		$value = substr($value,$index,1)." ";
		return $value;
	}

	/**
	 * Get substring of a value
	 */
	function substr($obj, $args) {
		$value = $args[0];
		$start = $args[1];
		if (count($args)>2) {
			$end = $args[2];
			$value = substr($value,$start,$end);
		}
		else
			$value = substr($value,$start);
		return $value;
	}

	/**
	 * Picks month part from a date
	 */
	function monthpart($obj, $args) {
		$value = $args[0];

		if (((int)$value)>0)
			return strtoupper(date("F", $value));
	}

	/**
	 * Picks date part from a date
	 */
	function datepart($obj, $args) {
		$value = $args[0];

		if (((int)$value)>0)
			return date("j", $value);
	}

	/**
	 * Picks year part from a date
	 */
	function yearpart($obj, $args) {
		$value = $args[0];

		if (((int)$value)>0)
			return date("Y", $value);
	}

	/**
	 * Converts argument to uppercase
	 */
	function upper($obj, $args) {
		$value = $args[0];
		$value = strtoupper($value);
		return $value;
	}

	/**
	 * Displays the area code of the given phone number
	 */
	function phone_areacode($obj, $args) {
		$phone_number = $args[0];
		$phone_number = preg_replace('/[^[:digit:]]/', '', $phone_number);
		if (strlen($phone_number) == 11)
			return substr($phone_number, 1, 3); // Skip the 1 prefix
		return substr($phone_number, 0, 3);
	}

	/**
	 * Displays the given phone number without the areacode
	 */
	function phone_local($obj, $args) {
		$phone_number = $args[0];
		$phone_number = preg_replace('/[^[:digit:]]/', '', $phone_number);
		if (strlen($phone_number) == 11)
			$phone_number = substr($phone_number, 4); // Skip the 1 prefix and area code
		else
			$phone_number = substr($phone_number, 3); // Skip the area code

		return substr($phone_number, 0, 3) . "-" . substr($phone_number, 3);
	}

	/**
	 * Displays the full phone number formated as NNN-NNN-NNNN
	 */
	function phone($obj, $args) {
		$phone_number = $args[0];

		// extact digits
		$digits = preg_replace('/[^[:digit:]]/', '', $phone_number);

		// remove extra digits
        $digits = substr($digits,0,10);

		if ($digits != "" && strlen($digits) < 10)
			$digits = substr("0000000000",0,digits.length) + digits;

		return substr($digits, 0, 3) . "-" . substr($digits, 3, 3) . "-" . substr($digits, 6);
	}

    /**
	 * Draws a checkmark if the first string argument contains the second string argument
	 * This is a rendering function - it renders its own result
	 * 
	 * Syntax: checkmark(expr1, expr2) - draws a checkmark if expr2 is a substring of expr1.
	 */
	public function render_checkbox($obj,$args) {
		$propval = isset($args[0]) ? $args[0] : "1";
		$chkval = isset($args[1]) ? $args[1] : "1";
		
		if ($this->pdf->displayNameOnly)
			$this->pdf->writeErrorText($this->fieldInfo->rowid.":".substr($chkval,0,10), $this->fieldInfo->x, $this->fieldInfo->y, $this->fieldInfo->w, $this->fieldInfo->h);
		else
		{	
			if ($chkval === "1" || strtolower($chkval) === "true")
				$isChecked = $propval === "1" || $propval === 1 || $propval === true;
			else if ($chkval === "0" || strtolower($chkval) === "false")
				$isChecked = $propval === "0" || $propval === 0 || $propval === false; // Note: null in the DB never matches
			else
				$isChecked = strstr($propval,$chkval)!==false;
		
			if ($isChecked)
				$this->pdf->writeCheckmark($this->fieldInfo->x, $this->fieldInfo->y, $this->fieldInfo->w, $this->fieldInfo->h);
		}

		return 0;
	}

	/**
	 * Tries to first reduce font size and if that does not work, raises text a little so it is above labels in PDF
	 */
	public function render_raise_when_too_long($obj,$args)
	{
		$value = $args[0];

		$oldFontSize = $this->pdf->getFontSizePt();

		// Check size
		// If too big reduce font and check size
		if ($this->pdf->GetStringWidth($value) > $this->fieldInfo->w) {
			$this->pdf->SetFontSize($oldFontSize*0.75);
			$this->fieldInfo->y = $this->fieldInfo->y - $this->fieldInfo->h*0.1;
		}

		// If still too big raise text by 50% of height of bounding box
		if ($this->pdf->GetStringWidth($value) > $this->fieldInfo->w)
			$this->fieldInfo->y = $this->fieldInfo->y - $this->fieldInfo->h*0.5;

		$this->pdf->writeFieldInfoValue($this->fieldInfo, $value, 0);
		$this->pdf->SetFontSize($oldFontSize);

		return 0;
	}

    /**
	 * Draws text but allows it to wrap
	 */
	public function render_wrap($obj,$args)
	{
		$value = $args[0];

		$this->pdf->writeFieldInfoValue($this->fieldInfo, $value, 0, true);

		return 0;
	}

    /**
	 * Draws an oval if the first and second parameters are equal
	 */
	public function render_oval($obj,$args) {
		$a = isset($args[0]) ? $args[0] : "1";
		$b = isset($args[1]) ? $args[1] : "1";

		if ($this->pdf->displayNameOnly)
			$this->pdf->writeErrorText($this->fieldInfo->rowid.":".substr($chkval,0,10), $this->fieldInfo->x, $this->fieldInfo->y, $this->fieldInfo->w, $this->fieldInfo->h);
		else if ($a == $b)
			$this->pdf->writeOval($this->fieldInfo->x, $this->fieldInfo->y, $this->fieldInfo->w, $this->fieldInfo->h);

		return 0;
	}
}

/**
 * Used internally to track tokens in parsed expression
 */
class ConsumeResult {
	var $isRendered;
	var $expr;
	var $nextIndex;

	function __construct($expr, $nextIndex, $isRendered) {
		$this->isRendered = $isRendered;
		$this->expr = $expr;
		$this->nextIndex = $nextIndex;
	}
}

?>