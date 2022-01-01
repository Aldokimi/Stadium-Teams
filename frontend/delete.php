<?php
$id = reset($_GET);
require_once(__DIR__ . "/../backend/utils/storage.inc.php");
$commentsStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/comments.json"));
$commentsStorage->delete($id);