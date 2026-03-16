<?php

namespace convergine\cookiebuddy\records;

use craft\db\ActiveRecord;

/**
 * LegalPolicyDocument ActiveRecord
 *
 * @property int         $id
 * @property string      $type
 * @property string      $version
 * @property string      $website_url
 * @property string      $company_name
 * @property string      $settings_json
 * @property string      $rendered_html
 * @property string      $language
 * @property string      $status
 * @property string|null $effective_date
 * @property string      $dateCreated
 * @property string      $dateUpdated
 * @property string      $uid
 */
class LegalPolicyDocument extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%legal_policy_documents}}';
    }
}
