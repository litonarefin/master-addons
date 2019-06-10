<?php
namespace MasterAddons\Addons\BusinessHours;

use MasterAddons\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'business-hours';
	}

	public function get_widgets() {
		return [
			'Business_Hours',
		];
	}
}
