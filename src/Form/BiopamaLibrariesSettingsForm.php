<?php
/**
 * @file
 * Contains \Drupal\biopama_libraries\BiopamaLibrariesSettingsForm
 */
namespace Drupal\biopama_libraries\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure biopama_libraries settings for this site.
 */
class BiopamaLibrariesSettingsForm extends ConfigFormBase {

  public function getFormId() {
    return 'biopama_libraries_settings';
  }
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'biopama_libraries.settings',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('biopama_libraries.settings');
	$settings = $config->get();
	$libraries = array_keys($config->get());
    
	//Help
    $form['help'] = array(
      '#type' => 'details',
      '#title' => t('HELP'),
      '#open' => TRUE,
    );
	$form['help']['help_text'] = array(
	  '#markup' => '<H5>What do the delivery types mean?</H5>'.
	  '<ul>'.
	  '<li>"Local Development" is the normal (not minified) copy in the /libraries folder</li>'.
	  '<li>"Local Production" is the minified copy in the /libraries folder</li>'.
	  '<li>"Composer" will use the copy that was installed from composer (if it exsists, this does not work yet)</li>'.
	  '<li>"CDN" Lets you choose where the Library comes from</li>'.
	  '<li>"Do not load" assumes you do not need the library so it will not be loaded. Can be useful in advanced scenarios.</li>'.
	  '</ul>'.
	  '<br>'.
	  'For the css and js files served locally the naming syntax is [library_name].css and [library_name].js (production) or [library_name].min.css and [library_name].min.js (development).',
	);
	foreach( $libraries as $library){
	  if ($library == "_core"){ //remove the core drupal metadate from the settings
		  continue;
	  }
	
      $form[$library] = array(
        '#type' => 'details',
        '#title' => t($library),
        '#open' => FALSE,
      );
      $form[$library][$library.'_delivery_method'] = array(
        '#type' => 'radios',
        '#title' => t('Choose a delivery method.'),
	    '#description' => $this->t('Default: Local minimized'),
        '#options' => array(
          0 => t('Local Development'),
          1 => t('Local Production'),
          2 => t('Composer'),
		  3 => t('CDN'),
		  4 => t('Do not load'),
	    ),
        '#default_value' => $config->get($library.'.delivery_method'),
      );
      $form[$library][$library.'_cdn_js'] = array(
        '#type' => 'textfield',
        '#title' => $library.' JS CDN',
        '#description' => $this->t('Specify the CDN for the JavaScript.'),
        '#default_value' => $config->get($library.'.cdn_js'),
        '#states' => [
          'visible' => [
            ':input[name="'.$library.'_delivery_method"]' => ['value' => 3],
          ],
        ],
      );
      $form[$library][$library.'_cdn_css'] = array(
        '#type' => 'textfield',
        '#title' => $library.' CSS CDN',,
        '#description' => $this->t('Specify the CDN for the CSS.'),
        '#default_value' => $config->get($library.'.cdn_css'),
        '#states' => [
          'visible' => [
            ':input[name="'.$library.'_delivery_method"]' => ['value' => 3],
          ],
        ],
      );
	  $value = \Drupal::service('check_biopama_form')->check_if_file_present($settings, $library); 
	  drupal_set_message($value, 'error', TRUE);
	}
    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	$config = $this->config('biopama_libraries.settings');
	$libraries = array_keys($config->get());
	foreach( $libraries as $library){
	  if ($library == "_core"){ //remove the core drupal metadate from the settings
		  continue;
	  }
	  $this->config('biopama_libraries.settings')
      ->set($library.'.delivery_method', $form_state->getValue($library.'_delivery_method'))
	  ->set($library.'.cdn_js', $form_state->getValue($library.'_cdn_js'))
	  ->set($library.'.cdn_css', $form_state->getValue($library.'_cdn_css'))
      ->save();
	}
    parent::submitForm($form, $form_state);
  }
}

?>