<?php

function calPMT($amount, $months, $interest_rate)
{
	$interest_rate /= 1200;
	$amount = $interest_rate * -$amount * pow((1 + $interest_rate), $months) / (1 - pow((1 + $interest_rate), $months));
	return round($amount, 2);
}

if (!empty($_POST['submit'])) {
	$months = array(60, 36, 24);
	echo '<h4>Calcul rate lunare:</h4>';
	foreach ($months as $month) {
		echo '<p><b>' . $month . ' rate: </b>' . calPMT((float)$_POST['amount'], (int)$month, (float)$_POST['interest_rate']) . ' RON</p>';
	}
	echo '<hr/>';
}

?>

<form method="post">
    <p>
        <label for="amount">Suma</label>
        <input name="amount" type="number" step="0.01" min="0.01" value="6245.17"/> RON
    </p>
    <p>
        <label for="interest_rate">Dobanda anuala</label>
        <input name="interest_rate" type="number" step="0.01" min="0.01" value="14.99"/> %
    </p>
    <br/>
    <input type="submit" name="submit" value="Calculeaza rata lunara"/>
</form>
