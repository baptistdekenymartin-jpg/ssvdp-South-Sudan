<?php
header('Location: album.php' . (isset($_GET['id']) ? '?id=' . (int) $_GET['id'] : ''));
exit;
