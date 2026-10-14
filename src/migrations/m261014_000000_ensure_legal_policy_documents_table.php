<?php

namespace convergine\cookiebuddy\migrations;

use craft\db\Migration;

/**
 * m261014_000000_ensure_legal_policy_documents_table migration.
 *
 * Creates the policy documents table on sites that are still missing it:
 * fresh 1.0.7 installs marked m250101_000001_create_legal_policy_tables as applied
 * without creating the table, and sites updated from 1.0.6 or earlier through the
 * control panel never ran it, because the schema version wasn't bumped.
 */
class m261014_000000_ensure_legal_policy_documents_table extends Migration
{
    public function safeUp(): bool
    {
        if ($this->db->tableExists('{{%legal_policy_documents}}')) {
            return true;
        }

        return (new m250101_000001_create_legal_policy_tables(['db' => $this->db]))->safeUp();
    }

    public function safeDown(): bool
    {
        // The table belongs to m250101_000001_create_legal_policy_tables; nothing to undo here.
        return true;
    }
}
