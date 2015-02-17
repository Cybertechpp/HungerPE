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
use pocketmine\plugin\PluginPharLoader;
use pocketmine\plugin\PluginLoader;
use CyberTech\StartQueingTimer;

class Main extends PluginBase implements Listener{
    
    
    public $gameState = 0;
 
    public function onEnable() {
        $this->chkConfig();
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->reloadConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info( TextFormat::GREEN . "HungerPE - Enabled!" );
        $this->db = new \SQLite3($this->getDataFolder() . "HungerPE.db");// This logs payer wins and VIP players
        //Start 10 Min Timer to Start Game If No Game is In Progress and The Minimum People is Meet.
        return true;
    }
    /* HungerPE Commands */
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
    public function chkConfig() {
        $hgworld = $this->getConfig()->get("HG-WORLDS");
        $lobby = $this->getConfig()->get("LOBBY-WORLD");
        $spawn = $this->getConfig()->get("SPAWN-WORLD");
        $msg = $this->getConfig()->get("JOIN-MESSAGE");
        if($hgworld == null) {
            $this->getLogger()->info( TextFormat::GREEN . "ERROR - Configuration Error!" );
            $this->getPluginLoader()->disablePlugin($this);
            return true;
        }
        if($lobby == null) {
            $this->getLogger()->info( TextFormat::GREEN . "ERROR - Configuration Error!" );
            $this->getPluginLoader()->disablePlugin($this);
            return true;
        }
        if($spawn == null) {
            $this->getLogger()->info( TextFormat::GREEN . "ERROR - Configuration Error!" );
            $this->getPluginLoader()->disablePlugin($this);
            return true;
        }
        if($msg == null) {
            $this->getLogger()->info( TextFormat::GREEN . "ERROR - Configuration Error!" );
            $this->getPluginLoader()->disablePlugin($this);
            return true;
        }
    }
    /* On Player Join */
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $alert = $event->getJoinMessage();
        $currworld = $player->getLevel();
        $hgworld = $this->getConfig()->get("HG-WORLDS");
        $lobby = $this->getConfig()->get("LOBBY-WORLD");
        $spawn = $this->getConfig()->get("SPAWN-WORLD");
        $msg = $this->getConfig()->get("JOIN-MESSAGE");
        $event->setJoinMessage("");
        if($this->gameState == 0) {
            if($currworld == $hgworld) {
                $player->teleport($this->getServer()->getLevelByName($spawn)->getSpawnLocation(), 0, 0);
                $this->initAlert($spawn, $alert);
                if($msg == false) {
                    return true;
                }
                $player->sendMessage($msg);   
                return true;
            }
            if($currworld == $lobby) {
                $player->teleport($this->getServer()->getLevelByName($spawn)->getSpawnLocation(), 0, 0);
                $this->initAlert($spawn, $alert);
                if($msg == false) {
                    return true;
                }
                $player->sendMessage($msg);   
                return true;
            }else{
                foreach($this->getServer()->getLevels()->getName() as $w) {
                    if($w == $lobby || $w == $hgworld) {
                        return false;
                    }
                    $this->initAlert($w, $alert);
                }
                if($msg == false) {
                    return true;
                }
                $player->sendMessage($msg);   
                return true;
            }
        }
        if($this->gameState == 1) {
            if($currworld == $hgworld) {
                $player->teleport($this->getServer()->getLevelByName($spawn)->getSpawnLocation(), 0, 0);
                $this->initAlert($spawn, $alert);
                if($msg == false) {
                    return true;
                }
                $player->sendMessage($msg);   
                return true;
            }
            if($currworld == $lobby) {
                $player->teleport($this->getServer()->getLevelByName($spawn)->getSpawnLocation(), 0, 0);
                $this->initAlert($spawn, $alert);
                if($msg == false) {
                    return true;
                }
                $player->sendMessage($msg);   
                return true;
            }else{
                foreach($this->getServer()->getLevels()->getName() as $w) {
                    if($w == $lobby || $w == $hgworld) {
                        return false;
                    }
                    $this->initAlert($w, $alert);
                }
                if($msg == false) {
                    return true;
                }
                $player->sendMessage($msg);   
                return true;
            }
        }
        
    }
    /* Alert Seperator */
    public function initAlert($world, $message){
    	if($this->getServer()->getLevelByName($world)){
            foreach($this->getServer()->getLevelByName($world)->getPlayers() as $players){
    		$players->sendMessage($message);
            }
            return true;
    	}else{
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
}
