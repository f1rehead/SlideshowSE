window.slideshow_jquery_image_gallery_backend_script.editSlideshow.slideManager = function () {
	var $ = jQuery,
		self = {};

	self.uploader = null;

	self._delegatedBound = false;

	/**
	 * True when this is the slideshow CPT add/edit screen (classic or block).
	 */
	self.isSlideshowEditScreen = function () {
		var body = document.body;
		if ( ! body || ! body.classList.contains( 'post-type-slideshow' ) ) {
			return false;
		}
		return (
			body.classList.contains( 'post-php' ) ||
			body.classList.contains( 'post-new-php' ) ||
			body.classList.contains( 'block-editor-page' )
		);
	};

	/**
	 * Sortable + color pickers; safe to call again after meta boxes mount.
	 */
	self.refreshSlideChrome = function () {
		self.indexSlidesOrder();
		self.tryInitSortable();
		self.initStaticColorPickers();
	};

	self.tryInitSortable = function () {
		var $list = $( '.sortable-slides-list' );
		if ( ! $list.length || $list.data( 'ui-sortable' ) ) {
			return;
		}
		$list.sortable( {
			revert: true,
			placeholder: 'sortable-placeholder',
			forcePlaceholderSize: true,
			handle: '.hndle',
			stop: function () {
				self.indexSlidesOrder();
			},
			cancel: 'input, select, textarea',
		} );
	};

	/**
	 * Settings meta box fields only; avoid re-init on elements already wrapped.
	 */
	self.initStaticColorPickers = function () {
		$( '.wp-color-picker-field' ).each( function () {
			var $el = $( this );
			if ( ! $el.closest( '.wp-picker-container' ).length ) {
				$el.wpColorPicker( { width: 234 } );
			}
		} );
	};

	/**
	 * Delegated handlers so Insert / Open / Close work when the Slides meta box
	 * appears after document ready (block editor and delayed meta box layout).
	 */
	self.bindDelegatedSlideControls = function () {
		if ( self._delegatedBound ) {
			return;
		}
		self._delegatedBound = true;

		$( document )
			.off( 'click.slideshowSEOpen', '.open-slides-button' )
			.on( 'click.slideshowSEOpen', '.open-slides-button', function ( event ) {
				event.preventDefault();

				$( '.sortable-slides-list .sortable-slides-list-item' ).each(
					function ( listItemIndex, listItem ) {
						var $listItem = $( listItem );

						if ( ! $listItem.find( '.inside' ).is( ':visible' ) ) {
							$listItem.find( '.handlediv' ).trigger( 'click' );
						}
					}
				);
			} );

		$( document )
			.off( 'click.slideshowSEClose', '.close-slides-button' )
			.on( 'click.slideshowSEClose', '.close-slides-button', function ( event ) {
				event.preventDefault();

				$( '.sortable-slides-list .sortable-slides-list-item' ).each(
					function ( listItemIndex, listItem ) {
						var $listItem = $( listItem );

						if ( $listItem.find( '.inside' ).is( ':visible' ) ) {
							$listItem.find( '.handlediv' ).trigger( 'click' );
						}
					}
				);
			} );

		$( document )
			.off( 'click.slideshowSEInsText', '.slideshow-insert-text-slide' )
			.on( 'click.slideshowSEInsText', '.slideshow-insert-text-slide', function ( event ) {
				event.preventDefault();
				self.insertTextSlide( event );
			} );

		$( document )
			.off( 'click.slideshowSEInsVideo', '.slideshow-insert-video-slide' )
			.on( 'click.slideshowSEInsVideo', '.slideshow-insert-video-slide', function ( event ) {
				event.preventDefault();
				self.insertVideoSlide( event );
			} );

		$( document )
			.off( 'click.slideshowSEInsImg', '.slideshow-insert-image-slide' )
			.on( 'click.slideshowSEInsImg', '.slideshow-insert-image-slide', function ( event ) {
				self.mediaUploader( event );
			} );

		$( document )
			.off( 'click.slideshowSEDelete', '.slideshow-delete-slide' )
			.on( 'click.slideshowSEDelete', '.slideshow-delete-slide', function ( event ) {
				event.preventDefault();
				self.deleteSlide(
					$( event.currentTarget ).closest( '.sortable-slides-list-item' )
				);
			} );
	};

	/**
	 *
	 */
	self.init = function () {
		if ( ! self.isSlideshowEditScreen() ) {
			return;
		}

		window.slideshow_jquery_image_gallery_backend_script.editSlideshow.isCurrentPage = true;

		self.bindDelegatedSlideControls();
		self.refreshSlideChrome();

		$( window )
			.off( 'load.slideshowSESM' )
			.on( 'load.slideshowSESM', self.refreshSlideChrome );

		// Block editor: meta boxes can mount shortly after ready.
		window.setTimeout( self.refreshSlideChrome, 500 );
		window.setTimeout( self.refreshSlideChrome, 1500 );
	};

	/**
	 * Deletes slide from DOM
	 *
	 * @param $slide
	 */
	self.deleteSlide = function ( $slide ) {
		var confirmMessage = 'Are you sure you want to delete this slide?',
			extraData = window.slideshow_jquery_image_gallery_backend_script_editSlideshow;

		if (
			typeof extraData === 'object' &&
			typeof extraData.localization === 'object' &&
			extraData.localization.confirm !== undefined &&
			extraData.localization.confirm.length > 0
		) {
			confirmMessage = extraData.localization.confirm;
		}

		if ( ! confirm( confirmMessage ) ) {
			return;
		}

		self.removeDescriptionEditor( $slide );
		$slide.remove();
	};

	/**
	 * Removes a dynamically initialized TinyMCE instance before the slide is deleted.
	 *
	 * @param {jQuery} $slide Slide list item.
	 */
	self.removeDescriptionEditor = function ( $slide ) {
		var $textarea = $slide.find(
			'.slideshow-description-editor-field textarea.description'
		);
		var editorId = $textarea.attr( 'id' );

		if (
			! editorId ||
			typeof wp === 'undefined' ||
			! wp.editor ||
			typeof wp.editor.remove !== 'function'
		) {
			return;
		}

		wp.editor.remove( editorId );
	};

	/**
	 * Initializes TinyMCE on a newly inserted text slide description field.
	 *
	 * @param {jQuery} $textSlide Cloned text slide element.
	 */
	self.initTextSlideDescriptionEditor = function ( $textSlide ) {
		var externalData,
			editorSettings,
			$field,
			$textarea,
			editorId;

		if (
			typeof wp === 'undefined' ||
			! wp.editor ||
			typeof wp.editor.initialize !== 'function'
		) {
			return;
		}

		externalData =
			window.slideshow_jquery_image_gallery_backend_script_editSlideshow;

		if (
			! externalData ||
			typeof externalData.descriptionEditor !== 'object'
		) {
			return;
		}

		editorSettings = externalData.descriptionEditor;
		$field = $textSlide.find( '.slideshow-description-editor-field' );
		$textarea = $field.find( 'textarea.description' );

		if ( ! $textarea.length ) {
			return;
		}

		$field.find( '.wp-editor-wrap' ).remove();

		editorId =
			'slideshow-text-description-' +
			Date.now() +
			'-' +
			Math.floor( Math.random() * 10000 );
		$textarea.attr( 'id', editorId );

		wp.editor.initialize( editorId, editorSettings );
	};

	/**
	 * Loop through sortable slides list items, setting slide orders
	 */
	self.indexSlidesOrder = function () {
		$( '.sortable-slides-list .sortable-slides-list-item' ).each( function ( slideID, slide ) {
			$.each( $( slide ).find( 'input, select, textarea' ), function ( key, input ) {
				var $input = $( input ),
					name = $input.attr( 'name' );

				if ( name === undefined || name.length <= 0 ) {
					return;
				}

				name = name.replace( /[\[\]']+/g, ' ' ).split( ' ' );

				$input.attr(
					'name',
					name[ 0 ] + '[' + ( slideID + 1 ) + '][' + name[ 2 ] + ']'
				);
			} );
		} );
	};

	/**
	 * Opens the WordPress 3.5 media uploader.
	 */
	self.mediaUploader = function ( event ) {
		event.preventDefault();

		var uploaderTitle,
			externalData;

		if ( typeof wp === 'undefined' || ! wp.media ) {
			return;
		}

		if ( self.uploader ) {
			self.uploader.open();
			return;
		}

		externalData = window.slideshow_jquery_image_gallery_backend_script_editSlideshow;

		uploaderTitle = '';

		if (
			typeof externalData === 'object' &&
			typeof externalData.localization === 'object' &&
			externalData.localization.uploaderTitle !== undefined &&
			externalData.localization.uploaderTitle.length > 0
		) {
			uploaderTitle = externalData.localization.uploaderTitle;
		}

		self.uploader = wp.media.frames.slideshow_jquery_image_galler_uploader = wp.media(
			{
				frame: 'select',
				title: uploaderTitle,
				multiple: true,
				library: {
					type: 'image',
				},
			}
		);

		self.uploader.on( 'select', function () {
			var attachments = self.uploader.state().get( 'selection' ).toJSON(),
				attachment,
				attachmentID;

			for ( attachmentID in attachments ) {
				if ( ! attachments.hasOwnProperty( attachmentID ) ) {
					continue;
				}

				attachment = attachments[ attachmentID ];

				self.insertImageSlide(
					attachment.id,
					attachment.title,
					attachment.description,
					attachment.url,
					attachment.alt
				);
			}
		} );

		self.uploader.open();
	};

	/**
	 * Inserts image slide into the slides list
	 */
	self.insertImageSlide = function (
		id,
		title,
		description,
		src,
		alternativeText
	) {
		var $imageSlide = $( '.image-slide-template' )
			.find( '.sortable-slides-list-item' )
			.clone( true, true );

		$imageSlide.find( '.attachment' ).attr( 'src', src );
		$imageSlide.find( '.attachment' ).attr( 'title', title );
		$imageSlide.find( '.attachment' ).attr( 'alt', alternativeText );
		$imageSlide.find( '.title' ).attr( 'value', title );
		$imageSlide.find( '.description' ).html( description );
		$imageSlide.find( '.alternativeText' ).attr( 'value', alternativeText );
		$imageSlide.find( '.postId' ).attr( 'value', id );

		$imageSlide.find( '.title' ).attr( 'name', 'slides[0][title]' );
		$imageSlide.find( '.titleElementTagID' ).attr( 'name', 'slides[0][titleElementTagID]' );
		$imageSlide.find( '.description' ).attr( 'name', 'slides[0][description]' );
		$imageSlide
			.find( '.descriptionElementTagID' )
			.attr( 'name', 'slides[0][descriptionElementTagID]' );
		$imageSlide.find( '.url' ).attr( 'name', 'slides[0][url]' );
		$imageSlide.find( '.urlTarget' ).attr( 'name', 'slides[0][urlTarget]' );
		$imageSlide.find( '.alternativeText' ).attr( 'name', 'slides[0][alternativeText]' );
		$imageSlide.find( '.noFollow' ).attr( 'name', 'slides[0][noFollow]' );
		$imageSlide.find( '.type' ).attr( 'name', 'slides[0][type]' );
		$imageSlide.find( '.postId' ).attr( 'name', 'slides[0][postId]' );

		$( '.sortable-slides-list' ).prepend( $imageSlide );

		self.tryInitSortable();
		self.indexSlidesOrder();
	};

	/**
	 * Inserts text slide into the slides list
	 */
	self.insertTextSlide = function () {
		var $textSlide = $( '.text-slide-template' )
			.find( '.sortable-slides-list-item' )
			.clone( true, true );

		$textSlide.find( '.title' ).attr( 'name', 'slides[0][title]' );
		$textSlide.find( '.titleElementTagID' ).attr( 'name', 'slides[0][titleElementTagID]' );
		$textSlide.find( '.description' ).attr( 'name', 'slides[0][description]' );
		$textSlide
			.find( '.descriptionElementTagID' )
			.attr( 'name', 'slides[0][descriptionElementTagID]' );
		$textSlide.find( '.textColor' ).attr( 'name', 'slides[0][textColor]' );
		$textSlide.find( '.color' ).attr( 'name', 'slides[0][color]' );
		$textSlide.find( '.url' ).attr( 'name', 'slides[0][url]' );
		$textSlide.find( '.urlTarget' ).attr( 'name', 'slides[0][urlTarget]' );
		$textSlide.find( '.noFollow' ).attr( 'name', 'slides[0][noFollow]' );
		$textSlide.find( '.type' ).attr( 'name', 'slides[0][type]' );

		$textSlide.find( '.color, .textColor' ).wpColorPicker();

		$( '.sortable-slides-list' ).prepend( $textSlide );

		self.initTextSlideDescriptionEditor( $textSlide );
		self.tryInitSortable();
		self.indexSlidesOrder();
	};

	/**
	 * Inserts video slide into the slides list
	 */
	self.insertVideoSlide = function () {
		var $videoSlide = $( '.video-slide-template' )
			.find( '.sortable-slides-list-item' )
			.clone( true, true );

		$videoSlide.find( '.videoId' ).attr( 'name', 'slides[0][videoId]' );
		$videoSlide.find( '.showRelatedVideos' ).attr( 'name', 'slides[0][showRelatedVideos]' );
		$videoSlide.find( '.type' ).attr( 'name', 'slides[0][type]' );

		$( '.sortable-slides-list' ).prepend( $videoSlide );

		self.tryInitSortable();
		self.indexSlidesOrder();
	};

	$( document ).bind( 'slideshowBackendReady', self.init );

	return self;
}();
