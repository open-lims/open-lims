$(document).ready(function()
{
	value_handler = new ValueHandler("DataValueAddValues", "[[DECIMAL_SEPARATOR]]", "[[THOUSAND_SEPARATOR]]");
	auto_field = new autofield(undefined, 'DataValueAddValues');	
	base_form_init();
});