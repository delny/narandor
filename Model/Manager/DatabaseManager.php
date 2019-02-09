<?php

abstract class DatabaseManager
{
    /**
     * Database Connection
     *
     * @return \PDO
     */
    protected function getBdd()
    {
        $host = DB["DB_HOST_NAME"];
        $db = DB["DB_DATABASE"];
        $login = DB["DB_USER_NAME"];
        $pass = DB["DB_PASSWORD"];

        $bdd = new \PDO('mysql:host='.$host.';dbname='.$db,$login,$pass,array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)
        );
        return $bdd;
    }
}
