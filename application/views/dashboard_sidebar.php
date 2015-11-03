<div class="col-sm-3 sidebar">
	<h1>Menu</h1>

	<?php 
		// show_sess();		
	?>

	<div class="list-group">
		<?php if(is_group('admin')){?>

			<a href="<?= site_url('customer')?>" class="list-group-item">Urus Pelanggan</a>
			<a href="<?= site_url('blaster')?>" class="list-group-item">Hantar SMS</a>
			<a href="#" class="list-group-item">Urus Produk</a>
			<a href="<?= site_url('dashboard/report')?>" class="list-group-item">Laporan</a>
			<a href="<?= site_url('dashboard/top_affiliate')?>" class="list-group-item">Top Affiliate</a>
			<a href="<?= site_url('dashboard/channel_report')?>" class="list-group-item">Channel Sales</a>
			<a href="<?= site_url('dashboard/dripmail')?>" class="list-group-item">Dripmail</a>
		
		<?php }elseif(is_group('affiliate')){?>
		
			<a href="<?= site_url('dashboard/aff_products')?>" class="list-group-item">Senarai Produk</a>
			<a href="<?= site_url('dashboard/aff_sales')?>" class="list-group-item">Sales</a>
		
		<?php } ?>
	</div>
</div>