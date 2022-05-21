<?php

namespace Steamlinker;

class Parser {

    const STEAM_DOMAINS = array('steamcommunity.com', 'store.steampowered.com');

    public static function getSteamLinks(string $input): array {
        preg_match_all("/\bhttps?:\/\/\S+/i", $input, $matches);
        $links = array();
        foreach($matches[0] as $match){
            $data = parse_url($match);
            $data['path'] = preg_split('@/@', $data['path'], NULL, PREG_SPLIT_NO_EMPTY);
            $links[] = $data;
        }

        return $links;
    }

    public static function ConvertLink(Array $input): false|string {


        switch($input['host']){

            case 'steamcommunity.com':

                if($input['path'][0] == 'sharedfiles') {
                    if ($input['path'][1] == 'filedetails') {
                        return static::Workshop(substr($input['query'], 3));
                    }
                }

                if($input['path'][0] == 'app') {

                    if ($input['path'][1] == 'allnews') {
                        return static::GameNews($input['path'][1]);
                    }

                    return static::GameHub($input['path'][1]);
                }

                if($input['path'][0] == 'chat') {
                    return static::FriendsList();
                }

                break;

            case 'store.steampowered.com':
                if($input['path'][0] == 'app'){
                    return static::StoreApp($input['path'][1]);
                }

                if($input['path'][0] == 'publisher' || $input['path'][0] == 'sale'){
                    return static::StorePublisher($input['path'][1]);
                }

                if($input['path'][0] == 'promotion'){
                    if ($input['path'][1] == 'familysharing') {
                        return static::FamilySharing();
                    }
                }

        }

        return false;

    }

    private static function StoreApp(string $id): string {
        return "steam://store/$id";
    }

    private static function StorePublisher(string $id): string {
        return "steam://publisher/$id";
    }

    private static function GameHub(string $id): string {
        return "steam://url/GameHub/$id";
    }

    private static function GameNews(string $id): string {
        return "steam://appnews/$id";
    }

    private static function Workshop(string $id): string {
        return "steam://url/CommunityFilePage/$id";
    }

    private static function FriendsList(): string {
        return "steam://open/friends";
    }

    private static function FamilySharing(): string {
        return "steam://url/FamilySharing";
    }

}