<?php

require('../vendor/autoload.php');

use Cordpuller\Discord;
use Cordpuller\libs\builder\CommandBuilder;
use Cordpuller\libs\fieldmaps\ApplicationCommandTypes;

$config = include('../.env.php');

if(!isset($_POST['authorization']) || $_POST['authorization'] !== $config['REGISTER_PASSWORD']){
    http_response_code(403);
    die('Forbidden');
}


// Create instance
$discord = new Discord($config['APPLICATION_ID'], $config['PUBLIC_KEY'], $config['PRIVATE_KEY'], $config['TOKEN']);

$app = $discord->getCurrentApplication();
echo '<h1>' . $app->getName() . ' <img src="' . $app->getIconURL() . '" style="width: 25px; border-radius: 100%"/></h1><br>';

$link_app_command = new CommandBuilder();
$link_app_command
    ->setType(ApplicationCommandTypes::MESSAGE)
    ->setName("Get Direct Link")
    ->addLocalizedName("nl", "Directe Link ophalen");

$discord->registerCommand($link_app_command);