<?php
    $_POST['module'] = 'delete';
    $_POST['id'] = $_GET['id'];
    include_once('crud_query.php');
    header('Location: index.php');
?>