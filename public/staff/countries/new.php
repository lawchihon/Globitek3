<?php
require_once('../../../private/initialize.php');

// Set default values for all variables the page needs.
$errors = array();
$country = array(
  'name' => '',
  'code' => ''
);

if(is_post_request()) {
  if (!request_is_same_domain()) {
    $errors[] = "Request from different domain.";
  }
  if (!csrf_token_is_valid()) {
    $errors[] = "Invalid CSRF tokens.";
  }

  if (empty($errors)) {
    // Confirm that values are present before accessing them.
    if(isset($_POST['name'])) { $country['name'] = $_POST['name']; }
    if(isset($_POST['code'])) { $country['code'] = $_POST['code']; }
  
    $result = insert_country($country);
    if($result === true) {
      $new_id = db_insert_id($db);
      redirect_to('show.php?id=' . $new_id);
    } else {
      $errors = $result;
    }
  }
}
?>
<?php $page_title = 'Staff: New Country'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="main-content">
  <a href="index.php">Back to Countries List</a><br />

  <h1>New Country</h1>

  <?php echo display_errors($errors); ?>

  <form action="new.php" method="post">
    <?php echo csrf_token_tag(); ?>
    Name:<br />
    <input type="text" name="name" value="<?php echo h($country['name']); ?>" /><br />
    Code:<br />
    <input type="text" name="code" value="<?php echo h($country['code']); ?>" /><br />
    <br />
    <input type="submit" name="submit" value="Create"  />
  </form>

</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
