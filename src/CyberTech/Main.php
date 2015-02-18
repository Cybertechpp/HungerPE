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
        $this->loadYml();
        $this->chkConfig();
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->reloadConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info( TextFormat::GREEN . "HungerPE - Enabled!" );
        $this->db = new \SQLite3($this->getDataFolder() . "HungerPE.db");// This logs payer wins and VIP players
        //Start 10 Min Timer to Start Game If No Game is In Progress and The Minimum People is Meet.
        if(!$this->getConfig()->get("ENABLED")) { // If user chooses to enable plugin or not!
            $this->getPluginLoader()->disablePlugin($this);
            return true;
        }
        return true;
    }
    /* HungerPE Commands */
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){    
        $battels = $this->battels;
        switch($command->getName()){
            case "hg":
                if ($args[0] == "join"){
                   //$this->JoinHG($args[0], $args[1]);
                    $this->CopyHGWorld("1");
                }
                if ($args[0] == "list"){
                    //List HG
                }
                return true;
                default:
                    return false;
        }
    }
    
    private $queing = array();
    public function JoinHG(Player $player, $game){
        $playern = $player->getName();
        array_push($this->queing[$game], $playern);
        //Check For Start
    }
    
    private $gamestats;
    public function CheckHGStart($arena) {
        //10 Players Are Queing For the arean and arena is not in progress
        if (($this->HGS['Minimum-Players'] == count($this->queing[$arena])) && ($this->gamestats[$arena] == 'stopped')){
            //Clear Inv and TP to HG
            //
            //Start HG
        }
    }
    
    public function PlayerPrepraeForStart($arena) {
        $base = $this->queing[$arena];
        foreach ($base as $p){
            $player = $this->getServer()->getPlayerExact($p);
            if ($player instanceof Player){
                $playern = $player->getName();
                
            }
        }
    }
    
    public function CopyHGWorld($arena){
        $path = $this->getServer()->getDataPath();
        $world = $this->HGW[($this->HGID[$arena])];
        $prefix = $this->HGS['HG-World-Prefix'];
        $topath = $path."/worlds/".$world;
        $frompath = $path."/worlds/".$prefix.$world;
        if (copy($frompath, $topath)){
            //Sucess
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
                foreach($this->getServer()->getLevels() as $w) {
                    if($w->getName() == $lobby || $w->getName() == $hgworld) {
                        return true;
                    }
                    $this->initAlert($w->getName(), $alert);
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
                foreach($this->getServer()->getLevels() as $w) {
                    if($w->getName() == $lobby || $w->getName() == $hgworld) {
                        return false;
                    }
                    $this->initAlert($w->getName(), $alert);
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
        private $HGS;
        private $HGW;
        private $HGID;
}
