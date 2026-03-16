<?php

namespace convergine\cookiebuddy\models;

use Craft;
use craft\base\Model;
use convergine\cookiebuddy\questions\PrivacyPolicyQuestions;
use DateTime;

/**
 * PrivacyPolicyModel
 *
 * Holds, validates, and exposes all wizard data needed to render the privacy policy template.
 * Passed directly to privacy-policy.twig as {{ policy }}.
 */
class PrivacyPolicyModel extends Model
{
    //  Business Info 
    public string $company_name       = '';
    public string $company_legal_name = '';
    public string $website_url        = '';
    public string $contact_email      = '';
    public string $business_country   = '';
    public string $business_address   = '';

    //  DPO 
    public bool   $has_dpo    = false;
    public string $dpo_name   = '';
    public string $dpo_email  = '';

    //  Jurisdiction 
    /** @var string[] e.g. ['gdpr', 'ccpa', 'pipeda'] */
    public array  $jurisdictions            = [];
    public bool   $serves_children         = false;
    public bool   $eu_representative        = false;
    public string $eu_representative_details = '';

    //  Data Collection 
    /** @var string[] */
    public array $data_types          = [];
    /** @var string[] */
    public array $collection_methods  = [];
    public bool  $has_user_accounts   = false;

    //  Special Category Data 
    public bool  $collects_special_category_data = false;
    /** @var string[] */
    public array $special_category_types = [];

    //  Processing Purposes & Legal Bases 
    /** @var string[] */
    public array $processing_purposes = [];
    /**
     * Associative array: purpose_key => basis_key (e.g. ['analytics_improve' => 'legit'])
     * @var array<string,string>
     */
    public array $legal_bases_map = [];

    //  Automated Decisions 
    public bool   $uses_automated_decisions   = false;
    public string $automated_decision_details = '';

    //  Marketing Opt-out 
    public string $marketing_unsubscribe_method = '';

    //  Third-Party Source Types 
    /** @var string[] e.g. ['advertising_networks', 'social_platforms'] */
    public array $third_party_source_types = [];

    //  Third Parties 
    /** @var string[] */
    public array  $analytics_services            = [];
    /** @var string[] */
    public array  $advertising_services          = [];
    /** @var string[] */
    public array  $payment_services              = [];
    /** @var string[] */
    public array  $email_services                = [];
    /** @var string[] */
    public array  $support_services              = [];
    /** @var string[] */
    public array  $social_services               = [];
    /** @var string[] */
    public array  $hosting_services              = [];
    public string $other_third_parties           = '';
    public bool   $shares_data_with_third_parties = false;

    //  CCPA Opt-out URL 
    public string $ccpa_optout_url = '';

    //  Cookies 
    public bool   $uses_cookies          = true;
    /** @var string[] */
    public array  $cookie_types          = [];
    public string $cookie_consent_method = 'banner';

    //  Retention & Security 
    public string $retention_period = 'necessary';
    public string $retention_custom = '';
    /** @var string[] */
    public array  $security_measures             = [];
    public bool   $data_transfers_outside_eea    = false;
    /** @var string[] */
    public array  $transfer_safeguards           = [];

    //  Breach Notification 
    public bool   $notifies_breach_users         = true;
    public string $breach_notification_timeframe = '';

    //  User Rights 
    /** @var string[] */
    public array  $rights_granted        = [];
    public string $rights_contact_method = 'email';
    public string $rights_form_url       = '';
    public string $supervisory_authority = '';

    //  Policy Preferences 
    public string $policy_language = 'en_gb';
    public string $effective_date  = '';
    public string $policy_version  = '1.0';
    /** @var string[] */
    public array  $notify_changes  = [];

    // 

    public function init(): void
    {
        parent::init();
        if (empty($this->effective_date)) {
            $this->effective_date = (new DateTime())->format('Y-m-d');
        }
    }

    /**
     * Returns merged array of all third-party service keys.
     * Useful for checking if *any* third-party services are used.
     */
    public function getThirdPartyServices(): array
    {
        return array_merge(
            $this->analytics_services,
            $this->advertising_services,
            $this->payment_services,
            $this->email_services,
            $this->support_services,
            $this->social_services,
            $this->hosting_services,
        );
    }

    /**
     * Validation rules.
     * Required rules are built dynamically from PrivacyPolicyQuestions so that
     * adding required => true to any question automatically enforces validation here.
     */
    public function rules(): array
    {
        $rules = [
            // Email validation
            ['contact_email', 'email'],
            ['dpo_email', 'email'],

            // URL validation
            ['website_url', 'url'],
            ['rights_form_url', 'url'],
            ['ccpa_optout_url', 'url'],

            // Boolean fields
            [
                [
                    'has_dpo', 'serves_children', 'eu_representative',
                    'has_user_accounts', 'uses_cookies', 'shares_data_with_third_parties',
                    'data_transfers_outside_eea', 'collects_special_category_data',
                    'uses_automated_decisions', 'notifies_breach_users',
                ],
                'boolean',
            ],

            // Array fields (indexed)
            [
                [
                    'jurisdictions', 'data_types', 'collection_methods',
                    'processing_purposes', 'third_party_source_types',
                    'analytics_services', 'advertising_services', 'payment_services',
                    'email_services', 'support_services', 'social_services', 'hosting_services',
                    'cookie_types', 'security_measures', 'transfer_safeguards',
                    'rights_granted', 'notify_changes', 'special_category_types',
                ],
                'each',
                'rule' => ['string'],
            ],

            // Select/in rules
            ['retention_period', 'in', 'range' => ['necessary', '1_year', '2_years', '3_years', '5_years', '7_years', 'custom']],
            ['rights_contact_method', 'in', 'range' => ['email', 'form', 'portal']],
            ['cookie_consent_method', 'in', 'range' => ['banner', 'implied', 'none']],
            ['breach_notification_timeframe', 'in', 'range' => ['72h', '1week', 'asap', '']],
        ];

        // Build required rules from questions — single source of truth
        foreach (PrivacyPolicyQuestions::getRequiredFields() as $field) {
            if ($field['depends_on'] === null) {
                $rules[] = [$field['id'], 'required'];
            } else {
                // Conditionally required: only when the depends_on condition is met
                $dependsKey = array_key_first($field['depends_on']);
                $dependsVal = $field['depends_on'][$dependsKey];
                $rules[] = [
                    $field['id'], 'required',
                    'when' => function (self $model) use ($dependsKey, $dependsVal): bool {
                        $actual = $model->$dependsKey;
                        if ($dependsVal === true) {
                            return (bool)$actual;
                        }
                        return is_array($actual)
                            ? in_array($dependsVal, $actual, true)
                            : $actual === $dependsVal;
                    },
                ];
            }
        }
        return $rules;
    }

    /**
     * Populate model from raw wizard POST data.
     */
    public static function fromWizardData(array $data): self
    {
        $model = new self();

        // Scalar string fields
        $stringFields = [
            'company_name', 'company_legal_name', 'website_url', 'contact_email', 'business_country',
            'business_address', 'dpo_name', 'dpo_email', 'eu_representative_details',
            'other_third_parties', 'cookie_consent_method', 'retention_period',
            'retention_custom', 'rights_contact_method', 'rights_form_url',
            'supervisory_authority', 'policy_language', 'policy_version',
            'automated_decision_details', 'marketing_unsubscribe_method',
            'ccpa_optout_url', 'breach_notification_timeframe',
        ];
        foreach ($stringFields as $field) {
            if (isset($data[$field])) {
                $model->$field = (string)$data[$field];
            }
        }

        // Craft dateField submits as name[date] in locale format (e.g. '3/3/2026').
        // Normalise to Y-m-d for MySQL.
        $rawDate = null;
        if (!empty($data['effective_date']['date'])) {
            $rawDate = (string)$data['effective_date']['date'];
        } elseif (isset($data['effective_date']) && is_string($data['effective_date']) && $data['effective_date'] !== '') {
            $rawDate = $data['effective_date'];
        }
        if ($rawDate !== null) {
            $ts = strtotime($rawDate);
            $model->effective_date = $ts !== false ? date('Y-m-d', $ts) : $rawDate;
        }

        // Boolean fields
        $boolFields = [
            'has_dpo', 'serves_children', 'eu_representative', 'has_user_accounts',
            'uses_cookies', 'shares_data_with_third_parties', 'data_transfers_outside_eea',
            'collects_special_category_data', 'uses_automated_decisions', 'notifies_breach_users',
        ];
        foreach ($boolFields as $field) {
            $model->$field = !empty($data[$field]);
        }

        // Array fields (checkboxes / multiselect — indexed)
        $arrayFields = [
            'jurisdictions', 'data_types', 'collection_methods',
            'processing_purposes', 'third_party_source_types',
            'analytics_services', 'advertising_services', 'payment_services',
            'email_services', 'support_services', 'social_services', 'hosting_services',
            'cookie_types', 'security_measures', 'transfer_safeguards',
            'rights_granted', 'notify_changes', 'special_category_types',
        ];
        foreach ($arrayFields as $field) {
            if (isset($data[$field])) {
                $model->$field = is_array($data[$field])
                    ? array_values(array_filter($data[$field]))
                    : [$data[$field]];
            }
        }

        // Associative map: purpose => legal basis
        if (isset($data['legal_bases_map']) && is_array($data['legal_bases_map'])) {
            $map = [];
            foreach ($data['legal_bases_map'] as $purpose => $basis) {
                $basis = (string)$basis;
                if ($basis !== '') {
                    $map[(string)$purpose] = $basis;
                }
            }
            $model->legal_bases_map = $map;
        }

        return $model;
    }

    /**
     * Serialise to array for DB storage.
     */
    public function toStorageArray(): array
    {
        return [
            'company_name'                    => $this->company_name,
            'company_legal_name'              => $this->company_legal_name,
            'website_url'                     => $this->website_url,
            'contact_email'                   => $this->contact_email,
            'business_country'                => $this->business_country,
            'business_address'                => $this->business_address,
            'has_dpo'                         => $this->has_dpo,
            'dpo_name'                        => $this->dpo_name,
            'dpo_email'                       => $this->dpo_email,
            'jurisdictions'                   => $this->jurisdictions,
            'serves_children'                 => $this->serves_children,
            'eu_representative'               => $this->eu_representative,
            'eu_representative_details'       => $this->eu_representative_details,
            'data_types'                      => $this->data_types,
            'collection_methods'              => $this->collection_methods,
            'has_user_accounts'               => $this->has_user_accounts,
            'collects_special_category_data'  => $this->collects_special_category_data,
            'special_category_types'          => $this->special_category_types,
            'processing_purposes'             => $this->processing_purposes,
            'legal_bases_map'                 => $this->legal_bases_map,
            'third_party_source_types'        => $this->third_party_source_types,
            'uses_automated_decisions'        => $this->uses_automated_decisions,
            'automated_decision_details'      => $this->automated_decision_details,
            'marketing_unsubscribe_method'    => $this->marketing_unsubscribe_method,
            'analytics_services'              => $this->analytics_services,
            'advertising_services'            => $this->advertising_services,
            'payment_services'                => $this->payment_services,
            'email_services'                  => $this->email_services,
            'support_services'                => $this->support_services,
            'social_services'                 => $this->social_services,
            'hosting_services'                => $this->hosting_services,
            'other_third_parties'             => $this->other_third_parties,
            'shares_data_with_third_parties'  => $this->shares_data_with_third_parties,
            'ccpa_optout_url'                 => $this->ccpa_optout_url,
            'uses_cookies'                    => $this->uses_cookies,
            'cookie_types'                    => $this->cookie_types,
            'cookie_consent_method'           => $this->cookie_consent_method,
            'retention_period'                => $this->retention_period,
            'retention_custom'                => $this->retention_custom,
            'security_measures'               => $this->security_measures,
            'data_transfers_outside_eea'      => $this->data_transfers_outside_eea,
            'transfer_safeguards'             => $this->transfer_safeguards,
            'notifies_breach_users'           => $this->notifies_breach_users,
            'breach_notification_timeframe'   => $this->breach_notification_timeframe,
            'rights_granted'                  => $this->rights_granted,
            'rights_contact_method'           => $this->rights_contact_method,
            'rights_form_url'                 => $this->rights_form_url,
            'supervisory_authority'           => $this->supervisory_authority,
            'policy_language'                 => $this->policy_language,
            'effective_date'                  => $this->effective_date,
            'policy_version'                  => $this->policy_version,
            'notify_changes'                  => $this->notify_changes,
        ];
    }

    /**
     * Restore model from DB storage array.
     */
    public static function fromStorageArray(array $data): self
    {
        return self::fromWizardData($data);
    }
}
