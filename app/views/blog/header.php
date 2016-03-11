<!DOCTYPE html>
<html lang="en-us">
<head>

<?php echo (isset($meta) ? $meta : '');?>

<?php echo(isset($title) ? "<title>$title</title>" : '')?>

<?php echo(isset($description) ? "<meta name=\"description\" content=\"$description\"> ": '');?>					

<?php echo(isset($keywords) ? "<meta name=\"keywords\" content=\"$keywords\"> ": '');?>		

</head>
<body>