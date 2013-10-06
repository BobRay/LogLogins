<?php
/** @var $modx modX */

$allContexts = array();
if (isset($user)) {
    $modx->user =& $user;
}
$ac = isset($attributes['addContexts'])
    ? $attributes['addContexts']
    : array();

$lc = isset($attributes['loginContext'])
    ? $attributes['loginContext']
    : '';

if ( (empty($ac))) {
    $allContexts[] = $lc;
} else {
    $allContexts = $ac;
    if (! in_array($lc, $ac)) {
        $allContexts[] = $lc;
    }
}

$msg = !empty($allContexts)? implode(',', $allContexts) : '';

$modx->log(MODX::LOG_LEVEL_ERROR, 'All: ' . print_r($allContexts, true));

$setting = $modx->getObject('modUserSetting',
    array(
         'user' => $user->get('id'),
         'key' => 'LogLogins.contexts',
    )
);


$act = 'unknown';
switch ($modx->event->name) {

    case 'OnWebLogin':
    case 'OnManagerLogin':

        if (! $setting) {
            $setting = $modx->newObject('modUserSetting');
            $setting->set('user', $user->get('id'));
            $setting->set('key', 'loglogins_contexts');
            $setting->set('namespace', 'loglogins');
        }
        if ($setting) {
            $setting->set('value', $msg);
            $setting->save();
        }
        $act = $modx->lexicon('login');
        $modx->logManagerAction($act, 'modContext', $msg);
        break;

    case 'OnWebLogout':
    case 'OnManagerLogout':
        if ($setting) {
            $msg = $setting->get('value');
        }
        $modx->lexicon->load('core:default');
        $modx->context = $modx->context->get('key');
        $act = $modx->lexicon('logout');
        $modx->logManagerAction($act, 'modContext', $msg);
        break;
}


