<?php
/**
 * Copyright (c) 2010-2014 Zulwi Studio All Rights Reserved.
 * Author  @JerryLocke
 * Date    2014/7/27
 * Blog    http://Jerry.hk
 * Email   i@Jerry.hk
 * Team    http://www.zhuwei.cc
 */

if (!defined('LITE_PATH')) exit;

class IndexController extends Controller {
	function Index() {
		dump(DB::getInstance()->table('cache')->field(array('k', 'v'))->where("k=plugins")->select());
	}
}
