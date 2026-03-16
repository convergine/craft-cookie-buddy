(function () {
    'use strict';

    // Wizard: depends_on visibility

    function cbFieldSatisfies(fieldName, expectValue) {
        // Multiselect checkboxes: name="fieldName[]"
        var checkboxes = document.querySelectorAll('input[type="checkbox"][name="' + fieldName + '[]"]');
        if (checkboxes.length > 0) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].value === expectValue && checkboxes[i].checked) {
                    return true;
                }
            }
            return false;
        }

        // Lightswitch hidden input: name="fieldName"
        var hidden = document.querySelector('input[type="hidden"][name="' + fieldName + '"]');
        if (hidden) {
            if (expectValue === 'true' || expectValue === '1') {
                return hidden.value === '1';
            }
            return hidden.value === expectValue;
        }

        // Select element
        var select = document.querySelector('select[name="' + fieldName + '"]');
        if (select) {
            return select.value === expectValue;
        }

        // Plain text/email/url input
        var input = document.querySelector('input[name="' + fieldName + '"]');
        if (input) {
            return input.value === expectValue;
        }

        return false;
    }

    function cbUpdateDependentFields() {
        var wrappers = document.querySelectorAll('[data-depends-on]');
        wrappers.forEach(function (wrapper) {
            var field = wrapper.getAttribute('data-depends-on');
            var value = wrapper.getAttribute('data-depends-value');
            if (cbFieldSatisfies(field, value)) {
                wrapper.classList.remove('hidden');
            } else {
                wrapper.classList.add('hidden');
            }
        });
    }

    // Wizard: legal basis map
    // Data is provided by the template via window.cbLegalBasisConfig

    function cbBuildLegalBasisRows() {
        var config = window.cbLegalBasisConfig || {};
        var basesOptions = config.basesOptions || [];
        var savedMap     = config.savedMap || {};
        var emptyLabel   = config.emptyLabel || '— select —';

        var tbody  = document.getElementById('pp-legal-basis-rows');
        var notice = document.getElementById('pp-legal-basis-empty');
        if (!tbody) return;

        var checked = Array.from(
            document.querySelectorAll('input[type="checkbox"][name="processing_purposes[]"]:checked')
        );

        // Preserve existing select values before clearing
        var currentMap = {};
        tbody.querySelectorAll('select').forEach(function (sel) {
            var m = sel.name.match(/\[(.+)\]$/);
            if (m) currentMap[m[1]] = sel.value;
        });

        tbody.innerHTML = '';
        if (checked.length === 0) {
            notice.style.display = '';
            return;
        }
        notice.style.display = 'none';

        checked.forEach(function (cb) {
            var purpose = cb.value;
            // Find translated label from the rendered checkbox label
            var labelEl = document.querySelector('label[for="processing_purposes-' + purpose + '"]');
            var purposeText = labelEl ? labelEl.textContent.trim() : purpose;

            var tr = document.createElement('tr');

            var tdLabel = document.createElement('td');
            tdLabel.style.padding = '.4rem .5rem';
            tdLabel.style.verticalAlign = 'middle';
            tdLabel.textContent = purposeText;

            var tdSelect = document.createElement('td');
            tdSelect.style.padding = '.4rem .5rem';

            var sel = document.createElement('select');
            sel.name = 'legal_bases_map[' + purpose + ']';
            sel.className = 'select fullwidth';

            var emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = emptyLabel;
            sel.appendChild(emptyOpt);

            basesOptions.forEach(function (opt) {
                var o = document.createElement('option');
                o.value = opt.value;
                o.textContent = opt.label;
                // Restore: check preserved map first, then savedMap from server
                if ((currentMap[purpose] || savedMap[purpose]) === opt.value) {
                    o.selected = true;
                }
                sel.appendChild(o);
            });

            tdSelect.appendChild(sel);
            tr.appendChild(tdLabel);
            tr.appendChild(tdSelect);
            tbody.appendChild(tr);
        });
    }

    // Wizard: GDPR rights pre-selection

    var cbGdprRightsApplied = false;
    var cbCoreGdprRights = ['access', 'rectification', 'erasure', 'portability', 'restriction', 'objection', 'withdraw_consent'];

    function cbApplyGdprRightsDefaults() {
        if (cbGdprRightsApplied) return;
        var gdprCb = document.querySelector('input[type="checkbox"][name="jurisdictions[]"][value="gdpr"]');
        if (gdprCb && gdprCb.checked) {
            cbGdprRightsApplied = true;
            cbCoreGdprRights.forEach(function (right) {
                var cb = document.querySelector('input[type="checkbox"][name="rights_granted[]"][value="' + right + '"]');
                if (cb && !cb.checked) {
                    cb.checked = true;
                }
            });
        }
    }

    // Wizard: international transfer auto-warning
    // Transfer notice text is provided by the template via window.cbWizardConfig

    var cbNonEeaServices = [
        'ga4', 'google_analytics', 'google_tag', 'google_ads', 'facebook_pixel',
        'linkedin_pixel', 'twitter_pixel', 'tiktok_pixel', 'bing_ads',
        'aws', 'gcp', 'azure', 'cloudflare', 'vercel',
        'mailchimp', 'hubspot', 'salesforce', 'klaviyo', 'convertkit', 'activecampaign', 'mailerlite',
        'stripe', 'paypal', 'braintree',
        'mixpanel', 'hotjar', 'segment', 'heap', 'clarity',
        'intercom', 'zendesk', 'drift',
        'google_login', 'facebook_login', 'twitter_login', 'github_login'
    ];

    var cbTransferNoticeId = 'pp-transfer-auto-notice';

    function cbUpdateTransferNotice() {
        var gdprCb = document.querySelector('input[type="checkbox"][name="jurisdictions[]"][value="gdpr"]');
        if (!gdprCb || !gdprCb.checked) return;

        var hasNonEea = cbNonEeaServices.some(function (svc) {
            // Check any of the service multiselect groups
            var serviceFields = ['analytics_services', 'advertising_services', 'payment_services',
                                 'email_services', 'support_services', 'social_services', 'hosting_services'];
            return serviceFields.some(function (field) {
                var cb = document.querySelector('input[type="checkbox"][name="' + field + '[]"][value="' + svc + '"]:checked');
                return !!cb;
            });
        });

        var transferWrap = document.getElementById('wrap-data_transfers_outside_eea');
        if (!transferWrap) return;

        var existing = document.getElementById(cbTransferNoticeId);
        if (hasNonEea) {
            if (!existing) {
                var notice = document.createElement('p');
                notice.id = cbTransferNoticeId;
                notice.className = 'light';
                notice.style.cssText = 'margin:.25rem 0 0; color:#c06b00; font-size:.875em;';
                notice.textContent = (window.cbWizardConfig && window.cbWizardConfig.transferNoticeText) || '';
                transferWrap.appendChild(notice);
            }
        } else {
            if (existing) existing.remove();
        }
    }

    // Wizard: init

    if (document.getElementById('privacy-policy-wizard')) {
        // Run on page load
        cbUpdateDependentFields();

        // Listen to checkbox/select changes
        document.addEventListener('change', function (e) {
            if (e.target.type === 'checkbox' || e.target.tagName === 'SELECT') {
                cbUpdateDependentFields();
            }
        });

        // Listen to Craft lightswitch toggles via MutationObserver
        var cbHiddenInputs = document.querySelectorAll('.lightswitch input[type="hidden"]');
        cbHiddenInputs.forEach(function (hiddenInput) {
            var observer = new MutationObserver(cbUpdateDependentFields);
            observer.observe(hiddenInput, { attributes: true, attributeFilter: ['value'] });
        });

        // Also listen to lightswitch click as a fallback
        document.addEventListener('click', function (e) {
            if (e.target.closest && e.target.closest('.lightswitch')) {
                // Small timeout to let Craft update the hidden input first
                setTimeout(cbUpdateDependentFields, 50);
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.type === 'checkbox' && e.target.name === 'jurisdictions[]' && e.target.value === 'gdpr' && e.target.checked) {
                cbApplyGdprRightsDefaults();
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.type === 'checkbox') {
                cbUpdateTransferNotice();
            }
        });

        // Build on load and whenever processing_purposes checkboxes change
        cbBuildLegalBasisRows();
        document.addEventListener('change', function (e) {
            if (e.target.type === 'checkbox' && e.target.name === 'processing_purposes[]') {
                cbBuildLegalBasisRows();
            }
        });

        // Run on page load in case form is re-rendered after validation failure
        cbApplyGdprRightsDefaults();
        cbUpdateTransferNotice();
    }

    // View: copy functions
    // Label strings are provided by the template via window.cbViewConfig

    function cbCopyToClipboard(text, btn, originalLabel, successLabel) {
        var done = function () {
            if (btn && successLabel) {
                btn.textContent = successLabel;
                setTimeout(function () { btn.textContent = originalLabel; }, 2000);
            }
        };
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(done);
        } else {
            var ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            done();
        }
    }

    window.cbCopyEmbed = function () {
        var text = document.getElementById('embed-code').innerText;
        cbCopyToClipboard(text);
    };

    window.cbCopyPolicyHtml = function () {
        var config = window.cbViewConfig || {};
        var btn = document.getElementById('copy-policy-html-btn');
        var text = document.querySelector('.legal-policy-preview').innerHTML;
        cbCopyToClipboard(text, btn, config.labelCopyHtml, config.labelCopied);
    };

    window.cbCopyPolicyText = function () {
        var config = window.cbViewConfig || {};
        var btn = document.getElementById('copy-policy-text-btn');
        var text = document.querySelector('.legal-policy-preview').innerText;
        cbCopyToClipboard(text, btn, config.labelCopyText, config.labelCopied);
    };

}());
