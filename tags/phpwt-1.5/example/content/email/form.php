<?php
require_once 'form.inc.php';

/**
 * Send the email
 */
function send() {
  $message = stripslashes($_POST['message']);
  $name = stripslashes($_POST['name']);
  $subject = stripslashes($_POST['subject']);
  $message = <<<END
$message
-- 
$name
END;
  $from = "From: ".stripslashes($_POST['email'])."\n";
  $from .= "Content-type: text/plain; charset=utf-8";
  @mail('example@example.com', $subject, $message, $from);
}

if (!$_POST['submit']) {
}
else {
  $reasons = array();

  if (!validateText($_POST['name'])) {
    array_push($reasons, 'email.field.name.error');
  }

  if (!validateEmail($_POST['email'])) {
    array_push($reasons, 'email.field.email.error');
  }

  if (!validateText($_POST['subject'])) {
    array_push($reasons, 'email.field.subject.error');
  }

  if (!validateText($_POST['message'])) {
    array_push($reasons, 'email.field.message.error');
  }

  if (count($reasons) > 0) {
    $data = array('error-reasons' => $reasons);
    return array('template' => 'failed', 'result' => $data);
  }
  else {
    send();
    return array('template' => 'success');
  }
}

?>