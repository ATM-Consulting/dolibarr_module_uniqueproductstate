<?php
/* Copyright (C) 2010-2013	Regis Houssin		<regis.houssin@inodbox.com>
 * Copyright (C) 2010-2011	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012-2013	Christophe Battarel	<christophe.battarel@altairis.fr>
 * Copyright (C) 2012       Cédric Salvador     <csalvador@gpcsolutions.fr>
 * Copyright (C) 2012-2014  Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2013		Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2017		Juanjo Menent		<jmenent@2byte.es>
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
 *
 * Need to have following variables defined:
 * $object (invoice, order, ...)
 * $conf
 * $langs
 * $element     (used to test $user->rights->$element->creer)
 * $permtoedit  (used to replace test $user->rights->$element->creer)
 * $inputalsopricewithtax (0 by default, 1 to also show column with unit price including tax)
 * $outputalsopricetotalwithtax
 * $usemargins (0 to disable all margins columns, 1 to show according to margin setup)
 *
 * $type, $text, $description, $line
 */

// Protection to avoid direct call of template
if (empty($object) || !is_object($object))
{
	print "Error, template page can't be called as URL";
	exit;
}
global $objectline;

print "<!-- BEGIN PHP TEMPLATE objectline_title.tpl.php -->\n";

// Title line
print "<thead>\n";

print '<tr class="liste_titre nodrag nodrop">';

// Ref
print '<td class="linecolnum">'.$langs->trans('Ref').'</td>';

// serial number
print '<td class="linecolsn nowrap" style="width: 80px">'.$langs->trans('batch_number').'</td>';

// shipping date
print '<td class="linecoldate nowrap center" style="width: 80px">'.$langs->trans('ShippingDate').'</td>';

// current state
print '<td class="linecolcurrentstate nowrap center" style="width: 80px">'.$langs->trans('CurrentState').'</td>';

// noticed state
print '<td class="linecolnoticedstate nowrap center" style="width: 80px">';
print '<span id="noticedstatetitle">'.$langs->trans('NoticedState').'</span>';
print '<span id="changeNoticedState" style="display:none;">'.$langs->trans('ChangeStateTo').' : '.$objectline->getStateSelect();
print '<button id="submitChange"></button>';
print '</span></td>';

if ($object->status == 0 && $action != 'editline')
{
	print '<td class="linecoledit"></td>'; // No width to allow autodim

	print '<td class="linecoldelete" style="width: 10px"></td>';
}
else
{
	print '<td colspan="2"></td>';
	$coldisplay = $coldisplay + 2;
}

//if ($action == 'selectlines')
if ($object->status == 1 && $action != 'editline')
{
	print '<td class="linecolcheckall center">';
	print '<input type="checkbox" class="linecheckboxtoggle" />';
	?>
	<script>
		$(document).ready(function() {
			$(".linecheckboxtoggle").click(function() {
				var checkBoxes = $(".linecheckbox");
				var changeNoticedState = $('#changeNoticedState');
				var noticedstatetitle = $('#noticedstatetitle');
				var checkedBox = $('input[type="checkbox"]:checked');

				checkBoxes.prop("checked", this.checked);

				if ((checkedBox.length > 0 || this.checked) && changeNoticedState.css('display') == 'none')
				{
					noticedstatetitle.hide();
					changeNoticedState.show();
				}

				if (!this.checked)
				{
					noticedstatetitle.show();
					changeNoticedState.hide();
				}
				// $('input[name="line_checkbox[1]"]').css('display')
			});

			$(".linecheckbox").change(function() {

				var changeNoticedState = $('#changeNoticedState');
				var noticedstatetitle = $('#noticedstatetitle');
				var checkedBox = $('input[type="checkbox"]:checked');


				if (checkedBox.length > 0 && changeNoticedState.css('display') == 'none')
				{
					noticedstatetitle.hide();
					changeNoticedState.show();
				}

				if (checkedBox.length == 0)
				{
					noticedstatetitle.show();
					changeNoticedState.hide();
				}
			});
		});
	</script>
	<?php
	print '</td>';
}

print "</tr>\n";
print "</thead>\n";

print "<!-- END PHP TEMPLATE objectline_title.tpl.php -->\n";
