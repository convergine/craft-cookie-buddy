<?php

namespace convergine\cookiebuddy\services;

use Craft;
use craft\base\Component;
use convergine\cookiebuddy\models\PrivacyPolicyModel;
use convergine\cookiebuddy\records\LegalPolicyDocument;

/**
 * PrivacyPolicyService
 *
 * Renders the privacy policy Twig template from a populated PrivacyPolicyModel.
 * Also handles saving rendered HTML to the DB via ActiveRecord.
 */
class PrivacyPolicyService extends Component
{
    private const LANGUAGE_MAP = [
        'en_gb' => 'en',
        'en_us' => 'en',
        'fr'    => 'fr',
        'de'    => 'de',
        'es'    => 'es',
        'it'    => 'it',
        'nl'    => 'nl',
        'da'    => 'da',
    ];

    /**
     * Render the privacy policy HTML from a PrivacyPolicyModel.
     * Temporarily switches Craft::$app->language to the policy's selected language
     * so that all |t('convergine-cookiebuddy') calls inside the template use the
     * correct translation file.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function render(PrivacyPolicyModel $model): string
    {
        $targetLang   = self::LANGUAGE_MAP[$model->policy_language] ?? 'en';
        $originalLang = Craft::$app->language;
        Craft::$app->language = $targetLang;
        try {
            return Craft::$app->getView()->renderTemplate(
                'convergine-cookiebuddy/policies/privacy-policy',
                ['policy' => $model]
            );
        } finally {
            Craft::$app->language = $originalLang;
        }
    }

    /**
     * Generate and save a policy document from wizard POST data.
     * Returns the new document ID on success, or false on validation failure.
     */
    public function generateFromWizardData(array $wizardData): int|false
    {
        $model = PrivacyPolicyModel::fromWizardData($wizardData);

        if (!$model->validate()) {
            Craft::warning(
                'Privacy policy generation failed validation: ' . json_encode($model->getErrors()),
                __METHOD__
            );
            return false;
        }

        $html = $this->render($model);

        return $this->saveDocument($model, $html);
    }

    /**
     * Save the generated policy to the legal_policy_documents table.
     * Archives any existing published document for the same URL first.
     *
     * @return int  The new document ID
     */
    public function saveDocument(PrivacyPolicyModel $model, string $renderedHtml): int
    {
        // Archive any existing published version for this site
        $existing = LegalPolicyDocument::find()
            ->where([
                'type'        => 'privacy-policy',
                'website_url' => $model->website_url,
                'status'      => 'published',
            ])
            ->one();

        if ($existing) {
            $existing->status = 'archived';
            $existing->save(false);
        }

        // Insert new published version
        $record = new LegalPolicyDocument();
        $record->type           = 'privacy-policy';
        $record->version        = $model->policy_version;
        $record->website_url    = $model->website_url;
        $record->company_name   = $model->company_name;
        $record->settings_json  = json_encode($model->toStorageArray());
        $record->rendered_html  = $renderedHtml;
        $record->language       = $model->policy_language;
        $record->status         = 'published';
        $record->effective_date = $model->effective_date ?: null;
        $record->save(false);

        return (int)$record->id;
    }

    /**
     * Load a previously generated policy by document ID and restore its model.
     */
    public function loadDocument(int $documentId): ?PrivacyPolicyModel
    {
        $record = LegalPolicyDocument::findOne($documentId);

        if (!$record) {
            return null;
        }

        $settings = json_decode($record->settings_json, true);
        return PrivacyPolicyModel::fromStorageArray($settings);
    }

    /**
     * Re-render a saved policy (e.g. after editing settings).
     *
     * @return string|null  Rendered HTML or null if document not found
     */
    public function rerenderDocument(int $documentId): ?string
    {
        $record = LegalPolicyDocument::findOne($documentId);
        if (!$record) {
            return null;
        }

        $model = PrivacyPolicyModel::fromStorageArray(
            json_decode($record->settings_json, true)
        );

        $html = $this->render($model);

        $record->rendered_html = $html;
        $record->save(false);

        return $html;
    }
}
