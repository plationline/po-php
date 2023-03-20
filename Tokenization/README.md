# Plati.Online Tokenization

1. You need to authorize a master tranzaction. There are 2 possible methods:
* *use card verification*, f_amount = 0. Documented in <b>card_verification.php</b> or
* *authorize a payment*, f_amount > 0. The client checks on Plati.Online payment page his option to save the card details for future payments. Documented in <b>auth.php</b>
<br/>
2. Every time you want to authorize a new payment by token you need to obtain the token using the master f_order_number and x_trans_id (from step 1). Documented in <b>query_for_token.php</b>. If you get a token, the master transaction allows you to authorize future payments using token.
<br/><br/>
3. Authorize a new payment using the token previously obtained, using 3DSecure or not
* *non3DSecure payment* - MIT payment. Documented in <b>mit_non3DS.php</b> or
* *3DSecure payment* - Documented in <b>tokenization_3DS.php</b>

