<?php

namespace convergine\cookiebuddy\migrations;

use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration {
	/**
	 * @inheritdoc
	 */
	public function safeUp(): bool {
		// ─── Policy Documents ────────────────────────────────────────────────
		// Stores every generated / saved policy document.
		if (!$this->db->tableExists('{{%legal_policy_documents}}')) {
			$this->createTable('{{%legal_policy_documents}}', [
				'id'             => $this->primaryKey(),
				// Policy type: 'privacy-policy' | 'cookie-policy' | 'terms' | 'aup'
				'type'           => $this->string(50)->notNull(),
				// Semantic version string, e.g. "1.0", "2.1"
				'version'        => $this->string(20)->notNull()->defaultValue('1.0'),
				// Source site
				'website_url'    => $this->string(512)->notNull(),
				'company_name'   => $this->string(255)->notNull(),
				// Full serialised wizard answers (JSON)
				'settings_json'  => $this->longText()->notNull(),
				// Final rendered HTML output
				'rendered_html'  => $this->longText()->notNull(),
				// ISO 639-1 language code, e.g. 'en_gb', 'fr'
				'language'       => $this->string(10)->notNull()->defaultValue('en_gb'),
				// 'draft' | 'published' | 'archived'
				'status'         => $this->string(20)->notNull()->defaultValue('draft'),
				'effective_date' => $this->date()->null(),
				'dateCreated'    => $this->dateTime()->notNull(),
				'dateUpdated'    => $this->dateTime()->notNull(),
				'uid'            => $this->uid(),
			]);

			$this->createIndex(null, '{{%legal_policy_documents}}', ['type', 'website_url', 'status']);
			$this->createIndex(null, '{{%legal_policy_documents}}', ['status']);
		}


		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown(): bool {
		$this->dropTableIfExists('{{%legal_policy_documents}}');
		return true;
	}
}
