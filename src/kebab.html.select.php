<?php
/**
*	HTML Element abstraction class
*
*	Generates a valid HTML's SELECT element, filled with given values
*	@author: Miguel Pragier
*	@file: kebab.html.select.php
*	@lastRevision: 2017-07-06
*	@version 0.1.215
*/

namespace Pragier\Kebab;

class KebabHtmlSelect
{
	private $id;
	private $name;
	private $cssClasses;
	private $inlineStyle;
	private $behaviour;
	private $options;
	private $selectedOption;
	private $optGroups;
	private $autoFocus;
	private $multiple;
	private $required;

	function __construct( $id )
	{
		$this->id = $id;
		$this->cssClasses = [];
		$this->inlineStyle = [];
		$this->behaviour = [];
		$this->optionsValues = [];
		$this->optionsTexts = [];
		$this->selectedOption = null;
		$this->optGroups = [];
		$this->autoFocus = false;
		$this->multiple = false;
		$this->required = false;
	}

	/**
	  * addClass( $css_class )
	  *
	  * Add a css class for our element
	  */
	public function addClass( $css_class )
	{
		array_push($this->cssClasses, $css_class);
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// SetMultple()
	//
	// Turns the element in a multline/multiselect list
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function setMultiple()
	{
		$this->multiple = TRUE;
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// AddStyle( $property, $value )
	//
	// Add a inline css style
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function addStyle( $property, $value )
	{
		$this->inlineStyle[$property] = $value;
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// SetAutoFocus()
	//
	// Set the autofocus property
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function setAutoFocus()
	{
		$this->autoFocus = TRUE;
	}
	
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// setRequired()
	//
	// Set the html required attribute property
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function setRequired( $required = true )
	{
		$this->required = $required;
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// AddBehaviour( $event, $command )
	//
	// Add a javascript command for a html event.
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function addBehaviour( $event, $command )
	{
		$this->behaviour[$event] = $command;
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// AddOption( $value, $text )
	//
	// Add an option to our select.
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function addOption( $value, $text )
	{
		array_push($this->optionsValues, $value);
		array_push($this->optionsTexts, $text);
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// AddGroup( $label )
	//
	// Add an optgroup to our select.
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function addGroup( $label )
	{
		$position = count($this->optionsValues);
		array_push($this->optGroups, array($position, $label));
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// SetSelectedOption( $value )
	//
	// Define the selected option.
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function setSelectedOption( $value )
	{
		$this->selectedOption = $value;
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// AddRange( $first_option, $last_option )
	//
	// Insert a numerical range on options, where the value is equal the text.
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function addRange( $first_option, $last_option )
	{
		if ( intval($first_option) < intval($last_option) )
		{
			for ( $i = $first_option; $i <= $last_option; $i++ )
				$this->AddOption($i, $i);
		}
		else
		{
			for ( $i = $first_option; $i >= $last_option; $i-- )
				$this->AddOption($i, $i);
		}
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// AddArray( $options_array )
	//
	// Add the elements of a simple array at the end of current options.
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function addArray( $options_array )
	{
		foreach ( $options_array as $option )
			$this->AddOption($option[0], $option[1]);
	}

	/**
	* AddWeekRange( $idiom )
	*
	* Insert the days of week, from 1 to 7 - sunday to saturday - using numbers to value and complete textual names as text.
	*/
	public function addWeekRange( $idiom )
	{
		$WEEKDAYS = array('pt', 'en', 'es');
		$WEEKDAYS['pt'] = array('Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado');
		$WEEKDAYS['en'] = array('Sunday','Monday','Tuesday','Wednesday','Thrusday','Friday','Saturday');
		$WEEKDAYS['es'] = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo');

		// Definition of months source:
		$myWeekDay = $WEEKDAYS[strtolower($idiom)];

		for ( $i=1; $i<8; $i++ )
			$this->AddOption(($i+1), $myWeekDay[$i]);
	}

	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// AddMonthRange( $idiom )
	//
	// Insert the months of the year, from 1 to 12, using numbers to value and complete textual names as text.
	// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function addMonthRange( $idiom, $from_current_month = FALSE )
	{
		$MONTHS = array('pt', 'en', 'es');
		$MONTHS['pt'] = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
		$MONTHS['en'] = array('January','February','March','April','May','June','July','August','September','October','November','December');
		$MONTHS['es'] = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Deciembre');

		// Definition of months source:
		$myMonth = $MONTHS[strtolower($idiom)];

		// Where to start:
		$firstMonth = $from_current_month ? date('M') : 1;

		//
		for ( $i=$firstMonth; $i <=12; $i++ )
			$this->AddOption($i, $myMonth[($i-1)]);
	}

	/**
	// output()
	//
	// Generate the resulting element and return its html code.
	*/
	public function output( $output_only_options = false )
	{
		$out = null;
		
		if ( !$output_only_options ){
			$myClasses = null;
			$myInlineStyle = null;
			$myBehaviour = null;
			
			if ( count($this->cssClasses) >= 1 ){
				$tmp = implode(' ', $this->cssClasses);

				$myClasses = 'class="'.$tmp.'"';
			}

			if ( count($this->inlineStyle) >= 1 ){
				$buffer = '';
				
				foreach ( $this->inlineStyle as $key=>$value )
					$buffer .= "$key:$value;";

				$myInlineStyle = "style='$buffer'";
			}

			if ( count($this->behaviour) >= 1 ){
				$buffer = '';

				foreach ( $this->behaviour as $key=>$value )
					$buffer .= "$key=\"$value\"";

				$myBehaviour = $buffer;
			}
			
			$autoFocus = $this->autoFocus ? 'autofocus' : '';
			$multiple = $this->multiple ? 'multiple' : '';
			$req = $this->required ? 'required' : '';
			$out = sprintf('<select id="%s" name="%s" %s %s %s %s %s %s>', $this->id, $this->name, $myClasses, $myInlineStyle, $autoFocus, $myBehaviour, $multiple, $req);
		}

		$qtdOptions = count($this->optionsValues);

		if ( $qtdOptions >= 1 )
		{
			$buffer = '';
			$position = 0;
			$optEndMark = 0; // Controls optgroup end tag.

			for ( $i = 0; $i < $qtdOptions; $i++ )
			{
				$value = $this->optionsValues[$i];
				$text = $this->optionsTexts[$i];

				if ( count($this->optGroups) >= 1 )
				{
					foreach ( $this->optGroups as $og )
					{
						if ( intval($og[0]) == intval($position) )
						{
							if ( $optEndMark > 0 ) // There is a previous group waiting for the end tag.
							{
								$buffer .= '</optgroup>';
								$optEndMark = 0;
							}

							$buffer .= "<optgroup label='$og[1]'>";
							$optEndMark = 1;
						}
					}
				}

				if ( trim(strval($value)) != trim(strval($this->selectedOption)) )
					$buffer .= "<option value=\"$value\">$text</option>";
				else
					$buffer .= "<option value='$value' selected>$text</option>";

				$position++;
			}

			if ( $optEndMark >= 1 )
				$buffer .= '</optgroup>';

			$out .= $buffer;
		}

		if ( !$output_only_options )
			$out .= '</select>';
		
		return $out;
	}

	/**
	// ChangeIDAndOutput()
	//
	// Change ID and Name attributes and generate the resulting element and return its html code.
	*/
	public function outputWithNewId( $new_id )
	{
		$this->id = $new_id;

		return $this->output();
	}
}
