/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	// config.skin = 'bootstrapck';
	// config.skin = 'moonocolor';

	config.extraPlugins = 'youtube';
	config.allowedContent = true;
	config.extraAllowedContent = 'iframe[*]';
	// Video width:
	config.youtube_width = '320';
	// Video height:
	config.youtube_height = '240';
	// Show related videos:
	config.youtube_related = false;
	// Use old embed code:
	config.youtube_older = false;
	// Enable privacy-enhanced mode:
	config.youtube_privacy = false;
	// responsivo
	config.youtube_responsive = true;

	config.entities = false;
	config.htmlEncodeOutput = false;
	// config.skin = 'moono-dark';	
};
