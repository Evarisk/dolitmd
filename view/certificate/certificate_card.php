<?php
/* Copyright (C) 2023 EVARISK <technique@evarisk.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \file       view/certificate/certificate_card.php
 *  \ingroup    dolitmd
 *  \brief      Page to display and use specific certificate
 */

// Load dolitmd environment
if (file_exists('../../dolitmd.main.inc.php')) {
    require_once __DIR__ . '/../../dolitmd.main.inc.php';
} else {
    die('Include of dolitmd main fails');
}

// Libraries
if (isModEnabled('project')) {
    require_once DOL_DOCUMENT_ROOT . '/core/class/html.formprojet.class.php';

    require_once DOL_DOCUMENT_ROOT . '/projet/class/project.class.php';
    require_once DOL_DOCUMENT_ROOT . '/projet/class/task.class.php';
}
if (isModEnabled('societe')) {
	require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
	require_once DOL_DOCUMENT_ROOT . '/contact/class/contact.class.php';
}

// Global variables definitions
global $conf, $db, $hookmanager, $langs, $user;

// Load translation files required by the page
saturne_load_langs();

// Get parameters
$action      = GETPOST('action', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'quickcretion'; // To manage different context of search
$cancel      = GETPOST('cancel', 'aZ09');
$backtopage  = GETPOST('backtopage', 'alpha');

// Initialize technical objects
if (isModEnabled('societe')) {
    $thirdparty = new Societe($db);
	$contact    = new Contact($db);
}

// Initialize view objects
$form = new Form($db);
if (isModEnabled('project')) {
    $formproject = new FormProjets($db);
}

$hookmanager->initHooks(['certificatecard']); // Note that conf->hooks_modules contains array

$date_start = dol_mktime(0, 0, 0, GETPOST('projectstartmonth', 'int'), GETPOST('projectstartday', 'int'), GETPOST('projectstartyear', 'int'));

// Security check - Protection if external user
$permissiontoread = $user->rights->dolitmd->read;
$permissiontoadd  = $user->rights->dolitmd->write;
saturne_check_access($permissiontoread);

/*
 * Actions
 */

$parameters = [];
$reshook = $hookmanager->executeHooks('doActions', $parameters, $project, $action); // Note that $action and $project may have been modified by some hooks
if ($reshook < 0) {
    setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
    $error = 0;

    $backurlforlist = dol_buildpath('/certificate/certificate_list.php', 1);

    if (empty($backtopage) || ($cancel && empty($id))) {
        if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
            if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
                $backtopage = $backurlforlist;
            } else {
                $backtopage = dol_buildpath('/certificate/certificate_card.php', 1) . '?id=' . ((!empty($id) && $id > 0) ? $id : '__ID__');
            }
        }
    }

    if ($cancel) {
        if (!empty($backtopageforcancel)) {
            header('Location: ' .$backtopageforcancel);
            exit;
        } elseif (!empty($backtopage)) {
            header('Location: ' .$backtopage);
            exit;
        }
        $action = '';
    }

    if ($action == 'add' && $permissiontoadd) {
        if (!$error) {

            //$result = $object->create($user);
            //if (!$error && $result > 0) {
                // Do something
            //} else {
            //    $langs->load('errors');
            //    setEventMessages($project->error, $project->errors, 'errors');
            //    $error++;
            //}
            if (!$error) {
                if (!empty($backtopage)) {
                    $backtopage = preg_replace('/--IDFORBACKTOPAGE--|__ID__/', $project->id, $backtopage); // New method to autoselect project after a New on another form object creation
                    header('Location: ' . $backtopage);
                    exit;
                } else {
                    header('Location:card.php?id=' . $project->id);
                    exit;
                }
            } else {
                $db->rollback();
                unset($_POST['ref']);
                $action = 'create';
            }
        } else {
            $action = 'create';
        }
    }
}

/*
 * View
 */

$title    = $langs->trans('Certificate');
$help_url = 'FR:Module_DoliTMD';

saturne_header(0, '', $title, $help_url);

if (empty($permissiontoread)) {
    accessforbidden($langs->trans('NotEnoughPermissions'), 0);
    exit;
}

// Quick add project/task
if ($permissiontoread) {
    print load_fiche_titre($langs->trans('Certificate'), '', 'project');
    print dol_get_fiche_head();


    print dol_get_fiche_end();
}



// End of page
llxFooter();
$db->close();
