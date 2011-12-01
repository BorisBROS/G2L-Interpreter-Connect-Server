<?php
/*
 * Mysql Ajax Table Editor
 *
 * Copyright (c) 2008 Chris Kitchen <info@mysqlajaxtableeditor.com>
 * All rights reserved.
 *
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://www.mysqlajaxtableeditor.com
 */
require_once('Common.php');
class HomePage extends Common
{
	
	function displayHtml()
	{
		echo '<p><a href="Example1.php">Example 1</a></p>';
		echo '<p><a href="Example2.php">Example 2</a></p>';
	}
	
	function HomePage()
	{
		$this->displayHeaderHtml();
		$this->displayHtml();
		$this->displayFooterHtml();
	}
}
$lte = new HomePage();
?>