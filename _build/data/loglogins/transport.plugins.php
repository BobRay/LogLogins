<?php
/**
 * plugins transport file for LogLogins extra
 *
 * Copyright 2013-2023 Bob Ray <https://bobsguides.com>
 * Created on 10-05-2013
 *
 * @package loglogins
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $plugins */


$plugins = array();

$plugins[1] = $modx->newObject('modPlugin');
$plugins[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'LogLogins',
  'description' => '',
  'properties' => 
  array (
  ),
  'disabled' => true,
), '', true, true);
$plugins[1]->setContent(file_get_contents($sources['source_core'] . '/elements/plugins/loglogins.plugin.php'));

return $plugins;
