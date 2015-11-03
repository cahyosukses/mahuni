<div class="col-sm-3 sidebar">
	<h1>Menu</h1>

	<?php 
		// show_sess();		
	?>

	<div class="list-group">
		<?php if(is_group('admin')){?>

			<a href="<?= site_url('customer')?>" class="list-group-item">Urus Pelanggan</a>
		
		<?php }elseif(is_group('developer')){?>

			<a href="<?= site_url('page/create')?>" class="list-group-item">Page Baru</a>
			<a href="<?= site_url('page/list')?>" class="list-group-item">Senarai Page</a>

		<?php }elseif(is_group('affiliate')){?>
		
			<a href="<?= site_url('dashboard/aff_products')?>" class="list-group-item">Senarai Produk</a>
			<a href="<?= site_url('dashboard/aff_sales')?>" class="list-group-item">Sales</a>
		
		<?php } ?>
	</div>
</div>