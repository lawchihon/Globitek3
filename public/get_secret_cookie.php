<?php
  require_once('../private/initialize.php');
?>
<?php
  // Sign Cookie
  function signing_checksum($string) {
    $salt = "qi02BcXzp639"; // makes process hard to guess
    return hash('sha1', $string . $salt);
  }

  function signed_string_is_valid($signed_string) {
    $array = explode('--', $signed_string);
    // if not 2 parts it is malformed or not signed
    if(count($array) != 2) { return false; }

    $new_checksum = signing_checksum($array[0]);
    return ($new_checksum === $array[1]);
  }

  //
  const CIPHER_METHOD = 'AES-256-CBC';

  $message = $_COOKIE['scrt'];

  if (signed_string_is_valid($message)) {
    $message = substr($message,0,strpos($message, "--"));
    
    $key = 'scrt';
    
    // Needs a key of length 32 (256-bit)
    $key = str_pad($key, 32, '*');
    
    // Base64 decode before decrypting
    $iv_with_ciphertext = base64_decode($message);
      
    // Separate initialization vector and encrypted string
    $iv_length = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = substr($iv_with_ciphertext, 0, $iv_length);
    $ciphertext = substr($iv_with_ciphertext, $iv_length);
    
    // Decrypt
    $newMessage = openssl_decrypt($ciphertext, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    echo "Received: " . $newMessage;
  }
  else{
    echo "Invalid Sign";
  }

?>