/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * @return {Array<Object>} Slideshow choices from localized script data.
 */
function getSlideshows() {
	const g = typeof window !== 'undefined' ? window.globals : undefined;
	if ( ! g || ! g.slideshows ) {
		return [];
	}
	return Array.isArray( g.slideshows )
		? g.slideshows
		: Object.values( g.slideshows );
}

/**
 * @param {Object} props
 * @param {string} props.src
 * @param {string} [props.alt]
 * @param {number} [props.width]
 * @param {number} [props.height]
 * @return {JSX.Element} Aspect-ratio-safe preview image.
 */
function PreviewImage( { src, alt = '', width, height } ) {
	const imgProps = {
		className: 'f1rehead-slideshow-se-block-edit__preview-image',
		src,
		alt,
	};
	if ( width > 0 && height > 0 ) {
		imgProps.width = width;
		imgProps.height = height;
	}
	return <img { ...imgProps } />;
}

/**
 * @param {Object|null|undefined} preview First-slide preview payload from PHP.
 * @return {JSX.Element} Static confirmation preview.
 */
function SlideshowPreview( { preview } ) {
	const type = preview && preview.type ? preview.type : 'empty';

	let body = null;

	if ( type === 'attachment' && preview.imageUrl ) {
		body = (
			<PreviewImage
				src={ preview.imageUrl }
				alt={ preview.alt || '' }
				width={ preview.width }
				height={ preview.height }
			/>
		);
	} else if ( type === 'video' && preview.imageUrl ) {
		body = (
			<div className="f1rehead-slideshow-se-block-edit__preview-video">
				<PreviewImage
					src={ preview.imageUrl }
					alt={ preview.alt || '' }
					width={ preview.width || 480 }
					height={ preview.height || 360 }
				/>
				<span className="f1rehead-slideshow-se-block-edit__preview-badge">
					{ preview.label || __( 'Video slide', 'slideshow-se' ) }
				</span>
			</div>
		);
	} else if ( type === 'video' ) {
		body = (
			<div className="f1rehead-slideshow-se-block-edit__preview-message">
				{ preview.label || __( 'Video slide', 'slideshow-se' ) }
			</div>
		);
	} else if ( type === 'text' ) {
		const textStyle = {};
		if ( preview.backgroundColor ) {
			textStyle.backgroundColor = preview.backgroundColor;
		}
		if ( preview.textColor ) {
			textStyle.color = preview.textColor;
		}
		body = (
			<div
				className="f1rehead-slideshow-se-block-edit__preview-text"
				style={ textStyle }
			>
				{ preview.title ? (
					<div className="f1rehead-slideshow-se-block-edit__preview-text-title">
						{ preview.title }
					</div>
				) : null }
				{ preview.description ? (
					<div className="f1rehead-slideshow-se-block-edit__preview-text-description">
						{ preview.description }
					</div>
				) : null }
				{ ! preview.title && ! preview.description ? (
					<div className="f1rehead-slideshow-se-block-edit__preview-message">
						{ preview.label ||
							__( 'Text slide', 'slideshow-se' ) }
					</div>
				) : null }
			</div>
		);
	} else {
		body = (
			<div className="f1rehead-slideshow-se-block-edit__preview-message">
				{ __( 'This slideshow has no slides.', 'slideshow-se' ) }
			</div>
		);
	}

	return (
		<div className="f1rehead-slideshow-se-block-edit__preview-inner">
			{ body }
		</div>
	);
}

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
	const slideshows = getSlideshows();

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

	const blockProps = useBlockProps( {
		className: 'f1rehead-slideshow-se-block-edit',
	} );

	return (
		<div { ...blockProps }>
			<div className="f1rehead-slideshow-se-block-edit__preview">
				{ hasSelection && selectedSlideshow ? (
					<SlideshowPreview preview={ selectedSlideshow.preview } />
				) : hasSelection ? (
					<div className="f1rehead-slideshow-se-block-edit__preview-placeholder">
						{ __(
							'Selected slideshow is unavailable.',
							'slideshow-se'
						) }
					</div>
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
