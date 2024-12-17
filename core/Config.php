<?php

namespace core;

// All the configuration values are stored here
class Config
{
    // const DB_TYPE = 'sqlite'; // mysql or sqlite
    const DB_TYPE = 'mysql'; // mysql or sqlite

    const DB_HOST = 'localhost';
    const DB_PORT = '3306';

    const DB_NAME = 'wedding_decoration_company';

    const DB_USER = 'root';
    const DB_PASSWORD = '';

    const HIDE_FORM_FIELD_RULE_DEBUG_TEXT = true; // Delete from the rule error message the name of the rule (e.g. [RULE_REQUIRED])

    const TIME_ZONE = 'Europe/Bucharest';
}
