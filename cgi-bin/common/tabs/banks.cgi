#####################################################################
########                   banks movements                  #########
#####################################################################
sub load_tabsconf {
# --------------------------------------------------------
	if($in{'tab'} eq 3){
		## Notes Tab
		$va{'tab_type'}  = 'logs';
		$va{'tab_title'} = &trans_txt('logs');
		$va{'tab_table'} = 'sl_banks';
	}
}

1;