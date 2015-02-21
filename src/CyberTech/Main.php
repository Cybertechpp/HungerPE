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
use pocketmine\level\Explosion;
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
use CyberTech\StartArena;



class Main extends PluginBase implements Listener{
    
    
    public $gameState = 0;
    public $config;
    public function onEnable() {
        $temp = new Config('Config.yml'. Config::YAML);
        $this->config = $temp->getAll();
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
    
    public function OnDisable(){
        foreach ($this->worldsopen as $w){
            $path = $this->getServer()->getDataPath();
            $topath = $path."worlds/".$w."/";
            echo $topath;
            $this->delete_directory($topath);
        }
    }
    
    /* HungerPE Commands */
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        switch($command->getName()){
            case "hg":
                if ($args[0] == "join"){
                   $this->JoinHG($sender, $args[1]);
                }
                if ($args[0] == "list"){
                    //List HG
                }
                
                if ($args[0] == "open"){
                    return true;
                }
            default:
                return false;
        }
    }
    
    public $queing = array();
    public function JoinHG(Player $player, $game){
        /*if (count($this->queing[$game]) == 0){
            //Start Timer
        }*/
        $playern = $player->getName();
        if (!in_array($game, $this->queing)){
            $this->queing[$game] = array();
            //array_push($this->queing, array($game));
        }
        
        $this->queing[$game][] = "$playern";
        print_r($this->queing);
        //array_push($this->queing[$game], array("$playern"));
        $this->CheckHGStart($game);
        //Check For Start
    }
    
    public $gamestats;
    public function CheckHGStart($arena) {
        //10 Players Are Queing For the arean and arena is not in progress
        $other = new StartArena ( $this );
        $other->ThaPrepareArenaForStart($this, $arena);
        if (($this->getConfig()->get('Minimum-Players') <= count($this->queing[$arena])) && ($this->gamestats[$arena] == 'stopped')){
            //Clear Inv and TP to HG
            //
            //Start HG
            
            //$this->getServer()->getPluginManager()->callEvent($other);
            
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
    
    public $worldsopen = array();


public function StartGameTimer() {
    $task = new StartGameTimer ($this);
    $time = 2 * 1200;
    $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask($task, $time, $time);
}


    public function chkConfig() {
        $hgworld = $this->getConfig()->get("HG-Worlds");
        $lobby = $this->getConfig()->get("Lobby-World");
        $spawn = $this->getConfig()->get("Spawn-World");
        $msg = $this->getConfig()->get("Join-Message");
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
        $hgworld = $this->getConfig()->get("HG-Worlds");
        $lobby = $this->getConfig()->get("Lobby-World");
        $spawn = $this->getConfig()->get("Spawn-World");
        $msg = $this->getConfig()->get("Join-Message");
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
    
    /* On Player Join */
    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $alert = $event->getQuitMessage();
        $currworld = $player->getLevel();
        $hgworld = $this->getConfig()->get("HG-Worlds");
        $lobby = $this->getConfig()->get("Lobby-World");
        $spawn = $this->getConfig()->get("Spawn-World");
        $msg = $this->getConfig()->get("Join-Message");
        $event->setJoinMessage("");
        if($this->gameState == 1) {
             //Remove any array/sessions here
            return true;
        }
    }
    
    /* On Player Death */
    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer()->getLocation();
        $loc = $player->getPosition();
        $name = $player->getName();
        $currworld = $player->getLevel();
        $spawn = $this->getConfig()->get("Spawn-World");
        $hgworld = $this->getConfig()->get("HG-Worlds");
        if($currworld == $hgworld && $this->gameState == 1) {
            $player->teleport($this->getServer()->getLevelByName($spawn)->getSpawnLocation(), 0, 0);
            $explosion = new Explosion ( new Position ( $arenaPos->x, $arenaPos->y + 8, $arenaPos->z, $level ), 1 );
            $explosion->explode ();
            $event->setCancelled(true);
            return true;
        }else{
            return true;
        }
    }
    
    /* On Player Break */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $currworld = $player->getLevel();
        $hgworld = $this->getConfig()->get("HG-Worlds");
        if(!$currworld == $hgworld && !$event->getBlock()->getName() == "Oak Leaves" && !$this->gameState == 1) {
            $event->setCancelled(true);
            return true;
        }else{
            return true;
        }
    }
    
    /* On Player Place */
    public function onPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $currworld = $player->getLevel();
        $hgworld = $this->getConfig()->get("HG-Worlds");
        if($currworld == $hgworld) {
            $event->setCancelled(true);
            return true;
        }else{
            return true;
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
    
    public function SetHGSpawn(Player $player,$num, $arena) {
        $mmm = (new Config("Config.yml", Config::YAML ,array()));
        $conf = $mmm->getAll();
        $loc['x'] = $player->getPosition()->x;
        $loc['y'] = $player->getPosition()->y;
        $loc['z'] = $player->getPosition()->z;
        $conf[$arena][$num]['x'] = $loc['x'];
        $conf[$arena][$num]['y'] = $loc['y'];
        $conf[$arena][$num]['z'] = $loc['z'];
        $player->sendMessage("Spawn Set!");
        $mmm->setAll($conf);
        $mmm->save();
        return true;//hey
    }


    public function LoadYML() {
   /* new Config (new Config($this->getServer()->getDataPath() . "/plugins/Skills/" . "Skill-Settings.yml", Config::YAML ,array()));
    new Config (new Config($this->getServer()->getDataPath() . "/plugins/Skills/" . "World-Settings.yml", Config::YAML ,array()));
    */
    }
    
        public $HGS;
        public $HGW;
        public $HGID;
}
