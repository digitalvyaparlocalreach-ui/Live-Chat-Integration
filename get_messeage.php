<?php
header('Content-Type: text/plain');

$log_file = 'messages.txt';

if (file_exists($log_file)) {
    echo file_get_contents($log_file);
} else {
    echo '';
}
?>
