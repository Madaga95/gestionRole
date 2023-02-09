<?php

abstract class Model{
    private static $pdo;
    //mysql://root:azerty2526@127.0.0.1:3306/helpdev?serverVersion=8.0.31
    private static function setBdd(){
        self::$pdo = new PDO("mysql:host=localhost;dbname=gestioncompte;charset=utf8", "root", "");
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    protected function getBdd(){
        if(self::$pdo === null){
            self::setBdd();
        }
        return self::$pdo;
    }
}