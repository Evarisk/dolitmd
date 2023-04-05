<?php
/* Copyright (C) 2023 EVARISK <technique@evarisk.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    admin/setup.php
 * \ingroup dolitmd
 * \brief   dolitmd setup page.
 */

// Load dolitmd environment
if (file_exists('../dolitmd.main.inc.php')) {
	require_once __DIR__ . '/../dolitmd.main.inc.php';
} else {
	die('Include of dolitmd main fails');
}

// Libraries
require_once __DIR__ . '/../lib/dolitmd.lib.php';

// Global variables definitions
global $db, $langs, $user;

// Load translation files required by the page
saturne_load_langs();

// Get parameters
$backtopage = GETPOST('backtopage', 'alpha');

// Security check - Protection if external user
$permissiontoread = $user->rights->dolitmd->adminpage->read;
saturne_check_access($permissiontoread);

/*
 * View
 */

$title    = $langs->trans('ModuleSetup', 'dolitmd');
$help_url = 'FR:Module_dolitmd';

saturne_header(0,'', $title, $help_url);

// Subheader
$linkback = '<a href="' . ($backtopage ?: DOL_URL_ROOT . '/admin/modules.php?restore_lastsearch_values=1') . '">' . $langs->trans('BackToModuleList') . '</a>';
print load_fiche_titre($title, $linkback, 'dolitmd_color@dolitmd');

// Configuration header
$head = dolitmd_admin_prepare_head();
print dol_get_fiche_head($head, 'settings', $title, -1, 'dolitmd_color@dolitmd');

$db->close();
llxFooter();
