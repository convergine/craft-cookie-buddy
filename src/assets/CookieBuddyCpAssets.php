<?php
namespace convergine\cookiebuddy\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use craft\web\View;

class CookieBuddyCpAssets extends AssetBundle {
    public function init(): void {
        $this->sourcePath = '@convergine/cookiebuddy/assets/cp';
	     $this->depends = [CpAsset::class];
        $this->js = ['cp-legal.js'];
        $this->css = ['cp-legal.css'];
        parent::init();
    }
}
