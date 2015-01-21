<?php

// This file keeps track of upgrades to
// the glossary module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_glossary_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2012022000) {

        // Define field approvaldisplayformat to be added to glossary
        $table = new xmldb_table('glossary');
        $field = new xmldb_field('approvaldisplayformat', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'default', 'defaultapproval');

        // Conditionally launch add field approvaldisplayformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // glossary savepoint reached
        upgrade_mod_savepoint(true, 2012022000, 'glossary');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.
    if($oldversion < 2014051201){

        // Define table glossary_formats_tabs to be created.
        $table = new xmldb_table('glossary_formats_tabs');

        // Adding fields to table glossary_formats_tabs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('formatid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('standard', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('author', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('category', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('date', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table glossary_formats_tabs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('formatid', XMLDB_KEY_FOREIGN, array('formatid'), 'glossary_formats', array('id'));

        // Conditionally launch create table for glossary_formats_tabs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Glossary savepoint reached.
        upgrade_mod_savepoint(true, 2014051201, 'glossary');
    }

    return true;
}


