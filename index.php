<style>html{color:white;background:black;}</style><pre><?php

$ca='{"balances":[{"asset_code":"BILLEX","asset_issuer":"GBILKAL6TH56OTUIWRVUCUGTV22NHLH5QFWBHCUPVHBX6B5FVJYSSIBD"},{"asset_code":"BILLEX1","asset_issuer":"GDEDBZL37MBCR4N63E2OQ4SPN2Z6R7QPIBQBUVWHM2FIN5I7MEJ5C5EF"},{"asset_code":"BILLEXC","asset_issuer":"GA6C4G2JKL5JBUMXTZK2TIIE2XN7DGAO3ZE3TZWMJ4OMBOCTZBW6S2IX"},{"asset_code":"BINC","asset_issuer":"GDXYOVUNAH4KGTOUPPBHRJLANMMRKBOFR5LJCFKAG3U5AAXPA424WOWQ"},{"asset_code":"BINCOME","asset_issuer":"GB47S227MZ37TLYO2RD5EH4IT2DVWH7RRC67Z2VAKMV4TKFDF4GWYBLY"},{"asset_code":"BONUS","asset_issuer":"GCP7IKROLJVDTQC34CNZGL6OAAWJ62H5MO3IST7CTOEYK7GWRR4CHDUH"},{"asset_code":"CENTUS","asset_issuer":"GD43GJWAV4WVMW5O4LL27FFFXF5SFNQY7NYPHOMHFHFNYAI2W6OTYEMU"},{"asset_code":"CENTUSX","asset_issuer":"GD7I4VIGF2LJEK6XKZDFLWRT6NFVIXN2CGLVSXEPNWJWTN4QRGX4226Z"},{"asset_code":"DBC","asset_issuer":"GDGHJL32AYPBNKLJQXWEASJVXBDWS2JO7LICIMGS4JVIPXH6KGO5SQBS"},{"asset_code":"DEPO","asset_issuer":"GC7J3ZOQ4GG4QKS57JQOQQWAP636GCO6JFL5HTJRVNRWILAAM6BDSKLX"},{"asset_code":"USD","asset_issuer":"GAQTQSTVA6QWZSWTE4CVZLU6XDP3H3DVEXIIG2XMZGMLY3XCY5GZMNMI"}]}';
$ca=json_decode($ca,true);
$ca=$ca['balances'];
$hz= 'https://horizon.stellar.org/accounts/';
if(isset($_GET['a']))
{
	$a=$_GET['a'];
	$d=file_get_contents($hz.$a);
	$d=json_decode($d,true);
	unset($d['_links']);
	unset($d['id']);
	unset($d['account_id']);
	unset($d['subentry_count']);
	unset($d['inflation_destination']);
	unset($d['home_domain']);
	unset($d['last_modified_ledger']);
	unset($d['last_modified_time']);
	unset($d['num_sponsoring']);
	unset($d['num_sponsored']);
	unset($d['paging_token']);
	unset($d['flags']);
	unset($d['data']);
	$bal=$d['balances'];
	$bf=array();
	foreach($d['balances'] as $k => $v)
	{
		unset($d['balances'][$k]['balance']);
		unset($d['balances'][$k]['limit']);
		unset($d['balances'][$k]['buying_liabilities']);
		unset($d['balances'][$k]['selling_liabilities']);
		unset($d['balances'][$k]['last_modified_ledger']);
		unset($d['balances'][$k]['is_authorized']);
		unset($d['balances'][$k]['is_authorized_to_maintain_liabilities']);
		unset($d['balances'][$k]['asset_type']);
		$d['balances'][$k]=$d['balances'][$k]['asset_code'].'-'.$d['balances'][$k]['asset_issuer'];
	}
	foreach($ca as $k => $v)
	{
		$ca[$k]=$ca[$k]['asset_code'].'-'.$ca[$k]['asset_issuer'];
	}
	$ca=array_flip($ca);
	foreach($ca as $k => $v)
	{
		$ca[$k]='<span style="color:red;">not found</span>';
	}
	$b=$d['balances'];
	unset($d['balances']);
	foreach($ca as $k => $v)
	{
		if(in_array($k,$b))
		{
			$ca[$k]='<span style="color:#0f0;">found</span>';
			$ka=explode("-",$k);
			$code=$ka[0];
			$issuer=$ka[1];
			$bf[$k]=0;
			foreach($bal as $kk => $vv)
			{
				if($vv['asset_code']==$code&&$vv['asset_issuer']==$issuer)
				{
					$bf[$k]=$vv['balance'];
				}
			}
		}
	}
	foreach($bf as $k => $v)
	{
		if($bf[$k]>0)
		{
			$ka=explode("-",$k);
			$code=$ka[0];
			$issuer=$ka[1];
			if($code=="CENTUS"||$code=="BINCOME"||$code=="USD")$ca[$k] .= ' - 5%: '.(floor($bf[$k]*0.05*pow(10,7))/pow(10,7));
			else $ca[$k] .= ' - '.(floor($bf[$k]*1*pow(10,7))/pow(10,7));
		}
	}
	$t=$d['thresholds'];
	$s=$d['signers'];
	foreach($s as $k => $v)
	{
		$s[$k]='w'.$s[$k]['weight'].' '.$s[$k]['key'];
	}
	$s=array_flip($s);
	$message="invalid";$missing='<span style="color:red;">missing</span>';
	if($t['high_threshold']==1){$message="not_multisig";$missing="not_multisig";}
	if($t['high_threshold']==0){$message="not_multisig";$missing="not_multisig";}
	$v2=$t['high_threshold'];
	foreach($s as $k => $v)
	{
		$s[$k]=$message;
	}
	$valid='<span style="color:#0f0;">valid</span>';
	foreach($s as $k => $v)
	{
		if($v2==2)
		{
			if($k=="w1 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM")$s[$k]=$valid;
			if($k=="w1 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U")$s[$k]=$valid;
			if($k=="w1 ".$a)$s[$k]=$valid;
		}
		else if($v2==20)
		{
			if($k=="w10 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM")$s[$k]=$valid;
			if($k=="w10 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U")$s[$k]=$valid;
			if($k=="w10 ".$a)$s[$k]=$valid;			
		}
		else
		{
			if($k=="w1 ".$a)$s[$k]=$valid;			
			if($k=="w1 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM")$s[$k]=$valid;
			if($k=="w1 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U")$s[$k]=$valid;
			if($k=="w10 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM")$s[$k]=$valid;
			if($k=="w10 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U")$s[$k]=$valid;
		}
	}
	if($v2==2){
		if(!isset($s["w1 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM"]))$s['w1 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM']=$missing;
		if(!isset($s["w1 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U"]))$s['w1 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U']=$missing;
	}
	else if($v2==20){
		if(!isset($s["w10 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM"]))$s['w10 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM']=$missing;
		if(!isset($s["w10 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U"]))$s['w10 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U']=$missing;
	}
	else{
		if(!isset($s["w1 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM"]))$s['w1 GC4DQQE7PZ62GYJDUHV5YD5Z4WESGRILL74TGCD7UYVB4SMFP4BW75EM']=$missing;
		if(!isset($s["w1 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U"]))$s['w1 GCH2XRYQVJQ24HWIBDZ3QSBQD4GWI6MRVOBZIVOVDSJSYYL5P4NERX4U']=$missing;		
	}
	foreach($t as $k => $v)
	{
		if($v==2&&$v2==2)$t[$k]='<span style="color:#0f0;">valid value of 2</span>';
		else if($v==20&&$v2==20)$t[$k]='<span style="color:#0f0;">valid value of 20</span>';
		else $t[$k]=$message." value of ".$v;
	}
	function array_dump($data){
		echo '<hr>';
		foreach($data as $k => $v)
		{
			echo $k.'::',$v."\n";
		}
		echo '<hr>';
	}
	echo 'Key Thresholds';
	array_dump($t);
	echo 'Signatories';
	array_dump($s);
	echo 'Centus Team Assets';
	array_dump($ca);
	echo 'Sequence';
	echo '<hr>';
	echo $d['sequence'];
}
	?>
	<form method=get action="/centus-address/">
	<label>stellar address:</label><input type=text name=a>
	<input type=submit>
	</form>
	<?php
