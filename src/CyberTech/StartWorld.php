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
        
        public function StartWord(Main $main, $arena) {
            $this->CopyHGWorld($main, $arena);
            return true;
        }
        
        public function StopWorld(Main $main, $arena) {
            $path = $main->getServer()->getDataPath();
            $world = $main->worldsopen[$arena];
            $dirname = $path.$world;
            $this->delete_directory($dirname);
            return true;
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
        public function CopyHGWorld(Main $main ,$arena){
        $path = $main->getServer()->getDataPath();
        $world = $main->getConfig()->get(($this->getConfig()->get($arena)));
        $prefix = $main->getConfig()->get('HG-World-Prefix');
        $frompath = $path."worlds/".$world."/";
        $topath = $path."worlds/".$prefix.$world."/";
        $main->worldsopen[$arena] = $prefix.$world;
        $this->recurse_copy($frompath, $topath);
    }
    
    function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}
}


