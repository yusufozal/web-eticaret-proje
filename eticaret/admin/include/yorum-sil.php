<?php
if(!empty($_GET["ID"]))
{
	$ID=$VT->filter($_GET["ID"]);
		$veri=$VT->VeriGetir("yorumlar","WHERE ID=?",array($ID),"ORDER BY ID ASC",1);
		if($veri!=false) //resim var ise
		{
            
			$sil=$VT->SorguCalistir("DELETE FROM yorumlar","WHERE ID=?",array($ID),1); //veritabanından sil
			?>
        <meta http-equiv="refresh" content="0;url=<?=SITE?>yorumlar"> 
        <?php
		}
		else
		{
			?>
        <meta http-equiv="refresh" content="0;url=<?=SITE?>yorumlar">
        <?php
		}
}
else
{
	?>
        <meta http-equiv="refresh" content="0;url=<?=SITE?>">
        <?php
}
 ?>