<?php

class MyDB {

    public static function connectdb() {
        $cfg = Propel::getConfiguration();
        $dsn = $cfg['datasources']['propel']['connection']['dsn'];
        $dsn_parts = explode(';', $dsn);
        $dsn_part_1_parts = explode('=', $dsn_parts[0]);
        $dsn_part_2_parts = explode('=', $dsn_parts[1]);
        $dbname = $dsn_part_1_parts[1];
        $host = $dsn_part_2_parts[1];
        $username = $cfg['datasources']['propel']['connection']['user'];
        $password = $cfg['datasources']['propel']['connection']['password'];

        $result = mysql_connect($host, $username, $password);
        mysql_query("use $dbname");
        mysql_query("SET NAMES 'utf8'");
        return $result;
    }

}