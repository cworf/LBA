<?php

class TT_Log{
    
    static private $file = "/var/www/tt_log.txt"; // default path
    static private $verbosity = 1; // verbosity - the level which shows how important is the log message, it is used to filter the log messages (greater value means greater importance, minimum is 1), set to 1 to log everything, set to 0 to disable logging
    static private $fixed_verbosity = false;
    
    static function set_path($file) {
        self::$file = $file;
    }

    static function get_path(){
        return self::$file;
    }
    
    static function set_verbosity($verbosity) {
        self::$verbosity = $verbosity;
    }

    static function get_verbosity(){
        return self::$verbosity;
    }

    static function set_fixed_verbosity($fixed){
        self::$fixed_verbosity = (bool)$fixed;
    }

    static function get_fixed_verbosity(){
        return self::$verbosity;
    }
    
    // verbosity - set the importance of current log, minimum is 1 (default), greater value means more important log
    // export - write the value outputed by var_export function
    static function write($text, $export=null, $verbosity=1){
        if(self::$verbosity&&((self::$fixed_verbosity&&$verbosity==self::$verbosity)||(!self::$fixed_verbosity&&$verbosity>=self::$verbosity)))
            if($export)
                file_put_contents(self::$file, var_export($text,true)."\n", FILE_APPEND);
            else
                file_put_contents(self::$file, $text."\n", FILE_APPEND);
    }
    
}

// Ex:
// TT_Log::write('blabla');