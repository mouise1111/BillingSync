<?php
sleep(10);
require_once './app/message-broker/consumers/queue_consumer.php';

// billing = invoice
echo "Starting Billinsync ...\n";

while (true) {
  consumeMessages();
}
