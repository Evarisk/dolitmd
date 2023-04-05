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
 *  \file       view/certificate/certificate_list.php
 *  \ingroup    dolitmd
 *  \brief      Page to display list of certificates
 */

// Load dolitmd environment
if (file_exists('../../dolitmd.main.inc.php')) {
	require_once __DIR__ . '/../../dolitmd.main.inc.php';
} else {
	die('Include of dolitmd main fails');
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

// Initialize objects
// Technical objets
$object      = new Certificate($db);
$extrafields = new ExtraFields($db);

// View objects
$form = new Form($db);

$hookmanager->initHooks(['certificatelist']);

// Security check - Protection if external user
$permissiontoread = $user->rights->dolitmd->read;
//$permissiontodelete  = $user->rights->dolitmd->delete;
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
	$backtopage = dol_buildpath('/view/certificate/certificate_list.php', 1);


}

/*
 * View
 */

$title    = $langs->trans('CertificateList');
$help_url = 'FR:Module_DoliTMD';

saturne_header(0, '', $title, $help_url);

if (empty($permissiontoread)) {
	accessforbidden($langs->trans('NotEnoughPermissions'), 0);
	exit;
}
