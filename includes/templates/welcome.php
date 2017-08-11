<?php
/**
 * Onehopsmsservice Welcome Doc Comment
 *
 * @category Class
 * @package  Onehop
 * @author   Screen-Magic Mobile Media Inc.
 * @license  https://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://screen-magic.com
 */

defined( 'ABSPATH' ) || exit( 'No direct script access allowed!' );
$image_path = OnehopSMSPlugin::onehop_get_plugin_url().'assets/images/';
?>

<div class="welwrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="acc_banner">
					<img alt="Welcome to Onehop" src="<?php echo esc_html( $image_path ).'acc_banner.png'; ?>" usemap="#onehopmap" />
					<map name="onehopmap">
						<area shape="rect" coords="500,125,675,200" href="http://www.onehop.co" alt="Onehop.co" target="_blank">
					</map>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="custom_container">
					<div class="onehop_list">
						<h2>With Onehop on Wordpress, you can SMS all your:</h2>
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<ul>
								<li>Order Confirmation</li>
								<li>Order Completion</li>
								<li>Order In Process</li>                              
							</ul>
						</div>

						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<ul>
								<li>Out of Stock Alerts</li>
								<li>Back in Stock Alerts</li>
								<li>Offers & Promotions</li>
							</ul>
						</div>
					</div>

					<h2 class="acc_title">Get Started with Onehop on Wordpress</h2>

					<div class="panel-group" id="accordion">

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
										<span class="seq_no">1</span> Sign up on Onehop & Integrate with Wordpress
									<i class="indicator glyphicon glyphicon-triangle-right"></i>
									</a>
								</h4>
							</div>

							<div id="collapseOne" class="panel-collapse collapse">
								<div class="panel-body">
									<ul class="acc_1_list">
										<li>Go to <span class="stronger">Onehop.co</span> then <span class="stronger">Sign Up</span> or <span class="stronger">Login </span>
											<br />

											<img src="<?php echo esc_html( $image_path ).'acc_1_1.png'; ?>" />
										</li>

										<li>After logging into <span class="stronger">Onehop.co</span> click on your <span class="stronger">&lt;User Name&gt;</span> then click on <span class="stronger">Profile</span>
											<br />
											<img src="<?php echo esc_html( $image_path ).'acc_1_2.png'; ?>" />
										</li>

										<li>On the <span class="stronger">Profile Page</span>, copy the <span class="stronger">API Key;</span><br />
											&lbrace;generate a new API Key if none is present&rbrace;
											<br />
											<img src="<?php echo esc_html( $image_path ).'acc_1_3.png'; ?>" />
										</li>

										<li>On <span class="stronger">Wordpress Dashboard</span> go to <span class="stronger">Onehop SMS Services > Configuration</span><br />
											<img src="<?php echo esc_html( $image_path ).'acc_1_4.png'; ?>" />
										</li>

									</ul>
								</div>
							</div>
						</div>


						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
										<span class="seq_no">2</span> Search, Select and Start using SMS on onehop
									<i class="indicator glyphicon glyphicon-triangle-right"></i>
									</a>
								</h4>
							</div>
							<div id="collapseTwo" class="panel-collapse collapse">
								<div class="panel-body">
									<ul class="acc_2_list">
										<li>
											<img src="<?php echo esc_html( $image_path ).'acc_2_1.png'; ?>" />
											On <span class="stronger">Onehop.co</span> use the <span class="stronger">Search</span> to find products according to your requirement 
											<br />
										</li>

										<li>
											<img src="<?php echo esc_html( $image_path ).'acc_2_2.png'; ?>" />
											Proceed to <span class="stronger">Checkout</span> and purchase the products you chose 
											<br />
										</li>

										<li>
											<img src="<?php echo esc_html( $image_path ).'acc_2_3.png'; ?>" />
											Find the products purchased in your <span class="stronger">Product Inventory</span> page 
											<br />
										</li>

										<li>
											<img src="<?php echo esc_html( $image_path ).'acc_2_4.png'; ?>" />
											Follow the <span class="stronger">onboarding emails</span> to configure and start using SMS on Onehop 
											<br />
										</li>


									</ul>
								</div>
							</div>
						</div>


						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
										<span class="seq_no">3</span>Send SMS from Wordpress
									<i class="indicator glyphicon glyphicon-triangle-right"></i>
									</a>
								</h4>
							</div>

							<div id="collapseThree" class="panel-collapse collapse">
								<div class="panel-body">
									<p class="acc_3_data">
										Upon configuring your API key in the Configuration tab,
								you will notice these additional tabs on the menu screen.
									</p>

									<ul class="acc_3_list">
										<li>You can send a single SMS using the <span class="stronger">Send SMS</span> tab on the menu screen.</li>
										<li>You can add, edit or delete templates with placeholder  texts using  <span class="stronger">Manage Templates</span> tab</li>
										<li>Set automated rules for sending SMS with <span class="stronger">SMS Automation</span> tab</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="onehop_docs">
						<h2 class="acc_title productdoc_title">Product Documentation</h2>
						<ul class="download_sec">
							<li>
								<img src="<?php echo esc_html( $image_path ).'pdf_icon.png'; ?>" />
								<a href="http://onehop.co/partners/wordpress/Userguide_Onehop_on_Wordpress.pdf" target="_blank">Onehop for Wordpress user guide</a> </li>
							<li>
								<img src="<?php echo esc_html( $image_path ).'pdf_icon.png'; ?>" />
								<a href="http://onehop.co/blog/wp-content/uploads/Onehop_User_Manual.pdf" target="_blank">Onehop marketplace guide book</a></li>
							<li>
								<img src="<?php echo esc_html( $image_path ).'pdf_icon.png'; ?>" />
								<a href="http://onehop.co/docs/v1/" target="_blank">API documentation for custom development</a></li>
						</ul>
					</div>
					<h2 class="acc_title support_title">Contact Support</h2>
					<div class="contact_info">
						<img src="<?php echo esc_html( $image_path ).'email_icon.png'; ?>" />
						Email us at <span class="stronger"><a href="mailto:support@screen-magic.com">support@screen-magic.com</a></span><br />
						<img src="<?php echo esc_html( $image_path ).'phone_icon.png'; ?>" />
						Call us at USA: <span class="stronger">1-844-833-8350</span>, UK: <span class="stronger">0-808-101-3450</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	
