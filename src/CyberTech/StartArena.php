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
            foreach ($this->plugin->gamestats as $g){
            $this->plugin->CheckHGStart($arena, $timer);
            }
        }
        
        public function ThaPrepareArenaForStart(Main $other, $arena) {
            foreach($other->queing[$arena] as $a){
                echo "\r\n Players Name $a \r\n";
                $player = $other->getServer()->getPlayerExact($a);
                $playern = $player->getName();
                echo $playern;
            }
                
            //
            
        }
}


