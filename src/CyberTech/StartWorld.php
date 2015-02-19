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
        
        function delete_directory($dirname) {
         if (is_dir($dirname))
           $dir_handle = opendir($dirname);
	 if (!$dir_handle)
	      return false;
	 while($file = readdir($dir_handle)) {
	       if ($file != "." && $file != "..") {
	            if (!is_dir($dirname."/".$file))
	                 unlink($dirname."/".$file);
	            else
	                 $this->delete_directory($dirname.'/'.$file);
	       }
	 }
	 closedir($dir_handle);
	 rmdir($dirname);
	 return true;
}
    
}


