<?php
/**
 * @file
 * Provide services for MySQL and Apache via Ansible.
 */

/**
 * Implements hook_hosting_feature().
 */
function devshop_rancher_hosting_feature() {
    $features['rancher'] = array(
        'title' => t('Rancher Services'),
        'description' => t('Use Rancher for Aegir Services.'),
        'status' => HOSTING_FEATURE_DISABLED,
        'module' => 'devshop_rancher',
        'group' => 'advanced',
    );
    return $features;
}
