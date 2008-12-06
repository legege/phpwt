<h1>Email</h1>

<?php
if (getTemplate()->getName() == 'failed') {
  $errorimg = '<img src="'.Toolkit::resourceURL('/media/images/error.png').'" border="0" width="16" height="16" alt="Error!" style="vertical-align: middle;" />';
  
  echo "<p>$errorimg ".(Resources::getString('email.submit.error', 'global'))."</p>\n";

  $data = getActionData();
  $reasons = $data['error-reasons'];
  
  if (count($reasons) > 0) {
    echo "<ul>\n";
    foreach($reasons as $reason) {
      echo "  <li>".Resources::getString($reason, 'global')."</li>\n";
    }
    echo "</ul>\n";
  }
}
?>

<form method="post" action="<?php echo Toolkit::pageURL('/contact/email'); ?>">

<table>
  <tbody>
    <tr>
      <th><label for="name">Name</label></th>
      <td><input type="text" name="name" id="name" size="40" maxlength="255" value="<?php echo $_POST['name']; ?>" /></td>
    </tr>
    <tr>
      <th><label for="email">Email</label></th>
      <td><input type="text" name="email" id="email" size="40" maxlength="255" value="<?php echo $_POST['email']; ?>" /></td>
    </tr>
    <tr>
      <th><label for="subject">Subject</label></th>
      <td><input type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php echo $_POST['subject']; ?>" /></td>
    </tr>
    <tr>
      <th><label for="message">Message</label></th>
      <td><textarea name="message" rows="5" cols="45"><?php echo $_POST['message']; ?></textarea></td>
    </tr>
  </tbody>
</table>

<p>
  <input type="submit" name="submit" value="Send" />
</p>

</form>