</div>

	<div id="site-footer" class="navbar-fixed-bottom">
		<div class="navbar-inner navbar-bottom-inner">

			<div class="container-fluid social-footer hidden-desktop hidden-tablet">
			<ul>
				<li class="first"><a href="http://profiles.wordpress.org/norcross" title="Norcross at WordPress" target="_blank"><i class="icon icon-wordpress"></i></a></li>
				<li><a href="http://github.com/norcross" title="Norcross on Github" target="_blank"><i class="icon icon-github"></i></a></li>
				<li><a href="http://twitter.com/norcross/" title="Norcross on Twitter" target="_blank"><i class="icon icon-twitter"></i></a></li>
				<li><a href="https://plus.google.com/101309579396817654042/posts" title="Norcross on Google Plus" target="_blank"><i class="icon icon-google-plus"></i></a></li>
				<li class="last"><a href="<?php bloginfo('rss2_url'); ?>" title="RSS Feed" target="_blank"><i class="icon icon-rss"></i></a></li>
			</ul>
			</div>

			<div class="container-fluid">
			<p class="footer-text">
			<span class="foot-left">&copy;<?php echo date('Y'); ?> <?php bloginfo('name'); ?></span>
			<span class="foot-right">Built on <a href="http://wordpress.org" target="_blank" title="WordPress">WordPress</a> and coffee | <a href="<?php bloginfo('url'); ?>/terms-conditions/">Disclosures</a></span>
			<span class="foot-center jump-top hidden-phone"><i class="icon icon-hand-up" title="Return To Top"></i></span>
			</p>
			</div>

		</div>
	<?php wp_footer(); ?>
	</div>


	</body>
</html>
