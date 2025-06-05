<?php
namespace convergine\cookiebuddy\assets;

use craft\web\AssetBundle;

class CookieBuddyAssets extends AssetBundle {
    public function init() : void {
        $this->sourcePath = '@convergine/cookiebuddy/assets/dist';
        $this->js = ['cookiebuddy.js'];
        $this->css = ['cookiebuddy.css'];
        parent::init();
    }
}
