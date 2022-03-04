<footer>
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-6 col-xl-3">
				<div class="footer__title">
					Me suivre
				</div>
				<div class="foot-bod-ico">
					<a href="https://twitter.com/LeSiteduDev" target="_blank" class="social-cust social-cust-tw">
						<i class="bi bi-twitter"></i>
					</a>
					<a href="https://www.youtube.com/channel/UCmJ6Z6gXjjylSVbPoBvj-ow" target="_blank" class="social-cust social-cust-yb">
						<i class="bi bi-youtube"></i>
					</a>
					<a href="https://www.facebook.com/sitedudev/" target="_blank" class="social-cust social-cust-fb">
						<i class="bi bi-facebook"></i>
					</a>
					<a href="https://discord.gg/myu8WY5" target="_blank" class="social-cust social-cust-dc">
						<i class="bi bi-discord"></i>
					</a>
				</div>
			</div>
			<div class="col-12 col-md-6 col-xl-3">
				<div class="footer__title">
					Aidez le site
				</div>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="RZ7KHWFLTGKCL">
					<button type="submit" border="0" name="submit" title="PayPal" class="social-cust social-cust-tw">
						<i class="bi bi-paypal"></i>
					</button>
				</form>
			</div>
			<div class="col-12 col-md-6 col-xl-3">
				<div class="footer__title">
					Actuellement
				</div>
				<div class="footer__others">
					1 visiteur
				</div>
				<div class="footer__others">
					1 en ligne
				</div>
			</div>
			<style type="text/css">
					.footer__others{
						font-weight: 500;
					}
				</style>
			<div class="col-12 col-md-6 col-xl-3">
				<div class="footer__title">
					Autres
				</div>
				<div class="footer__others">
					<a href="<?= URL ?>a-propos">À propos</a> 
				</div>
				<div class="footer__others">
					<a href="<?= URL ?>">Nous contacter</a>
				</div>
				<div class="footer__others">
					<a href="<?= URL ?>">CGU</a>
				</div>
			</div>
			<div class="col-12 col-md-12 col-xl-12">
				<div class="footer__footer">
					© 2020 - <?= date('Y') ?> Sitedudev 
				</div>
			</div>
		</div>
	</div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/smoothscroll/1.4.10/SmoothScroll.min.js"></script>
<script src="<?= URL ?>js/bootstrap.min.js"></script>
<script type="text/javascript">
	var toastElList = [].slice.call(document.querySelectorAll('.toast'))
	var toastList = toastElList.map(function (toastEl) {
	  return new bootstrap.Toast(toastEl, {animation: true, autohide: true, delay: 5000})
	})

	function Toastshow(e){
		e.show()
	}


	var URL = "<?= URL ?>";
</script>
<script src="<?= URL ?>js/register.js"></script>