<?php
/*
 * Nathan Breit
 * Global To Local Admin Portal
 * Based on Example1.php from Mysql Ajax Table Editor
 * http://www.mysqlajaxtableeditor.com
 */

//TODO:
//Password protection

require_once('Common.php');
require_once('php/lang/LangVars-en.php');
require_once('php/AjaxTableEditor.php');



class ManageInterpreters extends Common
{
	var $Editor;

	//Make sure that phone numbers are specified only as digits (i.e. no dashes or parens)
	function validatePhone($col, $val, $info)
	{
		return preg_match("/^(\d)*$/", $val) > 0;
	}
	//This function displays the schedule for the interpreter specified in $this->Editor->info
	function showSchedule()
	{
		$interpreter_id = $this->Editor->escapeData($this->Editor->info);

		//TODO: For now lets just have a button that links to the scheduler.
		//I'm a bit worried about how the javascript from the two libraries will interact
		//(especially considering the way mate does navigation) so an iframe might be the best way to put this into the page.
		
		/*
		$html = <<<EOF
		 	<center>
			<button onclick="window.location.href='../scheduler/samples/scheduler/g2l_scheduler.php?interpreter_id=$interpreter_id'">Scheduler</button>
		 	</center>
EOF;*/
		$html = <<<EOF
		<center><p>Note: the schedule updates itself independent of the form above.</p></center>
		<iframe src='../scheduler/g2l_scheduler.php?interpreter_id=$interpreter_id' width="100%" height="1055">
		  <p>Your browser does not support iframes.</p>
		</iframe>
EOF;
		$no_schedule_message = <<<EOF
		<center><p>Note: the schedule is not available until the interpreter has been added.</p></center>
EOF;

		if(!$interpreter_id) $html = $no_schedule_message;
/*
		$html .= '<center><form><fieldset><legend>Available:</legend>';
		$html .= '<select><option>Sunday</option><option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option></select><br />';

		$i = 0;
		while ($i < 24) {
			$time = $i % 12 + 1;
			$AMPM = 'AM';
			if( $i >= 12 ){
				$AMPM = 'PM';
			}
				
			$html .= "<input type=\"checkbox\" />$time :00 $AMPM<br />";
			$html .= "<input type=\"checkbox\" />$time :30 $AMPM<br />";
			$i++;
		}

		$html .= '</fieldset> </form></center>';*/

		$this->Editor->retArr[] = array('layer_id' => 'scheduleLayer', 'where' => 'innerHTML', 'value' => $html);
	}
	function clearSchedule()
	{
		$this->Editor->retArr[] = array('layer_id' => 'scheduleLayer', 'where' => 'innerHTML', 'value' => '');
	}

	function displayHtml()
	{
		?>
			<center><img src="images/g2l_logo.jpg" alt="global to local logo" /></center>

			<br />
	
			<div align="left" style="position: relative;"><div id="ajaxLoader1"><img src="images/ajax_loader.gif" alt="Loading..." /></div></div>
			
			<br />

			<div id="historyButtonsLayer" align="left">
			</div>
	
			<div id="historyContainer">
				<div id="information">
				</div>
		
				<div id="titleLayer" style="padding: 2px; font-weight: bold; font-size: 18px; text-align: center;">
				</div>
		
				<div id="tableLayer" align="center">
				</div>
				
				<div id="recordLayer" align="center">
				</div>		
				
				<div id="searchButtonsLayer" align="center">
				</div>
			</div>
			
			<div id="scheduleLayer">
				
			</div>

			<script type="text/javascript">
				trackHistory = false;
				var ajaxUrl = '<?php echo $_SERVER['PHP_SELF']; ?>';
				toAjaxTableEditor('update_html','');
			</script>
		<?php
	}

	function initiateEditor()
	{

		$validateFunction = array(&$this,'validatePhone');

		$tableColumns['id'] = array('display_text' => 'ID', 'perms' => 'VQSXO');
		$tableColumns['first'] = array('display_text' => 'First Name', 'perms' => 'EVCTAXQSHO', 'req' => true);
		$tableColumns['last'] = array('display_text' => 'Last Name', 'perms' => 'EVCTAXQSHO', 'req' => true);
		$tableColumns['g2lphone'] = array('display_text' => 'G2L Phone', 'perms' => 'EVCTAXQSHO', 'req' => true, 'val_fun' => $validateFunction);
		$tableColumns['altphone'] = array('display_text' => 'Alternate Phone', 'perms' => 'EVCAXQSHO', 'val_fun' => $validateFunction);
		$tableColumns['email'] = array('display_text' => 'Email', 'perms' => 'EVCAXQSHO');
		//select_query:
		//Query to create drop down list (must return 2 columns: the first column is the select values and the second column is what gets displayed).
		//see: http://www.mysqlajaxtableeditor.com/Documentation.php
		$my_select_query = 'SELECT language_name_string,language_name_string FROM languages;';
		$tableColumns['language1'] = array('display_text' => 'Language 1', 'perms' => 'EVCTAXQSHO', 'select_query' => $my_select_query, 'req' => true);
		$tableColumns['language2'] = array('display_text' => 'Language 2', 'perms' => 'EVCTAXQSHO', 'select_query' => $my_select_query);
		// TODO: Make this display as yes/no
		$tableColumns['active'] = array('display_text' => 'Active', 'perms' => 'EVCTAXQSHO',
		                                'checkbox' => array('checked_value' => true, 'un_checked_value' => false), 'default' => true);

		$tableName = 'interpreters';
		$primaryCol = 'id';
		$errorFun = array(&$this,'logError');
		$permissions = 'EAVIDQCSXHO';
		
		$this->Editor = new AjaxTableEditor($tableName,$primaryCol,$errorFun,$permissions,$tableColumns);
		$this->Editor->setConfig('tableInfo','cellpadding="1" width="1000" class="mateTable"');
		$this->Editor->setConfig('orderByColumn','last');
		$this->Editor->setConfig('addRowTitle','Add Interpreter');
		$this->Editor->setConfig('editRowTitle','Edit Interpreter');
		//$this->Editor->setConfig('iconTitle','Edit Employee');
		
		$showScheduleFunction = array(&$this,'showSchedule');

		$this->Editor->setConfig('addScreenFun', $showScheduleFunction);
		$this->Editor->setConfig('editScreenFun', $showScheduleFunction);
		$this->Editor->setConfig('viewScreenFun', $showScheduleFunction);
		$this->Editor->setConfig('tableScreenFun', array(&$this,'clearSchedule'));

	}
	
	
	function ManageInterpreters()
	{
		if(isset($_POST['json']))
		{
			session_start();
			// Initiating lang vars here is only necessary for the logError, and mysqlConnect functions in Common.php. 
			// If you are not using Common.php or you are using your own functions you can remove the following line of code.
			$this->langVars = new LangVars();
			$this->mysqlConnect();
			if(ini_get('magic_quotes_gpc'))
			{
				$_POST['json'] = stripslashes($_POST['json']);
			}
			if(function_exists('json_decode'))
			{
				$data = json_decode($_POST['json']);
			}
			else
			{
				require_once('php/JSON.php');
				$js = new Services_JSON();
				$data = $js->decode($_POST['json']);
			}
			if(empty($data->info) && strlen(trim($data->info)) == 0)
			{
				$data->info = '';
			}
			$this->initiateEditor();
			$this->Editor->main($data->action,$data->info);
			if(function_exists('json_encode'))
			{
				echo json_encode($this->Editor->retArr);
			}
			else
			{
				echo $js->encode($this->Editor->retArr);
			}
		}
		else if(isset($_GET['export']))
		{
            session_start();
            ob_start();
            $this->mysqlConnect();
            $this->initiateEditor();
            echo $this->Editor->exportInfo();
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/x-msexcel");
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="'.$this->Editor->tableName.'.csv"');
            exit();
        }
		else
		{
			$this->displayHeaderHtml();
			$this->displayHtml();
			$this->displayFooterHtml();
		}
	}
}
$lte = new ManageInterpreters();
?>
