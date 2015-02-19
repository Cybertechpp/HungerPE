<?php

namespace CyberTech;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;

class StartGameTimer extends PluginTask {
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


