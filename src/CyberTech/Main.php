<?php
/*
 * HungerPE (v0.0.0.1) by CyberTech++
 * Developer: CyberTech++ (Yungtechboy1)
 * Website: http://www.cybertechpp.com
 * Date: 2/16/15 9:04 PM (CST)
 * Copyright & License: (C) 2015 Cybertech++
 * All Rights Reserved
 */

namespace CyberTech;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\inventory\BaseInventory;
use pocketmine\inventory\Inventory;
use CyberTech\StartQueingTimer;

class Main extends PluginBase implements Listener{
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        //Start 10 Min Timer to Start Game If No Game is In Progress and The Minimum People is Meet.
        return true;
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){    
    $battels = $this->battels;
    switch($command->getName()){
        case "hg":
            if ($args[0] == "join"){
                $this->Encrypt($args[0], $args[1]);
            }
            return true;
        default:
            return false;

        
        
    }
    }
    
    
    /* HungerPE Player Join */
    private $queing = array();
    public function HungerPEJoin(Player $player){
        $playern = $player->getName();
        array_push($this->queing, $playern);
        //Check HungerPE for Start
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