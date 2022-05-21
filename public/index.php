<?php

ini_set("log_errors", 1);
ini_set("error_log", "/app/logs/php_errors.log");
require('../vendor/autoload.php');

use Cordpuller\Discord;
use Cordpuller\interactions\ApplicationCommandInteraction;
use Cordpuller\libs\errors\DiscordException;
use Cordpuller\libs\fieldmaps\ApplicationCommandTypes;
use Cordpuller\libs\flags\MessageFlags;
use Steamlinker\Parser;

try{

    // Create instance
    $config = include('../.env.php');
    $discord = new Discord($config['APPLICATION_ID'], $config['PUBLIC_KEY'], $config['PRIVATE_KEY'], $config['TOKEN']);
    $interaction = $discord->parseRequestAsInteraction();

    if($interaction instanceof ApplicationCommandInteraction){

        if($interaction->getType() == ApplicationCommandTypes::MESSAGE){

            if($interaction->getName() === "Get Direct Link"){

                $messages = $interaction->getResolved()['messages'];
                $key = array_key_first($messages);


                error_log(json_encode($messages[$key]));
                $response_links = array();
                foreach(Parser::getSteamLinks($messages[$key]['content']) as $link){
                    if($link){
                        $response_links[] = Parser::ConvertLink($link);
                    }
                }

                $text = implode(PHP_EOL, $response_links);

                $flags = new MessageFlags("EPHEMERAL");
                $interaction->reply($text, null, $flags);

            }

        }

    }

}catch(DiscordException $ex){
    http_response_code($ex->getCode());
    die(json_encode(array(
        "error" => array(
            "code" => $ex->getCode(),
            "message" => $ex->getMessage()
        )
    )));
}