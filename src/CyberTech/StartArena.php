<?php

namespace CyberTech;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;

class StartArena extends PluginTask {
	private $plugin;
	
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		parent::__construct ( $plugin );
	}
	
	public function onRun($ticks) {
            return true;
        }
        
        public function ThaPrepareArenaForStart(Main $other, $arena) {
            foreach($other->queing[$arena] as $a){
                echo "\r\n Players Name $a \r\n";
                $player = $other->getServer()->getPlayerExact($a);
                $playern = $player->getName();
                echo $playern;
                foreach ($other->queing[$arena] as $p){
                    $player = $other->getServer()->getPlayerExact($p);
                    if ($player instanceof Player){
                        $playern = $player->getName();
                        
                        
                    }
                }
                //TP Player
                //Clear Inv
                //Load World
                
            }
        }
        
        public function TPToArena(Player $player, $arena, Main $other) {
            $other->
        }
        
        public function ClearInv(Player $player){
            
        }
}


