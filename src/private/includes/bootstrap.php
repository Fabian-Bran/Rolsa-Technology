<?php
// Tenp functions
//TODO add actual logic
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_logged_in() {
  return false;
}

function csrf_field() {
  return true;
}
?>