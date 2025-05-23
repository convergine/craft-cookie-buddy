<?php
namespace convergine\cookiebuddy;

use convergine\cookiebuddy\assets\CookieBuddyAssets;
use convergine\cookiebuddy\models\SettingsModel;
use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\web\UrlManager;
use craft\web\View;
use yii\base\Event;

class CookieBuddyPlugin extends Plugin {
	public static string $plugin;
	public ?string $name = 'Cookie Buddy';

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
			}
		);
	}

    private function setEvents() : void {
        /** @var $settings SettingsModel */
        $settings = $this->getSettings();

        if(Craft::$app->getRequest()->getIsSiteRequest() && $settings->isEnabled()) {
            Event::on(
                View::class,
                View::EVENT_BEGIN_BODY,
                function(Event $event) {
                    $view = Craft::$app->getView();

	                $oldTemplatesPath = $view->getTemplatesPath();
	                $view->setTemplatesPath(__DIR__ . '/templates');
	                $variables = $view->renderTemplate('_variables.twig');
	                $view->setTemplatesPath($oldTemplatesPath);

                    //minify
                    $variables = preg_replace('/\s+/', ' ', $variables);
                    $variables = str_replace([' >', '< ', ' ,'], ['>', '<', ','], $variables);

                    $view->registerJs($variables, View::POS_HEAD);
                    $view->registerAssetBundle(CookieBuddyAssets::class);
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
