/**
 * Gutenberg blocks that are disabled by BC Douglas Fir Theme
 */
window.onload = function () {
	wp.blocks.unregisterBlockType('core/table');
	wp.blocks.unregisterBlockType('core/verse');
	wp.blocks.unregisterBlockType('core/video');
	wp.blocks.unregisterBlockType('core/pullquote');
	wp.blocks.unregisterBlockType('core/code');
	wp.blocks.unregisterBlockType('core/latest-comments');
	wp.blocks.unregisterBlockType('core/columns');
	wp.blocks.unregisterBlockType('core/column');
	wp.blocks.unregisterBlockType('core/button');
	wp.blocks.unregisterBlockType('core/buttons');
	wp.blocks.unregisterBlockType('core/search');
	wp.blocks.unregisterBlockType('core/calendar');

	wp.blocks.unregisterBlockType('core/navigation');
	wp.blocks.unregisterBlockType('core/navigation-link');
	wp.blocks.unregisterBlockType('core/navigation-submenu');
	wp.blocks.unregisterBlockType('core/site-logo');
	wp.blocks.unregisterBlockType('core/site-title');
	wp.blocks.unregisterBlockType('core/site-tagline');
	wp.blocks.unregisterBlockType('core/post-comments');
	wp.blocks.unregisterBlockType('core/loginout');
	wp.blocks.unregisterBlockVariation( 'core/group', 'group-row' );

	// WordPress 6.0.x
	wp.blocks.unregisterBlockType('core/avatar');
	wp.blocks.unregisterBlockType('core/comments-query-loop');
	wp.blocks.unregisterBlockType('core/post-comments-form');
	wp.blocks.unregisterBlockType('core/post-author-biography');

	// WordPress 6.1.x
	wp.blocks.unregisterBlockType('core/comments');

	// WordPress 7.0 and 7.1
	wp.blocks.unregisterBlockType('core/playlist');
	wp.blocks.unregisterBlockType('core/tabs');
	wp.blocks.unregisterBlockType('core/tab-list');
	wp.blocks.unregisterBlockType('core/tab-panels');
	wp.blocks.unregisterBlockType('core/tab-panel');
	wp.blocks.unregisterBlockType('core/accordion');
};
