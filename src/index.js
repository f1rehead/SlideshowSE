/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';


/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 */
registerBlockType('boonstra/slideshow', {
	/**
	 * This is the display title for your block, which can be translated with `i18n` functions.
	 * The block inserter will show this name.
	 */
	title: __('Slideshow', 'boonstra'),

	/**
	 * This is a short description for your block, can be translated with `i18n` functions.
	 * It will be shown in the Block Tab in the Settings Sidebar.
	 */
	description: __('A slideshow block', 'boonstra'),

	/**
	 * Blocks are grouped into categories to help users browse and discover them.
	 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
	 */
	category: 'common',

	/**
	 * An icon property should be specified to make it easier to identify a block.
	 * These can be any of WordPress’ Dashicons, or a custom svg element.
	 */
	icon: 'format-gallery',

	/**
	 * Optional block extended support features.
	 */
	supports: {
		// Removes support for an HTML mode.
		html: false,
	},

	keywords: [
		__('slideshow carousel'),
		__('image photo photograph'),
		__('video youtibe vimeo'),
	],
	attributes: {
		selectedSlideshow: { type: 'string' },
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	edit: (props) => {
		// Creates a <p class='wp-block-cgb-block-f1reslider'></p>.

		function updateSlideshow(ev) {
			props.setAttributes({
				selectedSlideshow: ev.target.value,
			});
		}

		return (
			<div>
				<label>Slideshow: </label> <select onChange={updateSlideshow} value={props.attributes.selectedSlideshow}>
					{
						Object.values(globals.slideshows).map(slideshow => {
							return (
								<option value={slideshow.ID} key={slideshow.ID}>{slideshow.post_title}</option>
							);
						})
					}
				</select>
			</div>
		);
	},

	/**
	 * The "save" property must be specified and must be a valid function. This is a dynamic block,
	 * so return null.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @return {null}
	 */
	save: () => {
		return null;
	},
});
