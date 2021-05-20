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
 * $dateSelector
 * $forceall (0 by default, 1 for supplier invoices/orders)
 * $element     (used to test $user->rights->$element->creer)
 * $permtoedit  (used to replace test $user->rights->$element->creer)
 * $senderissupplier (0 by default, 1 for supplier invoices/orders)
 * $inputalsopricewithtax (0 by default, 1 to also show column with unit price including tax)
 * $outputalsopricetotalwithtax
 * $usemargins (0 to disable all margins columns, 1 to show according to margin setup)
 * $object_rights->creer initialized from = $object->getRights()
 * $disableedit, $disablemove, $disableremove
 *
 * $text, $description, $line
 */

// Protection to avoid direct call of template
if (empty($object) || !is_object($object))
{
	print "Error, template page can't be called as URL";
	exit;
}

global $mysoc;
global $forceall, $senderissupplier, $inputalsopricewithtax, $outputalsopricetotalwithtax;

// add html5 elements
$domData  = ' data-element="'.$line->element.'"';
$domData .= ' data-id="'.$line->id.'"';
$domData .= ' data-qty="'.$line->qty.'"';
$domData .= ' data-product_type="'.$line->product_type.'"';

$coldisplay = 0;
?>
<!-- BEGIN PHP TEMPLATE objectline_view.tpl.php -->
<tr  id="row-<?php print $line->id?>" class="drag drop oddeven" <?php print $domData; ?> >
	<td class="linecolnum"><?php $coldisplay++; ?><div id="line_<?php print $line->id; ?>"></div>
<?php
	/**
	 * @var UniqueProductStateline $line
	 */
	// ref product
	if ($line->fk_product > 0)
	{
		$line->fetch_product();
		print $line->product->getNomUrl(1) . ' - '.$line->product->label;
	}
	print '</td>';

	// serial number
	$productLot = new Productlot($line->db);
	$res = $productLot->fetch(0, $line->fk_product, $line->serial_number);
	print '<td class="linecolsn nowrap" style="width: 80px">';
	if ($res > 0) print $productLot->getNomUrl(1);
	$coldisplay++;
	print '</td>';

	// shipping date
	print '<td class="linecoldate nowrap center">'.dol_print_date($line->shipping_date).'</td>';
	$coldisplay++;

	// current state
	print '<td class="linecolcurrentstate nowrap center">';
	$line->printState();
	print'</td>';
	$coldisplay++;

	//noticed state
	print '<td class="linecolnoticedstate nowrap center">';
	$line->printState('noticed');
	print'</td>';
	$coldisplay++;


if ($object->status == 0 && ($user->rights->uniqueproductstate->uniqueproductstate->write) && $action != 'editline') {
	print '<td class="linecoledit center">';
	$coldisplay++;
	?>
		<a class="editfielda reposition" href="<?php print $_SERVER["PHP_SELF"].'?id='.$this->id.'&amp;action=editline&amp;lineid='.$line->id.'#line_'.$line->id; ?>">
		<?php print img_edit().'</a>';

	print '</td>';

	print '<td class="linecoldelete center">';
	$coldisplay++;
	if (($line->fk_prev_id == null) && empty($disableremove)) { //La suppression n'est autorisée que si il n'y a pas de ligne dans une précédente situation
		print '<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$this->id.'&amp;action=ask_deleteline&amp;lineid='.$line->id.'">';
		print img_delete();
		print '</a>';
	}
	print '</td>';

} else {
	print '<td colspan="2"></td>';
	$coldisplay = $coldisplay + 2;
}

if ($object->status == 1 && $action != 'editline') { ?>
	<td class="linecolcheck center"><input type="checkbox" class="linecheckbox" name="line_checkbox[<?php print $i + 1; ?>]" value="<?php print $line->id; ?>" ></td>
<?php }

print "</tr>\n";

//Line extrafield
if (!empty($extrafields))
{
	print $line->showOptionals($extrafields, 'view', array('style'=>'class="drag drop oddeven"', 'colspan'=>$coldisplay), '', '', 1);
}

print "<!-- END PHP TEMPLATE objectline_view.tpl.php -->\n";
