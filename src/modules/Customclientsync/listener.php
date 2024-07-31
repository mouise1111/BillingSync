<?php

require_once 'index.php';

use FossBilling\Modules\CustomClientSync\index;

$sync = new CustomClientSync();
$sync->receiveClientData();

