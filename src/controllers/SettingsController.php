<?php

namespace convergine\cookiebuddy\controllers;

use convergine\cookiebuddy\CookieBuddyPlugin;
use craft\web\Controller;
use yii\web\Response;

class SettingsController extends Controller {
	public $customMessagesNamespace = '';
	public $currentSiteHandle = '';

	public function init(): void {
		parent::init();
		$this->currentSiteHandle = $this->request->getParam( 'site','default' );
		if ( $site = \Craft::$app->sites->getSiteByHandle( $this->currentSiteHandle ) ) {
			$this->customMessagesNamespace = "settings[customMessages][{$site->handle}]";
		}


	}

	public function actionGeneral(): Response {
		$settings = CookieBuddyPlugin::getInstance()->getSettings();

		return $this->renderTemplate( 'convergine-cookiebuddy/settings/_general', [
			'settings' => $settings,
		] );
	}

	public function actionGoogle(): Response {
		$settings = CookieBuddyPlugin::getInstance()->getSettings();

		return $this->renderTemplate( 'convergine-cookiebuddy/settings/_google', [
			'settings' => $settings,
		] );
	}

	public function actionCustomization(): Response {
		$settings = CookieBuddyPlugin::getInstance()->getSettings();

		return $this->renderTemplate( 'convergine-cookiebuddy/settings/_customization', [
			'settings'                => $settings,
			'customMessagesNamespace' => $this->customMessagesNamespace,
			'siteOptions'             => $this->getSiteOptions(),
			'currentSiteHandle'       => $this->currentSiteHandle
		] );
	}

	private function getSiteOptions(): array {
		$options = [];
		foreach ( \Craft::$app->sites->getAllSites() as $site ) {
			$options[] = [
				'label' => "{$site->name} ( {$site->language} )",
				'value' => $site->handle
			];
		}

		return $options;
	}
}
