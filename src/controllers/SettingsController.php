<?php

namespace convergine\cookiebuddy\controllers;

use convergine\cookiebuddy\CookieBuddyPlugin;
use craft\web\Controller;
use yii\web\Response;

class SettingsController extends Controller {
    public function actionGeneral() : Response {
        $settings = CookieBuddyPlugin::getInstance()->getSettings();

        return $this->renderTemplate('convergine-cookiebuddy/settings/_general', [
            'settings' => $settings,
        ]);
    }

    public function actionGoogle() : Response {
        $settings = CookieBuddyPlugin::getInstance()->getSettings();

        return $this->renderTemplate('convergine-cookiebuddy/settings/_google', [
            'settings' => $settings,
        ]);
    }

    public function actionCustomization() : Response {
        $settings = CookieBuddyPlugin::getInstance()->getSettings();

        return $this->renderTemplate('convergine-cookiebuddy/settings/_customization', [
            'settings' => $settings,
        ]);
    }
}
