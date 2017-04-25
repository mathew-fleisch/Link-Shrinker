<?php
require 'flight/Flight.php';

Flight::route('/', function(){
    Flight::render('main_page.php', array());
});

Flight::start();
?>
