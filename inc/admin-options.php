<div class="wrap">
	<?php screen_icon("options-general"); ?>
	<h2>Stand Out! Text Emphasis</h2>
	<div class="standout_admin_examples">
		<h4>[standout]Text[/standout]</h4>
		<p>Basic usage. Defaults to yellow highlighter effect.</p>
		<h4>[standout fx="underline"]Text[/standout]</h4>
		<p>Specify the desired effect. In this case, underline</p>
		<h4>[standoutbox]Text[/standoutbox]</h4>
		<p>This create a "Johnson Box" to surround the content. Other effects can be used within it.</p>
		<h4>[standout fx="highlighter,underline"]Text[/standout]</h4>
		<p>Double effects seperated by comma.</p>

	</div>
	<h3>How to use Stand Out!</h3>
	<img src="<?php echo plugins_url( 'images/tutorial-editor-button.jpg', dirname(__FILE__)); ?>" style="float: left; border: solid 1px; margin: 0 10px 10px 0;" />
	<p>There are no settings to set, and no checkboxes to check. Stand Out is good to go with the magic of shortcodes!</p>
	<p>You'll find a new button included in your text editor toolbar when working on posts and pages. It looks a little like an exclamation mark inside an orange circle. Actually, it looks a lot like that. This button gives you a menu of text emphasis effects to choose from.</p>
	<h4 style="clear: left;">Step by step:</h4>
	<ol>
		<li>Highlight text in your page editor that you want to emphasize.</li>
		<li>Click the Stand Out button and choose an effect.</li>
		<li>Your text will be wrapped in the [standout] shortcode.</li>
		<li>Save your page and your effects should be visible.</li>
	</ol>
	<h3>Tips & Tricks</h3>
	<ul>
		<li>This plugin only loads the required CSS styles on a page that is actively using the shortcodes. (it's smart that way) If your effects don't show up, try re-saving the page and refreshing.</li>
		<li>Though it's not currently available through the orange button menu, you can layer effects by listing multiple fx seperated by a comma. This is a new feature, and we welcome any and all bug reports. Note that Wordpress does not allow shortcodes inside of shortcodes (of the same type), so this is the only way to double up effects.</li>
	</ul>

	<p>Check out the <a href="#">Stand Out plugin support forum</a> on Wordpress.org</p>

	<p>Plugin created by <a href="http://www.wpbiz.co/">WPbiz.co</a>. Check in with us for the latest developments. If you like this plugin, we do <a href="http://www.wpbiz.co/donate/">appreciate your donations</a>.</p>

</div>