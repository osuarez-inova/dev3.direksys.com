#!/usr/bin/perl
####################################################################
########                WAREHOUSE RECEIPT                ########
####################################################################

sub load_tabsconf {
# --------------------------------------------------------
	if ($in{'tab'} eq 2){
		## Notes Tab
		$va{'tab_type'}  = 'notes';
		$va{'tab_title'} = &trans_txt('notes');
		$va{'tab_table'} = 'sl_locations_notes';
	}elsif($in{'tab'} eq 3){
		## Notes Tab
		$va{'tab_type'}  = 'logs';
		$va{'tab_title'} = &trans_txt('logs');
		$va{'tab_table'} = 'sl_locations';
	}
}

#############################################################################
#############################################################################
#   Function: load_tabs1
#
#       Es: Muestra la Cobertura del Warehouses
#       En: Displays the coverage Warehouses
#
#
#    Created on: 09/01/2013  01:34pm
#
#    Author: Carlos Haas
#
#    Modifications:
#
#        - 
#
#   Parameters:
#
#      - 
#
#  Returns:
#
#      - 
#
#   See Also:
#
#
#
sub load_tabs1 {
#############################################################################
#############################################################################	
	## ITEMS
	my ($choices_on,$tot_qty,$vendor_sku);
	my (@c) = split(/,/,$cfg{'srcolors'});
	
	my ($sth) = &Do_SQL("SELECT COUNT(DISTINCT(ID_products)) 
		FROM sl_warehouses_location 
		INNER JOIN sl_locations ON sl_locations.ID_warehouses = sl_warehouses_location.ID_warehouses AND sl_locations.Code = sl_warehouses_location.Location 
		WHERE ID_locations='$in{'id_locations'}'
		AND sl_warehouses_location.Quantity>0;");
	$va{'matches'} = $sth->fetchrow;
	if ($va{'matches'}>0){
		(!$in{'nh'}) and ($in{'nh'}=1);
		$first = ($in{'nh'} - 1) * $usr{'pref_maxh'};			
		($va{'pageslist'},$qs) = &pages_list($in{'nh'},"/cgi-bin/mod/$usr{'application'}/dbman",$va{'matches'},$usr{'pref_maxh'});
		my ($sth) = &Do_SQL("SELECT ID_parts,ID_products,Model,Name,SUM(Quantity) AS Quantity 
			FROM sl_warehouses_location 
			INNER JOIN sl_parts ON ID_parts = RIGHT( ID_products, 4 ) 
			INNER JOIN sl_locations ON sl_locations.ID_warehouses = sl_warehouses_location.ID_warehouses AND sl_locations.Code = sl_warehouses_location.Location 
			WHERE ID_locations ='$in{'id_locations'}' 
			AND sl_warehouses_location.Quantity>0
			GROUP BY ID_parts 
			ORDER BY ID_parts LIMIT $first,$usr{'pref_maxh'};");
		while ($rec = $sth->fetchrow_hashref){
			$d = 1 - $d;
			$va{'searchresults'} .= "<tr bgcolor='$c[$d]' onmouseover='m_over(this)' onmouseout='m_out(this)' onClick=\"trjump('/cgi-bin/mod/$usr{'application'}/dbman?cmd=mer_parts&view=$rec->{'ID_parts'}')\">\n";
			$va{'searchresults'} .= "   <td class='smalltext' valign='top'>".&format_sltvid($rec->{'ID_products'})."</td>\n";
			$va{'searchresults'} .= "   <td class='smalltext'>$rec->{'Model'}<br>$rec->{'Name'}</td>\n";
			#$va{'searchresults'} .= "   <td class='smalltext' valign='top'>$rec->{'Location'}</td>\n";
			$va{'searchresults'} .= "   <td class='smalltext' align='right' valign='top'>".&format_number($rec->{'Quantity'})."</td>\n";
			$va{'searchresults'} .= "</tr>\n";
			$tot_qty += $rec->{'Quantity'};
		}
		$va{'searchresults'} .= qq|
			<tr>
				<td colspan='2' class='smalltext' align="right">|.&trans_txt('mer_vpo_total').qq|</td>
				<td align="right" class='smalltext' style="border-top:thick double #808080;">|.&format_number($tot_qty).qq|</td>
			</tr>\n|;
	}else{
		$va{'pageslist'} = 1;
		$va{'searchresults'} = qq|
			<tr>
				<td colspan='3' align="center">|.&trans_txt('search_nomatches').qq|</td>
			</tr>\n|;
	}	
}

#############################################################################
#############################################################################
#   Function: load_tabs4
#
#       Es: Informacion de Lotes
#       En: 
#
#
#    Created on: 09/01/2013  01:34pm
#
#    Author: Carlos Haas
#
#    Modifications:
#
#        - 
#
#   Parameters:
#
#      - 
#
#  Returns:
#
#      - 
#
#   See Also:
#
#
#
sub load_tabs4 {
#############################################################################
#############################################################################	
	## ITEMS
	my ($choices_on,$tot_qty,$vendor_sku);
	my (@c) = split(/,/,$cfg{'srcolors'});
	$in{'code'} = &load_name('sl_locations','ID_locations',$in{'id_locations'},'Code');
	
	my ($sth) = &Do_SQL("SELECT COUNT(*) FROM sl_locations_lots WHERE Location='$in{'id_locations'}';");
	$va{'matches'} = $sth->fetchrow;
	if ($va{'matches'}>0){
		(!$in{'nh'}) and ($in{'nh'}=1);
		$first = ($in{'nh'} - 1) * $usr{'pref_maxh'};			
		($va{'pageslist'},$qs) = &pages_list($in{'nh'},"/cgi-bin/mod/$usr{'application'}/dbman",$va{'matches'},$usr{'pref_maxh'});
		my ($sth) = &Do_SQL("SELECT * FROM sl_locations_lots INNER JOIN sl_parts ON ID_parts = (sl_locations_lots.ID_products-400000000) WHERE ID_locations ='$in{'id_locations'}' GROUP BY ID_parts ORDER BY ID_parts LIMIT $first,$usr{'pref_maxh'};");
		while ($rec = $sth->fetchrow_hashref){
			$d = 1 - $d;
			$va{'searchresults'} .= "<tr bgcolor='$c[$d]' onmouseover='m_over(this)' onmouseout='m_out(this)' onClick=\"trjump('/cgi-bin/mod/$usr{'application'}/dbman?cmd=mer_parts&view=$rec->{'ID_parts'}')\">\n";
			$va{'searchresults'} .= "   <td class='smalltext' valign='top'>".&format_sltvid($rec->{'ID_products'})."</td>\n";
			$va{'searchresults'} .= "   <td class='smalltext'>$rec->{'Model'}<br>$rec->{'Name'}</td>\n";
			$va{'searchresults'} .= "   <td class='smalltext' align='right' valign='top'>".&format_number($rec->{'Quantity'})."</td>\n";
			$va{'searchresults'} .= "</tr>\n";
			$tot_qty += $rec->{'Quantity'};
		}
		$va{'searchresults'} .= qq|
			<tr>
				<td colspan='2' class='smalltext' align="right">|.&trans_txt('mer_vpo_total').qq|</td>
				<td align="right" class='smalltext' style="border-top:thick double #808080;">|.&format_number($tot_qty).qq|</td>
			</tr>\n|;
	}else{
		$va{'pageslist'} = 1;
		$va{'searchresults'} = qq|
			<tr>
				<td colspan='3' align="center">|.&trans_txt('search_nomatches').qq|</td>
			</tr>\n|;
	}
}



#############################################################################
#############################################################################
#   Function: load_tabs5
#
#       Es: Muestra movimientos de salida/entrada en la gaveta
#       En: Displays location transaction
#
#
#    Created on: 09/01/2013  01:34pm
#
#    Author: RB
#
#    Modifications:
#
#        - 
#
#   Parameters:
#
#      - 
#
#  Returns:
#
#      - 
#
#   See Also:
#
#
#
sub load_tabs5 {
#############################################################################
#############################################################################	
	## ITEMS
	my ($choices_on,$tot_qty,$vendor_sku);
	my (@c) = split(/,/,$cfg{'srcolors'});
	
	my $query_list = "SELECT sl_skus_trans.ID_products, sl_skus_trans.Type, sl_skus_trans.Location, sl_skus_trans.Type_trans, sl_skus_trans.tbl_name, sl_skus_trans.ID_trs, sl_skus_trans.Quantity, sl_skus_trans.Cost, sl_skus_trans.Date, sl_skus_trans.Time,sl_skus_trans.ID_admin_users 
	FROM sl_skus_trans 
	INNER JOIN sl_locations ON sl_skus_trans.Location=sl_locations.Code AND sl_skus_trans.ID_warehouses=sl_locations.ID_warehouses
	WHERE sl_skus_trans.ID_warehouses = '$in{'id_warehouses'}' AND sl_locations.ID_locations='$in{'id_locations'}'";

	my ($sth) = &Do_SQL($query_list);
	$va{'matches'} = $sth->rows();
	
	if ($va{'matches'}>0){

		(!$in{'nh'}) and ($in{'nh'}=1);
		$first = ($in{'nh'} - 1) * $usr{'pref_maxh'};			
		($va{'pageslist'},$qs) = &pages_list($in{'nh'},"/cgi-bin/mod/$usr{'application'}/dbman",$va{'matches'},$usr{'pref_maxh'});
		
		my ($sth) = &Do_SQL("$query_list ORDER BY sl_skus_trans.ID_products_trans DESC LIMIT $first,$usr{'pref_maxh'};");
		while ($rec = $sth->fetchrow_hashref){
			my %cmds = (
			"sl_orders", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=opr_orders&view='.$rec->{'ID_trs'}
			,"sl_returns", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=opr_returns&view='.$rec->{'ID_trs'}
			,"sl_purchases", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=mer_po&view='.$rec->{'ID_trs'}
			,"sl_adjustments", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=opr_adjustments&view='.$rec->{'ID_trs'}
			,"sl_wreceipts", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=mer_wreceipts&view='.$rec->{'ID_trs'}
			,"sl_manifests", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=opr_manifests&view='.$rec->{'ID_trs'}
			,"sl_parts_productions", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=mer_parts_productions&view='.$rec->{'ID_trs'}
			,"sl_skustransfers", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=opr_skustransfers&view='.$rec->{'ID_trs'}
			,"sl_purchaseorders", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=mer_po&view='.$rec->{'ID_trs'}
			,"sl_creditmemos", '/cgi-bin/mod/'.$usr{'application'}.'/dbman?cmd=opr_creditmemos&view='.$rec->{'ID_trs'});
		
			$d = 1 - $d;
			my $pname = &load_name('sl_parts', 'ID_parts', ($rec->{'ID_products'} - 400000000),'Name');
			my $uname = &load_db_names('admin_users', 'ID_admin_users', $rec->{'ID_admin_users'},"[FirstName] [LastName]");
			$va{'searchresults'} .= "<tr bgcolor='$c[$d]'>\n";
			$va{'searchresults'} .= "   <td class='smalltext' align='left' valign='top'>$rec->{'Date'} $rec->{'Time'}</td>\n";
			$va{'searchresults'} .= "   <td class='smalltext' valign='top'><a href='/cgi-bin/mod/$usr{'application'}/dbman?cmd=mer_parts&view=".($rec->{'ID_products'} - 400000000)."')'>".&format_sltvid($rec->{'ID_products'})."</a></td>\n";
			$va{'searchresults'} .= "   <td class='smalltext'>$pname</td>\n";
			$va{'searchresults'} .= "   <td class='smalltext' align='left' valign='top'>$rec->{'Location'}</td>\n";
			$va{'searchresults'} .= qq| <td class='smalltext' align='left' valign='top'><a href="$cmds{$rec->{'tbl_name'}}">$rec->{'Type_trans'}</a></td>\n|;
			$va{'searchresults'} .= "   <td class='smalltext' align='right' valign='top'>".&format_number($rec->{'Quantity'})."</td>\n";
			$va{'searchresults'} .= "   <td class='smalltext' align='right' valign='top'>$uname (<a href='/cgi-bin/mod/$usr{'application'}/dbman?cmd=usrman&view=$rec->{'ID_admin_users'}'>$rec->{'ID_admin_users'}</a>)</td>\n";
			$va{'searchresults'} .= "</tr>\n";
		
		}

	}else{

		$va{'pageslist'} = 1;
		$va{'searchresults'} = qq|
			<tr>
				<td colspan='6' align="center">|.&trans_txt('search_nomatches').qq|</td>
			</tr>\n|;

	}
}

1;