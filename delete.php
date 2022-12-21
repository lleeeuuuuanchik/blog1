<?php
$forAuth = true;
include 'includes/core.php';

$post = getPost($_GET['id']);

if(hasAccess($post)) {
    $db->query("DELETE FROM posts WHERE id = " . $post['id']);
}

redirect('index.php');
