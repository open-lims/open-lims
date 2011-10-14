/*
 * version: 0.4.0.0
 * author: Roman Konertz <konertz@open-lims.org>
 * copyright: (c) 2008-2011 by Roman Konertz
 * license: GPLv3
 * 
 * This file is part of Open-LIMS
 * Available at http://www.open-lims.org
 * 
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;
 * version 3 of the License.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */

List = function(ajax_handler, ajax_run, ajax_count_run, argument_array,
		json_get_array, css_main_id, entries_per_page, row_array) {
	var sort_array = new Array();

	var sort_value = "";
	var sort_method = "";
	var page = 1;

	var get_array = getQueryParams(document.location.search);

	var parsed_row_array = $.parseJSON(row_array);
	var colspan = parsed_row_array.length;

	var number_of_entries = 0;
	var number_of_pages = 0;

	this.reload = function() {
		load_content(sort_value, sort_method, page);
	}

	// this.get_argument_array = function()
	// {
	// return argument_array;
	// }
	//	
	// this.set_argument_array = function(array)
	// {
	// argument_array = array;
	// }

	function count_entries() {
		$.ajax({
			type : "POST",
			url : ajax_handler + "?session_id=" + get_array['session_id']
					+ "&run=" + ajax_count_run,
			data : "argument_array=" + argument_array,
			async : false,
			success : function(data) {
				number_of_entries = parseInt(data);
				if (number_of_entries == 0) {
					number_of_pages = 1;
				} else {
					number_of_pages = Math.ceil(number_of_entries
							/ entries_per_page);
				}
			}
		});
	}

	function load_content(sort_value, sort_method, local_page) {
		var local_height = $("#" + css_main_id).height();

		page = local_page;

		$("#" + css_main_id).contents().detach();

		var margin = parseInt(local_height / 2);
		margin = Math.floor(margin);
		margin -= 8;
		if (margin < 0) // init
			margin = 10;

		$("#" + css_main_id)
				.append(
						"<tr><td colspan='"
								+ colspan
								+ "'><div style='text-align:center; margin-top:"
								+ margin
								+ "px;'><img src='images/animations/loading_circle_small.gif' alt='Loading...' /></div></td></tr>");

//		$("#" + css_main_id).height(local_height);

		count_entries();

		$.ajax({
			type : "GET",
			url : "core/modules/base/list.ajax.php",
			data : "username=" + get_array['username'] + "&session_id="
					+ get_array['session_id'] + "&run=get_page_bar&page="
					+ page + "&number_of_pages=" + number_of_pages
					+ "&css_page_id=" + css_main_id + "Page",
			async : false,
			success : function(data) {
				$("#" + css_main_id + "PageBar").html(data);

				$("." + css_main_id + "Page").each(function() {
					$(this).click(function() {
						var id = $(this).attr("id");
						page = id.replace(css_main_id + "Page", "");
						load_content(sort_value, sort_method, page);
					});
				});
			}
		});

		$.ajax({
			type : "GET",
			url : "core/modules/base/list.ajax.php",
			data : "username=" + get_array['username'] + "&session_id="
					+ get_array['session_id']
					+ "&run=get_page_information&number_of_entries="
					+ number_of_entries + "&number_of_pages=" + number_of_pages
					+ "",
			async : false,
			success : function(data) {
				$("#" + css_main_id).parent().parent().children(
						".ListPageInformation").html(data);
			}
		});

//		 console.log(argument_array);
		if (argument_array != "\"0\"" && argument_array.indexOf("user_id") < 0
				&& argument_array.indexOf("organisation_unit_id") < 0) // user_id?
																		// organisation_unit_id?
		{
			var argument_parts = argument_array.split(",");
			var current_folder_id = argument_parts[1].replace(/]/g, "")
					.replace(/\"/g, "");
			var current_virtual_folder_id = argument_parts[3].replace(/]/g, "")
					.replace(/\"/g, "");
		}

		$.ajax({
			type : "GET",
			url : "core/modules/base/list.ajax.php",
			data : "username=" + get_array['username'] + "&session_id="
					+ get_array['session_id']
					+ "&run=get_folder_name_by_id&folder_id="
					+ current_folder_id,
			success : function(data) {
				if (data != "") {
					data = "Current Folder: " + data;
					$("#" + css_main_id).parent().parent().children(
							".OverviewTableRight").html(data);
				}
			}
		});

		$.ajax({
			type : "POST",
			url : ajax_handler + "?username=" + get_array['username']
					+ "&session_id=" + get_array['session_id'] + "&run="
					+ ajax_run + "&sortvalue=" + sort_value + "&sortmethod="
					+ sort_method + "&page=" + page,
			data : "row_array=" + row_array + "&argument_array="
					+ argument_array + "&entries_per_page=" + entries_per_page
					+ "&get_array=" + json_get_array,
			success : function(data) {
				var last_height = $("#" + css_main_id).height();
				$("#" + css_main_id).height("auto");
				$("#" + css_main_id).html(data);
				var new_height = $("#" + css_main_id).height();
				$("#" + css_main_id).children().remove();
				$("#" + css_main_id).append("<div></div>"); // element must not
															// be empty to
															// animate height
				if (new_height != last_height) {
					$("#" + css_main_id).height(last_height);
					$("#" + css_main_id).animate({
						"height" : new_height
					}, "fast", function() {
						$("#" + css_main_id).html(data);
//						 $("#"+css_main_id).html(data).hide().fadeIn("fast");
						// $("#"+css_main_id).html(data).animate({opacity: 0},
						// 0).animate({opacity: 1}, 1000);

						list_click_handler();
					});
				} else {
					$("#" + css_main_id).html(data);
					// $("#"+css_main_id).html(data).hide().fadeIn("fast");
					list_click_handler();
				}

			}
		});

		// innere hilfsfunktion für load_content
		function list_click_handler() {
			$("#" + css_main_id + " > tr")
					.each(
							function() {
								var link = $(this).children("td:nth-child(2)")
										.children();
								if ($(link).is("div")) {
									link = $(link).children("a");
									$(link)
											.click(
													function(evt) {
														evt.preventDefault();
														var href = $(link)
																.attr("href");
														var href_parts = href
																.split("&");
														var folder_id = href_parts[href_parts.length - 1]
																.substr(
																		10,
																		href_parts[href_parts.length - 1].length);
														argument_array = argument_array
																.replace(
																		current_folder_id,
																		folder_id);
														$
																.ajax({
																	type : "POST",
																	url : ajax_handler
																			+ "?username="
																			+ get_array['username']
																			+ "&session_id="
																			+ get_array['session_id']
																			+ "&run="
																			+ ajax_run
																			+ "&sortvalue="
																			+ sort_value
																			+ "&sortmethod="
																			+ sort_method
																			+ "&page="
																			+ page,
																	data : "row_array="
																			+ row_array
																			+ "&argument_array="
																			+ argument_array
																			+ "&entries_per_page="
																			+ entries_per_page
																			+ "&get_array="
																			+ json_get_array,
																	async : true,
																	success : function(
																			data) {
																		load_content(
																				sort_value,
																				sort_method,
																				page);
																	}
																});
													});
								}
							});
		}
	}

	load_content(sort_value, sort_method, page);

	function check_array(sort_value) {
		if (sort_array != undefined) {
			var sort_array_length = sort_array.length;

			if (sort_array_length >= 1) {
				for ( var i = 0; i <= sort_array_length - 1; i++) {
					if (sort_array[i][0] == sort_value) {
						return i;
					}
				}
				return -1;
			} else {
				return -1;
			}
		} else {
			return -1;
		}
	}

	function change_symbol(id, symbol) {
		$("." + css_main_id + "Row").each(
				function() {
					var local_id = $(this).attr("id");

					if (local_id == id) {
						if (symbol == "upside") {
							$("#" + local_id + " > a > img").attr("src",
									"images/upside.png");
						} else {
							$("#" + local_id + " > a > img").attr("src",
									"images/downside.png");
						}
					} else {
						$("#" + local_id + " > a > img").attr("src",
								"images/nosort.png");
					}
				});
	}

	$("." + css_main_id + "Row").each(function() {
		$(this).click(function() {
			var id = $(this).attr("id");
			sort_value = id.replace(css_main_id + "Row", "");

			var sort_method_key = check_array(sort_value);
			if (sort_method_key != -1) {
				if (sort_array[sort_method_key][1] == "asc") {
					sort_array[sort_method_key][1] = "desc";
					sort_method = "desc";

					change_symbol(id, "downside");
				} else {
					sort_array[sort_method_key][1] = "asc";
					sort_method = "asc";

					change_symbol(id, "upside");
				}
			} else {
				sort_array_length = sort_array.length;

				sort_array[sort_array_length] = new Array();
				sort_array[sort_array_length][0] = sort_value;
				sort_array[sort_array_length][1] = "asc";
				sort_method = "asc";

				change_symbol(id, "upside");
			}

			load_content(sort_value, sort_method, page);
		});
	});

}