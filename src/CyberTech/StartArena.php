<?php

namespace CyberTech;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;

class StartGameTimer extends Main {
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
}


