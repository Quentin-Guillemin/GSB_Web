<div class="col-12">
	<h1>Comptes rendus du <?php echo $RAP_DATE1; ?> au <?php echo $RAP_DATE2; ?></h1>

	<div class="mb-3">
		<?php
		$base_url = "index.php?uc=compteRendu&ac=";
		$end_url = "&REDIRECT=".$REDIRECT."&RAP_DATE1=".$_REQUEST['RAP_DATE1']."&RAP_DATE2=".$_REQUEST['RAP_DATE2']."&PRA_NUM=".$_REQUEST['PRA_NUM'];
		if ($REDIRECT == "P")
		{
			?>

			<a href="<?php echo $base_url.'consulter'.$end_url; ?>">Retour aux critères de recherche</a>

			<?php
		}
		elseif ($REDIRECT == "R")
		{
			?>

			<a href="<?php echo $base_url.'consulterRegion'.$end_url; ?>">Retour aux critères de recherche</a>

			<?php
		}
		?>

	</div>
</div>

<div class="col-12 p-2">
	<table class="table table-striped table-hover">
		<tr>
			<th>Numéro</th>
			<th>Date du rapport</th>
			<th>Date de la visite</th>
			<th>Praticien</th>
		</tr>
		<?php
		foreach ($lesRapports as $unRapport)
		{
			$rapportURL = "index.php?uc=compteRendu&ac=detailRapport&RAP_NUM=".$unRapport['RAP_NUM']."&REDIRECT=".$REDIRECT."&RAP_DATE1=".$_REQUEST['RAP_DATE1']."&RAP_DATE2=".$_REQUEST['RAP_DATE2']."&PRA_NUM=".$_REQUEST['PRA_NUM'];
			?>

			<tr>
				<td>
					<a href="<?php echo $rapportURL; ?>">
						<div>
							<?php echo $unRapport['RAP_NUM']; ?>
						</div>
					</a>
				</td>
				<td>
					<a href="<?php echo $rapportURL; ?>">
						<div>
							<?php echo $unRapport['RAP_DATE']; ?>
						</div>
				</td>
				<td>
					<a href="<?php echo $rapportURL; ?>">
						<div>
							<?php echo $unRapport['RAP_DATEVISITE']; ?>
						</div>
				</td>
				<td>
					<a href="<?php echo $rapportURL; ?>">
						<div>
							<?php echo $unRapport['PRA_NUM'].' - '.$unRapport['PRA_NOM']; ?>
						</div>
					</a>
				</td>
			</tr>
			<?php
		} 
		?>

	</table>
</div>