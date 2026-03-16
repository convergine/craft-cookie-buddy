<?php

namespace convergine\cookiebuddy\questions;

/**
 * PrivacyPolicyQuestions
 *
 * Defines all wizard questions for generating a Privacy Policy.
 * Each step maps to a CP wizard screen.
 *
 * Question types: text, email, url, select, multiselect, toggle, textarea, country_select
 */
class PrivacyPolicyQuestions
{
    /**
     * Returns all wizard steps and their questions.
     *
     * @return array
     */
    public static function getSteps(): array
    {
        return [

            
            // STEP 1: Business Information
            [
                'id'          => 'business_info',
                'title'       => 'Your Business',
                'description' => 'Tell us about your business so we can personalise your policy.',
                'questions'   => [
                    [
                        'id'          => 'company_name',
                        'label'       => 'Company / Website Name',
                        'type'        => 'text',
                        'required'    => true,
                        'placeholder' => 'Acme Ltd',
                        'help'        => 'The trading name or display name of your business or website.',
                    ],
                    [
                        'id'          => 'company_legal_name',
                        'label'       => 'Registered Legal Entity Name',
                        'type'        => 'text',
                        'required'    => false,
                        'placeholder' => 'e.g. Acme Ltd',
                        'help'        => 'The full registered name of your business, if different from the display name above (e.g. Acme Ltd, Acme GmbH). Required for GDPR Article 13 controller identity.',
                    ],
                    [
                        'id'          => 'website_url',
                        'label'       => 'Website URL',
                        'type'        => 'url',
                        'required'    => true,
                        'placeholder' => 'https://example.com',
                        'help'        => 'The primary URL of the website this policy covers.',
                    ],
                    [
                        'id'          => 'contact_email',
                        'label'       => 'Privacy Contact Email',
                        'type'        => 'email',
                        'required'    => true,
                        'placeholder' => 'privacy@example.com',
                        'help'        => 'Where users can send privacy-related requests (e.g. data deletion).',
                    ],
                    [
                        'id'          => 'business_country',
                        'label'       => 'Country Where Your Business is Registered',
                        'type'        => 'country_select',
                        'required'    => true,
                        'help'        => 'This determines which privacy laws apply to your business.',
                    ],
                    [
                        'id'          => 'business_address',
                        'label'       => 'Business Address',
                        'type'        => 'textarea',
                        'required'    => true,
                        'rows'        => 3,
                        'placeholder' => "123 Main Street\nCity, Postcode\nCountry",
                        'help'        => 'For GDPR compliance (Article 13), provide your full registered address including street, city, postcode, and country.',
                    ],
                ],
            ],

            
            // STEP 2: Data Protection Officer (conditional)
            [
                'id'          => 'dpo',
                'title'       => 'Data Protection Officer',
                'description' => 'GDPR requires some businesses to appoint a DPO. Let us know if you have one.',
                'questions'   => [
                    [
                        'id'      => 'has_dpo',
                        'label'   => 'Do you have a Data Protection Officer (DPO)?',
                        'type'    => 'toggle',
                        'default' => false,
                        'help'    => 'Required for public authorities and organisations that process data at scale.',
                    ],
                    [
                        'id'          => 'dpo_name',
                        'label'       => 'DPO Name',
                        'type'        => 'text',
                        'required'    => false,
                        'placeholder' => 'Jane Smith',
                        'depends_on'  => ['has_dpo' => true],
                    ],
                    [
                        'id'          => 'dpo_email',
                        'label'       => 'DPO Email',
                        'type'        => 'email',
                        'required'    => false,
                        'placeholder' => 'dpo@example.com',
                        'depends_on'  => ['has_dpo' => true],
                    ],
                ],
            ],

            
            // STEP 3: Jurisdiction & Compliance
            [
                'id'          => 'jurisdiction',
                'title'       => 'Compliance & Jurisdiction',
                'description' => 'Select all regions whose privacy laws you need to comply with.',
                'questions'   => [
                    [
                        'id'      => 'jurisdictions',
                        'label'   => 'Which privacy regulations apply to your website?',
                        'type'    => 'multiselect',
                        'required'=> true,
                        'help'    => 'Select all that apply. Choose GDPR if you have visitors from the EU/UK.',
                        'options' => [
                            ['value' => 'gdpr',    'label' => 'GDPR (European Union / UK)'],
                            ['value' => 'ccpa',    'label' => 'CCPA / CPRA (California, USA)'],
                            ['value' => 'pipeda',  'label' => 'PIPEDA (Canada)'],
                            ['value' => 'aus',     'label' => 'Australian Privacy Act'],
                            ['value' => 'generic', 'label' => 'Generic / Other'],
                        ],
                    ],
                    [
                        'id'      => 'serves_children',
                        'label'   => 'Does your website knowingly collect data from children under 13?',
                        'type'    => 'toggle',
                        'default' => false,
                        'help'    => 'If yes, COPPA (USA) compliance sections will be added.',
                    ],
                    [
                        'id'      => 'eu_representative',
                        'label'   => 'Do you have an EU/UK representative (if your business is outside EU/UK)?',
                        'type'    => 'toggle',
                        'default' => false,
                        'depends_on' => ['jurisdictions' => 'gdpr'],
                    ],
                    [
                        'id'          => 'eu_representative_details',
                        'label'       => 'EU/UK Representative Details',
                        'type'        => 'textarea',
                        'required'    => false,
                        'rows'        => 3,
                        'placeholder' => 'Name, address, and contact email of your EU/UK representative.',
                        'depends_on'  => ['eu_representative' => true],
                    ],
                ],
            ],

            
            // STEP 4: Data You Collect
            [
                'id'          => 'data_collection',
                'title'       => 'Data You Collect',
                'description' => 'Tell us what personal information your website collects from visitors.',
                'questions'   => [
                    [
                        'id'      => 'data_types',
                        'label'   => 'What types of personal data do you collect?',
                        'type'    => 'multiselect',
                        'required'=> true,
                        'help'    => 'Select all that apply.',
                        'options' => [
                            ['value' => 'name',           'label' => 'Full name'],
                            ['value' => 'email',          'label' => 'Email address'],
                            ['value' => 'phone',          'label' => 'Phone number'],
                            ['value' => 'address',        'label' => 'Postal / billing address'],
                            ['value' => 'payment',        'label' => 'Payment / credit card details'],
                            ['value' => 'ip_address',     'label' => 'IP address'],
                            ['value' => 'location',       'label' => 'Geolocation data'],
                            ['value' => 'device',         'label' => 'Device & browser information'],
                            ['value' => 'usage',          'label' => 'Website usage / behavioural data'],
                            ['value' => 'account',        'label' => 'Account login credentials'],
                            ['value' => 'profile',        'label' => 'User profile / preferences'],
                            ['value' => 'health',         'label' => 'Health or medical data'],
                            ['value' => 'biometric',      'label' => 'Biometric data'],
                            ['value' => 'social',         'label' => 'Social media profile data'],
                            ['value' => 'ugc',            'label' => 'User-generated content (comments, reviews)'],
                        ],
                    ],
                    [
                        'id'      => 'collection_methods',
                        'label'   => 'How do you collect this data?',
                        'type'    => 'multiselect',
                        'required'=> true,
                        'options' => [
                            ['value' => 'contact_form',    'label' => 'Contact / enquiry form'],
                            ['value' => 'registration',    'label' => 'Account registration'],
                            ['value' => 'checkout',        'label' => 'Checkout / purchase process'],
                            ['value' => 'newsletter',      'label' => 'Newsletter signup'],
                            ['value' => 'cookies',         'label' => 'Cookies & tracking technologies'],
                            ['value' => 'analytics',       'label' => 'Analytics tools (automatic)'],
                            ['value' => 'social_login',    'label' => 'Social media login (OAuth)'],
                            ['value' => 'survey',          'label' => 'Surveys or quizzes'],
                            ['value' => 'live_chat',       'label' => 'Live chat / support widget'],
                            ['value' => 'third_party',     'label' => 'From third-party partners'],
                        ],
                    ],
                    [
                        'id'         => 'third_party_source_types',
                        'label'      => 'What categories of third-party sources do you receive data from?',
                        'type'       => 'multiselect',
                        'depends_on' => ['collection_methods' => 'third_party'],
                        'options'    => [
                            ['value' => 'advertising_networks', 'label' => 'Advertising networks and data brokers'],
                            ['value' => 'social_platforms',     'label' => 'Social media platforms'],
                            ['value' => 'business_partners',    'label' => 'Business partners and resellers'],
                            ['value' => 'public_records',       'label' => 'Publicly available sources'],
                            ['value' => 'analytics_providers',  'label' => 'Analytics and measurement providers'],
                        ],
                    ],
                    [
                        'id'      => 'has_user_accounts',
                        'label'   => 'Does your website have user accounts / logins?',
                        'type'    => 'toggle',
                        'default' => false,
                    ],
                    [
                        'id'      => 'collects_special_category_data',
                        'label'   => 'Do you collect special category (sensitive) personal data?',
                        'type'    => 'toggle',
                        'default' => false,
                        'help'    => 'Special category data includes health/medical, biometric, racial or ethnic origin, political opinions, religious beliefs, trade union membership, genetic data, or sex life / sexual orientation. GDPR Article 9 requires explicit consent or another specific legal basis.',
                    ],
                    [
                        'id'      => 'special_category_types',
                        'label'   => 'Which types of special category data do you collect?',
                        'type'    => 'multiselect',
                        'depends_on' => ['collects_special_category_data' => true],
                        'options' => [
                            ['value' => 'racial_ethnic', 'label' => 'Racial or ethnic origin'],
                            ['value' => 'political',     'label' => 'Political opinions'],
                            ['value' => 'religious',     'label' => 'Religious or philosophical beliefs'],
                            ['value' => 'trade_union',   'label' => 'Trade union membership'],
                            ['value' => 'health',        'label' => 'Health or medical data'],
                            ['value' => 'genetic',       'label' => 'Genetic data'],
                            ['value' => 'biometric',     'label' => 'Biometric data (for unique identification)'],
                            ['value' => 'sex_life',      'label' => 'Sex life or sexual orientation'],
                        ],
                    ],
                ],
            ],

            
            // STEP 5: Why You Use the Data (Legal Bases)
            [
                'id'          => 'data_purposes',
                'title'       => 'Why You Use Personal Data',
                'description' => 'Select all the purposes for which you process personal data.',
                'questions'   => [
                    [
                        'id'      => 'processing_purposes',
                        'label'   => 'What do you use personal data for?',
                        'type'    => 'multiselect',
                        'required'=> true,
                        'options' => [
                            ['value' => 'service_delivery',  'label' => 'Providing and operating the website/service'],
                            ['value' => 'orders',            'label' => 'Processing orders and payments'],
                            ['value' => 'support',           'label' => 'Customer support'],
                            ['value' => 'marketing_email',   'label' => 'Sending marketing / promotional emails'],
                            ['value' => 'personalisation',   'label' => 'Personalising user experience'],
                            ['value' => 'analytics_improve', 'label' => 'Analytics to improve the website'],
                            ['value' => 'legal_compliance',  'label' => 'Legal and regulatory compliance'],
                            ['value' => 'fraud_prevention',  'label' => 'Fraud prevention and security'],
                            ['value' => 'research',          'label' => 'Research and development'],
                            ['value' => 'advertising',       'label' => 'Targeted / behavioural advertising'],
                        ],
                    ],
                    [
                        'id'         => 'legal_bases_map',
                        'label'      => 'Legal basis per processing purpose',
                        'type'       => 'legal_basis_map',
                        'depends_on' => ['jurisdictions' => 'gdpr'],
                        'help'       => 'GDPR Article 6 requires you to identify a lawful basis for each processing activity. Select the most appropriate basis for each purpose you have chosen above.',
                        'bases'      => [
                            ['value' => 'consent',     'label' => 'Consent'],
                            ['value' => 'contract',    'label' => 'Contract'],
                            ['value' => 'legal_oblig', 'label' => 'Legal obligation'],
                            ['value' => 'vital',       'label' => 'Vital interests'],
                            ['value' => 'public_task', 'label' => 'Public task'],
                            ['value' => 'legit',       'label' => 'Legitimate interests'],
                        ],
                    ],
                    [
                        'id'      => 'uses_automated_decisions',
                        'label'   => 'Do you use automated decision-making or profiling?',
                        'type'    => 'toggle',
                        'default' => false,
                        'help'    => 'Includes personalisation algorithms, ad targeting profiles, credit scoring, or behavioural analysis. GDPR Article 22 requires disclosure if decisions produce legal or similarly significant effects.',
                    ],
                    [
                        'id'          => 'automated_decision_details',
                        'label'       => 'Describe the automated decisions / profiling used',
                        'type'        => 'textarea',
                        'required'    => false,
                        'rows'        => 3,
                        'placeholder' => 'e.g. We use behavioural data to personalise product recommendations. No decisions with legal or similarly significant effects are made automatically.',
                        'depends_on'  => ['uses_automated_decisions' => true],
                    ],
                    [
                        'id'         => 'marketing_unsubscribe_method',
                        'label'      => 'How can users unsubscribe from marketing emails?',
                        'type'       => 'select',
                        'required'   => false,
                        'depends_on' => ['processing_purposes' => 'marketing_email'],
                        'options'    => [
                            ['value' => 'link',     'label' => 'Unsubscribe link in every email'],
                            ['value' => 'reply',    'label' => 'Reply STOP / Unsubscribe to the email'],
                            ['value' => 'email_us', 'label' => 'Email us to unsubscribe'],
                            ['value' => 'account',  'label' => 'Via account settings / preferences'],
                        ],
                    ],
                ],
            ],

            
            // STEP 6: Third-Party Services
            [
                'id'          => 'third_parties',
                'title'       => 'Third-Party Services',
                'description' => 'Tell us which third-party tools and services your website uses.',
                'questions'   => [
                    [
                        'id'      => 'analytics_services',
                        'label'   => 'Analytics & tracking tools',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'google_analytics', 'label' => 'Google Analytics'],
                            ['value' => 'ga4',              'label' => 'Google Analytics 4'],
                            ['value' => 'google_tag',       'label' => 'Google Tag Manager'],
                            ['value' => 'mixpanel',         'label' => 'Mixpanel'],
                            ['value' => 'hotjar',           'label' => 'Hotjar'],
                            ['value' => 'clarity',          'label' => 'Microsoft Clarity'],
                            ['value' => 'plausible',        'label' => 'Plausible Analytics'],
                            ['value' => 'fathom',           'label' => 'Fathom Analytics'],
                            ['value' => 'heap',             'label' => 'Heap'],
                            ['value' => 'segment',          'label' => 'Segment'],
                        ],
                    ],
                    [
                        'id'      => 'advertising_services',
                        'label'   => 'Advertising & remarketing',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'google_ads',     'label' => 'Google Ads / AdSense'],
                            ['value' => 'facebook_pixel', 'label' => 'Meta / Facebook Pixel'],
                            ['value' => 'linkedin_pixel', 'label' => 'LinkedIn Insight Tag'],
                            ['value' => 'twitter_pixel',  'label' => 'Twitter / X Pixel'],
                            ['value' => 'tiktok_pixel',   'label' => 'TikTok Pixel'],
                            ['value' => 'bing_ads',       'label' => 'Microsoft / Bing Ads'],
                        ],
                    ],
                    [
                        'id'      => 'payment_services',
                        'label'   => 'Payment processors',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'stripe',     'label' => 'Stripe'],
                            ['value' => 'paypal',     'label' => 'PayPal'],
                            ['value' => 'square',     'label' => 'Square'],
                            ['value' => 'braintree',  'label' => 'Braintree'],
                            ['value' => 'klarna',     'label' => 'Klarna'],
                            ['value' => 'afterpay',   'label' => 'Afterpay / Clearpay'],
                        ],
                    ],
                    [
                        'id'      => 'email_services',
                        'label'   => 'Email marketing & CRM',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'mailchimp',      'label' => 'Mailchimp'],
                            ['value' => 'klaviyo',        'label' => 'Klaviyo'],
                            ['value' => 'hubspot',        'label' => 'HubSpot'],
                            ['value' => 'sendinblue',     'label' => 'Sendinblue / Brevo'],
                            ['value' => 'convertkit',     'label' => 'ConvertKit'],
                            ['value' => 'activecampaign', 'label' => 'ActiveCampaign'],
                            ['value' => 'mailerlite',     'label' => 'MailerLite'],
                            ['value' => 'salesforce',     'label' => 'Salesforce'],
                        ],
                    ],
                    [
                        'id'      => 'support_services',
                        'label'   => 'Support & chat tools',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'intercom',    'label' => 'Intercom'],
                            ['value' => 'zendesk',     'label' => 'Zendesk'],
                            ['value' => 'freshdesk',   'label' => 'Freshdesk'],
                            ['value' => 'livechat',    'label' => 'LiveChat'],
                            ['value' => 'tawk',        'label' => 'Tawk.to'],
                            ['value' => 'drift',       'label' => 'Drift'],
                        ],
                    ],
                    [
                        'id'      => 'social_services',
                        'label'   => 'Social media & login',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'google_login',    'label' => 'Google Login (OAuth)'],
                            ['value' => 'facebook_login',  'label' => 'Facebook / Meta Login'],
                            ['value' => 'twitter_login',   'label' => 'Twitter / X Login'],
                            ['value' => 'apple_login',     'label' => 'Sign in with Apple'],
                            ['value' => 'github_login',    'label' => 'GitHub Login'],
                        ],
                    ],
                    [
                        'id'      => 'hosting_services',
                        'label'   => 'Hosting & infrastructure',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'aws',         'label' => 'Amazon Web Services (AWS)'],
                            ['value' => 'gcp',         'label' => 'Google Cloud Platform'],
                            ['value' => 'azure',       'label' => 'Microsoft Azure'],
                            ['value' => 'cloudflare',  'label' => 'Cloudflare'],
                            ['value' => 'digitalocean','label' => 'DigitalOcean'],
                            ['value' => 'vercel',      'label' => 'Vercel'],
                        ],
                    ],
                    [
                        'id'          => 'other_third_parties',
                        'label'       => 'Any other third-party services not listed above?',
                        'type'        => 'textarea',
                        'required'    => false,
                        'rows'        => 3,
                        'placeholder' => 'e.g. Typeform (survey tool), Calendly (scheduling)...',
                    ],
                    [
                        'id'      => 'shares_data_with_third_parties',
                        'label'   => 'Do you share or sell personal data to third-party partners or advertisers?',
                        'type'    => 'toggle',
                        'default' => false,
                        'help'    => 'Required disclosure for CCPA. Selling includes sharing for advertising.',
                    ],
                    [
                        'id'          => 'ccpa_optout_url',
                        'label'       => '"Do Not Sell or Share My Personal Information" page URL',
                        'type'        => 'url',
                        'required'    => false,
                        'placeholder' => 'https://example.com/do-not-sell',
                        'help'        => 'CCPA requires a dedicated opt-out page if you sell or share personal data. Leave blank if not applicable.',
                        'depends_on'  => ['shares_data_with_third_parties' => true],
                    ],
                ],
            ],

            
            // STEP 7: Cookies
            [
                'id'          => 'cookies',
                'title'       => 'Cookies & Tracking',
                'description' => 'Tell us how your website uses cookies and similar technologies.',
                'questions'   => [
                    [
                        'id'      => 'uses_cookies',
                        'label'   => 'Does your website use cookies?',
                        'type'    => 'toggle',
                        'default' => true,
                    ],
                    [
                        'id'      => 'cookie_types',
                        'label'   => 'What types of cookies does your website use?',
                        'type'    => 'multiselect',
                        'depends_on' => ['uses_cookies' => true],
                        'options' => [
                            ['value' => 'essential',     'label' => 'Essential / strictly necessary cookies'],
                            ['value' => 'functional',    'label' => 'Functional / preference cookies'],
                            ['value' => 'analytics',     'label' => 'Analytics / performance cookies'],
                            ['value' => 'marketing',     'label' => 'Marketing / advertising cookies'],
                            ['value' => 'social',        'label' => 'Social media cookies'],
                        ],
                    ],
                    [
                        'id'      => 'cookie_consent_method',
                        'label'   => 'How do you obtain cookie consent?',
                        'type'    => 'select',
                        'depends_on' => ['uses_cookies' => true],
                        'options' => [
                            ['value' => 'banner',      'label' => 'Cookie consent banner'],
                            ['value' => 'implied',     'label' => 'Implied consent (continued browsing)'],
                            ['value' => 'none',        'label' => 'No consent mechanism'],
                        ],
                    ],
                ],
            ],

            
            // STEP 8: Data Retention & Security
            [
                'id'          => 'retention_security',
                'title'       => 'Data Retention & Security',
                'description' => 'How long do you keep data and how do you protect it?',
                'questions'   => [
                    [
                        'id'      => 'retention_period',
                        'label'   => 'How long do you retain personal data?',
                        'type'    => 'select',
                        'required'=> true,
                        'options' => [
                            ['value' => 'necessary',  'label' => 'Only as long as necessary'],
                            ['value' => '1_year',     'label' => 'Up to 1 year'],
                            ['value' => '2_years',    'label' => 'Up to 2 years'],
                            ['value' => '3_years',    'label' => 'Up to 3 years'],
                            ['value' => '5_years',    'label' => 'Up to 5 years'],
                            ['value' => '7_years',    'label' => 'Up to 7 years (legal/tax requirement)'],
                            ['value' => 'custom',     'label' => 'Custom (specify below)'],
                        ],
                    ],
                    [
                        'id'          => 'retention_custom',
                        'label'       => 'Custom retention period details',
                        'type'        => 'textarea',
                        'required'    => false,
                        'rows'        => 2,
                        'depends_on'  => ['retention_period' => 'custom'],
                        'placeholder' => 'e.g. Account data: 3 years after last login. Order data: 7 years.',
                    ],
                    [
                        'id'      => 'security_measures',
                        'label'   => 'What security measures do you use to protect data?',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'ssl',           'label' => 'SSL / TLS encryption (HTTPS)'],
                            ['value' => 'encryption',    'label' => 'Data encryption at rest'],
                            ['value' => 'access_control','label' => 'Access controls and authentication'],
                            ['value' => 'firewalls',     'label' => 'Firewalls and intrusion detection'],
                            ['value' => 'audits',        'label' => 'Regular security audits'],
                            ['value' => 'staff_training','label' => 'Staff privacy training'],
                            ['value' => 'pseudonymisation','label' => 'Pseudonymisation / anonymisation'],
                        ],
                    ],
                    [
                        'id'      => 'data_transfers_outside_eea',
                        'label'   => 'Do you transfer personal data outside the EEA / UK?',
                        'type'    => 'toggle',
                        'default' => false,
                        'depends_on' => ['jurisdictions' => 'gdpr'],
                        'help'    => 'Required if your servers or third-party services are outside the EU/UK.',
                    ],
                    [
                        'id'      => 'transfer_safeguards',
                        'label'   => 'What safeguards do you use for international transfers?',
                        'type'    => 'multiselect',
                        'depends_on' => ['data_transfers_outside_eea' => true],
                        'options' => [
                            ['value' => 'scc',          'label' => 'Standard Contractual Clauses (SCCs)'],
                            ['value' => 'adequacy',     'label' => 'Adequacy decision by the European Commission'],
                            ['value' => 'bcr',          'label' => 'Binding Corporate Rules (BCRs)'],
                            ['value' => 'consent_transfer', 'label' => 'Explicit consent from the data subject'],
                        ],
                    ],
                    [
                        'id'      => 'notifies_breach_users',
                        'label'   => 'Will you notify users in the event of a personal data breach?',
                        'type'    => 'toggle',
                        'default' => true,
                        'help'    => 'GDPR requires notifying the supervisory authority within 72 hours. Users must be notified if there is a high risk to their rights and freedoms.',
                    ],
                    [
                        'id'         => 'breach_notification_timeframe',
                        'label'      => 'Within what timeframe will you notify affected users?',
                        'type'       => 'select',
                        'required'   => false,
                        'depends_on' => ['notifies_breach_users' => true],
                        'options'    => [
                            ['value' => '72h',   'label' => 'Within 72 hours'],
                            ['value' => '1week', 'label' => 'Within 1 week'],
                            ['value' => 'asap',  'label' => 'As soon as reasonably practicable'],
                        ],
                    ],
                ],
            ],

            
            // STEP 9: User Rights
            [
                'id'          => 'user_rights',
                'title'       => 'User Rights',
                'description' => 'Privacy laws grant users specific rights over their data.',
                'questions'   => [
                    [
                        'id'      => 'rights_granted',
                        'label'   => 'Which user rights do you honour?',
                        'type'    => 'multiselect',
                        'required'=> true,
                        'help'    => 'Select all that you support. GDPR mandates most of these.',
                        'options' => [
                            ['value' => 'access',        'label' => 'Right to access their data'],
                            ['value' => 'rectification', 'label' => 'Right to correct inaccurate data'],
                            ['value' => 'erasure',       'label' => 'Right to erasure ("right to be forgotten")'],
                            ['value' => 'portability',   'label' => 'Right to data portability'],
                            ['value' => 'restriction',   'label' => 'Right to restrict processing'],
                            ['value' => 'objection',     'label' => 'Right to object to processing'],
                            ['value' => 'no_auto',       'label' => 'Rights related to automated decision-making'],
                            ['value' => 'opt_out_sale',  'label' => 'Right to opt out of data sale (CCPA)'],
                            ['value' => 'withdraw_consent','label' => 'Right to withdraw consent'],
                        ],
                    ],
                    [
                        'id'          => 'rights_contact_method',
                        'label'       => 'How can users exercise their rights?',
                        'type'        => 'select',
                        'required'    => true,
                        'options'     => [
                            ['value' => 'email',  'label' => 'By emailing the privacy contact'],
                            ['value' => 'form',   'label' => 'Via an online request form'],
                            ['value' => 'portal', 'label' => 'Via a self-service privacy portal'],
                        ],
                    ],
                    [
                        'id'          => 'rights_form_url',
                        'label'       => 'URL of your privacy request form',
                        'type'        => 'url',
                        'required'    => false,
                        'placeholder' => 'https://example.com/privacy-request',
                        'depends_on'  => ['rights_contact_method' => 'form'],
                    ],
                    [
                        'id'      => 'supervisory_authority',
                        'label'   => 'Which supervisory authority can users lodge complaints with?',
                        'type'    => 'select',
                        'required'=> false,
                        'depends_on' => ['jurisdictions' => 'gdpr'],
                        'help'    => 'This is the data protection authority in your country.',
                        'options' => [
                            ['value' => 'ico',              'label' => 'ICO (UK)'],
                            ['value' => 'dpc',              'label' => 'DPC (Ireland)'],
                            ['value' => 'cnil',             'label' => 'CNIL (France)'],
                            ['value' => 'bfdi',             'label' => 'BfDI (Germany)'],
                            ['value' => 'agpd',             'label' => 'AEPD (Spain)'],
                            ['value' => 'garante',          'label' => 'Garante (Italy)'],
                            ['value' => 'ap',               'label' => 'AP (Netherlands)'],
                            ['value' => 'apd',              'label' => 'APD/GBA (Belgium)'],
                            ['value' => 'uodo',             'label' => 'UODO (Poland)'],
                            ['value' => 'imy',              'label' => 'IMY (Sweden)'],
                            ['value' => 'dsb',              'label' => 'DSB (Austria)'],
                            ['value' => 'datatilsynet_dk',  'label' => 'Datatilsynet (Denmark)'],
                            ['value' => 'datatilsynet_no',  'label' => 'Datatilsynet (Norway)'],
                            ['value' => 'cnpd',             'label' => 'CNPD (Portugal)'],
                            ['value' => 'uoou',             'label' => 'ÚOOÚ (Czech Republic)'],
                            ['value' => 'naih',             'label' => 'NAIH (Hungary)'],
                            ['value' => 'anspdcp',          'label' => 'ANSPDCP (Romania)'],
                            ['value' => 'tsa',              'label' => 'Tietosuojavaltuutettu (Finland)'],
                            ['value' => 'other',            'label' => 'Other EU/EEA authority'],
                        ],
                    ],
                ],
            ],

            
            // STEP 10: Policy Preferences
            [
                'id'          => 'policy_preferences',
                'title'       => 'Policy Preferences',
                'description' => 'Final options for how the policy is presented.',
                'questions'   => [
                    [
                        'id'      => 'policy_language',
                        'label'   => 'Policy language',
                        'type'    => 'select',
                        'required'=> true,
                        'default' => 'en_gb',
                        'options' => [
                            ['value' => 'en_gb', 'label' => 'English (UK)'],
                            ['value' => 'en_us', 'label' => 'English (US)'],
                            ['value' => 'fr',    'label' => 'French'],
                            ['value' => 'de',    'label' => 'German'],
                            ['value' => 'es',    'label' => 'Spanish'],
                            ['value' => 'it',    'label' => 'Italian'],
                            ['value' => 'nl',    'label' => 'Dutch'],
                            ['value' => 'da',    'label' => 'Danish'],
                        ],
                    ],
                    [
                        'id'      => 'effective_date',
                        'label'   => 'Policy effective date',
                        'type'    => 'date',
                        'required'=> true,
                        'default' => 'today',
                    ],
                    [
                        'id'          => 'policy_version',
                        'label'       => 'Policy version number',
                        'type'        => 'text',
                        'required'    => false,
                        'placeholder' => '1.0',
                        'default'     => '1.0',
                    ],
                    [
                        'id'      => 'notify_changes',
                        'label'   => 'How will you notify users of policy changes?',
                        'type'    => 'multiselect',
                        'options' => [
                            ['value' => 'email',   'label' => 'Email notification'],
                            ['value' => 'banner',  'label' => 'Website banner/notice'],
                            ['value' => 'update',  'label' => 'Updating the effective date on this page'],
                        ],
                    ],
                ],
            ],

        ];
    }

    /**
     * Returns a flat map of all question IDs for validation.
     */
    public static function getAllQuestionIds(): array
    {
        $ids = [];
        foreach (self::getSteps() as $step) {
            foreach ($step['questions'] as $q) {
                $ids[] = $q['id'];
            }
        }
        return $ids;
    }

    /**
     * Returns all questions marked required => true, each with its depends_on condition.
     * Used by PrivacyPolicyModel::rules() to build required validators dynamically.
     *
     * @return array  [ ['id' => string, 'depends_on' => array|null], ... ]
     */
    public static function getRequiredFields(): array
    {
        $required = [];
        foreach (self::getSteps() as $step) {
            foreach ($step['questions'] as $q) {
                if (!empty($q['required'])) {
                    $required[] = [
                        'id'         => $q['id'],
                        'depends_on' => $q['depends_on'] ?? null,
                    ];
                }
            }
        }
        return $required;
    }
}
