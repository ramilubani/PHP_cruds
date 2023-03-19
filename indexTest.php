<?php

require_once 'db.php';
require_once 'user.php';

$user = new User('Wael Salah', 45, 'Egypt - Cairo', 1.4, 7500);

$user->create();