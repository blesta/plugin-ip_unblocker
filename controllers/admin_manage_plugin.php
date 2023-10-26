<?php
/**
 * IP Unblocker manage plugin controller
 *
 * @package blesta
 * @subpackage blesta.plugins.ip_unblocker
 * @copyright Copyright (c) 2023, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class AdminManagePlugin extends AppController
{
    /**
     * Performs necessary initialization
     */
    private function init()
    {
        // Require login
        $this->parent->requireLogin();

        Language::loadLang('ip_unblocker_manage_plugin', null, PLUGINDIR . 'ip_unblocker' . DS . 'language' . DS);

        $this->plugin_id = $this->get[0] ?? null;

        // Set the page title
        $this->parent->structure->set(
            'page_title',
            Language::_(
                'IpUnblockerManagePlugin.'
                . Loader::fromCamelCase($this->action ? $this->action : 'index') . '.page_title',
                true
            )
        );
    }

    /**
     * Returns the view to be rendered when managing this plugin
     */
    public function index()
    {
        $this->init();

        if (!isset($this->Companies)) {
            Loader::loadComponents($this, ['Companies']);
        }

        // Get setting
        $client_set_ip = $this->Companies->getSetting(Configure::get('Blesta.company_id'), 'ip_unblocker_client_set_ip');

        $vars = [
            'plugin_id' => $this->plugin_id,
            'client_set_ip' => $client_set_ip->value ?? 'false'
        ];

        if (!empty($this->post)) {
            if (!isset($this->post['client_set_ip'])) {
                $this->post['client_set_ip'] = 'false';
            }

            $this->Companies->setSetting(
                Configure::get('Blesta.company_id'),
                'ip_unblocker_client_set_ip',
                $this->post['client_set_ip']
            );

            // Success
            $this->parent->flashMessage('message', Language::_('IpUnblockerManagePlugin.!success.settings_updated', true));
            $this->redirect($this->base_uri . 'settings/company/plugins/manage/' . $this->plugin_id);
        }

        // Set the view to render for all actions under this controller
        $this->view->setView(null, 'IpUnblocker.default');

        return $this->partial('admin_manage_plugin', $vars);
    }
}
