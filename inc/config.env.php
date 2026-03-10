<?php
if (!class_exists('development')) {
    class development
    {
        var $HOST   = "172.21.111.67";
        var $DB_USER   = "cgonzalez";
        var $DB_PASS   = "cgonzalez2016";
        var $DB    = "proservipol_test";
        function getHost() { return $this->HOST; }
        function getUser() { return $this->DB_USER; }
        function getPass() { return $this->DB_PASS; }
        function getDB() { return $this->DB; }
    }
}

if (!class_exists('production')) {
    class production
    {
        var $HOST   = "172.21.111.67";
        var $DB_USER   = "proservipolv3";
        var $DB_PASS   = "carta77";
        var $DB    = "DB_PROSERVIPOL_V3";
        function getHost() { return $this->HOST; }
        function getUser() { return $this->DB_USER; }
        function getPass() { return $this->DB_PASS; }
        function getDB() { return $this->DB; }
    }
}

if (!class_exists('personal')) {
    class personal
    {
        var $HOST     = "172.21.104.13";
        var $DB_USER  = "proservipol2016";
        var $DB_PASS  = "proservipol2016";
        var $DB       = "DB_Personal";
        function getHost() { return $this->HOST; }
        function getUser() { return $this->DB_USER; }
        function getPass() { return $this->DB_PASS; }
        function getDB() { return $this->DB; }
    }
}
?>