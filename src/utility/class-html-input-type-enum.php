<?php
/**
 * @author  Christoph Bessei
 * @version 0.01
 */

/**
 * @method static Html_Input_Type_Enum VIEW()
 * @method static Html_Input_Type_Enum EDIT()
 */
class Html_Input_Type_Enum extends Enum {
	const TEXT     = 'text';
	const PASSWORD = 'password';
	const RADIO    = 'radio';
	const CHECKBOX = 'checkbox';
	const SUBMIT   = 'submit';
	const TEXTAREA = 'textarea';
	const SELECT   = 'select';
	const HIDDEN   = 'hidden';
} 