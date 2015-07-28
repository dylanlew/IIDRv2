<?php
class Adminlanguages extends Languages
{
	function __construct()
	{	parent::__construct(false);
	} //  end of fn __construct
	
	function AssignLanguage($lang = array())
	{	return new AdminLanguage($lang);
	} // end of fn AssignLanguage
	
	function ListLanguages()
	{	
		echo '<table><tr class="newlink"><th colspan="7"><a href="langedit.php">Create new language</a></th></tr><tr><th>Code</th><th>Language</th><th>Display Name</th><th>Order</th><th>Default country</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->languages as $lang)
		{	
			echo '<tr class="stripe', $i++ % 2, '"><td><a href="langedit.php?lang=', $lang->code, '">', $lang->code, '</a></td><td>', $lang->details['langname'], '</td><td>', $lang->details['listname'], '</td><td>', (int)$lang->details['disporder'], '</td><td>', $this->GetCountry($lang->details['country']), '</td><td>', $lang->details['live'] ? 'Yes' : '', '</td><td><a href="langedit.php?lang=', $lang->code, '">edit</a>';
			if ($lang->CanDelete())
			{	echo '&nbsp;|&nbsp;<a href="languages.php?dellang=', $lang->code, '">delete</a>';
			}
			echo '&nbsp;|&nbsp;<a href="fptbylang.php?lang=', $lang->code, '">site text</a></td></tr>';
		}
		echo '</table>';
	} // end of fn ListLanguages
	
} // end of defn Adminlanguages
?>