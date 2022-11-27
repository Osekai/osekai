/* 
 * Javascript BBCode Parser Config Options
 * @author Philip Nicolcev
 * @license MIT License
 */

var parserColors = [ 'gray', 'silver', 'white', 'yellow', 'orange', 'red', 'fuchsia', 'blue', 'green', 'black', '#cd38d9' ];

var parserTags = {
	'b': {
		openTag: function(params,content) {
			return '<b>';
		},
		closeTag: function(params,content) {
			return '</b>';
		}
	},
	'code': {
		openTag: function(params,content) {
			return '<code>';
		},
		closeTag: function(params,content) {
			return '</code>';
		},
		noParse: true
	},
	'color': {
		openTag: function(params,content) {
			var colorCode = params.substr(1) || "inherit";
			BBCodeParser.regExpAllowedColors.lastIndex = 0;
			BBCodeParser.regExpValidHexColors.lastIndex = 0;
			if ( !BBCodeParser.regExpAllowedColors.test( colorCode ) ) {
				if ( !BBCodeParser.regExpValidHexColors.test( colorCode ) ) {
					colorCode = "inherit";
				} else {
					if (colorCode.substr(0,1) !== "#") {
						colorCode = "#" + colorCode;
					}
				}
			}

			return '<span style="color:' + colorCode + '">';
		},
		closeTag: function(params,content) {
			return '</span>';
		}
	},
	'i': {
		openTag: function(params,content) {
			return '<i>';
		},
		closeTag: function(params,content) {
			return '</i>';
		}
	},
	'img': {
		openTag: function(params,content) {

			var myUrl = content;

			BBCodeParser.urlPattern.lastIndex = 0;
			if ( !BBCodeParser.urlPattern.test( myUrl ) ) {
				myUrl = "";
			}

			return '<img class="bbCodeImage" src="' + myUrl + '">';
		},
		closeTag: function(params,content) {
			return '';
		},
		content: function(params,content) {
			return '';
		}
	},
	'list': {
		openTag: function(params,content) {
			return '<ul>';
		},
		closeTag: function(params,content) {
			return '</ul>';
		},
		restrictChildrenTo: ["*", "li"]
	},
	'noparse': {
		openTag: function(params,content) {
			return '<p>';
		},
		closeTag: function(params,content) {
			return '</p>';
		},
		noParse: true
	},
	'quote': {
		openTag: function(params,content) {
			return '<q>';
		},
		closeTag: function(params,content) {
			return '</q>';
		}
	},
	's': {
		openTag: function(params,content) {
			return '<s>';
		},
		closeTag: function(params,content) {
			return '</s>';
		}
	},
	'size': {
		openTag: function(params,content) {
			var mySize = parseInt(params.substr(1),10) || 0;
			if (mySize < 10 || mySize > 20) {
				mySize = 'inherit';
			} else {
				mySize = mySize + 'px';
			}
			return '<span style="font-size:' + mySize + '">';
		},
		closeTag: function(params,content) {
			return '</span>';
		}
	},
	'u': {
		openTag: function(params,content) {
			return '<span style="text-decoration:underline">';
		},
		closeTag: function(params,content) {
			return '</span>';
		}
	},
	'url': {
		openTag: function(params,content) {

			var myUrl;

			if (!params) {
				myUrl = content.replace(/<.*?>/g,"");
			} else {
				myUrl = params.substr(1);
			}

			BBCodeParser.urlPattern.lastIndex = 0;
			if ( !BBCodeParser.urlPattern.test( myUrl ) ) {
				myUrl = "#";
			}

			return '<a href="' + myUrl + '">';
		},
		closeTag: function(params,content) {
			return '</a>';
		}
	}
};
