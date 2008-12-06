<?php

function validateText($value) {
  return ereg('^.{3,}$', $value);
}

function validateNumber($value) {
  return ereg('^[0-9]{10,18}$', $value);
}

function validateEmail($value) {
  return eregi('^[a-z0-9.­_]+@[a-z0-9.-]+\.[a-z.]{2,5}$', $value);
}

?>