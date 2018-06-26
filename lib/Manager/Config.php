<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/23/18
 * Time: 9:01 PM
 */

namespace Manager;

class Config
{
    private static $pdo = null; ///< The PDO object
    private $email = '';        ///< Site owner email address
    private $dbHost = null;     ///< Database host name
    private $dbUser = null;     ///< Database user name
    private $dbPassword = null; ///< Database password
    private $tablePrefix = '';  ///< Database table prefix
    private $root = '';         ///< Site root

    const PRIVATEKEY = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIIEoQIBAAKCAQB7DEZTRAeFsSEhKRNcd2SEO+azmjbxz14NSe4MWSkuTfr33hYD
w/zLlq+vA6h8h81RHiUgpnScQ9UxFfM3gzUxYNZWgHtlEYyFUwdfe8X4oJZk2On2
XW5+F2wJUIe9JS+337ATopEdKVFKvHz+gfJSVbJ+vdd1UNjJgWBx0ZGLNbLapEwT
qjmIckDzkPnn1kCTCHnNHkWQgXbcXLX1kMPsTVqWiyx9eysqqjmCZdBYaRIBIDX5
zdJdo6ncnJ7rTrRYT+HLrtLHrdKUHBsSLzKyli6Wv0wFNrJeOsnGxXA0V5E/psET
Rg+MPchVzL9BywkEjc3Y5v0Bqc/XsLvkRMGZAgMBAAECggEAA6+Db70K6KM8lcyw
KsXcueU9bMXCvY1ziLxdJ/uHsz5ctQ3u5N/683IpAvn+EvTSXoqjnA3AUmnpudWp
elEwx7DZ2q9wgr960QeCogaIEIFm39BreZik1ZwH/WNvHWg+vKgzkvL8m74AFFf+
+nST+IlellNDX90zER4R8HbRgDTpelp+DH47PS5HPM5Oe7Sv74f50PKdNpQF1nlO
9q41YG31jAYSEyeWObZLZW5cMubsHmVNKTrOVgm8jmYMPkFPnnFkmPPZPWyyYXj9
VCxeRJ+FldTG6X9uSrSFq7XJvAnYM96Joiw31YHLKpg/qKj3BK57R1/f1Nknln0M
GfEjAQKBgQDsnUdENevmdGp8uJnQAw0z8jdbqim7q2GKTlhRSr79wfgI5LpLHwcu
WwtzRP62bMZgOpo7jPEFMakukSdTKyaiS3L9Cr0MO5DzizEdvBiadNMGjWSDsi0D
1D/e4PRrCfIlRnUI+qEdCW2Kp8VIpcfRXuNqZwL0uSz7Rnqz8Lvv4QKBgQCFIRFW
xImK11ItdlzZCUdV5EBx3xWkeV+C2xJrtgMxaWjA8FVd4jbGQnIovLedNjc7Ub/P
DxmhX5zEkav8SjD0eonQwWhtDHJygmoJkuZqNOvyyDtBkoEJazoeYWNJALgSatFH
U72YK78Rrhr9/Vl2SpKHMlwdk5trobDNhyFouQKBgA3AgmDbhW9qernu/LmTQ9Qu
EruYIz7OEig4r3diEcGr4V+a85zkG8W88uhrLSarIch7/3TQlz2HCl8zfoad7mvm
WLOOSTiJyb3t7BffU8q+WXl7BEmHNIiRcHjiuDH0bQdvlePEtVJ7tsslPxke0YNA
sZUAkbJphMzB5uXIaxPhAoGACRYVsd9eJ9zEXhf7BFEuzjzy7RK1znD+RNcg7bsR
grjYDnsYyHydEnEMi509xvwhTuoodkBolmwJLh1nKKQDrVwDtfzNXMwBr7EY/ahK
E6ujAwIJkVMnfXYVFGe/OAdViORDfmPHx/AMbW99piI5jepPD+0u/lHJxNHXWF/F
tvkCgYAtPkAfjkqoGPIRS1DkkNFUUFeYEN4CtIvsuKQhJB+cPVxqzXtGoqFWOSxF
JDo5ayJQ0G9lRIpBlOBpuo5kfP4CeQOGDbGmrNTTVEqxpScRmRB5iEfrkaHx2wFp
FLonYhnbEsmZDv1kJp94xfpGHaTAZn9HX/FVH1o/bA+bpICb7w==
-----END RSA PRIVATE KEY-----
EOD;
    const PUBLICKEY  = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBITANBgkqhkiG9w0BAQEFAAOCAQ4AMIIBCQKCAQB7DEZTRAeFsSEhKRNcd2SE
O+azmjbxz14NSe4MWSkuTfr33hYDw/zLlq+vA6h8h81RHiUgpnScQ9UxFfM3gzUx
YNZWgHtlEYyFUwdfe8X4oJZk2On2XW5+F2wJUIe9JS+337ATopEdKVFKvHz+gfJS
VbJ+vdd1UNjJgWBx0ZGLNbLapEwTqjmIckDzkPnn1kCTCHnNHkWQgXbcXLX1kMPs
TVqWiyx9eysqqjmCZdBYaRIBIDX5zdJdo6ncnJ7rTrRYT+HLrtLHrdKUHBsSLzKy
li6Wv0wFNrJeOsnGxXA0V5E/psETRg+MPchVzL9BywkEjc3Y5v0Bqc/XsLvkRMGZ
AgMBAAE=
-----END PUBLIC KEY-----
EOD;



    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * @param string $tablePrefix
     */
    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param string $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }


    /**
     * Configure the database
     * @param $host
     * @param $user
     * @param $password
     * @param $prefix
     */
    public function dbConfigure($host, $user, $password, $prefix) {
        $this->dbHost = $host;
        $this->dbUser = $user;
        $this->dbPassword = $password;
        $this->tablePrefix = $prefix;
    }

    /**
     * Database connection function
     * @return null|\ PDO object that connects to the database
     */
    function pdo() {
        // This ensures we only create the PDO object once
        if(self::$pdo !== null) {
            return self::$pdo;
        }

        try {
            self::$pdo = new \PDO($this->dbHost,
                $this->dbUser,
                $this->dbPassword);
        } catch(\PDOException $e) {
            // If we can't connect we die!
            echo "<pre>";
            print_r([$this->dbHost, $this->dbUser]);
            print_r($e);
            echo "</pre>";
            die("Unable to select database");
        }

        return self::$pdo;
    }

}