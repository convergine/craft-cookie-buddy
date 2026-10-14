<?php

namespace convergine\cookiebuddy\controllers;

use convergine\cookiebuddy\CookieBuddyPlugin;
use convergine\cookiebuddy\models\PrivacyPolicyModel;
use convergine\cookiebuddy\questions\PrivacyPolicyQuestions;
use convergine\cookiebuddy\records\LegalPolicyDocument;
use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LegalPoliciesController extends Controller
{
    /**
     * Lists all saved privacy policy documents.
     */
    public function actionIndex(): Response
    {
        $documents = LegalPolicyDocument::find()
            ->where(['type' => 'privacy-policy'])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->renderTemplate('convergine-cookiebuddy/legal/_index', [
            'documents' => $documents,
        ]);
    }

    /**
     * Renders the wizard form for generating a new privacy policy.
     * Detects the current Craft site language and passes a preferredLang variable
     * so the wizard can pre-select the matching policy language.
     */
    public function actionWizard(): Response
    {
        $site     = Craft::$app->getSites()->getCurrentSite();
        $siteLang = strtolower(substr($site->language, 0, 2));
        $langMap  = [
            'en' => 'en_gb',
            'fr' => 'fr',
            'de' => 'de',
            'es' => 'es',
            'it' => 'it',
            'nl' => 'nl',
            'da' => 'da',
        ];
        $preferredLang = $langMap[$siteLang] ?? 'en_gb';

        return $this->renderTemplate('convergine-cookiebuddy/legal/_wizard', [
            'steps'         => PrivacyPolicyQuestions::getSteps(),
            'preferredLang' => $preferredLang,
        ]);
    }

    /**
     * POST handler: generates a policy from wizard data and saves it to the DB.
     */
    public function actionGenerate(): Response
    {
        $this->requirePostRequest();

        $postData = Craft::$app->request->post();
        $model    = PrivacyPolicyModel::fromWizardData($postData);
        if (!$model->validate()) {
            return $this->renderTemplate('convergine-cookiebuddy/legal/_wizard', [
                'steps'     => PrivacyPolicyQuestions::getSteps(),
                'model'     => $model,
                'submitted' => $postData,
            ]);
        }

        $service = CookieBuddyPlugin::getInstance()->privacyPolicy;
        $docId   = $service->saveDocument($model, $service->render($model));

        Craft::$app->session->setNotice(Craft::t('convergine-cookiebuddy', 'Privacy policy generated successfully.'));
        return $this->redirect(UrlHelper::cpUrl('convergine-cookiebuddy/legal/view', ['id' => $docId]));
    }

    /**
     * Displays a saved policy document.
     */
    public function actionView(): Response
    {
        $id = (int)Craft::$app->request->get('id');

        $document = LegalPolicyDocument::findOne($id);

        if (!$document) {
            throw new NotFoundHttpException('Policy document not found.');
        }

        return $this->renderTemplate('convergine-cookiebuddy/legal/_view', [
            'document' => $document,
        ]);
    }

    /**
     * Renders the wizard prefilled with a saved policy's answers for editing.
     */
    public function actionEdit(): Response
    {
        $id = (int)Craft::$app->request->get('id');

        $document = LegalPolicyDocument::findOne($id);

        if (!$document) {
            throw new NotFoundHttpException('Policy document not found.');
        }

        // The date field reads model.effective_date rather than submitted values,
        // so the restored model is passed too.
        return $this->renderTemplate('convergine-cookiebuddy/legal/_wizard', [
            'steps'      => PrivacyPolicyQuestions::getSteps(),
            'model'      => CookieBuddyPlugin::getInstance()->privacyPolicy->loadDocument($id),
            'submitted'  => json_decode($document->settings_json, true) ?: [],
            'documentId' => $document->id,
        ]);
    }

    /**
     * POST handler: re-renders an existing policy from edited wizard data and
     * overwrites it in place, keeping its ID so embed snippets stay valid.
     */
    public function actionUpdate(): Response
    {
        $this->requirePostRequest();

        $id = (int)Craft::$app->request->post('id');

        if (!LegalPolicyDocument::find()->where(['id' => $id])->exists()) {
            throw new NotFoundHttpException('Policy document not found.');
        }

        $postData = Craft::$app->request->post();
        $model    = PrivacyPolicyModel::fromWizardData($postData);
        if (!$model->validate()) {
            return $this->renderTemplate('convergine-cookiebuddy/legal/_wizard', [
                'steps'      => PrivacyPolicyQuestions::getSteps(),
                'model'      => $model,
                'submitted'  => $postData,
                'documentId' => $id,
            ]);
        }

        $service = CookieBuddyPlugin::getInstance()->privacyPolicy;
        $service->updateDocument($id, $model, $service->render($model));

        Craft::$app->session->setNotice(Craft::t('convergine-cookiebuddy', 'Privacy policy updated.'));
        return $this->redirect(UrlHelper::cpUrl('convergine-cookiebuddy/legal/view', ['id' => $id]));
    }

    /**
     * Deletes a policy document and redirects to the index.
     */
    public function actionDelete(): Response
    {
        $this->requirePostRequest();

        $id = (int)Craft::$app->request->post('id');
        $record = LegalPolicyDocument::findOne($id);

        if ($record) {
            $record->delete();
            Craft::$app->session->setNotice(Craft::t('convergine-cookiebuddy', 'Policy deleted.'));
        }

        return $this->redirect(UrlHelper::cpUrl('convergine-cookiebuddy/legal/privacy-policy'));
    }
}
