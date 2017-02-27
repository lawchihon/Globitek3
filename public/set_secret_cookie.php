<?php
  require_once('../private/initialize.php');
?>
<?php
  // Use PHP's OpenSSL functions to encrypt data with a key.
  const CIPHER_METHOD = 'AES-256-CBC';

  $plaintext = 'I have a secret to tell.';
  $key = 'scrt';

  // Needs a key of length 32 (256-bit)
  $key = str_pad($key, 32, '*');

  // Create an initialization vector which randomizes the
  // initial settings of the algorithm, making it harder to decrypt.
  // Start by finding the correct size of an initialization vector 
  // for this cipher method.
  $iv_length = openssl_cipher_iv_length(CIPHER_METHOD);
  $iv = openssl_random_pseudo_bytes($iv_length);

  // Encrypt
  $encrypted = openssl_encrypt($plaintext, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);

  // Return $iv at front of string, need it for decoding
  $message = $iv . $encrypted;
  
  // Encode just ensures encrypted characters are viewable/savable
  $newMessage = base64_encode($message);

  // Sign Cookie
  function signing_checksum($string) {
    $salt = "qi02BcXzp639"; // makes process hard to guess
    return hash('sha1', $string . $salt);
  }
  
  function sign_string($string) {
    return $string . '--' . signing_checksum($string);
  }

  // Set Secret Cookie
  setcookie('scrt', sign_string($newMessage));
?>

<a href="get_secret_cookie.php" type="button" >Click to Get Secret</a>