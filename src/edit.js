/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed from the editor.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute update callback.
 *
 * @return {JSX.Element} Block editor UI.
 */
export default function Edit( { attributes, setAttributes } ) {
	const slideshows = ( () => {
		const g = typeof window !== 'undefined' ? window.globals : undefined;
		if ( ! g || ! g.slideshows ) {
			return [];
		}
		return Array.isArray( g.slideshows )
			? g.slideshows
			: Object.values( g.slideshows );
	} )();

	function updateSlideshow( ev ) {
		setAttributes( {
			selectedSlideshow: ev.target.value,
		} );
	}

	const selectId = 'slideshow-se-block-slideshow-select';

	return (
		<div>
			<label
				className="components-placeholder__label"
				htmlFor={ selectId }
			>
				{ __( 'Slideshow', 'slideshow-se' ) }:
			</label>{ ' ' }
			<select
				id={ selectId }
				onChange={ updateSlideshow }
				value={ attributes.selectedSlideshow }
			>
				{ slideshows.map( ( slideshow ) => (
					<option value={ slideshow.ID } key={ slideshow.ID }>
						{ slideshow.post_title }
					</option>
				) ) }
			</select>
		</div>
	);
}
