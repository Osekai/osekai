/* 
 * Javascript BBCode Parser
 * @author Philip Nicolcev
 * @license MIT License
 */

var BBCodeParser = (function(parserTags, parserColors) {
	'use strict';
	
	var me = {},
		urlPattern = /^(?:https?|file|c):(?:\/{1,3}|\\{1})[-a-zA-Z0-9:;@#%&()~_?\+=\/\\\.]*$/,
		emailPattern = /[^\s@]+@[^\s@]+\.[^\s@]+/,
		fontFacePattern = /^([a-z][a-z0-9_]+|"[a-z][a-z0-9_\s]+")$/i,
		tagNames = [],
		tagNamesNoParse = [],
		regExpAllowedColors,
		regExpValidHexColors = /^#?[a-fA-F0-9]{6}$/,
		ii, tagName, len;
		
	// create tag list and lookup fields
	for (tagName in parserTags) {
		if (!parserTags.hasOwnProperty(tagName))
			continue;
		
		if (tagName === '*') {
			tagNames.push('\\' + tagName);
		} else {
			tagNames.push(tagName);
			if ( parserTags[tagName].noParse ) {
				tagNamesNoParse.push(tagName);
			}
		}

		parserTags[tagName].validChildLookup = {};
		parserTags[tagName].validParentLookup = {};
		parserTags[tagName].restrictParentsTo = parserTags[tagName].restrictParentsTo || [];
		parserTags[tagName].restrictChildrenTo = parserTags[tagName].restrictChildrenTo || [];

		len = parserTags[tagName].restrictChildrenTo.length;
		for (ii = 0; ii < len; ii++) {
			parserTags[tagName].validChildLookup[ parserTags[tagName].restrictChildrenTo[ii] ] = true;
		}
		len = parserTags[tagName].restrictParentsTo.length;
		for (ii = 0; ii < len; ii++) {
			parserTags[tagName].validParentLookup[ parserTags[tagName].restrictParentsTo[ii] ] = true;
		}
	}
	
	regExpAllowedColors = new RegExp('^(?:' + parserColors.join('|') + ')$');
	
	/* 
	 * Create a regular expression that captures the innermost instance of a tag in an array of tags
	 * The returned RegExp captures the following in order:
	 * 1) the tag from the array that was matched
	 * 2) all (optional) parameters included in the opening tag
	 * 3) the contents surrounded by the tag
	 * 
	 * @param {type} tagsArray - the array of tags to capture
	 * @returns {RegExp}
	 */
	function createInnermostTagRegExp(tagsArray) {
		var openingTag = '\\[(' + tagsArray.join('|') + ')\\b(?:[ =]([\\w"#\\-\\:\\/= ]*?))?\\]',
			notContainingOpeningTag = '((?:(?=([^\\[]+))\\4|\\[(?!\\1\\b(?:[ =](?:[\\w"#\\-\\:\\/= ]*?))?\\]))*?)',
			closingTag = '\\[\\/\\1\\]';
			
		return new RegExp( openingTag + notContainingOpeningTag + closingTag, 'i');
	}
	
	/*
	 * Escape the contents of a tag and mark the tag with a null unicode character.
	 * To be used in a loop with a regular expression that captures tags.
	 * Marking the tag prevents it from being matched again.
	 * 
	 * @param {type} matchStr - the full match, including the opening and closing tags
	 * @param {type} tagName - the tag that was matched
	 * @param {type} tagParams - parameters passed to the tag
	 * @param {type} tagContents - everything between the opening and closing tags
	 * @returns {String} - the full match with the tag contents escaped and the tag marked with \u0000
	 */
	function escapeInnerTags(matchStr, tagName, tagParams, tagContents) {
		tagParams = tagParams || "";
		tagContents = tagContents || "";
		tagContents = tagContents.replace(/\[/g, "&#91;").replace(/\]/g, "&#93;");
		return "[\u0000" + tagName + tagParams + "]" + tagContents + "[/\u0000" + tagName + "]";
	}
	
	/* 
	 * Escape all BBCodes that are inside the given tags.
	 * 
	 * @param {string} text - the text to search through
	 * @param {string[]} tags - the tags to search for
	 * @returns {string} - the full text with the required code escaped
	 */
	function escapeBBCodesInsideTags(text, tags) {
		var innerMostRegExp;
		if (tags.length === 0 || text.length < 7)
			return text;
		innerMostRegExp = createInnermostTagRegExp(tags);
		while (
			text !== (text = text.replace(innerMostRegExp, escapeInnerTags))
		);
		return text.replace(/\u0000/g,'');
	}
	
	/*
	 * Process a tag and its contents according to the rules provided in parserTags.
	 * 
	 * @param {type} matchStr - the full match, including the opening and closing tags
	 * @param {type} tagName - the tag that was matched
	 * @param {type} tagParams - parameters passed to the tag
	 * @param {type} tagContents - everything between the opening and closing tags
	 * @returns {string} - the fully processed tag and its contents
	 */
	function replaceTagsAndContent(matchStr, tagName, tagParams, tagContents) {
		tagName = tagName.toLowerCase();
		tagParams = tagParams || "";
		tagContents = tagContents || "";
		return parserTags[tagName].openTag(tagParams, tagContents) + (parserTags[tagName].content ? parserTags[tagName].content(tagParams, tagContents) : tagContents) + parserTags[tagName].closeTag(tagParams, tagContents);
	}
	
	function processTags(text, tagNames) {
		var innerMostRegExp;
		
		if (tagNames.length === 0 || text.length < 7)
			return text;
		
		innerMostRegExp = createInnermostTagRegExp(tagNames);
		
		while (
			text !== (text = text.replace(innerMostRegExp, replaceTagsAndContent))
		);
		
		return text;
	}
	
	/*
	 * Public Methods and Properties
	 */
	me.process = function(text, config) {
		text = escapeBBCodesInsideTags(text, tagNamesNoParse);
		
		return processTags(text, tagNames);
	};
	
	me.allowedTags = tagNames;
	me.urlPattern = urlPattern;
	me.emailPattern = emailPattern;
	me.regExpAllowedColors = regExpAllowedColors;
	me.regExpValidHexColors = regExpValidHexColors;
		
	return me;
})(parserTags, parserColors);
