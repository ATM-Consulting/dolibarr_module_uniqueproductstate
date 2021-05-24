<?php
/* Copyright (C) 2010-2012	Regis Houssin		<regis.houssin@inodbox.com>
 * Copyright (C) 2010-2014	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2012-2013	Christophe Battarel	<christophe.battarel@altairis.fr>
 * Copyright (C) 2012       Cédric Salvador     <csalvador@gpcsolutions.fr>
 * Copyright (C) 2014		Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2014       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) 2015-2016	Marcos García		<marcosgdf@gmail.com>
 * Copyright (C) 2018       Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2018		Ferran Marcet		<fmarcet@2byte.es>
 * Copyright (C) 2019		Nicolas ZABOURI		<info@inovea-conseil.com>
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
 * $senderissupplier (0 by default, 1 or 2 for supplier invoices/orders)
 * $inputalsopricewithtax (0 by default, 1 to also show column with unit price including tax)
 */

// Protection to avoid direct call of template
if (empty($object) || !is_object($object)) {
	print "Error: this template page cannot be called directly as an URL";
	exit;
}
$usemargins = 0;
if (!empty($conf->margin->enabled) && !empty($object->element) && in_array($object->element, array('facture', 'facturerec', 'propal', 'commande')))
{
	$usemargins = 1;
}
if (!isset($dateSelector)) global $dateSelector; // Take global var only if not already defined into function calling (for example formAddObjectLine)
global $forceall, $forcetoshowtitlelines, $senderissupplier, $inputalsopricewithtax;
if (!isset($dateSelector)) $dateSelector = 1; // For backward compatibility
elseif (empty($dateSelector)) $dateSelector = 0;
if (empty($forceall)) $forceall = 0;
if (empty($senderissupplier)) $senderissupplier = 0;
if (empty($inputalsopricewithtax)) $inputalsopricewithtax = 0;
// Define colspan for the button 'Add'
$colspan = 3; // Columns: total ht + col edit + col delete
if (!empty($conf->multicurrency->enabled) && $this->multicurrency_code != $conf->currency) $colspan++; //Add column for Total (currency) if required
if (in_array($object->element, array('propal', 'commande', 'order', 'facture', 'facturerec', 'invoice', 'supplier_proposal', 'order_supplier', 'invoice_supplier'))) $colspan++; // With this, there is a column move button
//print $object->element;
// Lines for extrafield
$objectline = new UniqueProductStateline($this->db);
//if (!empty($extrafields))
//{
//	if ($this->table_element_line == 'commandedet') {
//		$objectline = new OrderLine($this->db);
//	} elseif ($this->table_element_line == 'propaldet') {
//		$objectline = new PropaleLigne($this->db);
//	} elseif ($this->table_element_line == 'supplier_proposaldet') {
//		$objectline = new SupplierProposalLine($this->db);
//	} elseif ($this->table_element_line == 'facturedet') {
//		$objectline = new FactureLigne($this->db);
//	} elseif ($this->table_element_line == 'contratdet') {
//		$objectline = new ContratLigne($this->db);
//	} elseif ($this->table_element_line == 'commande_fournisseurdet') {
//		$objectline = new CommandeFournisseurLigne($this->db);
//	} elseif ($this->table_element_line == 'facture_fourn_det') {
//		$objectline = new SupplierInvoiceLine($this->db);
//	} elseif ($this->table_element_line == 'facturedet_rec') {
//		$objectline = new FactureLigneRec($this->db);
//	}
//}
print "<!-- BEGIN PHP TEMPLATE objectline_create.tpl.php  -->\n";
$nolinesbefore = (count($this->lines) == 0 || $forcetoshowtitlelines);
if ($nolinesbefore) {
	?>
	<tr class="liste_titre<?php echo ' liste_titre_add_' ?> nodrag nodrop">

		<td class="linecolnum">
			<div id="add"></div><span class="hideonsmartphone"><?php echo $langs->trans('AddNewLine'); ?></span>
		</td>
		<td class="linecolsn nowrap" style="width: 80px"><?php echo  $langs->trans('batch_number'); ?></td>
		<td class="linecoldate nowrap center" style="width: 80px"><?php echo  $langs->trans('ShippingDate'); ?></td>
		<td class="linecolcurrentstate nowrap center" style="width: 80px"><?php echo  $langs->trans('CurrentState'); ?></td>
		<td class="linecolnoticedstate nowrap center" style="width: 80px"><?php echo  $langs->trans('NoticedState'); ?></td>

		<td class="linecoledit" colspan="<?php echo $colspan; ?>">&nbsp;</td>
	</tr>
	<?php
}
?>
<tr class="pair nodrag nodrop nohoverpair<?php echo ($nolinesbefore) ? '' : ' liste_titre_create'; ?>">
	<?php
	$coldisplay = 0;

	$coldisplay++;
	?>
	<td class="nobottom linecolnum minwidth500imp">

		<?php

		print $this->getProductToAddSelect();

		/*// Editor wysiwyg
		require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
		$nbrows = ROWS_2;
		$enabled = (!empty($conf->global->FCKEDITOR_ENABLE_DETAILS) ? $conf->global->FCKEDITOR_ENABLE_DETAILS : 0);
		if (!empty($conf->global->MAIN_INPUT_DESC_HEIGHT)) $nbrows = $conf->global->MAIN_INPUT_DESC_HEIGHT;
		$toolbarname = 'dolibarr_details';
		if (!empty($conf->global->FCKEDITOR_ENABLE_DETAILS_FULL)) $toolbarname = 'dolibarr_notes';
		$doleditor = new DolEditor('dp_desc', GETPOST('dp_desc', 'restricthtml'), '', (empty($conf->global->MAIN_DOLEDITOR_HEIGHT) ? 100 : $conf->global->MAIN_DOLEDITOR_HEIGHT), $toolbarname, '', false, true, $enabled, $nbrows, '98%');
		$doleditor->Create();*/

		?>
		<input type="hidden" name="fk_product" id="fk_product">
		<input type="hidden" name="batch" id="batch">
		<input type="hidden" name="shipping_date" id="shipping_date">
		<input type="hidden" name="current_state" id="current_state">
		<input type="hidden" name="noticed_state" id="noticed_state" value="-1">
	</td>

	<td class="linecolsn nowrap" style="width: 80px">&nbsp;</td>
	<td class="linecoldate nowrap center" style="width: 80px">&nbsp;</td>
	<td class="linecolcurrentstate nowrap center" style="width: 80px">&nbsp;</td>
	<td class="linecolnoticedstate nowrap center" style="width: 80px">&nbsp;</td>

	<td class="nobottom linecoledit center valignmiddle" colspan="<?php echo $colspan; ?>">
		<input type="submit" class="button" value="<?php echo $langs->trans('Add'); ?>" name="addline" id="addline">
	</td>

	<?php

	$coldisplay += $colspan;
	?>

</tr>

<?php
//if (is_object($objectline)) {
//	print $objectline->showOptionals($extrafields, 'edit', array('colspan'=>$coldisplay), '', '', 1);
//}

print "<!-- END PHP TEMPLATE objectline_create.tpl.php -->\n";
