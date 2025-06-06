<?php
/*
* Phoenix Media Rename options management
*
*/

class phoenix_media_rename_options {

	private $options;
	public $option_update_revisions;
	public $option_sanitize_filename;
	public $option_remove_accents;
	public $option_debug_mode;
	public $option_create_redirection;
	public $option_serialize_if_filename_present;
	public $option_filename_header;
	public $option_filename_trailer;
	public $option_category_filename_header;
	public $option_category_filename_trailer;
	public $option_convert_to_lowercase;
	public $option_enable_alttext_integration;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->options = get_option('pmr_options');

		if (isset($this->options)){
			$this->option_update_revisions = $this->get_option_boolean($this->options, 'pmr_update_revisions', true);
			$this->option_sanitize_filename = $this->get_option_boolean($this->options, 'pmr_sanitize_filenames', true);
			$this->option_remove_accents = $this->get_option_boolean($this->options, 'pmr_remove_accents', true);
			$this->option_debug_mode = $this->get_option_boolean($this->options, 'pmr_debug_mode', false);
			$this->option_create_redirection = $this->get_option_boolean($this->options, 'pmr_create_redirection', false);
			$this->option_serialize_if_filename_present = $this->get_option_boolean($this->options, 'pmr_serialize_if_filename_present', true);
			$this->option_convert_to_lowercase = $this->get_option_boolean($this->options, 'pmr_filename_lowercase', true);
			$this->option_filename_header = $this->get_option_text($this->options, 'pmr_filename_header', '');
			$this->option_filename_trailer = $this->get_option_text($this->options, 'pmr_filename_trailer', '');
			$this->option_category_filename_header = $this->get_option_boolean($this->options, 'pmr_category_filename_header', false);
			$this->option_category_filename_trailer = $this->get_option_boolean($this->options, 'pmr_category_filename_trailer', false);
			$this->option_enable_alttext_integration = $this->get_option_boolean($this->options, 'pmr_enable_alttext_integration', false);

			$this->clear_options();
		}
	}

	/**
	 * Updates options if necessary
	 * Some option value could be changed during the rename process
	 *
	 * @param array $searches strings to search for
	 * @param array $replaces values to replace
	 *
	 * @return void
	 */
	public function update_options($searches, $replaces){
		$local_options = $this->get_all_options();

		foreach ($local_options as $option) {
			$option['value'] = phoenix_media_rename_lib::unserialize_deep($option['value']);
			$new_option = phoenix_media_rename_lib::replace_media_urls($option['value'], $searches, $replaces);
			if ($new_option != $option['value']) update_option($option['name'], $new_option);
		}
	}

#region private methods

	/**
	 * Clears options
	 *
	 * @return void
	 */
	private function clear_options(){
		//clear header and trailer
		if ($this->option_filename_header){
			$this->option_filename_header = trim($this->option_filename_header, " -");
		} else {
			$this->option_filename_header = '';
		}

		if ($this->option_filename_trailer){
			$this->option_filename_trailer = trim($this->option_filename_trailer, " -");
		} else {
			$this->option_filename_trailer = '';
		}
	}

	/**
	 * Retrive boolean option value
	 *
	 * @param array $options associative array containing the options
	 * @param string $name name of the variable to get
	 * @param boolean $default default value for variable to get
	 * @return boolean option value
	 */
	private function get_option_boolean($options, $name, $default){
		$result = $default;

		if (isset($options[$name])){
			if ($options[$name]) {
				$result = true;
			}else{
				$result = false;
			}
		} else {
			// default
			$result = $default;
		}

		return $result;
	}

	/**
	 * Retrive text option value
	 *
	 * @param array $options associative array containing the options
	 * @param string $name name of the variable to get
	 * @param boolean $default default value for variable to get
	 * @return string option value
	 */
	private function get_option_text($options, $name, $default = ''){
		if (isset($options[$name])){
			if ($options[$name]) {
				return $options[$name];
			}else{
				return '';
			}
		} else {
			// default
			return $default;
		}
	}

	/**
	 * Get all options
	 *
	 * @return array
	 */
	private function get_all_options() {
		return $GLOBALS['wpdb']->get_results("SELECT option_name as name, option_value as value FROM {$GLOBALS['wpdb']->options}", ARRAY_A);
	}

#endregion

}