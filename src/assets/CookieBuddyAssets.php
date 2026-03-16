<?php
namespace convergine\cookiebuddy\assets;

use craft\web\AssetBundle;

class CookieBuddyAssets extends AssetBundle {
    public function init() : void {
        $enabled = getenv('WEBPACK_DEV_SERVER_ENABLED');
        $port = getenv('WEBPACK_DEV_SERVER_PORT');
        if($enabled && !empty($port)) {
            $this->sourcePath = null;
            $this->baseUrl = 'http://localhost:'.getenv('WEBPACK_DEV_SERVER_PORT');
        } else {
            $this->sourcePath = '@convergine/cookiebuddy/assets/dist';
        }
        $this->js = ['cookiebuddy.js'];
        $this->css = ['cookiebuddy.css'];
        parent::init();
    }
}
