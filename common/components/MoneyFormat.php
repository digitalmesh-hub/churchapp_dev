<?php
namespace common\components;


use yii\base\Component;


class MoneyFormat extends Component {
	/**
	 * Function to format money
	 * @param integer $price
	 * return $thecash
	 */
	public function decimalWithComma($price) {
		$price = (number_format((float)$price, 2, '.', ''));
		$parts = explode('.', $price);
		$real = $parts[0];
		$fraction="";
		if (isset($parts[1]))
		{
			$fraction = $parts[1];
		}
		$explrestunits = "" ;
		if(strlen($real)>3) {
			$lastthree = substr($real, strlen($real)-3, strlen($real));
			$restunits = substr($real, 0, strlen($real)-3);
			$restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits;
			$expunit = str_split($restunits, 2);
			for($i=0; $i<sizeof($expunit); $i++) {
				if($i==0) {
					$explrestunits .= (int)$expunit[$i].","; 
				} else {
					$explrestunits .= $expunit[$i].",";
				}
			}
			$thecash = $explrestunits.$lastthree;
		} else {
			$thecash = $real;
		}
		$thecash .= ".".$fraction;
		return $thecash; 
	
	}

}
?>
