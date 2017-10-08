<?php

namespace App\Traits;

/**
 * Trait MysqlVersion
 * @package Illuminate\Database
 */
trait MysqlVersion
{
    protected function version()
    {
        switch (\DB::getDriverName()) {
            case 'mysql':
                $pdo = \DB::connection()->getPdo();
                $version = $pdo->query('select version()')->fetchColumn();
                $version = (float)mb_substr($version, 0, 6);

                return $version;
        }

        return false;
    }
}