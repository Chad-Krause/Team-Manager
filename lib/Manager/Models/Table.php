<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/23/18
 * Time: 8:21 PM
 */

namespace Manager;


class Table
{
    protected $config;        ///< The Config object
    protected $tableName;   ///< The table name to use

    /**
     * Constructor
     * @param Config $config The config object
     * @param $name string The base table name
     */
    public function __construct(Config $config, $name) {
        $this->config = $config;
        $this->tableName = $config->getTablePrefix() . $name;
    }

    /**
     * Get the database table name
     * @return string The table name
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * Diagnostic routine that substitutes into an SQL statement
     * @param $query string The queuy with : or ? parameters
     * @param $params array The arguments to substitute (what you pass to execute)
     * @return string SQL statement with substituted values
     */
    public function sub_sql($query, $params) {
        $keys = array();
        $values = array();

        // build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_numeric($value)) {
                $values[] = intval($value);
            } else {
                $values[] = '"' . $value . '"';
            }
        }

        $query = preg_replace($keys, $values, $query, 1, $count);
        return $query;
    }

    /**
     * Database connection function
     * @returns null|\ PDO object that connects to the database
     */
    public function pdo() {
        return $this->config->pdo();
    }

}
