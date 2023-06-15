<?php
/** @var $modx modX */
$pluginKey = 'loglogins_contexts';

 /* Make it run in either MODX 2 or MODX 3 */
 $prefix = $modx->getVersionData()['version'] >= 3
   ? 'MODX\Revolution\\'
   : '';


/* Set action based on event name */
if (strpos($modx->event->name, 'Login') !== false) {
    $act = $modx->lexicon('login');
} else {
    $modx->lexicon->load('core:default');
    $act = $modx->lexicon('logout');
}

/* Need this because $modx->user is not available in some cases
   and MODX uses it for the actor in logManagerActions()*/
if (isset($user)) {
    $modx->user =& $user;
}

$allContexts = array();


/* These are from the vars passed to the event on login.
   Converted here to a comma-separated string */
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

/* $msg is always 'mgr' for manager logins and logouts */
if (strpos($modx->event->name, 'Manager') !== false) {
    $msg = 'mgr';
}

switch ($modx->event->name) {
    /* Save contexts in user setting */
    case 'OnWebLogin':
        $setting = $modx->getObject($prefix . 'modUserSetting',
            array(
             'user' => $user->get('id'),
             'key' => $pluginKey,
            ));

        if (! $setting) {
            /* Create User Setting if it doesn't exist */
            $setting = $modx->newObject($prefix . 'modUserSetting');
            $setting->set('user', $user->get('id'));
            $setting->set('key', $pluginKey);
            $setting->set('namespace', 'loglogins');
        }
        /* Set value to login contexts */
        if ($setting) {
            $setting->set('value', $msg);
            $setting->save();
        }
        break;

    case 'OnWebLogout':
        /* Get contexts from user setting */
        $setting = $modx->getObject($prefix . 'modUserSetting',
            array(
                 'user' => $user->get('id'),
                 'key' => $pluginKey,
            ));

        if ($setting) {
            $msg = $setting->get('value');
        } else {
            $msg = '';
        }
        break;
}

$modx->logManagerAction($act, 'modContext', $msg);

return '';