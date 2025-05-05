<?php
namespace convergine\cookiebuddy\models;

use craft\base\Model;

class SettingsModel extends Model {
    public bool $isEnabled = true;

    public bool $sendGtag = true;

    public string $title = "Hello visitor, it's cookie time!";
    public string $description = "Cookies don't store your personal information and don't know who you are. Your browsing on our website is anonymous. It is important to note that cookies cannot harm your device.";
    public string $footer = "<a href=\"/privacy\">Privacy Policy</a>";

    public string $consentLayout = 'box';
    public string $consentPosition = 'bottom right';
    public bool $consentEqualWeightButtons = true;
    public bool $consentFlipButtons = false;

    public string $internalTitle = "Cookie Usage";
    public string $internalDescription = "A cookie is a small text file sent to your browser and stored on your device by a website you visit. Cookies may save information about the pages you visit and the devices you use, which in return can give us more insight about how you use our website so we can improve its usability and deliver more relevant content.";

    public string $requiredDescription = "These cookies are necessary for our website to function properly. They cannot be turned off in our systems.";
    public string $performanceDescription = "To measure and improve the performance of our website, we monitor events such as page visits, page scrolls, clicks on links, form submissions, or how many times a video is watched.";
    public string $targetingDescription = "We use Google to promote our services. Google can track your interactions on other websites, so you may see our ads while browsing sites that are not related to our business.";

    public string $moreTitle = "More information";
    public string $moreDescription = "For any query in relation to my policy on cookies and your choices, please <a class=\"cc__link\" href=\"/contact\">contact us</a>.";

    public string $preferencesLayout = 'box';
    public string $preferencesPosition = 'right';
    public bool $preferencesEqualWeightButtons = true;
    public bool $preferencesFlipButtons = false;

    public function isEnabled(): bool {
        return $this->isEnabled;
    }
}
