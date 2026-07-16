<?php
/*
 * ZoZo Dark — app-shell page footer (FEAT-0029 phase 2 · v2).
 *
 * Closes the tags opened in this theme's chrome/header.php (main >
 * content-shell > app) and renders the DS footer. Preserves the stock
 * footer contract: scripttime, optional Google-maps, generated-by /
 * copyright / admin+logout links (existing i18n keys) and the no-JS
 * notice. The AntiQar translation credit is kept verbatim via the
 * existing footer.msg.translation_credit key.
 */

    if (!defined('IN_HLSTATS')) {
        die('Do not access this file directly.');
    }

	global $scripttime, $db, $mode, $redirect_to_game;

	$scripttime  = round(microtime(true) - $scripttime, 4);
	$versionStats = eHtml($g_options['version']);
	$debugText   = eHtml("Executed {$db->querycount} queries, generated this page in {$scripttime} Seconds");
	$scriptUrl   = eHtml($g_options['scripturl']);

	$mapFile = INCLUDE_PATH . '/google_maps.php';
	$enableGoogleMaps = (
		$g_options["show_google_map"] == 1
		&& ($mode == "contents")
		&& ($redirect_to_game > 0)
		&& file_exists($mapFile)
	);

	if ($enableGoogleMaps) {
		include($mapFile);
	}
?>
		</main>

		<footer class="foot">
			<?php if (isset($_SESSION['nojs']) && $_SESSION['nojs'] == 1) : ?>
				<?=__('footer.msg.nojs')?><br>
			<?php endif; ?>
			<?=__('footer.label.generated_by')?>
			<a href="http://www.hlxce.com" target="_blank">HLstatsX Community Edition <?=$versionStats;?></a>
			<?php if ($g_options['showqueries'] == 1) : ?>
				<span class="dim"><?=$debugText;?></span>
			<?php endif; ?>
			<div class="credit">
				<?=__('footer.msg.translation_credit')?> <a href="#">AntiQar</a> (2010&ndash;2012)
			</div>
			<div class="credit">
				<?=__('footer.msg.copyright')?>
				&nbsp;·&nbsp;[<a href="<?=$scriptUrl;?>?mode=admin"><?=__('footer.link.admin')?></a>]
				<?php if (isset($_SESSION['loggedin'])) : ?>
					&nbsp;[<a href="hlstats.php?logout=1"><?=__('footer.link.logout')?></a>]
				<?php endif; ?>
			</div>
		</footer>
	</div><!-- /.content-shell -->
</div><!-- /.app -->

<?php if ($enableGoogleMaps && function_exists('printMap')) : ?>
	<?php printMap(); ?>
<?php endif; ?>
	</body>
</html>
