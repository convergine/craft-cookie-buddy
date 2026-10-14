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

	/**
	 * Saves the settings posted from one of the settings pages.
	 *
	 * Craft 5.8.22+ and 4.16.18+ store only the posted settings, and each page posts its own
	 * subset, so plugins/save-plugin-settings would reset the other pages' settings. Start from
	 * the current settings instead, leaving out values that come from a config file.
	 */
	public function actionSave(): ?Response {
		$this->requirePostRequest();
		$this->requireAdmin();

		$plugin     = CookieBuddyPlugin::getInstance();
		$fileConfig = \Craft::$app->getConfig()->getConfigFromFile( $plugin->handle );
		$settings   = array_merge(
			array_diff_key( $plugin->getSettings()->toArray(), $fileConfig ),
			$this->request->getBodyParam( 'settings', [] )
		);

		return \Craft::$app->getPlugins()->savePluginSettings( $plugin, $settings )
			? $this->asSuccess( \Craft::t( 'app', 'Plugin settings saved.' ) )
			: $this->asFailure( \Craft::t( 'app', 'Couldn’t save plugin settings.' ), routeParams: [ 'plugin' => $plugin ] );
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
