<?php
/*
 * Encryption - Decryption (v0.0.0.1) by CyberTech++
 * Developer: CyberTech++ (Yungtechboy1)
 * Website: http://www.cybertechpp.com
 * Date: 2/16/15 9:04 PM (CST)
 * Copyright & License: (C) 2015 Cybertech++
 * All Rights Reserved
 */

namespace CyberTech;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Main extends PluginBase implements Listener{
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        return true;
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){    
    $battels = $this->battels;
    switch($command->getName()){
        case "encrypt":
            if (isset($args[0]) && isset($args[1])){
                $this->Encrypt($args[0], $args[1]);
            }
            return true;
        case "decrypt":
            if (isset($args[0]) && isset($args[1])){
                $this->Encrypt($args[0], $args[1]);
            }
            return true;
        default:
            return false;

        
        
    }
    }
    
public function Encrypt($Decrypt , $passphrase){
        $filen = explode("/", $Decrypt);
        $filename = $filen[((count($filen)*1)-1)];
        //$passphrase = 'My secret';

        /* Turn a human readable passphrase
        * into a reproducable iv/key pair
        */
        $iv = substr(md5('iv'.$passphrase, true), 0, 8);
        $key = substr(md5('pass1'.$passphrase, true) . 
                   md5('pass2'.$passphrase, true), 0, 24);
        $opts = array('iv'=>$iv, 'key'=>$key);

        $fp = fopen($filename.'.enc', 'wb');
        stream_filter_append($fp, 'mcrypt.tripledes', STREAM_FILTER_WRITE, $opts);
        $wp = fopen('$e', 'rwb');
        $result = fread($wp,  5000);
        fwrite($fp, $result);
        fclose($fp);
        echo $result;
        
        return True;

}

public function Decrypt($Decrypt , $passphrase){
    //$Decrypt = array("plugins/Nett/src/CyberTech/DailyCheck.php");
    foreach($Decrypt as $d){
        //$passphrase = 'My secret';
        $iv = substr(md5('iv'.$passphrase, true), 0, 8);
        $key = substr(md5('pass1'.$passphrase, true) . 
               md5('pass2'.$passphrase, true), 0, 24);
        $opts = array('iv'=>$iv, 'key'=>$key);

        $fp = fopen($d, 'rb');
        stream_filter_append($fp, 'mdecrypt.tripledes', STREAM_FILTER_READ, $opts);
        $data = rtrim(stream_get_contents($fp));
        fclose($fp);

        return True;
    }
    
}
    }