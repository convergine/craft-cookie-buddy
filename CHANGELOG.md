# Cookie Buddy - Changelog

## 1.1.0 - 2026-10-14
### Added
- Added pre-made design themes: Light, Dark, Light Funky and Elegant Black.
- Added an option to turn non-essential cookie categories off by default, for opt-in jurisdictions.
- Added a consent revision setting that shows the consent banner again after privacy policy or cookie changes, with an optional per-site message.
- Added the ability to edit generated privacy policies. [Issue #8](https://github.com/convergine/craft-cookie-buddy/issues/8)

### Fixed
- Fixed the privacy policy effective date having its day and month swapped in d/m/Y locales. [Issue #7](https://github.com/convergine/craft-cookie-buddy/issues/7)
- Fixed an error when saving a privacy policy with an invalid effective date.
- Fixed settings on one settings page being reset when saving another, on Craft 5.8.22+ and 4.16.18+.
- Fixed missing `legal_policy_documents` database table on sites first installed on 1.0.7 or updated from 1.0.6 and earlier. [Issue #6](https://github.com/convergine/craft-cookie-buddy/issues/6)

## 1.0.9 - 2026-07-15
### Added
- Added an optional "Reject All" button on the initial consent banner, shown with equal prominence to "Accept All" for UK/EU compliance. Off by default, enabled per-site under Customization → Popup.

## 1.0.8 - 2026-03-30
### Fixed
- Fixed missing `legal_policy_documents` database table. [Issue #6](https://github.com/convergine/craft-cookie-buddy/issues/6)

## 1.0.7 - 2026-03-16
### Added
- Added policy generator with new compliance and e-commerce templates.

## 1.0.6 - 2025-09-17
### Added
- Allow resetting cookies. [Issue #2](https://github.com/convergine/craft-cookie-buddy/issues/2)

### Fixed
- Fixed an issue with having multiple rows in text. [Issue #5](https://github.com/convergine/craft-cookie-buddy/issues/5)

## 1.0.5 - 2025-08-20
### Fixed
- Fixed an issue with saving settings on single-language sites. [Issue #4](https://github.com/convergine/craft-cookie-buddy/issues/4)

## 1.0.4 - 2025-08-14
### Added
- Added multilingual support. [Issue #3](https://github.com/convergine/craft-cookie-buddy/issues/3)

## 1.0.3 - 2025-06-05
### Fixed
- Minification code and localhost references have been removed [Issue #1](https://github.com/convergine/craft-cookie-buddy/issues/1)

## 1.0.2 - 2025-06-02
### Fixed
- Register Cookie Buddy's template root instead of changing the template path, and make sure variables are loaded before the main script [Issue #1](https://github.com/convergine/craft-cookie-buddy/issues/1)

## 1.0.1 - 2025-05-23
### Fixed
- Installation issue fixed [Issue #1](https://github.com/convergine/craft-cookie-buddy/issues/1)  (Thanks to @wmdhosting & @heilapom for reporting this issue)

## 1.0.0 - 2025-05-05
### Added
- Initial release
