<?php
/**
 * @author Christoph Bessei
 */

namespace BIT\FUM\PluginManagement;

use BIT\FUM\Settings\Settings;

class Initialisation
{
    public static function run()
    {
        static::registerShortcodes();
        static::addAction();
        static::addFilter();
        static::registerScheduler();
        static::registerSettings();
    }

    /**
     * Register backend settings page / options page
     */
    protected static function registerSettings()
    {
        if (is_admin()) {
            Settings::register();
        }
    }

    /**
     *
     */
    protected static function addFilter()
    {
    }

    /**
     * Register shortcodes
     */
    protected static function registerShortcodes()
    {
    }

    /**
     *
     */
    protected static function addAction()
    {
    }

    /**
     *
     */
    protected static function registerScheduler()
    {
    }
}