<?php
$plainPassword = 'LabasNeatspesi';
$hashedPassword =  password_hash($plainPassword, PASSWORD_ARGON2ID);
$matched = sodium_crypto_pwhash_str_verify($hashedPassword, $plainPassword);


echo $hashedPassword. "\n";
if ( $matched ) {
    echo "Matched \n";
}
else {
    echo "Not Matched \n";
}