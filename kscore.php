<?php
/**
* Plugin Name: Klout score Widget
* Plugin URI: http://matthieufleitz.fr/
* Description: Display your Klout Score on your Wordpress website as a badge/widget using the Klout V2 API (not iFrame).
* Version: 1.4
* Author: Matthieu Fleitz
* Author URI: http://matthieufleitz.fr
*/
add_action('widgets_init','kscore_init');

function kscore_init(){
	register_widget("kscore_widget");
}

class kscore_widget extends WP_widget{

	function kscore_widget(){
		$options = array(
		"classname" => "kscore-widget",
		"description" => "Display your Klout Score on your Wordpress website as a badge/widget using the Klout V2 API (not iFrame)."
		);
		$this->wp_widget("widget-kscore","Klout Score Widget",$options);
	}

	function widget($args, $d){
		extract($args);

		echo $before_widget;
		echo $before_title.$d['titre'].$after_title;

		//First, we need to retreive the user's Klout ID
		$url_user_id = "http://api.klout.com/v2/identity.json/twitter?screenName=".$d['users']."&key=".$d['key']."";
		$data_user_id = json_decode((@file_get_contents($url_user_id)));
		$klout_user_id = $data_user_id->id;

		//Then, retreive the Klout score of the user, using the user's Klout ID and API key V2
		$url_kscore = "http://api.klout.com/v2/user.json/".$klout_user_id."/score?key=".$d['key']."";
		$data = json_decode((@file_get_contents($url_kscore)));

		//If everything works well, then display Klout score
		if($data){
			$kscore = $data->score;
		}

		//else display "?"
		else{
			$kscore = "?";
		}

		//HTML, feel free to modify the inline CSS or Background-img SRC
		?>

		<?php 
		//If user didn't specify the color code correclty we assign the white color by default...
		if(!preg_match('/^#[a-f0-9]{6}$/i', $d['background_color'])){
			$d['background_color'] = "#FFFFFF";
			} 
		?>

		<div class="plugin">
			<div style="position:relative;background:url('<?php echo plugin_dir_url( $file ); ?>klout-score-badge-with-klout-api-v2/img/klout-logo.png') <?php echo $d['background_color'] ?> no-repeat; width: 160px;height:140px;" >		
				<p style="font-size:<?php echo $d['score_size'] ?>px; font-weight:bold; line-height:140px; text-align:center;"><a style="text-decoration:none;color:#FFFFFF;" href="http://klout.com/#/<?php echo $d['users'] ?>"><?php print_r(round($kscore));?></a></p>
				<p style="text-decoration:none; position:absolute; top:125px; left:2px;"><a style="cursor:default;text-decoration:none;color:<?php echo $d['background_color'] ?>;" href="http://matthieufleitz.fr">expert Apple</a></p>
			</div>
		</div>

		<?php
		
		echo $after_widget;
	}


	function update($new, $old){
		return $new;
	}

	function form($d){
		//défault value
		$default = array(
			"titre" => "My Klout Score",
			"key" => "fyz62w72y5jbcmmdd4zzcxy5",
			"users" => "MatthieuFleitz",
			"background_color" => "#ffffff",
			"score_size" => "40"
			);

		$d = wp_parse_args($d,$default);

		?>
			<label for="<?php echo $this->get_field_id('titre'); ?>">Widget title: </label>
			<br />
			<input value="<?php echo $d['titre']; ?>" name="<?php echo $this->get_field_name('titre'); ?>" id="<?php echo $this->get_field_id('titre'); ?>" type="text" />

			<br />

			<label for="<?php echo $this->get_field_id('key'); ?>">Klout API partner Key V2:</label>
			<br />
			<small>Use this one or your own.</small>
			<input value="<?php echo $d['key']; ?>" name="<?php echo $this->get_field_name('key'); ?>" id="<?php echo $this->get_field_id('key'); ?>" type="text" />

			<br />

			<label for="<?php echo $this->get_field_id('users'); ?>">Klout username:</label>
			<input value="<?php echo $d['users']; ?>" name="<?php echo $this->get_field_name('users'); ?>" id="<?php echo $this->get_field_id('users'); ?>" type="text" />

			<br />

			
			<hr />
			<p>Plugin Customisation:</p>

			<label for="<?php echo $this->get_field_id('score_size'); ?>">Score Size:</label>
			<small>(Number of px)</small>
			<input value="<?php echo $d['score_size']; ?>" name="<?php echo $this->get_field_name('score_size'); ?>" id="<?php echo $this->get_field_id('score_size'); ?>" type="text" maxlength="2" />

			<br />

			<label for="<?php echo $this->get_field_id('background_color'); ?>">Background-color: </label>
			<small>(like this: #XXXXXX)</small>
			<input value="<?php echo $d['background_color']; ?>" name="<?php echo $this->get_field_name('background_color'); ?>" id="<?php echo $this->get_field_id('background_color'); ?>" type="text" maxlength="7"/>

			<hr />

			<p>Trouble? Please follow this link:</p>
			<a href="http://matthieufleitz.fr/site/article/afficher-score-Klout-dans-wordpress">Matthieufleitz.fr</a>
			<hr />

			<p>Like this widget? Thank you for sharing:</p>
			<div class="fb-like" data-href="http://matthieufleitz.fr/site/article/afficher-score-Klout-dans-wordpress" data-send="false" data-layout="box_count" data-width="450" data-show-faces="true" data-action="recommend"></div>



			<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1&appId=278560732245157";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
				</script>
		<?php
	}

}

?>