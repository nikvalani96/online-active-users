( function ( blocks, element, blockEditor, components, serverSideRender ) {
	'use strict';

	var createElement = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;

	blocks.registerBlockType( 'online-active-users/active-users', {
		edit: function () {
			return createElement(
				'div',
				useBlockProps(),
				createElement(
					components.Placeholder,
					{
						icon: 'groups',
						label: 'Online Active Users'
					},
					createElement( serverSideRender, {
						block: 'online-active-users/active-users'
					} )
				)
			);
		},
		save: function () {
			return null;
		}
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.serverSideRender
);