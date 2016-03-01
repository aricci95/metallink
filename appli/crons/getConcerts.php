<?php
        $headers = 'From: "MetalLink"<contact.metallink@gmail.com>' . "\n";
        $headers .='Reply-To: contact.metallink@gmail.com' . "\n";
        $headers .='Content-Type: text/html; charset="utf-8"' . "\n";
        $headers .='Content-Transfer-Encoding: 8bit';


        mail('aricci95@gmail.com', 'cron get concert bootstrap', 'try to launch', $headers);

 header('Location: http://www.metallink.fr/cron/getConcerts');
 exit();
?>
