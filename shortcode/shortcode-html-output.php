<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	$fields = json_decode( $entry->fields, true );
    $client_main_domain 	=  ($fields[26]['value'] != "")?$fields[26]['value']:'';
    $client_main_landing 	=  ($fields[27]['value'] != "")?$fields[27]['value']:'';
    $moz_all_domains[] 		= 	$client_main_landing;
   
    $moz_all_domains[] =  ($fields[29]['value'] != "")?$fields[29]['value']:'';
    $moz_all_domains[] =  ($fields[31]['value'] != "")?$fields[31]['value']:'';
    $moz_all_domains[] =  ($fields[33]['value'] != "")?$fields[33]['value']:'';
    $moz_all_domains[] =  ($fields[35]['value'] != "")?$fields[35]['value']:'';
    $moz_all_domains[] =  ($fields[37]['value'] != "")?$fields[37]['value']:'';
    //array_filter to remove empty value from array
    $moz_all_domains = array_filter($moz_all_domains, 'strlen');
 
	//initialize the library
	$moz_result = self::moz_api_url_metrics(self::$moz_access_id,self::$moz_secret_key,$moz_all_domains);
	$competitors_length = count($moz_result)-1;
	if(!empty($moz_result)) {
?>
	<div class="estimate_links_wrapper"
		<div class="user_data_wrapper">
			<h3><?php echo __( 'Users Data', self::$plugin_name ); ?></h3>
			<table class="user_data_table">
				<tr>
					<th><?php echo __( 'Landing Page', self::$plugin_name ); ?></th>
					<th><?php echo __( 'Referring Domains', self::$plugin_name ); ?></th>
					<th><?php echo __( 'Page Authority', self::$plugin_name ); ?></th>
					<th><?php echo __( 'Domain Authority', self::$plugin_name ); ?></th>
				</tr>
				<tr>
					<td><?php echo $moz_result[0]->uu; ?></td>
					<td><?php echo $moz_result[0]->ueid; ?></td>
					<td><?php echo $moz_result[0]->upa; ?></td>
					<td><?php echo $moz_result[0]->pda; ?></td>
				</tr>
			</table>
		</div>
		<div class="competitors_data_wrapper">
			<h3><?php echo __( 'Competitors Data', self::$plugin_name ); ?></h3>
			<table class="competitors_data_table">
				<tr>
					<th><?php echo __( 'Landing Page', self::$plugin_name ); ?></th>
					<th><?php echo __( 'Referring Domains', self::$plugin_name ); ?></th>
					<th><?php echo __( 'Page Authority', self::$plugin_name ); ?></th>
					<th><?php echo __( 'Domain Authority', self::$plugin_name ); ?></th>
				</tr>
				<?php foreach($moz_result as $key => $value){ 
					if($key > 0){
						$average_reffering_domains  += $value->ueid;
						$average_page_authority 	+= $value->upa;
						$average_domain_authority 	+= $value->pda;
				?>
					<tr>
						<td><?php echo $value->uu; ?></td>
						<td><?php echo $value->ueid; ?></td>
						<td><?php echo $value->upa; ?></td>
						<td><?php echo $value->pda; ?></td>
					</tr>
				<?php }  //end if condition
					} //end foreach 
					$average_reffering_domains 	= ceil($average_reffering_domains/$competitors_length); 
					$average_page_authority 	= ceil($average_page_authority/$competitors_length); 
					$average_domain_authority 	= ceil($average_domain_authority/$competitors_length);
					
					$referal_domains_required   = ceil($average_reffering_domains - $moz_result[0]->ueid);
					$twenty_percente_links 		= ceil($referal_domains_required * 20)/100;
					$total_months_required		= ceil($average_reffering_domains/$twenty_percente_links);					
				?>
				<tr>
					<td><strong><?php echo __( 'Total Average', self::$plugin_name ); ?></strong></td>
					<td><?php echo $average_reffering_domains; ?></td>
					<td><?php echo $average_page_authority; ?></td>
					<td><?php echo $average_domain_authority; ?></td>
				</tr>
			</table>
		</div>
		<div class="estimation_results_wrapper">
			<h3><?php echo __( 'Final Results', self::$plugin_name ); ?></h3>
			<p><strong><?php echo __( 'Total Referral Links Required', self::$plugin_name ); ?>: </strong> <?php echo $referal_domains_required; ?></p>
			<p><strong><?php echo __( 'Total Link Building Per Month', self::$plugin_name ); ?>: </strong> <?php echo $twenty_percente_links; ?></p>
			<p><strong><?php echo __( 'Total Month Required ', self::$plugin_name ); ?>: </strong> <?php echo $total_months_required; ?></p>
			<p><?php echo __( 'From the 20% per month of links required we would suggest the following DA split:', self::$plugin_name ); ?></p>
			<p><?php echo __( '5% DA20+', self::$plugin_name ); ?></p>
			<p><?php echo __( '10% DA30+', self::$plugin_name ); ?></p>
			<p><?php echo __( '5% DA40+', self::$plugin_name ); ?></p>
			<p><?php echo __( 'Total 20%', self::$plugin_name ); ?></p>
		</div>
	</div>
<?php } //inot empty  ?>