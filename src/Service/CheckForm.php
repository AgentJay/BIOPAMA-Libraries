<?php

// The namespace is Drupal\biopama_libraries\Service\CheckForm]
namespace Drupal\biopama_libraries\Service;

/**
 * The CheckForm service. Checks if the library exists when it's selected.
 */
class CheckForm {

  /**
   * Validate the Library Delivery Method Field to see if the local library is present if it's been selected.
   *
   * @return string
   * 
   */
  public function check_if_file_present($settings, $library) {
	$value;
	if ($settings[$library]['delivery_method'] == 0){
	  $path = DRUPAL_ROOT . '/libraries/' . $library . '/' . $library . '.js';
	} else if ($settings[$library]['delivery_method'] == 1){
	  $path = DRUPAL_ROOT . '/libraries/' . $library . '/' . $library.'.min.js';
	} else {
	  return;
	}

	$exists = is_file($path);
	$exists ? '' : $value .= 'Missing the '.$library.' Library. Please add it to /libraries/'.$library.' in the root of your web directory';
	
    return $value;
  }

}