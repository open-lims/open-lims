// Internal for print_r
function tab(length) {
	
	var result = '';
	
	for (var i = 0; i < length; i++) {
		result = result + "\t";
	}
	
	return result;
	
}


function print_r(print_r_array, layer) {
	
	var result = '';
	
	for (var value in print_r_array) {
	
		if (typeof print_r_array[value] == "object") {
			result = result + " " + tab(layer) + "[" + value + "]" + " => Array \n" + tab(layer) + "(" + "\n" + print_r(print_r_array[value], layer + 1) + "\n " + tab(layer) + " ) \n";
		}else{
			result = result + " " + tab(layer) + "[" + value + "]" + " => " + print_r_array[value] + "\n";
		}
		
	}
	
	return result;
	
}


function substr_replace(string, replacement, start, length){

	if (start > 0) {
		var before = string.substring(0,start-1);
	}else{
		var before = "";
	}
	
	var after = string.substring(start+length,string.length);
	
	return before+replacement+after;
	
} 

// Internal for unserialize
function outer_split(haystack, needle, left_delimeter, right_delimeter) {

	var i = 0;
	var split_break = false;
	var inner_count = 0;

	var temp_string = "";
	
	var return_array = new Array();
	
	while(haystack[i] != null) {

		if ((haystack[i] == left_delimeter) && (split_break == false)) {
			split_break = true;
		}

		if ((haystack[i] == left_delimeter) && (split_break == true)) {
			inner_count++;
		}

		
		if ((haystack[i] == needle) && (split_break == false)) {
			return_array.push(temp_string);
			temp_string = "";
		}else{
			temp_string = temp_string + haystack[i];
		}
		
		
		if ((haystack[i] == right_delimeter) && (split_break == true) && (inner_count > 0)) {
			inner_count--;
		}
		
		if ((haystack[i] == right_delimeter) && (split_break == true) && (inner_count == 0)) {
			split_break = false;
		}

		
		if ((haystack[i+1] == null) && (temp_string != "")) {
			return_array.push(temp_string);
		}

		i++;

	}

	return return_array;
	
}

//Internal for unserialize
function unserialize_array_correction(array) {

	var return_array = new Array();
	
	for (var i=0; i<=array.length-1; i++) {

		var last_delimeter_position = array[i].lastIndexOf("}");

		if ((last_delimeter_position != -1) && (array[i][last_delimeter_position+1] != null)) {
					
			var first_element = array[i].substring(0,last_delimeter_position+1);
			var last_element = array[i].substring(last_delimeter_position+1, array[i].length);
	
			return_array.push(first_element);
			return_array.push(last_element);

		}else{
			return_array.push(array[i]);
		}

	}

	return return_array;
	
}


function unserialize(unserialized_string) {

	if (unserialized_string) {
		
		var string_array = outer_split(unserialized_string, ":", "{", "}");

		switch(string_array[0]) {
	
			// Array
			case "a":
	
				var return_array = new Array();
				
				string_array[2] = substr_replace(string_array[2], "", 0, 1);
				string_array[2] = substr_replace(string_array[2], "", string_array[2].length, 1);
	
				if (string_array[2]) {
								
					var sub_array = outer_split(string_array[2], ";", "{", "}");
					sub_array = unserialize_array_correction(sub_array);
		
					// document.write("<pre>" + print_r(sub_array,0) + "</pre>");
					
					var array_length = sub_array.length-1;

					var return_key = "";
					
					for (var i=0; i<=array_length; i++) {
						if ((i%2) == 0) {
							return_key = unserialize(sub_array[i]);
						}else{
							return_array[return_key] = unserialize(sub_array[i]);
							return_key = "";
						}
					}
	
				}
	
				return return_array;
				
			break;
	
			// Integer
			case "i":
				return string_array[1];
			break;
	
			// Double
			case "d":
				return string_array[1];
			break;
	
			// String
			case "s":
	
				string_array[2] = substr_replace(string_array[2], "", 0, 1);
				string_array[2] = substr_replace(string_array[2], "", string_array[2].length, 1);
	
				return string_array[2];
				
			break;
	
			// Boolean
			case "b":
				return string_array[1];
			break;
				
		}	

	}
	
}


function serialize(serialize_mixed) {
	
	if (serialize_mixed) {
		
		var type = typeof serialize_mixed;
		
		switch(type) {
		
			case "boolean":
				if (serialize_mixed == true) {
					return "b:1;";
				}else{
					return "b:0;";
				}
			break;
			
			case "string":
				var string_length = serialize_mixed.length;
				return "s:" + string_length + ":\"" + serialize_mixed + "\";";	
			break;
			
			case "number":
				if (Math.round(serialize_mixed) == serialize_mixed) {
					return "i:" + serialize_mixed + ";";
				}else{
					return "d:" + serialize_mixed + ";";
				}
			break;
			
			case "object":
				
				// Überprüfen ob Array
				if(serialize_mixed instanceof Array) {
					
					var array_length = serialize_mixed.length - 1;
					
					if (array_length >= 0) {
					
						var return_string = "";
						
						var return_array_length = array_length+1;
						
							return_string = "a:" + return_array_length + ":{";
						
						// Wenn Array, dann Elementweise zerlegen, funktion rekursiv aufrufen
						for(var i=0;i<=array_length;i++) {
							
							// Rückgabewerte zusammensetzen
							return_string = return_string + "i:" + i + ";" + serialize(serialize_mixed[i]);
							
						}
						
						return_string = return_string + "}";
						
						return return_string;
						
					}else{
						return null;
					}
					
				}else{
					return null;
				}
				
			break;
			
			default:
				
			break;
		
		}				
		
	}	
	
}

