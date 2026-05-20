/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

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
 * @param {string}   props.clientId      Unique block instance id (editor).
 *
 * @return {JSX.Element} Block editor UI.
 */
export default function Edit( { attributes, setAttributes, clientId } ) {
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

	const selectId = `slideshow-se-block-slideshow-select-${ clientId }`;
	const selected = attributes.selectedSlideshow
		? String( attributes.selectedSlideshow )
		: '';
	const selectedId = parseInt( selected, 10 );
	const hasSelection =
		selected !== '' &&
		selected !== '0' &&
		! Number.isNaN( selectedId );
	const selectedSlideshow = hasSelection
		? slideshows.find( ( s ) => Number( s.ID ) === selectedId )
		: null;
	const selectedHeightPx =
		selectedSlideshow &&
		typeof selectedSlideshow.height === 'number' &&
		selectedSlideshow.height > 0
			? selectedSlideshow.height
			: null;
	const previewStyle =
		selectedHeightPx !== null
			? {
					'--f1rehead-slideshow-preview-max-height': `${ selectedHeightPx }px`,
			  }
			: undefined;

	return (
		<div className="f1rehead-slideshow-se-block-edit">
			<div
				className="f1rehead-slideshow-se-block-edit__preview"
				style={ previewStyle }
			>
				{ hasSelection ? (
					<ServerSideRender
						block="f1rehead/slideshow"
						attributes={ attributes }
					/>
				) : (
					<div className="f1rehead-slideshow-se-block-edit__preview-placeholder">
						{ __(
							'Select a slideshow to preview it here.',
							'slideshow-se'
						) }
					</div>
				) }
			</div>
			<div className="f1rehead-slideshow-se-block-edit__controls">
				<label
					className="f1rehead-slideshow-se-block-edit__label"
					htmlFor={ selectId }
				>
					{ __( 'Slideshow', 'slideshow-se' ) }:
				</label>
				<select
					id={ selectId }
					className="f1rehead-slideshow-se-block-edit__select"
					onChange={ updateSlideshow }
					value={ selected }
				>
					<option value="">
						{ __( '— Select —', 'slideshow-se' ) }
					</option>
					{ slideshows.map( ( slideshow ) => (
						<option value={ slideshow.ID } key={ slideshow.ID }>
							{ slideshow.post_title }
						</option>
					) ) }
				</select>
			</div>
		</div>
	);
}
