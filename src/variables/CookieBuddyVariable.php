<?php

namespace convergine\cookiebuddy\variables;

use convergine\cookiebuddy\records\LegalPolicyDocument;

/**
 * CookieBuddyVariable
 *
 * Exposes plugin functionality to Twig templates via the `craft.cookieBuddy` variable.
 */
class CookieBuddyVariable
{
    /**
     * Returns the rendered HTML for a saved privacy policy document.
     *
     * Usage in templates:
     *   {{ craft.cookieBuddy.renderedHtml(42)|raw }}
     *
     * @param int $id  The legal_policy_documents record ID
     * @return string  Rendered HTML, or empty string if not found
     */
    public function renderedHtml(int $id): string
    {
        $record = LegalPolicyDocument::findOne($id);

        return $record ? (string)$record->rendered_html : '';
    }
}
