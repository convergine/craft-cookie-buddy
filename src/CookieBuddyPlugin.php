<?php
namespace convergine\cookiebuddy;

use convergine\cookiebuddy\assets\CookieBuddyAssets;
use convergine\cookiebuddy\models\SettingsModel;
use convergine\cookiebuddy\services\PrivacyPolicyService;
use convergine\cookiebuddy\variables\CookieBuddyVariable;
use Craft;
use craft\base\Plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use yii\base\Event;

/**
 * @property PrivacyPolicyService $privacyPolicy
 */
class CookieBuddyPlugin extends Plugin {
	public static string $plugin;
	public ?string $name = 'Cookie Buddy';

	public static function config(): array
	{
		return [
			'components' => [
				'privacyPolicy' => PrivacyPolicyService::class,
			],
		];
	}

	public function init() : void {
		$this->hasCpSection = false;
		$this->hasCpSettings = true;
		parent::init();

		$this->setRoutes();
        $this->setEvents();
	}

	protected function setRoutes() : void {
		Event::on(
			UrlManager::class,
			UrlManager::EVENT_REGISTER_CP_URL_RULES,
			function (RegisterUrlRulesEvent $event) {
                $event->rules['convergine-cookiebuddy/settings/general'] = 'convergine-cookiebuddy/settings/general';
                $event->rules['convergine-cookiebuddy/settings/google'] = 'convergine-cookiebuddy/settings/google';
                $event->rules['convergine-cookiebuddy/settings/customization'] = 'convergine-cookiebuddy/settings/customization';
                $event->rules['convergine-cookiebuddy/legal/privacy-policy'] = 'convergine-cookiebuddy/legal-policies/index';
                $event->rules['convergine-cookiebuddy/legal/privacy-policy/new'] = 'convergine-cookiebuddy/legal-policies/wizard';
                $event->rules['convergine-cookiebuddy/legal/generate'] = 'convergine-cookiebuddy/legal-policies/generate';
                $event->rules['convergine-cookiebuddy/legal/view'] = 'convergine-cookiebuddy/legal-policies/view';
                $event->rules['convergine-cookiebuddy/legal/delete'] = 'convergine-cookiebuddy/legal-policies/delete';
			}
		);
	}

    private function setEvents() : void {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $e) {
                /** @var CraftVariable $variable */
                $variable = $e->sender;
                $variable->set('cookieBuddy', CookieBuddyVariable::class);
            }
        );

        /** @var $settings SettingsModel */
        $settings = $this->getSettings();

        if(Craft::$app->getRequest()->getIsSiteRequest() && $settings->isEnabled()) {
            Event::on(
                View::class,
                View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
                function(RegisterTemplateRootsEvent $event) {
                    $event->roots['convergine-cookiebuddy'] = __DIR__ . '/templates';
                }
            );

            Event::on(
                View::class,
                View::EVENT_BEGIN_BODY,
                function(Event $event) {
                    $view = Craft::$app->getView();
                    $variables = $view->renderTemplate('convergine-cookiebuddy/_variables');
                    $view->registerJs($variables, View::POS_HEAD);
                    $view->registerAssetBundle(CookieBuddyAssets::class, View::POS_END);
                }
            );
        }
    }

	protected function createSettingsModel(): SettingsModel {
		return new SettingsModel();
	}

	/**
	 * @return string|null
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 * @throws \yii\base\Exception
	 */
	protected function settingsHtml(): ?string {
		return \Craft::$app->getView()->renderTemplate(
			'convergine-cookiebuddy/settings',
			[ 'settings' => $this->getSettings() ]
		);
	}

	/**
	 * @return mixed
	 */
	public function getSettingsResponse(): mixed {
		return Craft::$app->controller->redirect(UrlHelper::cpUrl('convergine-cookiebuddy/settings/general'));
	}
}
