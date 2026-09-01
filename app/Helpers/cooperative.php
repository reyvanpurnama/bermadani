<?php

use App\Models\CooperativeSetting;

if (!function_exists('coop_setting')) {
    /**
     * Get a cooperative setting from database (cached).
     *
     * @param string $key   The setting key
     * @param mixed $default Default value if not found
     * @return string|null
     */
    function coop_setting(string $key, $default = null): ?string
    {
        return CooperativeSetting::getValue($key, $default);
    }
}

if (!function_exists('coop_config')) {
    /**
     * Smart resolver for cooperative config with DB fallback support.
     * Checks database setting first (e.g. 'coop_name'), falls back to config('cooperative.name').
     *
     * @param string $key   Dot-notation key relative to 'cooperative.'
     * @param mixed $default Default value
     * @return mixed
     */
    function coop_config(string $key, $default = null)
    {
        // Map dot notation to DB setting key overrides
        $dbKeyMap = [
            'name'                     => 'coop_name',
            'legal_name'               => 'coop_legal_name',
            'short_name'               => 'coop_short_name',
            'parent_org'               => 'coop_parent_org',
            'tagline'                  => 'coop_tagline',
            'landing_tagline'          => 'coop_landing_tagline',
            'website'                  => 'coop_website',
            'email_domain'             => 'coop_email_domain',
            'address'                  => 'coop_address',
            'city'                     => 'coop_city',
            'phone'                    => 'coop_phone',
            'logo_path'                => 'logo_path',
            'kop_surat_path'           => 'kop_surat_path',
            'favicon_path'             => 'favicon_path',
            'theme.primary'            => 'theme_primary',
            'theme.admin'              => 'theme_admin',
            'theme.member'             => 'theme_member',
            'theme.membership'         => 'theme_membership',
            'theme.supplier'           => 'theme_supplier',
            'finance.simpanan_wajib_default'    => 'fin_simwa_default',
            'finance.loan_admin_fee'            => 'fin_loan_admin_fee',
            'finance.bmt_simwa_deduction'       => 'fin_bmt_simwa_deduction',
            'finance.supplier_registration_fee' => 'fin_supplier_reg_fee',
            'finance.supplier_monthly_fee'      => 'fin_supplier_monthly_fee',
            'finance.consignment_profit_share'  => 'fin_consignment_share',
            'receipt.footer_text'      => 'receipt_footer_text',
            'receipt.policy_text'      => 'receipt_policy_text',
        ];

        // Check if DB override exists
        if (isset($dbKeyMap[$key])) {
            $dbValue = coop_setting($dbKeyMap[$key]);
            if ($dbValue !== null && $dbValue !== '') {
                return $dbValue;
            }
        }

        return config("cooperative.{$key}", $default);
    }
}
